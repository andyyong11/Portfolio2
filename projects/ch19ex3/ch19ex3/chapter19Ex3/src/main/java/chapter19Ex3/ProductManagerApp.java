//Andy Yong
//12/4/2024
//Product Manager App that creates, reads, updates, and deletes products from a list using A database
package chapter19Ex3;

import java.util.ArrayList;
import javafx.application.Application;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.*;
import javafx.stage.Stage;

public class ProductManagerApp extends Application {

    private TextField codeField = new TextField();
    private TextField descriptionField = new TextField();
    private TextField priceField = new TextField();
    private ListView<String> productList = new ListView<>();

    private Button searchButton;
    private Button cancelButton;
    private Button addButton;
    private Button updateButton;
    private Button deleteButton;
    private Button exitButton;

    private HBox searchCancelBox;
    private HBox actionButtonBox;

    @Override
    public void start(Stage primaryStage) {
        // Labels
        Label codeLabel = new Label("Product Code:");
        Label descriptionLabel = new Label("Description:");
        Label priceLabel = new Label("Price:");
        Label productListLabel = new Label("Product List:");

        // Initialize Buttons
        searchButton = new Button("Search");
        cancelButton = new Button("Cancel");
        addButton = new Button("Add");
        updateButton = new Button("Update");
        deleteButton = new Button("Delete");
        exitButton = new Button("Exit");

        // Disable Update and Delete initially
        updateButton.setDisable(true);
        deleteButton.setDisable(true);

        // Button Event Handlers
        searchButton.setOnAction(e -> searchProduct());
        cancelButton.setOnAction(e -> resetUI());
        addButton.setOnAction(e -> addProduct());
        updateButton.setOnAction(e -> toggleUIForAction("Update"));
        deleteButton.setOnAction(e -> toggleUIForAction("Delete"));
        exitButton.setOnAction(e -> primaryStage.close());

        // Layout for labels and text fields
        GridPane inputGrid = new GridPane();
        inputGrid.setHgap(10);
        inputGrid.setVgap(10);
        inputGrid.setPadding(new Insets(10));
        inputGrid.add(codeLabel, 0, 0);
        inputGrid.add(codeField, 1, 0);
        inputGrid.add(descriptionLabel, 0, 1);
        inputGrid.add(descriptionField, 1, 1);
        inputGrid.add(priceLabel, 0, 2);
        inputGrid.add(priceField, 1, 2);

        // Layout for Search and Cancel buttons
        searchCancelBox = new HBox(10, searchButton, cancelButton);
        searchCancelBox.setAlignment(Pos.CENTER_LEFT);
        searchCancelBox.setPadding(new Insets(10, 0, 10, 0));

        // Layout for Add, Update, Delete, and Exit buttons
        actionButtonBox = new HBox(10, addButton, updateButton, deleteButton, exitButton);
        actionButtonBox.setAlignment(Pos.CENTER);
        actionButtonBox.setPadding(new Insets(10, 0, 10, 0));

        // Layout for ListView and its label
        VBox listBoxContainer = new VBox(5, productListLabel, productList);
        listBoxContainer.setPadding(new Insets(10));

        // Main layout
        VBox mainLayout = new VBox(10, inputGrid, searchCancelBox, listBoxContainer, actionButtonBox);
        mainLayout.setPadding(new Insets(10));

        // Populate the ListView
        populateProductList();

        // Set ListView selection behavior
        productList.getSelectionModel().selectedItemProperty().addListener((obs, oldSelection, newSelection) -> {
            boolean hasSelection = newSelection != null;
            updateButton.setDisable(!hasSelection);
            deleteButton.setDisable(!hasSelection);

            if (hasSelection) {
                String[] parts = newSelection.split(" - ");
                codeField.setText(parts[0]);
                descriptionField.setText(parts[1]);
                priceField.setText(parts[2].replace("$", ""));
            } else {
                clearFields();
            }
        });

        // Scene and Stage setup
        Scene scene = new Scene(mainLayout, 450, 500);
        primaryStage.setTitle("Manager Appli");
        primaryStage.setScene(scene);
        primaryStage.show();
    }

    private void populateProductList() {
        ArrayList<Product> products = ProductDB.getAll();
        productList.getItems().clear();
        if (products != null) {
            for (Product product : products) {
                productList.getItems().add(product.getCode() + " - " + product.getDescription() + " - $" + String.format("%.2f", product.getPrice()));
            }
        }
    }

    private void searchProduct() {
        String code = codeField.getText().trim();
        String description = descriptionField.getText().trim();
        String price = priceField.getText().trim();

        // Validate price field (must be numeric or empty)
        if (!price.isEmpty()) {
            try {
                Double.parseDouble(price); // Check if price is a valid number
            } catch (NumberFormatException e) {
                showError("Invalid price. Please enter a numeric value.");
                return;
            }
        }

        // Fetch matching products
        ArrayList<Product> matchingProducts = ProductDB.get(code, description, price);

        productList.getItems().clear(); // Clear the current list
        if (!matchingProducts.isEmpty()) {
            for (Product product : matchingProducts) {
                productList.getItems().add(product.getCode() + " - " + product.getDescription() + " - $" + String.format("%.2f", product.getPrice()));
            }
        } else {
            showError("No matching products found.");
        }
    }

    private void addProduct() {
        String code = codeField.getText();
        String description = descriptionField.getText();
        double price;
        try {
            price = Double.parseDouble(priceField.getText());
        } catch (NumberFormatException e) {
            showError("Invalid price. Please enter a numeric value.");
            return;
        }

        Product product = new Product(code, description, price);
        if (ProductDB.add(product)) {
            populateProductList();
            clearFields();
        } else {
            showError("Error adding product.");
        }
    }

    private void updateProduct() {
        String code = codeField.getText();
        String description = descriptionField.getText();
        double price;
        try {
            price = Double.parseDouble(priceField.getText());
        } catch (NumberFormatException e) {
            showError("Invalid price. Please enter a numeric value.");
            return;
        }

        Product product = new Product(code, description, price);
        if (ProductDB.update(product)) {
            populateProductList();
            clearFields();
        } else {
            showError("Error updating product.");
        }
    }

    private void deleteProduct() {
        String code = codeField.getText();
        Product productToDelete = new Product(code, "", 0);
        if (ProductDB.delete(productToDelete)) {
            populateProductList();
            clearFields();
        } else {
            showError("Error deleting product.");
        }
    }

    private void toggleUIForAction(String action) {
        addButton.setVisible(false);
        updateButton.setVisible(false);
        deleteButton.setVisible(false);
        exitButton.setVisible(false);

        if (action.equals("Add")) {
            codeField.setDisable(false); // Enable for adding a new product
        } else {
            codeField.setDisable(true); // Disable for other actions
        }

        searchButton.setText(action);
        searchButton.setVisible(true);
        searchButton.setOnAction(e -> {
            if (action.equals("Delete")) {
                deleteProduct();
            } else if (action.equals("Update")) {
                updateProduct();
            } else if (action.equals("Add")) {
                addProduct();
            }
            resetUI();
        });

        searchCancelBox.getChildren().clear();
        searchCancelBox.getChildren().addAll(searchButton, cancelButton);
    }

    private void resetUI() {
        // Restore visibility of all buttons
        addButton.setVisible(true);
        updateButton.setVisible(true);
        deleteButton.setVisible(true);
        exitButton.setVisible(true);

        // Restore the default Search button behavior
        searchButton.setText("Search");
        searchButton.setVisible(true);
        searchButton.setOnAction(e -> searchProduct());

        // Restore the layout of the Search and Cancel buttons
        searchCancelBox.getChildren().clear();
        searchCancelBox.getChildren().addAll(searchButton, cancelButton);

        // Restore default Cancel button behavior
        cancelButton.setOnAction(e -> clearFields());

        // Re-enable the Product Code field
        codeField.setDisable(false);
    }


    private void clearFields() {
        codeField.clear();
        descriptionField.clear();
        priceField.clear();
    }

    private void showError(String message) {
        Alert alert = new Alert(Alert.AlertType.ERROR, message, ButtonType.OK);
        alert.showAndWait();
    }

    public static void main(String[] args) {
        launch(args);
    }
}

//Andy Yong
//12/3/2024
//Database file with a connection to the db 
//and methods to grab items from the database

package chapter19Ex3;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;

public class ProductDB {
    
    private static Connection getConnection() throws SQLException {
        String dbUrl = "jdbc:sqlite:products.sqlite";
        Connection connection = DriverManager.getConnection(dbUrl);
        return connection;
    }
    
    public static ArrayList<Product> getAll() {
        String sql = "SELECT ProductCode, Description, Price "
                   + "FROM Products ORDER BY ProductCode ASC";
        var products = new ArrayList<Product>();
        try (Connection connection = getConnection();
             PreparedStatement ps = connection.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                String code = rs.getString("ProductCode");
                String description = rs.getString("Description");
                double price = rs.getDouble("Price");

                Product p = new Product(code, description, price);
                products.add(p);
            }
            return products;
        } catch (SQLException e) {
            System.err.println(e);
            return null;
        }
    }

    public static ArrayList<Product> get(String code, String description, String price) {
        String sql = "SELECT ProductCode, Description, Price " +
                     "FROM Products " +
                     "WHERE ProductCode LIKE ? AND Description LIKE ? AND CAST(Price AS TEXT) LIKE ?";
        ArrayList<Product> products = new ArrayList<>();

        try (Connection connection = getConnection();
             PreparedStatement ps = connection.prepareStatement(sql)) {

            // Add wildcards for partial matching
            ps.setString(1, code.isEmpty() ? "%" : code + "%"); // Match code or ignore if empty
            ps.setString(2, description.isEmpty() ? "%" : "%" + description + "%"); // Match description or ignore if empty
            ps.setString(3, price.isEmpty() ? "%" : price + "%"); // Match price or ignore if empty

            ResultSet rs = ps.executeQuery();
            while (rs.next()) {
                String productCode = rs.getString("ProductCode");
                String productDescription = rs.getString("Description");
                double productPrice = rs.getDouble("Price");
                products.add(new Product(productCode, productDescription, productPrice));
            }
        } catch (SQLException e) {
            System.err.println(e);
        }

        return products;
    }


    public static boolean add(Product p) {
        String sql = "INSERT INTO Products (ProductCode, Description, Price) "
                   + "VALUES (?, ?, ?)";
        try (Connection connection = getConnection();
             PreparedStatement ps = connection.prepareStatement(sql)) {
            ps.setString(1, p.getCode());
            ps.setString(2, p.getDescription());
            ps.setDouble(3, p.getPrice());
            ps.executeUpdate();
            return true;
        } catch (SQLException e) {
            System.err.println(e);
            return false;
        }
    }

    public static boolean delete(Product p) {
        String sql = "DELETE FROM Products "
                   + "WHERE ProductCode = ?";
        try (Connection connection = getConnection();
             PreparedStatement ps = connection.prepareStatement(sql)) {
            ps.setString(1, p.getCode());
            ps.executeUpdate();
            return true;
        } catch (SQLException e) {
            System.err.println(e);
            return false;
        }
    }

    public static boolean update(Product p) {
        String sql = "UPDATE Products SET "
                   + "  Description = ?, "
                   + "  Price = ? "
                   + "WHERE ProductCode = ?";
        try (Connection connection = getConnection();
             PreparedStatement ps = connection.prepareStatement(sql)) {
            ps.setString(1, p.getDescription());
            ps.setDouble(2, p.getPrice());
            ps.setString(3, p.getCode());
            ps.executeUpdate();
            return true;
        } catch (SQLException e) {
            System.err.println(e);
            return false;
        }
    }
    
}
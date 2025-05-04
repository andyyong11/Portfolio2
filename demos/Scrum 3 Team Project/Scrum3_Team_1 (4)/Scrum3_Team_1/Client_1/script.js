// Helper function for API calls
async function apiCall(action, type, data = null) {
    const url = new URL('../Provider_1/API_1.php', window.location.href);
    
    let options = {
        method: data ? 'POST' : 'GET',
        headers: {}
    };

    if (action === 'get_all' || action === 'get') {
        // For GET requests, append parameters to URL
        url.searchParams.append('action', action);
        url.searchParams.append('type', type);
    } else if (data instanceof FormData) {
        // For POST requests with FormData
        data.append('action', action);
        data.append('type', type);
        options.body = data;
    } else {
        // For POST requests without data
        const formData = new FormData();
        formData.append('action', action);
        formData.append('type', type);
        options.body = formData;
    }

    try {
        console.log('Making API call:', { action, type, url: url.toString(), method: options.method });
        const response = await fetch(url, options);
        const result = await response.json();
        console.log('API Response:', result);
        
        if (!result.success && result.message) {
            throw new Error(result.message);
        }
        return result;
    } catch (error) {
        console.error('API Error:', error);
        return { 
            success: false, 
            message: error.message || 'Error occurred while fetching data'
        };
    }
}

// Helper function to populate dropdowns
async function populateDropdowns() {
    // Populate category dropdown
    const categoryResult = await apiCall("get_all", "category");
    const categorySelect = document.getElementById("categorySelect");
    if (categorySelect && categoryResult.success && categoryResult.data) {
        categorySelect.innerHTML = '<option value="">Select a category</option>';
        categoryResult.data.forEach(category => {
            categorySelect.innerHTML += `<option value="${category.category_id}">${category.name}</option>`;
        });
    }

    // Populate menu item dropdown
    const menuResult = await apiCall("get_all", "menu_item");
    const menuItemSelect = document.getElementById("menuItemSelect");
    if (menuItemSelect && menuResult.success && menuResult.data) {
        menuItemSelect.innerHTML = '<option value="">Select a menu item</option>';
        menuResult.data.forEach(item => {
            menuItemSelect.innerHTML += `<option value="${item.item_id}">${item.name}</option>`;
        });
    }
}

// Helper function to create table
function createTable(data, columns) {
    if (!data || data.length === 0) {
        return '<p>No data available</p>';
    }

    let table = '<table><thead><tr>';
    columns.forEach(column => {
        table += `<th>${column.label}</th>`;
    });
    table += '<th>Actions</th></tr></thead><tbody>';

    data.forEach(item => {
        table += '<tr>';
        columns.forEach(column => {
            // For ID fields, just display the value without making it editable
            if (column.key.endsWith('_id')) {
                table += `<td>${item[column.key]}</td>`;
            } else {
                table += `<td class="editable" data-field="${column.key}">${item[column.key]}</td>`;
            }
        });
        table += `<td>
            <button onclick="editItem('${item[columns[0].key]}', '${columns[0].type}')" class="edit-btn">Edit</button>
            <button onclick="deleteItem('${item[columns[0].key]}', '${columns[0].type}')" class="delete-btn">Delete</button>
        </td>`;
        table += '</tr>';
    });

    table += '</tbody></table>';
    return table;
}

// Category Functions
document.getElementById("categoryForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    const result = await apiCall("create", "category", new FormData(this));
    document.getElementById("categoryResult").innerHTML = 
        `<div class="${result.success ? 'success' : 'error'}-message">${result.message || 'Category created successfully!'}</div>`;
    if (result.success) {
        this.reset();
        await loadCategories();
        await populateDropdowns(); // Refresh dropdowns after creating a category
    }
});

async function loadCategories() {
    const result = await apiCall("get_all", "category");
    const columns = [
        { key: 'category_id', label: 'ID', type: 'category' },
        { key: 'name', label: 'Name' },
        { key: 'description', label: 'Description' }
    ];
    document.getElementById("categories").innerHTML = createTable(result.data, columns);
}

// Menu Item Functions
document.getElementById("menuForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    const result = await apiCall("create", "menu_item", new FormData(this));
    document.getElementById("menuResult").innerHTML = 
        `<div class="${result.success ? 'success' : 'error'}-message">${result.message || 'Menu item created successfully!'}</div>`;
    if (result.success) {
        this.reset();
        await loadMenuItems();
        await populateDropdowns(); // Refresh dropdowns after creating a menu item
    }
});

async function loadMenuItems() {
    const result = await apiCall("get_all", "menu_item");
    const categoryResult = await apiCall("get_all", "category");
    
    // Create a map of category IDs to names
    const categoryMap = {};
    if (categoryResult.success && categoryResult.data) {
        categoryResult.data.forEach(category => {
            categoryMap[category.category_id] = category.name;
        });
    }

    // Add category names to menu items
    if (result.success && result.data) {
        result.data.forEach(item => {
            item.category_name = categoryMap[item.category_id] || 'Unknown Category';
        });
    }

    const columns = [
        { key: 'item_id', label: 'ID', type: 'menu_item' },
        { key: 'name', label: 'Name' },
        { key: 'price', label: 'Price' },
        { key: 'category_name', label: 'Category' },
        { key: 'description', label: 'Description' },
        { key: 'available', label: 'Available' }
    ];
    document.getElementById("menuItems").innerHTML = createTable(result.data, columns);
}

// Ingredient Functions
document.getElementById("ingredientForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    const result = await apiCall("create", "ingredient", new FormData(this));
    document.getElementById("ingredientResult").innerHTML = 
        `<div class="${result.success ? 'success' : 'error'}-message">${result.message || 'Ingredient created successfully!'}</div>`;
    if (result.success) {
        this.reset();
        loadIngredients();
    }
});

async function loadIngredients() {
    const result = await apiCall("get_all", "ingredient");
    const menuResult = await apiCall("get_all", "menu_item");
    
    // Create a map of menu item IDs to names
    const menuItemMap = {};
    if (menuResult.success && menuResult.data) {
        menuResult.data.forEach(item => {
            menuItemMap[item.item_id] = item.name;
        });
    }

    // Add menu item names to ingredients
    if (result.success && result.data) {
        result.data.forEach(ingredient => {
            ingredient.menu_item_name = menuItemMap[ingredient.item_id] || 'Unknown Menu Item';
        });
    }

    const columns = [
        { key: 'ingredient_id', label: 'ID', type: 'ingredient' },
        { key: 'menu_item_name', label: 'Menu Item' },
        { key: 'name', label: 'Name' },
        { key: 'quantity', label: 'Quantity' },
        { key: 'unit', label: 'Unit' }
    ];
    document.getElementById("ingredients").innerHTML = createTable(result.data, columns);
}

// Edit and Delete Functions
async function editItem(id, type) {
    const row = event.target.closest('tr');
    const cells = row.getElementsByTagName('td');
    const form = document.createElement('form');
    form.className = 'edit-form';

    // Create input fields based on the type
    switch(type) {
        case 'category':
            form.innerHTML = `
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" value="${cells[1].textContent}" required>
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <input type="text" name="description" value="${cells[2].textContent}">
                </div>
            `;
            break;
        case 'menu_item':
            form.innerHTML = `
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" value="${cells[1].textContent}" required>
                </div>
                <div class="form-group">
                    <label>Price:</label>
                    <input type="number" name="price" value="${cells[2].textContent}" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Category ID:</label>
                    <span class="readonly-field">${cells[3].textContent}</span>
                    <input type="hidden" name="category_id" value="${cells[3].textContent}">
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <input type="text" name="description" value="${cells[4].textContent}">
                </div>
                <div class="form-group">
                    <label>Available:</label>
                    <select name="available">
                        <option value="1" ${cells[5].textContent === '1' ? 'selected' : ''}>Yes</option>
                        <option value="0" ${cells[5].textContent === '0' ? 'selected' : ''}>No</option>
                    </select>
                </div>
            `;
            break;
        case 'ingredient':
            form.innerHTML = `
                <div class="form-group">
                    <label>Menu Item ID:</label>
                    <span class="readonly-field">${cells[1].textContent}</span>
                    <input type="hidden" name="item_id" value="${cells[1].textContent}">
                </div>
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" value="${cells[2].textContent}" required>
                </div>
                <div class="form-group">
                    <label>Quantity:</label>
                    <input type="number" name="quantity" value="${cells[3].textContent}" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Unit:</label>
                    <input type="text" name="unit" value="${cells[4].textContent}" required>
                </div>
            `;
            break;
    }

    // Add save and cancel buttons
    form.innerHTML += `
        <div class="button-group">
            <button type="submit" class="save-btn">Save</button>
            <button type="button" onclick="cancelEdit()" class="cancel-btn">Cancel</button>
        </div>
    `;

    // Replace row content with form
    const container = document.createElement('td');
    container.colSpan = cells.length;
    container.appendChild(form);
    row.innerHTML = '';
    row.appendChild(container);

    // Handle form submission
    form.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        formData.append('id', id);
        
        const result = await apiCall('update', type, formData);
        if (result.success) {
            switch(type) {
                case 'category': loadCategories(); break;
                case 'menu_item': loadMenuItems(); break;
                case 'ingredient': loadIngredients(); break;
            }
        } else {
            alert('Failed to update: ' + (result.message || 'Unknown error'));
        }
    };
}

function cancelEdit() {
    // Reload all data to cancel edit
    loadAllData();
}

async function deleteItem(id, type) {
    if (confirm('Are you sure you want to delete this item?')) {
        const formData = new FormData();
        formData.append('id', id);
        
        const result = await apiCall('delete', type, formData);
        if (result.success) {
            switch(type) {
                case 'category': loadCategories(); break;
                case 'menu_item': loadMenuItems(); break;
                case 'ingredient': loadIngredients(); break;
            }
        } else {
            alert('Failed to delete: ' + (result.message || 'Unknown error'));
        }
    }
}

// Load all data function
async function loadAllData() {
    await Promise.all([
        loadCategories(),
        loadMenuItems(),
        loadIngredients()
    ]);
    await populateDropdowns(); // Populate dropdowns after loading all data
}

// Initial load
document.addEventListener('DOMContentLoaded', loadAllData);
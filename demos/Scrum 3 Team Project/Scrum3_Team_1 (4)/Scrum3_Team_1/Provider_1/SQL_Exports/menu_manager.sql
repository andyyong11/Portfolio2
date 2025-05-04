
-- Create database
CREATE DATABASE IF NOT EXISTS menu_manager;
USE menu_manager;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS ingredients;
DROP TABLE IF EXISTS menu_items;
DROP TABLE IF EXISTS categories;

-- Create categories table
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create menu_items table
CREATE TABLE menu_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(5,2) NOT NULL,
    category_id INT,
    description TEXT,
    available TINYINT DEFAULT 1,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Create ingredients table
CREATE TABLE ingredients (
    ingredient_id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    name VARCHAR(100) NOT NULL,
    quantity DECIMAL(5,2),
    unit VARCHAR(50),
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id)
);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Breakfast', 'Morning dishes'),
('Lunch', 'Midday meals'),
('Dinner', 'Evening meals'),
('Drinks', 'Beverages'),
('Desserts', 'Sweet treats');

-- Insert sample menu items
INSERT INTO menu_items (name, price, category_id, description, available) VALUES
('Pancakes', 5.99, 1, 'Fluffy pancakes with syrup', 1),
('BLT Sandwich', 7.49, 2, 'Bacon, lettuce, tomato on toasted bread', 1),
('Steak Dinner', 15.99, 3, 'Grilled steak with sides', 1),
('Lemonade', 2.49, 4, 'Fresh lemonade', 1),
('Chocolate Cake', 4.50, 5, 'Rich chocolate layer cake', 1),
('Omelette', 6.99, 1, 'Three-egg omelette with cheese', 1),
('Iced Coffee', 3.25, 4, 'Chilled brewed coffee with ice', 1);

-- Insert sample ingredients
INSERT INTO ingredients (item_id, name, quantity, unit) VALUES
(1, 'Flour', 0.5, 'cup'),
(1, 'Milk', 0.75, 'cup'),
(1, 'Egg', 1, 'piece'),
(2, 'Bacon', 3, 'slices'),
(2, 'Lettuce', 2, 'leaves'),
(2, 'Tomato', 2, 'slices'),
(3, 'Beef Steak', 1, 'piece'),
(3, 'Salt', 1, 'tsp'),
(3, 'Pepper', 0.5, 'tsp'),
(4, 'Lemon', 1, 'piece'),
(4, 'Sugar', 2, 'tbsp'),
(5, 'Chocolate', 0.5, 'cup'),
(5, 'Flour', 1, 'cup'),
(6, 'Egg', 2, 'pieces'),
(6, 'Cheese', 0.5, 'cup'),
(7, 'Coffee', 1, 'cup'),
(7, 'Ice', 1, 'cup');

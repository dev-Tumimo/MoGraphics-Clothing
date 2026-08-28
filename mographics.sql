/*
Author : Boitumelo Moshodi
MoGraphics Clothing Database
Database Management System: MySQL
 Purpose: Stores customer, product, cart, order, payment
         and administrative data for the MoGraphics e-commerce web application.
*/
CREATE DATABASE IF NOT EXISTS mographics_clothing;
USE mographics_clothing;

CREATE TABLE customers(
 customer_id INT AUTO_INCREMENT PRIMARY KEY,
 first_name VARCHAR(50) NOT NULL,last_name VARCHAR(50) NOT NULL,
  -- Email is unique to prevent duplicate customer accounts.
 email VARCHAR(100) UNIQUE NOT NULL,password VARCHAR(255) NOT NULL,
 phone VARCHAR(20),address VARCHAR(150),city VARCHAR(80),postal_code VARCHAR(20),
 created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE administrators(
 admin_id INT AUTO_INCREMENT PRIMARY KEY,
 first_name VARCHAR(50),last_name VARCHAR(50),
 email VARCHAR(100) UNIQUE NOT NULL,password VARCHAR(255) NOT NULL
);

CREATE TABLE categories(
 category_id INT AUTO_INCREMENT PRIMARY KEY,
 parent_id INT NULL,category_name VARCHAR(100) NOT NULL,
 description TEXT,is_active BOOLEAN DEFAULT TRUE,
 FOREIGN KEY(parent_id) REFERENCES categories(category_id) ON DELETE SET NULL
);

CREATE TABLE products(
 product_id INT AUTO_INCREMENT PRIMARY KEY,
 category_id INT NOT NULL,name VARCHAR(120) NOT NULL,description TEXT,
  -- Stores either a local image path or cloud image URL.
 image VARCHAR(255),is_active BOOLEAN DEFAULT TRUE,created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(category_id) REFERENCES categories(category_id)
);

CREATE TABLE product_variants(
 variant_id INT AUTO_INCREMENT PRIMARY KEY,
 product_id INT NOT NULL,size VARCHAR(20),colour VARCHAR(50),
 price DECIMAL(10,2) NOT NULL,stock_quantity INT DEFAULT 0,sku VARCHAR(50) UNIQUE,
 FOREIGN KEY(product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

CREATE TABLE carts(
 cart_id INT AUTO_INCREMENT PRIMARY KEY,customer_id INT UNIQUE NOT NULL,
 created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
);

CREATE TABLE cart_items(
 cart_item_id INT AUTO_INCREMENT PRIMARY KEY,cart_id INT NOT NULL,
 variant_id INT NOT NULL,quantity INT DEFAULT 1,
 FOREIGN KEY(cart_id) REFERENCES carts(cart_id) ON DELETE CASCADE,
 FOREIGN KEY(variant_id) REFERENCES product_variants(variant_id)
);

CREATE TABLE orders(
 order_id INT AUTO_INCREMENT PRIMARY KEY,customer_id INT NOT NULL,
 order_date DATETIME DEFAULT CURRENT_TIMESTAMP,status VARCHAR(30) DEFAULT 'Pending',
 total_amount DECIMAL(10,2) NOT NULL,shipping_address VARCHAR(255) NOT NULL,
 -- Connects each order to the customer who placed it.
 FOREIGN KEY(customer_id) REFERENCES customers(customer_id)
);

CREATE TABLE order_items(
 order_item_id INT AUTO_INCREMENT PRIMARY KEY,order_id INT NOT NULL,
 variant_id INT NOT NULL,quantity INT NOT NULL,unit_price DECIMAL(10,2) NOT NULL,
 FOREIGN KEY(order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
 FOREIGN KEY(variant_id) REFERENCES product_variants(variant_id)
);

CREATE TABLE payments(
 payment_id INT AUTO_INCREMENT PRIMARY KEY,order_id INT NOT NULL,
 amount DECIMAL(10,2) NOT NULL,payment_method VARCHAR(50),
  -- Can store a reference returned by a payment provider.
 payment_status VARCHAR(30) DEFAULT 'Pending',transaction_id VARCHAR(100),paid_at DATETIME,
 FOREIGN KEY(order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

INSERT INTO categories(category_name) VALUES('Graphic Tees'),('Hoodies'),('Outerwear');
INSERT INTO products(category_id,name,description,image) VALUES
(1,'Signature Graphic Tee','Heavy cotton statement tee','assets/images/graphic-tee.jpg'),
(2,'Cloudline Hoodie','Heavyweight everyday hoodie','assets/images/cloudline-hoodie.jpg'),
(3,'Metro Jacket','Modern structured outerwear','assets/images/metro-jacket.jpg');
-- Creates initial size, colour, price, stock and SKU records
INSERT INTO product_variants(product_id,size,colour,price,stock_quantity,sku) VALUES
(1,'M','Black',399,12,'MG-TEE-M-BLK'),
(2,'L','Grey',799,8,'MG-HOOD-L-GRY'),
(3,'M','Black',1199,5,'MG-JKT-M-BLK');


CREATE TABLE IF NOT EXISTS app_sessions(
    session_id VARCHAR(128) PRIMARY KEY,
    session_data MEDIUMTEXT NOT NULL,
    last_activity INT UNSIGNED NOT NULL,
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

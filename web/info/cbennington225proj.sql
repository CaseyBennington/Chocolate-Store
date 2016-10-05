CREATE DATABASE cbennington225proj;

USE cbennington225proj;

CREATE TABLE customers (
customer_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
first_name VARCHAR(20) NOT NULL,
last_name VARCHAR(40) NOT NULL,
street VARCHAR(80) NOT NULL,
city VARCHAR(40) NOT NULL,
state VARCHAR(2) NOT NULL,
zip VARCHAR(10) NOT NULL,
email VARCHAR(60) NOT NULL,
pass CHAR(40) NOT NULL,
customer_level TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
active CHAR(32),
registration_date DATETIME NOT NULL,
PRIMARY KEY (customer_id),
UNIQUE KEY (email),
INDEX login (email, pass)
) ENGINE=MyISAM;

CREATE TABLE products (
product_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
product_name VARCHAR(60) NOT NULL,
size VARCHAR(60) NOT NULL,
price DECIMAL(6,2) UNSIGNED NOT NULL,
quantity DECIMAL (10,0) NOT NULL,
image_name VARCHAR(60) NOT NULL,
PRIMARY KEY (product_id),
INDEX (product_name),
INDEX (price)
) ENGINE=MyISAM;

CREATE TABLE orders (
order_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
customer_id INT UNSIGNED NOT NULL,
total DECIMAL(10,2) UNSIGNED NOT NULL,
order_date TIMESTAMP,
PRIMARY KEY (order_id),
INDEX (customer_id),
INDEX (order_date)
) ENGINE=InnoDB;

CREATE TABLE order_contents (
oc_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
order_id INT UNSIGNED NOT NULL,
product_id INT UNSIGNED NOT NULL,
quantity INT UNSIGNED NOT NULL DEFAULT 1,
price DECIMAL(6,2) UNSIGNED NOT NULL,
ship_date DATETIME default NULL,
PRIMARY KEY (oc_id),
INDEX (order_id),
INDEX (product_id),
INDEX (ship_date)
) ENGINE=InnoDB;

INSERT INTO products (product_name, size, price, quantity, image_name) VALUES
('Dark Chocolate Peanut Brittle', '(1 lb.)', 15.00, 10000, '1'),
('Butterscotch Chocolate Squares', '(2 lb. canister)', 18.50, 5500, '2'),
('Nuts and Chews', '(3 lb. canister)', 21.50, 7500, '3'),
('Toffey Nut Popcorn', '(2 lb. canister)', 13.00, 15525, '4');
CREATE DATABASE IF NOT EXISTS stare_management
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE stare_management;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS order_items, orders, cart_items, carts, product_images,
    products, categories, brands, partners, employees, clients,
    role_permissions, permissions, users, roles;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE roles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE,
  description VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE permissions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE,
  description VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE role_permissions (
  role_id INT UNSIGNED NOT NULL,
  permission_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (role_id, permission_id),
  CONSTRAINT fk_rp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  CONSTRAINT fk_rp_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role_id INT UNSIGNED NOT NULL,
  status ENUM('active','pending','blocked') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE clients (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL UNIQUE,
  phone VARCHAR(30) NULL,
  address VARCHAR(255) NULL,
  gender ENUM('male','female','other') DEFAULT 'other',
  age TINYINT UNSIGNED NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_clients_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE employees (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL UNIQUE,
  phone VARCHAR(30) NULL,
  address VARCHAR(255) NULL,
  department VARCHAR(100) NULL,
  position VARCHAR(100) NULL,
  salary DECIMAL(12,2) NOT NULL DEFAULT 0,
  hire_date DATE NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_employees_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE partners (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  company VARCHAR(150) NULL,
  email VARCHAR(180) NULL,
  phone VARCHAR(30) NULL,
  address VARCHAR(255) NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  description TEXT NULL,
  image VARCHAR(500) NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE brands (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  description TEXT NULL,
  logo VARCHAR(500) NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(60) NOT NULL UNIQUE,
  name VARCHAR(180) NOT NULL,
  description TEXT NULL,
  cost_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  selling_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  discount DECIMAL(5,2) NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  category_id INT UNSIGNED NULL,
  brand_id INT UNSIGNED NULL,
  partner_id INT UNSIGNED NULL,
  image VARCHAR(500) NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_products_brand FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_products_partner FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE product_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  image_url VARCHAR(500) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE carts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  client_id INT UNSIGNED NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_carts_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE cart_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cart_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  UNIQUE KEY uq_cart_product (cart_id, product_id),
  CONSTRAINT fk_cart_items_cart FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
  CONSTRAINT fk_cart_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(40) NOT NULL UNIQUE,
  client_id INT UNSIGNED NOT NULL,
  employee_id INT UNSIGNED NULL,
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
  discount DECIMAL(12,2) NOT NULL DEFAULT 0,
  total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  shipping_address VARCHAR(255) NOT NULL,
  status ENUM('pending','confirmed','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT,
  CONSTRAINT fk_orders_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  product_name VARCHAR(180) NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  unit_price DECIMAL(12,2) NOT NULL,
  discount DECIMAL(5,2) NOT NULL DEFAULT 0,
  subtotal DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO roles (name, description) VALUES
('admin','Full system access'),
('employee','Back-office access controlled by permissions'),
('client','Customer storefront and own orders');

INSERT INTO permissions (name, description) VALUES
('view_dashboard','View dashboard'),
('view_categories','View categories'),
('manage_categories','Create, edit and delete categories'),
('view_brands','View brands'),
('manage_brands','Create, edit and delete brands'),
('view_products','View products'),
('manage_products','Create, edit and delete products'),
('view_partners','View partners'),
('manage_partners','Create, edit and delete partners'),
('view_clients','View clients'),
('manage_clients','Create, edit and delete clients'),
('view_employees','View employees'),
('manage_employees','Create, edit and delete employees'),
('manage_users','Manage users and account status'),
('manage_permissions','Manage role permissions'),
('view_orders','View orders'),
('create_orders','Create orders'),
('manage_orders','Update order status and assignments'),
('view_cart','Use cart'),
('view_reports','View reports');

INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions;

INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, id FROM permissions
WHERE name IN ('view_dashboard','view_categories','view_brands','view_products',
               'view_orders','create_orders','manage_orders');

INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, id FROM permissions
WHERE name IN ('view_dashboard','view_categories','view_brands','view_products',
               'create_orders','view_orders','view_cart');

INSERT INTO users (name,email,password,role_id,status) VALUES
('System Admin','admin@stare.local','$2y$12$YhgjVOjIZgfUG7TFeP7QJuhv4wcDGQyCmWs4lFT/HQOSvsT7F6ovu','1','active'),
('Sales Employee','employee@stare.local','$2y$12$YhgjVOjIZgfUG7TFeP7QJuhv4wcDGQyCmWs4lFT/HQOSvsT7F6ovu','2','active'),
('Demo Client','client@stare.local','$2y$12$YhgjVOjIZgfUG7TFeP7QJuhv4wcDGQyCmWs4lFT/HQOSvsT7F6ovu','3','active');

INSERT INTO employees (user_id,phone,address,department,position,salary,hire_date)
VALUES (2,'01000000001','Cairo, Egypt','Sales','Sales Employee',7000,'2026-01-10');

INSERT INTO clients (user_id,phone,address,gender,age)
VALUES (3,'01000000002','Cairo, Egypt','other',25);

INSERT INTO categories (name,description,image) VALUES
('Electronics','Phones, laptops and smart devices','https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=900'),
('Fashion','Clothing and everyday fashion','https://images.unsplash.com/photo-1445205170230-053b83016050?w=900'),
('Shoes','Casual and formal footwear','https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=900'),
('Accessories','Useful lifestyle accessories','https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=900');

INSERT INTO brands (name,description,logo) VALUES
('NovaTech','Technology products','https://dummyimage.com/160x80/111827/ffffff&text=NovaTech'),
('UrbanFit','Fashion and lifestyle','https://dummyimage.com/160x80/111827/ffffff&text=UrbanFit'),
('Apex','Everyday accessories','https://dummyimage.com/160x80/111827/ffffff&text=Apex');

INSERT INTO partners (name,company,email,phone,address) VALUES
('Ahmed Supply','Ahmed Supply Co.','supply@stare.local','01011111111','Cairo, Egypt'),
('Future Distribution','Future Distribution','future@stare.local','01022222222','Giza, Egypt');

INSERT INTO products
(sku,name,description,cost_price,selling_price,discount,stock,category_id,brand_id,partner_id,image) VALUES
('ST-1001','NovaBook 14','Lightweight laptop for work and study',650,899,5,15,1,1,1,'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=900'),
('ST-1002','Wireless Headphones','Noise cancelling wireless headphones',35,79.99,0,30,1,1,1,'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=900'),
('ST-1003','Classic T-Shirt','Premium cotton everyday t-shirt',8,19.99,10,50,2,2,2,'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=900'),
('ST-1004','Runner Shoes','Comfortable running shoes',30,69,5,22,3,2,2,'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=900'),
('ST-1005','Smart Watch','Fitness tracking smart watch',70,149,0,12,4,3,1,'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=900'),
('ST-1006','Leather Belt','Genuine leather belt',6,16.5,0,35,4,3,2,'https://images.unsplash.com/photo-1624222247344-550fb60583dc?w=900');

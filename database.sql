CREATE DATABASE IF NOT EXISTS b2b_pharmacy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE b2b_pharmacy;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('buyer','admin') NOT NULL DEFAULT 'buyer',
  business_name VARCHAR(150) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  brand VARCHAR(120) NOT NULL,
  category VARCHAR(80) NOT NULL,
  product_code VARCHAR(60) NOT NULL UNIQUE,
  pack_size VARCHAR(80) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_products_search (name, brand),
  INDEX idx_products_category (category)
) ENGINE=InnoDB;

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  total_amount DECIMAL(12,2) NOT NULL,
  status ENUM('Pending','Processing','Shipped','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  notes VARCHAR(500) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NULL,
  product_name VARCHAR(160) NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  line_total DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Portfolio demo products only; not medical advice or a real supply catalogue.
INSERT INTO products (name,brand,category,product_code,pack_size,description,price,stock) VALUES
('Vitamin C Supplement','WellSpring','Wellness','WS-VITC-100','Bottle of 100 tablets','Demo catalogue listing for a wellness product. Verify product details and regulatory status before real-world use.',185.00,120),
('Digital Thermometer','CarePoint','Medical Devices','CP-THERM-01','Pack of 1','Demo listing for a basic medical device. Follow manufacturer instructions and applicable requirements.',145.00,65),
('First Aid Dressing Pack','MedSupply','First Aid','MS-DRESS-10','Pack of 10','Demo first-aid supply listing for catalogue and inventory workflow testing.',210.00,42),
('Hand Sanitizer','PureCare','Hygiene','PC-SAN-500','500 ml bottle','Demo hygiene product listing. Product claims and specifications must be verified before sale.',95.00,90),
('Surgical Face Masks','SafeWear','Protective Supplies','SW-MASK-50','Pack of 50','Demo protective-supply listing. Confirm relevant standards and packaging information.',125.00,200),
('Digital Blood Pressure Monitor','CarePoint','Medical Devices','CP-BPM-02','Pack of 1','Demo medical-device listing. Check manufacturer documentation and applicable regulatory requirements.',1450.00,18),
('Reusable Hot Water Bag','WellSpring','Wellness','WS-HWB-01','Pack of 1','Demo wellness accessory listing for UI and inventory testing.',260.00,32),
('Gauze Roll','MedSupply','First Aid','MS-GAUZE-05','Pack of 5','Demo first-aid supply listing for bulk-order testing.',175.00,8);

-- Admin account is created by setup_admin.php on first local run.


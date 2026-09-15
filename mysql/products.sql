-- Docker init dump (hardened Phase 3). Mirrors database/schema.sql + database/seed/products.sql.
CREATE TABLE IF NOT EXISTS `products` (
  `sku` VARCHAR(64) NOT NULL PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `product_type` VARCHAR(20) NOT NULL,
  `product_attribute` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `chk_products_price_positive` CHECK (`price` > 0),
  CONSTRAINT `chk_products_type` CHECK (`product_type` IN ('DVD','Book','Furniture'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `products` (`sku`, `name`, `price`, `product_type`, `product_attribute`) VALUES
('SKUTestSKU000', 'NameTest000', 25.00, 'DVD', '200'),
('SKUTestSKU001', 'NameTest001', 25.00, 'Book', '200'),
('SKUTestSKU002', 'NameTest002', 25.00, 'Furniture', '200x200x200');

-- Canonical schema (Phase 3, step 11). Docker init mounts mysql/; this file is the
-- version-controlled source of truth, mysql/products.sql mirrors it for init.
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

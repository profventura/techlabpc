SET NAMES utf8mb4;
CREATE TABLE IF NOT EXISTS view_cards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  scope ENUM('dashboard','customers','students','groups','payments','laptops','software') NOT NULL,
  metric VARCHAR(120) NOT NULL,
  value BIGINT NOT NULL DEFAULT 0,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_scope_metric (scope, metric),
  KEY idx_metric (metric)
) ENGINE=InnoDB;
ALTER TABLE laptops ADD INDEX idx_laptops_status (status);
ALTER TABLE laptops ADD INDEX idx_laptops_customer (customer_id);
ALTER TABLE laptops ADD INDEX idx_laptops_group (group_id);

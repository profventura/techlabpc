SET NAMES utf8mb4;
ALTER TABLE view_cards
  MODIFY scope ENUM('dashboard','customers','students','groups','payments','laptops','software') NOT NULL;

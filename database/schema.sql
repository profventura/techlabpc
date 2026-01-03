

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','student') NOT NULL DEFAULT 'student',
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_students_role (role),
  KEY idx_students_active (active)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS work_groups (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  leader_student_id INT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_work_groups_leader FOREIGN KEY (leader_student_id) REFERENCES students(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS group_members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  group_id INT NOT NULL,
  student_id INT NOT NULL,
  role ENUM('leader','installer') NOT NULL DEFAULT 'installer',
  joined_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_group_member (group_id, student_id),
  CONSTRAINT fk_group_members_group FOREIGN KEY (group_id) REFERENCES work_groups(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_group_members_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  KEY idx_group_members_role (role),
  KEY idx_group_members_group (group_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  pc_requested_count INT NOT NULL DEFAULT 0,
  notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS software (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  version VARCHAR(100) NULL,
  license VARCHAR(120) NULL,
  cost DECIMAL(10,2) NULL,
  notes TEXT NULL,
  UNIQUE KEY uq_software_name_version (name, version)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS laptops (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(60) NOT NULL UNIQUE,
  brand_model VARCHAR(150) NOT NULL,
  cpu VARCHAR(120) NULL,
  ram VARCHAR(60) NULL,
  storage VARCHAR(120) NULL,
  screen VARCHAR(120) NULL,
  tech_notes TEXT NULL,
  scratches TEXT NULL,
  physical_condition VARCHAR(120) NULL,
  battery VARCHAR(120) NULL,
  condition_level ENUM('excellent','very_good','good','average','fair','poor') NOT NULL DEFAULT 'good',
  office_license VARCHAR(120) NULL,
  windows_license VARCHAR(120) NULL,
  other_software_request TEXT NULL,
  status ENUM('in_progress','ready','missing_office','missing_software','to_check','delivered') NOT NULL DEFAULT 'in_progress',
  customer_id INT NULL,
  group_id INT NULL,
  last_operator_student_id INT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_laptops_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_laptops_group FOREIGN KEY (group_id) REFERENCES work_groups(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_laptops_last_operator FOREIGN KEY (last_operator_student_id) REFERENCES students(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS laptop_software (
  id INT AUTO_INCREMENT PRIMARY KEY,
  laptop_id INT NOT NULL,
  software_id INT NOT NULL,
  installed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_laptop_software (laptop_id, software_id),
  CONSTRAINT fk_ls_laptop FOREIGN KEY (laptop_id) REFERENCES laptops(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_ls_software FOREIGN KEY (software_id) REFERENCES software(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS laptop_state_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  laptop_id INT NOT NULL,
  changed_by_student_id INT NULL,
  previous_status ENUM('in_progress','ready','missing_office','missing_software','to_check','delivered') NULL,
  new_status ENUM('in_progress','ready','missing_office','missing_software','to_check','delivered') NOT NULL,
  note VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_lsh_laptop FOREIGN KEY (laptop_id) REFERENCES laptops(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_lsh_student FOREIGN KEY (changed_by_student_id) REFERENCES students(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS laptop_group_assignments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  laptop_id INT NOT NULL,
  group_id INT NOT NULL,
  assigned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  unassigned_at TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT fk_lga_laptop FOREIGN KEY (laptop_id) REFERENCES laptops(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_lga_group FOREIGN KEY (group_id) REFERENCES work_groups(id) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY idx_lga_laptop (laptop_id),
  KEY idx_lga_group (group_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payment_transfers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  paid_at DATE NOT NULL,
  reference VARCHAR(190) NULL,
  receipt_path VARCHAR(255) NULL,
  pcs_paid_count INT NOT NULL DEFAULT 0,
  status ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_payments_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY idx_payments_customer (customer_id),
  KEY idx_payments_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS access_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NULL,
  event ENUM('login_ok','login_ko','logout') NOT NULL,
  ip VARCHAR(60) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_access_logs_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL ON UPDATE CASCADE,
  KEY idx_access_logs_event (event)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS action_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  actor_student_id INT NULL,
  action_type ENUM(
    'assign_laptop_to_customer',
    'change_laptop_status',
    'upload_receipt',
    'assign_laptop_to_group',
    'create_group','update_group','delete_group',
    'create_student','update_student','delete_student',
    'create_customer','update_customer','delete_customer',
    'create_payment','update_payment','delete_payment',
    'create_software','update_software','delete_software',
    'assign_member_to_group','remove_member_from_group',
    'create_laptop','update_laptop','delete_laptop'
  ) NOT NULL,
  laptop_id INT NULL,
  customer_id INT NULL,
  group_id INT NULL,
  note VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_action_logs_actor FOREIGN KEY (actor_student_id) REFERENCES students(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_action_logs_laptop FOREIGN KEY (laptop_id) REFERENCES laptops(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_action_logs_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_action_logs_group FOREIGN KEY (group_id) REFERENCES work_groups(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

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


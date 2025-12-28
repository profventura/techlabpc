USE techlabpc;

INSERT INTO customers (first_name, last_name, email, notes) VALUES
('Mario','Rossi','mario.rossi@example.com',''),
('Lucia','Bianchi','lucia.bianchi@example.com',''),
('Paolo','Verdi','paolo.verdi@example.com','');

INSERT INTO students (first_name,last_name,email,password_hash,role,active) VALUES
('Giulia','Neri','giulia.neri@example.com','$2y$10$Qe9cZ8Hh5B4s5JmpswYfGOH3bN0c1mNQeLx1aFzDWjKQ8iJQn5Y5K','student',1),
('Marco','Blu','marco.blu@example.com','$2y$10$Qe9cZ8Hh5B4s5JmpswYfGOH3bN0c1mNQeLx1aFzDWjKQ8iJQn5Y5K','student',1),
('Sara','Gialli','sara.gialli@example.com','$2y$10$Qe9cZ8Hh5B4s5JmpswYfGOH3bN0c1mNQeLx1aFzDWjKQ8iJQn5Y5K','student',1),
('Luca','Viola','luca.viola@example.com','$2y$10$Qe9cZ8Hh5B4s5JmpswYfGOH3bN0c1mNQeLx1aFzDWjKQ8iJQn5Y5K','student',1),
('Elena','Rosa','elena.rosa@example.com','$2y$10$Qe9cZ8Hh5B4s5JmpswYfGOH3bN0c1mNQeLx1aFzDWjKQ8iJQn5Y5K','student',1),
('Davide','Arancio','davide.arancio@example.com','$2y$10$Qe9cZ8Hh5B4s5JmpswYfGOH3bN0c1mNQeLx1aFzDWjKQ8iJQn5Y5K','student',1);

INSERT INTO work_groups (name, leader_student_id) VALUES
('Team Alpha', 1),
('Team Beta', 2);

INSERT INTO group_members (group_id, student_id, role) VALUES
(1,1,'leader'),
(1,3,'installer'),
(1,4,'installer'),
(1,5,'installer'),
(2,2,'leader'),
(2,3,'installer'),
(2,4,'installer'),
(2,5,'installer');

INSERT INTO laptops (code, brand_model, cpu, ram, storage, screen, condition_level, office_license, windows_license, status, customer_id, group_id)
VALUES
('PC-001','Dell Latitude 5400','i5-8365U','16GB','512GB SSD','14" FHD','good','OFF-2019-XXXXX','WIN-10-PRO-YYYYY','in_progress',1,1),
('PC-002','HP ProBook 450','i5-8250U','8GB','256GB SSD','15.6" FHD','good',NULL,'WIN-10-HOME-ZZZZZ','missing_software',1,1),
('PC-003','Lenovo ThinkPad T480','i7-8650U','16GB','512GB SSD','14" FHD','excellent','OFF-2019-AAAAA','WIN-11-PRO-BBBBB','to_check',2,2),
('PC-004','Acer Swift 3','Ryzen 5','8GB','512GB SSD','14" FHD','fair',NULL,'WIN-11-HOME-CCCCC','in_progress',NULL,2),
('PC-005','Asus VivoBook','i5-1135G7','16GB','1TB SSD','15.6" FHD','good','OFF-2019-DDDDD','WIN-11-PRO-EEEEE','ready',3,1);

INSERT INTO software (name, version, license, cost) VALUES
('Windows','11','Commercial',130.00),
('Office','2024','Commercial',70.00),
('Adobe Reader',NULL,'Free',NULL),
('Chrome',NULL,'Free',NULL);

INSERT INTO laptop_software (laptop_id, software_id) VALUES
(1,1),
(1,2),
(1,3),
(1,4),
(2,1),
(2,3),
(3,2),
(4,1),
(5,1),
(5,2);

INSERT INTO laptop_state_history (laptop_id, changed_by_student_id, previous_status, new_status, note) VALUES
(1,2,'to_check','in_progress','Diagnosi completata'),
(3,3,'in_progress','to_check','Verifica batteria'),
(5,2,'in_progress','ready','Consegna pronta');

INSERT INTO payment_transfers (customer_id, amount, paid_at, reference, receipt_path, status) VALUES
(1, 600.00, '2025-12-01', 'Bonifico-001', 'public/uploads/bonifico_001.pdf', 'verified'),
(2, 400.00, '2025-12-05', 'Bonifico-002', 'public/uploads/bonifico_002.pdf', 'pending'),
(3, 200.00, '2025-12-10', 'Bonifico-003', 'public/uploads/bonifico_003.pdf', 'verified');


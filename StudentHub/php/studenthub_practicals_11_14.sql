-- StudentHub Practicals 11–14 upgrade. Import into the existing studenthub database.
USE studenthub;

-- The event additions are guarded for MySQL versions without ADD COLUMN IF NOT EXISTS.
DELIMITER $$
CREATE PROCEDURE studenthub_add_column(IN tbl VARCHAR(64), IN col_name VARCHAR(64), IN definition_sql TEXT)
BEGIN
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = tbl AND column_name = col_name) THEN
    SET @sql = CONCAT('ALTER TABLE `', tbl, '` ADD COLUMN ', definition_sql);
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;
CALL studenthub_add_column('events','description','`description` TEXT NULL AFTER `title`');
CALL studenthub_add_column('events','poster_path','`poster_path` VARCHAR(255) NULL AFTER `category`');
CALL studenthub_add_column('events','status','`status` ENUM(''open'',''closed'') NOT NULL DEFAULT ''open'' AFTER `poster_path`');
CALL studenthub_add_column('events','created_at','`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
CALL studenthub_add_column('events','updated_at','`updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
DROP PROCEDURE studenthub_add_column;

CREATE TABLE IF NOT EXISTS audit_logs (
  log_id INT AUTO_INCREMENT PRIMARY KEY,
  actor_id INT NOT NULL,
  action VARCHAR(60) NOT NULL,
  entity_type VARCHAR(60) NOT NULL,
  entity_id INT NOT NULL,
  description VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_audit_created (created_at), INDEX idx_audit_actor (actor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- The student-management index is guarded, so re-importing the script does not fail.
DELIMITER $$
CREATE PROCEDURE studenthub_add_index(IN tbl VARCHAR(64), IN idx VARCHAR(64), IN cols TEXT)
BEGIN
  IF NOT EXISTS (SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = tbl AND index_name = idx) THEN
    SET @sql = CONCAT('CREATE INDEX `', idx, '` ON `', tbl, '` ', cols);
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;
CALL studenthub_add_index('students','idx_students_course_year','(course_id, year)');
DROP PROCEDURE studenthub_add_index;

-- Remove the former event category/status-filter index when upgrading an existing installation.
DELIMITER $$
CREATE PROCEDURE studenthub_drop_index(IN tbl VARCHAR(64), IN idx VARCHAR(64))
BEGIN
  IF EXISTS (SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = tbl AND index_name = idx) THEN
    SET @sql = CONCAT('DROP INDEX `', idx, '` ON `', tbl, '`');
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;
CALL studenthub_drop_index('events','idx_events_status_category_date');
DROP PROCEDURE studenthub_drop_index;

-- Test seed records use the reserved practical11.test domain and are never updated.
INSERT IGNORE INTO courses (course_name) VALUES ('BCA'), ('BSc IT'), ('BCom'), ('MBA');
INSERT INTO students (name,email,mobile,role,course_id,year,gender,password)
SELECT s.name,s.email,s.mobile,'student',c.course_id,s.year,s.gender,'$2y$10$QWSiVt7kCPzeF0xGCmeSQOR2DLrJVlv49YUvWiQJkMY20gClwDZPa'
FROM (
 SELECT 'Aarav Shah' name,'aarav.shah@practical11.test' email,'9876500001' mobile,'BCA' course,1 year,'Male' gender UNION ALL
 SELECT 'Diya Patel','diya.patel@practical11.test','9876500002','BCA',2,'Female' UNION ALL
 SELECT 'Vivaan Mehta','vivaan.mehta@practical11.test','9876500003','BSc IT',1,'Male' UNION ALL
 SELECT 'Anaya Joshi','anaya.joshi@practical11.test','9876500004','BSc IT',2,'Female' UNION ALL
 SELECT 'Reyansh Desai','reyansh.desai@practical11.test','9876500005','BCom',3,'Male' UNION ALL
 SELECT 'Isha Rao','isha.rao@practical11.test','9876500006','MBA',1,'Female' UNION ALL
 SELECT 'Kabir Singh','kabir.singh@practical11.test','9876500007','BCA',3,'Male' UNION ALL
 SELECT 'Myra Nair','myra.nair@practical11.test','9876500008','BSc IT',4,'Female' UNION ALL
 SELECT 'Arjun Kumar','arjun.kumar@practical11.test','9876500009','BCom',2,'Male' UNION ALL
 SELECT 'Sara Khan','sara.khan@practical11.test','9876500010','MBA',2,'Female' UNION ALL
 SELECT 'Dhruv Jain','dhruv.jain@practical11.test','9876500011','BCA',4,'Male' UNION ALL
 SELECT 'Kiara Bose','kiara.bose@practical11.test','9876500012','BSc IT',3,'Female' UNION ALL
 SELECT 'Advait Roy','advait.roy@practical11.test','9876500013','BCom',1,'Male' UNION ALL
 SELECT 'Aanya Iyer','aanya.iyer@practical11.test','9876500014','MBA',1,'Female' UNION ALL
 SELECT 'Rudra Das','rudra.das@practical11.test','9876500015','BCA',2,'Male' UNION ALL
 SELECT 'Meera Kulkarni','meera.kulkarni@practical11.test','9876500016','BSc IT',1,'Female' UNION ALL
 SELECT 'Neil Verma','neil.verma@practical11.test','9876500017','BCom',4,'Male' UNION ALL
 SELECT 'Riya Gupta','riya.gupta@practical11.test','9876500018','MBA',2,'Female' UNION ALL
 SELECT 'Om Prakash','om.prakash@practical11.test','9876500019','BCA',3,'Male' UNION ALL
 SELECT 'Tara Menon','tara.menon@practical11.test','9876500020','BSc IT',2,'Female'
) s JOIN courses c ON c.course_name=s.course
WHERE NOT EXISTS (SELECT 1 FROM students x WHERE x.email=s.email);

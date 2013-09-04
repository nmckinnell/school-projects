-- Nora McKinnell
-- Creates table off of test csv

USE coursewire_db;

DROP TABLE IF EXISTS classes;

CREATE TABLE classes (
  STUDENT_ID varchar(15) NOT NULL,  
  TERM_CODE varchar(6),
  CRN INT,
  SUBJECT_CODE varchar(4) NOT NULL,
  COURSE_NUMBER varchar(4) NOT NULL,
  COURSE_TITLE varchar(150),
  INSTRUCTORS varchar(100),
  PRIMARY KEY (STUDENT_ID,SUBJECT_CODE,COURSE_NUMBER)
  ) TYPE=innodb;
    
-- load data from given csv, use same column names
load data local infile 'classes.csv' into table classes fields terminated by ','
  enclosed by '"'
  lines terminated by '\r'
  (STUDENT_ID,TERM_CODE,SUBJECT_CODE,COURSE_NUMBER,COURSE_TITLE,INSTRUCTORS);   
  
-- create trigger to automatically add course_title if only one title available
DROP TRIGGER IF EXISTS classes_add_title;
DELIMITER $$
CREATE TRIGGER classes_add_title BEFORE INSERT ON classes
FOR EACH ROW
BEGIN
    SET NEW.COURSE_TITLE = (SELECT DISTINCT COURSE_TITLE FROM courses WHERE NEW.SUBJECT_CODE = courses.SUBJECT_CODE AND NEW.COURSE_NUMBER = courses.COURSE_NUMBER HAVING COUNT(DISTINCT COURSE_TITLE) = 1);
END$$
DELIMITER ;
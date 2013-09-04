-- Nora McKinnell
-- Creates table for students (users of the app)

USE coursewire_db;

DROP TABLE IF EXISTS students;

CREATE TABLE students (
  STUDENT_ID varchar(15) PRIMARY KEY NOT NULL,
  NAME varchar(100),
  GRAD_YEAR varchar(4) NOT NULL,
  MAJOR1 varchar(4),
  MAJOR2 varchar(4),
  IS_USER boolean DEFAULT TRUE
  ) TYPE=innodb;
    
-- load data from given csv, use same column names
load data local infile 'graduated-students.csv' into table students fields terminated by ','
  enclosed by '"'
  lines terminated by '\r'
  (STUDENT_ID,NAME,GRAD_YEAR,MAJOR1,MAJOR2,IS_USER);
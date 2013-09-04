-- Nora McKinnell

USE coursewire_db;

DROP TABLE IF EXISTS classes_shopped;

CREATE TABLE classes_shopped (
  STUDENT_ID varchar(15) NOT NULL,  
  TERM_CODE varchar(6) NOT NULL,
  CRN INT NOT NULL,
  PRIMARY KEY (STUDENT_ID,TERM_CODE,CRN)
  ) TYPE=innodb;
-- Nora McKinnell
-- Creates table off of test csv

USE coursewire_db;

DROP TABLE IF EXISTS courses;

CREATE TABLE courses (
  CRN INT NOT NULL,
  TERM_CODE varchar(6) NOT NULL,
  SUBJECT_CODE varchar(4) NOT NULL,
  COURSE_NUMBER varchar(4) NOT NULL,
  SECTION_NUMBER varchar(2),
  TERMS_OFFERED_CODE char(1),
  TERMS_OFFERED_DESC  varchar(30),
  NUMBER_ENROLLED INT,
  MAX_ENROLLMENT INT,
  CROSSLIST_IND varchar(2),
  COURSE_TITLE varchar(150) NOT NULL,
  INSTRUCTORS varchar(100),
  COURSE_DESCRIPTION varchar(1000),
  PREREQS varchar(500),
  DISTRIBUTION1 varchar(100),
  DISTRIBUTION2 varchar(100),
  DISTRIBUTION3 varchar(100),
  DAYS1 varchar(7),
  STARTEND1 varchar(25),
  LOC1 varchar(30),
  DAYS2 varchar(7),
  STARTEND2 varchar(25),
  LOC2 varchar(30),
  ALT_WED varchar(30),
  DAYS3 varchar(7),
  STARTEND3 varchar(25),
  LOC3 varchar(30),
  PRIMARY KEY (TERM_CODE,CRN)
  ) TYPE=innodb;

-- load data from given csv, use same column names
load data local infile 'courses.csv' into table courses fields terminated by ','
  enclosed by '"'
  lines terminated by '\n'
  (CRN,TERM_CODE,SUBJECT_CODE,COURSE_NUMBER,SECTION_NUMBER,TERMS_OFFERED_CODE,TERMS_OFFERED_DESC,NUMBER_ENROLLED,MAX_ENROLLMENT,CROSSLIST_IND,COURSE_TITLE,INSTRUCTORS,COURSE_DESCRIPTION,PREREQS,DISTRIBUTION1,DISTRIBUTION2,DISTRIBUTION3,DAYS1,STARTEND1,LOC1,DAYS2,STARTEND2,LOC2,ALT_WED,DAYS3,STARTEND3,LOC3);
  
-- need to convert class times to date to be sortable
ALTER TABLE courses ADD START1 time AFTER STARTEND1,
ADD END1 time AFTER START1,
ADD START2 time AFTER STARTEND2,
ADD END2 time AFTER START2,
ADD START3 time AFTER STARTEND3,
ADD END3 time AFTER START3;

UPDATE courses
SET START1 = str_to_date(startend1,'%l:%i %p'),
END1=str_to_date(substring(startend1,-8),'%l:%i %p'),
START2=str_to_date(startend2,'%l:%i %p'),
END2=str_to_date(substring(startend2,-8),'%l:%i %p'),
START3=str_to_date(startend3,'%l:%i %p'),
END3=str_to_date(substring(startend3,-8),'%l:%i %p');
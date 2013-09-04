<?php
/**
 * Adds a course to the classes the current user has taken
 */ 
session_start();
require_once('MDB2.php');
require_once('/home/cs304/public_html/php/MDB2-functions.php');
require_once('search-helper.php');
require_once('coursewire-dsn.inc');
$dbh = db_connect($coursewire_dsn);

// course is given as a subject code and course number separated by a space, need to be split for insertion
$course = explode(' ',$_REQUEST['course']);

$query = 'INSERT INTO classes(student_id,subject_code,course_number) VALUES (?,?,?)';
$terms = array($_SESSION['user'],strtoupper($course[0]),$course[1]);
prepared_statement($dbh,$query,$terms);
?>
<?php
/**
 * Removes course from the classes the current user has taken
 */ 
session_start();
require_once('MDB2.php');
require_once('/home/cs304/public_html/php/MDB2-functions.php');
require_once('search-helper.php');
require_once('coursewire-dsn.inc');
$dbh = db_connect($coursewire_dsn);

// course is identified from id, a composite of term code and course number
$course = explode('-',$_POST['course']);

$query = 'DELETE FROM classes WHERE student_id = ? AND subject_code = ? AND course_number = ?';
$terms = array($_SESSION['user'],strtoupper($course[0]),$course[1]);
prepared_statement($dbh,$query,$terms);

?>

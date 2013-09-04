<?php
/**
 * Removes course from the classes the current user shopped
 */ 
session_start();
require_once('MDB2.php');
require_once('/home/cs304/public_html/php/MDB2-functions.php');
require_once('search-helper.php');
require_once('coursewire-dsn.inc');
$dbh = db_connect($coursewire_dsn);

$query = 'DELETE FROM classes_shopped WHERE student_id = ? AND term_code = ? AND crn = ?';
$terms = array($_SESSION['user'],$_POST['term_code'],$_POST['crn']);
prepared_statement($dbh,$query,$terms);
?>

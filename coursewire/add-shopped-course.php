<?php
/**
 * Adds a course to the classes the current user has shopped
 */ 
session_start();
require_once('MDB2.php');
require_once('/home/cs304/public_html/php/MDB2-functions.php');
require_once('search-helper.php');
require_once('coursewire-dsn.inc');
$dbh = db_connect($coursewire_dsn);

$query = 'INSERT INTO classes_shopped(student_id,term_code,crn) VALUES (?,?,?)';
$terms = array($_SESSION['user'],$_POST['term_code'],$_POST['crn']);
prepared_statement($dbh,$query,$terms);
?>
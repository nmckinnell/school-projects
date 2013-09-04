<?php
/**
 * Called by profile.php
 * Alters student information
 */
session_start();
require_once('MDB2.php');
require_once('/home/cs304/public_html/php/MDB2-functions.php');
require_once('coursewire-dsn.inc');
$dbh = db_connect($coursewire_dsn);

$query = 'UPDATE students SET ';

// create where clause based on given terms to avoid overwriting existing information with empty strings
$where = array(); $terms = array();
if (!empty($_POST['name'])) { $where[] = 'name = ?'; $terms[] = $_POST['name']; }
if (!empty($_POST['year'])) { $where[] = 'grad_year = ?'; $terms[] = $_POST['year']; }
if (!empty($_POST['major1'])) { $where[] = 'major1 = ?'; $terms[] = $_POST['major1']; }
if (!empty($_POST['major2'])) { $where[] = 'major2 = ?'; $terms[] = $_POST['major2']; }

// add where clauses to query
$query .= implode(', ',$where);

$query .= ' WHERE student_id = ?';
$terms[] = $_SESSION['user'];

prepared_statement($dbh,$query,$terms);

// update session
$_SESSION['name'] = $_POST['name'];
$_SESSION['major1'] = $_POST['major1'];
$_SESSION['major2'] = $_POST['major2'];  
?>
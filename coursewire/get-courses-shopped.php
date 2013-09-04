<?php
/**
 * Called by shopped-courses.php
 * Returns a list of courses shopped by the student
 */ 
session_start();
require_once('MDB2.php');
require_once('/home/cs304/public_html/php/MDB2-functions.php');
require_once('search-helper.php');
require_once('coursewire-dsn.inc');
$dbh = db_connect($coursewire_dsn);
  
// get all shopped courses by given user
$query = 'SELECT s.crn,s.term_code,concat(c.subject_code," ",c.course_number) as course,c.course_title FROM classes_shopped s INNER JOIN courses c ON (s.crn=c.crn AND s.term_code=c.term_code) WHERE s.student_id = ?';
$resultset = prepared_query($dbh,$query,array($_SESSION['user']));

$courses_shopped = array();

if ($resultset->numRows() > 0) {	
	while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    // format course information
		$course_data = $row['course'];
    // add title if there is one
		if ($row['course_title'] != '') $course_data .= ': '.$row['course_title'];
    $course_data .= ' ('.$row['crn'].')';
    // add remove button
		$course_data .= ' <a href="javascript:void(0);" class="remove" crn='.$row['crn'].' term='.$row['term_code'].'>remove</a>';
		array_push($courses_shopped,$course_data);
	}
  // create one list of all shopped courses
	$course_list = implode('<br>',$courses_shopped);	
} else {
  // if no shopped courses, tell user about it
	$course_list = 'You don\'t have any shopped courses!';
}

$data = array(); // JSON array to be returned
$data['list'] = $course_list; // list of courses to be displayed
	
echo json_encode($data);
?>
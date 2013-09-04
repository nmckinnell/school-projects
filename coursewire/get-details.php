<?php
/**
 * Called by search-helper.php and planner.php
 * Returns a formatted course information for given course
 */
require_once('MDB2.php');
require_once('/home/cs304/public_html/php/MDB2-functions.php');
require_once('search-helper.php');
require_once('coursewire-dsn.inc');
$dbh = db_connect($coursewire_dsn);

session_start();

include('path.php');
?>

<div class="detail">
  <?php get_details(); ?>
</div>

<script language="javascript" type="text/javascript">
// add given course to list of shopped courses
function shopCourse(term_code,crn) {
  $.ajax({
    cache: false,
    type: 'POST',              
    url: '<?php echo $path ?>add-shopped-course.php',
    data: 'term_code='+term_code+'&crn='+crn,
    success: function () {
      $('#shop').append(' Added!'); // alert user of success
    },
    error: function (response) {
      alert("error: " + response.responseText);
    }
  });
}
// remove given course from list of shopped courses
function unshopCourse(term_code,crn) {
  $.ajax({
    cache: false,
    type: 'POST',              
    url: '<?php echo $path ?>remove-shopped-course.php',
    data: 'term_code='+term_code+'&crn='+crn,
    success: function () {
      $('#shop').append(' Removed!'); // alert user of success
    },
    error: function (response) {
      alert("error: " + response.responseText);
    }
  });
}
</script>

<?php
/*
 * Get course details for submitted course
 */
function get_details() {    
  global $dbh;
  
  // allow crn or course name to be used in query (currently only crn is being used)
  if (!empty($_GET['crn']) or !empty($_GET['course'])) {
    if (isset($_SESSION['user'])) {
      $query = 'SELECT term_code,crn,concat(subject_code," ",course_number) as course,course_title as title,course_description as description,instructors,days1,startend1,loc1,days2,startend2,loc2,days3,startend3,loc3,alt_wed,prereqs,distribution1,distribution2,distribution3,exists(select * from classes_shopped where student_id = ? and term_code = ? and crn = ?) as shopped FROM courses';
      $term = array($_SESSION['user'],$_GET['term_code'],$_GET['crn']);
    } else {
      $query = 'SELECT term_code,crn,concat(subject_code," ",course_number) as course,course_title as title,course_description as description,instructors,days1,startend1,loc1,days2,startend2,loc2,days3,startend3,loc3,alt_wed,prereqs,distribution1,distribution2,distribution3 FROM courses';
      $term = array();
    }
    
    // add additional where clause based on data given
    if (!empty($_GET['crn'])) {
      $term[] = $_GET['crn'];
      $query .= ' WHERE CRN = ?';
    } else {
      $course = explode(' ',$_GET['course']);
      $term[] = strtoupper($course[0]);
      $term[] = $course[1];
      $query .= ' WHERE subject_code = ? AND course_number = ?';
    }
    
    $resultset = prepared_query($dbh, $query, $term);
    
    if ($resultset->numRows() == 0) {
      echo "No course found for ".implode(' ',$term);
    } else {               
      $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
      display_course($row); // format course data
    }
  }
}

/*
 * Create HTML to format given course
 *
 * @param $c, array of course information
 */
function display_course($c) {
  global $termList;
  
  // create list of times
  $times = $c['days1'].' '.$c['startend1'];
  if ($c['days2']) $times .= '<br>'.$c['days2'].' ' . $c['startend2'];
  if ($c['days3']) $times .= '<br>'.$c['days3'].' ' . $c['startend3'];
  if ($c['alt_wed'] != 'N/A') $times .= '<br><span class="text-warning" style="font-variant:small-caps;">this class has alternate wednesday meetings</span>';
  
  // create list of locations
  $locs = $c['loc1'];
  if ($c['loc2'] and $c['loc2'] != $c['loc1']) $locs .= '<br>'.$c['loc2'];
  if ($c['loc3'] and $c['loc3'] != $c['loc1'] and $c['loc3'] != $c['loc2']) $locs .= '<br>'.$c['loc3'];
  
  // create list of any distribution requirements it fulfills
  $distrib = 'None';
  if ($c['distribution1']) $distrib = $c['distribution1'];
  if ($c['distribution2']) $distrib .= '<br>'.$c['distribution2'];
  if ($c['distribution3']) $distrib .= '<br>'.$c['distribution3'];
  
  // check if list of prerequisites for course is empty, if so tell user
  $c['prereqs']=='' ? $prereqs = 'None' : $prereqs = $c['prereqs']; 
  
  // add links to other sections of the same course if given
  if (!isset($_GET['sections'])) { $sections = get_sections($_GET['crn'],$c['term_code'],$c['course']); }
  if (isset($sections) and !empty($sections)) {
    $sections = '<p>Other sections: '.implode(', ',$sections).'</p>';
  } else $sections = '';
  
  // check whether course is being shopped or not if logged in
  if (isset($_SESSION['user'])) {
    if ($c['shopped'] == 1) {
        $shop_or_unshop = 'unshop';
      $shop_text = 'Stop shopping this course';
    } else {
      $shop_or_unshop = 'shop';
      $shop_text = 'Shop this course';
    }
    $shop = '<p id="shop"><a href="javascript:void(0);" onclick="'.$shop_or_unshop.'Course('.$c['term_code'].','.$_GET['crn'].')">'.$shop_text.'</a></p>';
  } else {
    $shop = '';
  }
  
  // create HTML formatting for course used variables defined above and those given
  $info = <<< EOF
  {$termList[$c['term_code']]} — {$c['course']} ({$c['crn']})<br>
  <h3>{$c['title']}</h3>
  <h4>{$c['instructors']}</h4><br>
  
  <table id="info">
    <tbody>
      <tr>
        <td width="70px">Times</td>
        <td>{$times}</td>
      </tr>
      <tr>
        <td>Location</td>
        <td>{$locs}</td>
      </tr>
      <tr>
        <td>Fulfills</td>
        <td>{$distrib}</td>
      </tr>
      <tr>
        <td>Prereqs</td>
        <td>{$prereqs}</td>
      </tr>
    </tbody>
  </table><br>
  
  <p>{$c['description']}</p>
  
  <p><a href="https://courses.wellesley.edu/display_single_course_cb.php?crn={$_GET['crn']}&semester={$c['term_code']}">View official details</a></p>

  {$sections}
  
  {$shop}
EOF;
  echo $info;
}

/*
 * Checks for and formats link for any other sections offered of given course
 *
 * @param $crn, CRN of course
 * @param $term, term offered
 * @param $course, course name (subject code and course number)
 */
function get_sections($crn,$term,$course) {
  global $dbh;
  
  // get all courses offered in same term with same course name
  $query = 'SELECT term_code,crn FROM courses WHERE term_code = ? and concat(subject_code," ",course_number) = ? ORDER BY section_number';
  
  $resultset = prepared_query($dbh, $query, array($term,$course));
  
  $sections = array();
  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    // if the CRN does not match current course, add it as a different section
    if ($row['crn'] != $crn) $sections[] = '<a href="javascript:void(0);" onclick="getDetail('.$row['term_code'].','.$row['crn'].')">'.$row['crn'].'</a>';
  }
  return $sections;        
}
?>
<?php
/**
 * Shown in Settings page in settings.php
 * Displays current user information in form for editing
 */
include('path.php');
session_start();
require_once('MDB2.php');
require_once('/home/cs304/public_html/php/MDB2-functions.php');
require_once('search-helper.php');
require_once('coursewire-dsn.inc');
$dbh = db_connect($coursewire_dsn);

// see if user already in table
$query = 'SELECT * FROM students WHERE student_id = ?';
$resultset = prepared_query($dbh,$query,array($_SESSION['user']));

if ($resultset->numRows() > 0) {
  $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);    
  // set new session variable for user pulldown
  if ($row['name'] !== '') $_SESSION['name'] = $row['name'];
} else {
  // insert user with bare information if does not exist
  $query = 'INSERT INTO students(student_id,is_user) VALUES (?,?)';
  $terms = array($_SESSION['user'],true);
  prepared_statement($dbh,$query,$terms);
}
?>

<div class="alert alert-success fade in">
    <strong>Settings saved!</strong> 
</div>
  
<form id='settings-form' class='form-inline' method='POST'>
  <table id="info">
  <tbody>
  <tr>
    <td width="80px"><label>Name</label></td>
    <td><input class="input-small" type="text" name="name"></td>
  </tr>
  <tr>
    <td><label>Class year</label></td>
    <td>
    <select class="span2" name="year">
      <option value=""></option>
      <option value="2013">2013</option>
      <option value="2014">2014</option>
      <option value="2015">2015</option>
      <option value="2016">2016</option>
      <option value="2017">2017</option>
      <option value="DS">Davis Scholar</option>
    </select>
    </td>
  </tr>
  </tbody>
  </table><br>
  
  <label>Major(s)</label>
  <span class="help-block">Please select your major(s), if any. If you have an individual major, please select the major you feel is most related if you would like to receive course recommendations for your major.</span>
  <select name="major1">
  <option value=""></option>
  <?php options($majorList); ?>        
  </select><br><br>
  
  <label>Second Major/Minor</label>
  <span class="help-block">Please select your second major or minor, if any.</span>
  <select name="major2">
  <option value=""></option>
  <?php options($majorList); ?>        
  </select>
  
  <p><center><button id="settings-submit" type="submit" class="btn btn-primary">Save</button>
  </center>
</form>

<script language="javascript" type="text/javascript">
$(document).ready(function() {
  <?php
  // prefill form with user data
  if ($row) {
      echo <<<EOF
      $('[name=name]').val('{$row['name']}');
      $('[name=year]').val('{$row['grad_year']}');
      $('[name=major1]').val('{$row['major1']}');
      $('[name=major2]').val('{$row['major2']}');
EOF;
  }
  ?>
});
    
$('#settings-form').submit(function(event){
  event.preventDefault();
  $('.alert').fadeOut(); // fade out all alerts if displaying
  var data = $(this).serialize();  // get form data
  $.ajax({
    cache: false,
    type: 'POST',              
    url: '<?php echo $path ?>edit-profile.php',
    data: data,
    success: function () { $('.alert').fadeIn(); }, // alert user on successful edit
    error: function (response) {
      alert("error: " + response.responseText);
    }
    });
});
</script>
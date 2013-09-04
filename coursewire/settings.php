<?php
/**
 * Displays settings page
 * Links to profile.php, course-history.php and shopped-courses.php
 */
 include('header.php');
 
 // check if logged in
if(!$loggedIn) {
  echo '<a href="?loginCAS">Please log in with CAS to see your settings page.</a>';
} else {
?>
<script language="javascript" type="text/javascript">
 $(document).ready(function() {
  // load profile page by default
  $.ajax({
   type: "GET",
   url: '<?php echo $path ?>profile.php',
   success: function(data) {
      $('#settings').html(data);
   }
  });
  $('[name=profile]').addClass('active');

  $('#profile').click(function () {
    $.ajax({
     type: "GET",
     url: '<?php echo $path ?>profile.php',
     success: function(data) {
        $('#settings').html(data);
     }
    });    
    reset_sidebar();
    $('[name=profile]').addClass('active');
  });

  $('#history').click(function () {
    $.ajax({
    type: "GET",
    url: '<?php echo $path ?>course-history.php',
    success: function(data) {
      $('#settings').html(data);
    }
    });
    reset_sidebar();
    $('[name=courses]').addClass('active');
  });

  $('#shopped').click(function () {
    $.ajax({
    type: "GET",
    url: '<?php echo $path ?>shopped-courses.php',
    success: function(data) {
      $('#settings').html(data);
    }
    });
    reset_sidebar();
    $('[name=shopped]').addClass('active');
  });
});

function reset_sidebar() {
  $('[name=profile]').removeClass('active');
  $('[name=courses]').removeClass('active');
  $('[name=shopped]').removeClass('active');
}    
</script>
<!-- sidebar nav -->
<legend>Settings</legend>
<div id="settings-nav" class="well">
<ul class="nav nav-list">
  <li class="nav-header">About You</li>
  <li name="profile"><a href="javascript:void(0);" id="profile">Profile</a></li>
  <li name="courses"><a href="javascript:void(0);" id="history">Course history</a></li>
  <li class="nav-header">Courses</li>  
  <li name="shopped"><a href="javascript:void(0);" id="shopped">Shopped courses</a></li>
  <li class="divider"></li>
  <li><a href="mailto:nmckinne@wellesley.edu">Questions?</a></li>
</ul>
</div>
<div id="settings">
</div>
<?php include('footer.php');
} ?>
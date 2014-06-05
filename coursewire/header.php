<?php
/**
 * Create header navigation and provides required files below
 */
include('path.php');
require('cas-login.php');
require_once('MDB2.php');
require_once('/home/cs304/public_html/php/MDB2-functions.php');
require_once('search-helper.php');

require_once('coursewire-dsn.inc');
$dbh = db_connect($coursewire_dsn);

// starts in cas-login.php session_start(); // initialize session

// check if user is in database when first logged in
if (isset($_SESSION['user']) and !isset($_SESSION['name']) and !isset($checked)) {
  $query = 'SELECT * FROM students WHERE student_id = ?';
  $resultset = prepared_query($dbh,$query,array($_SESSION['user']));

  if ($resultset->numRows() > 0) {
    $row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC);
    // if a name is set in the database, use it in session
    if (isset($row['name'])) {
      $_SESSION['name'] = $row['name'];
    } else { $_SESSION['name'] = $_SESSION['user']; } // otherwise use the username
    // check if any majors are set to display appropriate recommendations
    if (isset($row['major1'])) $_SESSION['major1'] = $row['major1'];
    if (isset($row['major2'])) $_SESSION['major2'] = $row['major2'];            
  } else {
    // use username as name if no user is found in database
    $_SESSION['name'] = $_SESSION['user'];
  }
  $checked = true; // mark as checked so it doesn't query each time navbar is loaded
} ?><!-- begin header html -->
<!doctype html>
<html lang='en'>
<head>
<meta charset="utf-8">
<title>Wellesley Coursewire</title>
<link href="<?php echo $path ?>css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $path ?>css/style.bare.css" rel="stylesheet">
<script type="text/JavaScript" src="<?php echo $path ?>js/jquery-1.9.1.min.js"></script>
<script type="text/JavaScript" src="<?php echo $path ?>js/bootstrap.min.js"></script>
<script src="<?php echo $path ?>js/SDA-logger.js"></script>
<script src="<?php echo $path ?>js/cookies.js"></script>
</head>

<body>
  <div class="container">
  <div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
  <div class="container">
    <a class="brand" href="<?php echo $path ?>">Wellesley Coursewire</a>
  <div class="nav-collapse">
    <ul class="nav">
      <li<?php if (strpos($_SERVER['SCRIPT_NAME'],'index') !== false) echo ' class="active"'; ?>><a href="<?php echo $path ?>">Dashboard</a></li>
      <li<?php if (strpos($_SERVER['SCRIPT_NAME'],'search') !== false) echo ' class="active"'; ?>><a href="<?php echo $path ?>search/">Search</a></li>
      <li<?php if (strpos($_SERVER['SCRIPT_NAME'],'planner') !== false) echo ' class="active"'; ?>><a href="<?php echo $path ?>planner/">Planner</a></li>
      <li<?php if (strpos($_SERVER['SCRIPT_NAME'],'about') !== false) echo ' class="active"'; ?>><a href="<?php echo $path ?>about/">About</a></li>
    </ul>
    <ul class="nav pull-right">
<?php
// display settings and log out is someone is logged in
if (isset($_SESSION['user'])) {
  echo <<< EOF
  <li class="pull-right dropdown">
  <a href="#" id="user" class="dropdown-toggle" data-toggle="dropdown">Hello, {$_SESSION['name']} <b class="caret"></b></a>
  <ul class="dropdown-menu" role="menu" aria-labelledby="user">
  <li role="presentation"><a role="menuitem" tabindex="-1" href="{$path}settings/">Settings</a></li>
  <li role="presentation"><a role="menuitem" tabindex="-1" href="?logoutCAS">Sign out</a></li>
  </ul>
  </li>
EOF;
} else {
  // otherwise display link to CAS for login
  echo <<< EOF
  <li class="pull-right dropdown">
  <a href="?loginCAS" id="user">Sign in</a>
  </li>
EOF;
}
?>
    </ul>
  </div><!--/.nav-collapse -->
  </div>
  </div>
  </div>
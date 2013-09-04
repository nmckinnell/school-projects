<?php
/**
 * Provides helper functions, primarily for search.php
 */
$termList = array('201202'=>'Spring 2012','201109'=>'Fall 2011');
    
$subjectList = array('AFR'=>'Africana Studies','AMST'=>'American Studies','ANTH'=>'Anthropology','ARAB'=>'Arabic','ARTH'=>'Art History','ARTS'=>'Art-Studio','ASTR'=>'Astronomy','BIOC'=>'Biochemistry','BISC'=>'Biological Science','CAMS'=>'Cinema and Media Studies','CHEM'=>'Chemistry','CHIN'=>'Chinese','CLCV'=>'Classical Civilization','CLSC'=>'Cognitive and Linguistic Science','CPLT'=>'Comparative Literature','CS'=>'Computer Science','ECON'=>'Economics','EDUC'=>'Education','ENG'=>'English','ES'=>'Environmental Studies','EXTD'=>'Extradepartmental','FREN'=>'French','GEOS'=>'Geosciences','GER'=>'German','GRK'=>'Greek','HEBR'=>'Hebrew','HIST'=>'History','HNUR'=>'Hindi/Urdu','ITAS'=>'Italian Studies','JPN'=>'Japanese','KOR'=>'Korean','LAT'=>'Latin','LING'=>'Linguistics','MATH'=>'Mathematics','ME/R'=>'Medieval/Renaissance','MES'=>'Middle Eastern Studies','MUS'=>'Music','NEUR'=>'Neuroscience','PEAC'=>'Peace and Justice Studies','PHIL'=>'Philosophy','PHYS'=>'Physics','POL'=>'Political Science (POL)','POL1'=>'Political Science (POL1)','POL2'=>'Political Science (POL2)','POL3'=>'Political Science (POL3)','POL4'=>'Political Science (POL4)','PORT'=>'Portuguese','PSYC'=>'Psychology','QR'=>'Quantitative Reasoning','REL'=>'Religion','RUSS'=>'Russian','SAS'=>'South Asia Studies','SOC'=>'Sociology','SPAN'=>'Spanish','SUST'=>'Sustainability','SWA'=>'Swahili','THST'=>'Theatre Studies','WGST'=>"Women's & Gender Studies",'WRIT'=>'Writing');

$majorList = array('AFR'=>'Africana Studies','AMST'=>'American Studies','ANTH'=>'Anthropology','ARAB'=>'Arabic','ARTH'=>'Art History','ARTS'=>'Art-Studio','ASTR'=>'Astronomy','ASPH'=>'Astrophysics','BIOC'=>'Biochemistry','BISC'=>'Biological Science','CAMS'=>'Cinema and Media Studies','CHEM'=>'Chemistry','CHIN'=>'Chinese','CLST'=>'Classics','CLCV'=>'Classical Civilization','CNEA'=>'Classical & Near Eastern Archeology','CLSC'=>'Cognitive and Linguistic Science','CPLT'=>'Comparative Literature','CS'=>'Computer Science','EAS'=>'East Asian Studies','ECON'=>'Economics','EDUC'=>'Education','EDCT'=>'Teacher Education','EDST'=>'Education Studies','ENG'=>'English','ES'=>'Environmental Studies','EXTD'=>'Extradepartmental','FREN'=>'French','FRST'=>'French Cultural Studies','GEOS'=>'Geosciences','GER'=>'German','GERS'=>'German Studies','GRK'=>'Greek','HEBR'=>'Hebrew','HIST'=>'History','HNUR'=>'Hindi/Urdu','IREC'=>'Intl Relations - Economics','IRHI'=>'Intl Relations - History','IRPS'=>'Intl Relations - Political Science','ITAS'=>'Italian Studies','JPN'=>'Japanese','JWST'=>'Jewish Studies','KOR'=>'Korean','LAT'=>'Latin','LAST'=>'Latin American Studies','LING'=>'Linguistics','MATH'=>'Mathematics','ME/R'=>'Medieval/Renaissance','MES'=>'Middle Eastern Studies','MUS'=>'Music','NEUR'=>'Neuroscience','PEAC'=>'Peace and Justice Studies','PHIL'=>'Philosophy','PHYS'=>'Physics','POL'=>'Political Science','PORT'=>'Portuguese','PSYC'=>'Psychology','QR'=>'Quantitative Reasoning','REL'=>'Religion','RUSS'=>'Russian','RAST'=>'Russian Area Studies','SAS'=>'South Asia Studies','SOC'=>'Sociology','SPAN'=>'Spanish','SUST'=>'Sustainability','SWA'=>'Swahili','THST'=>'Theatre Studies','WGST'=>"Women's & Gender Studies",'WRIT'=>'Writing');

$daysList = array('M'=>'Mon','T'=>'Tue','W'=>'Wed','Th'=>'Thu','F'=>'Fri');

$startend1List = array('08:30 am - 09:40 am'=>'8:30 am to 9:40 am','09:50 am - 11:00 am'=>'9:50 am to 11:00 am','11:10 am - 12:20 pm'=>'11:10 am to 12:20 pm','01:30 pm - 02:40 pm'=>'1:30 pm to 2:40 pm','02:50 pm - 04:00 pm'=>'2:50 pm - 4:00 pm','04:10 pm to 05:20 pm'=>'4:10 pm to 5:20 pm');

$start1List = array('09:00'=>'9 am','10:00'=>'10 am','11:00'=>'11 am','12:00'=>'12 pm','13:00'=>'1 pm','14:00'=>'2 pm','15:00'=>'3 pm','16:00'=>'4 pm','17:00'=>'5 pm','18:00'=>'6 pm','19:00'=>'7 pm');

$end1List = array('09:00'=>'9 am','10:00'=>'10 am','11:00'=>'11 am','12:00'=>'12 pm','13:00'=>'1 pm','14:00'=>'2 pm','15:00'=>'3 pm','16:00'=>'4 pm','17:00'=>'5 pm','18:00'=>'6 pm','19:00'=>'7 pm','20:00'=>'8 pm','21:00'=>'9 pm',);    

$distribList = array('ars'=>'ARS','ll'=>'LL','sba'=>'SBA','ec'=>'EC','rep'=>'REP','hs'=>'HS','mm'=>'MM','nps'=>'NPS','qr'=>'QR');

$sortList = array('subject_code'=>'Department','course_number'=>'Level','start1'=>'Time');

/*
 * Stores the GET parameters for the current search in session
 * Adds current search to past query list in session (max 5 entries)
 */
function store_session() {
  //if (empty($_GET)) session_destroy(); // clean up past search
  if (!empty($_GET)) {
    // create session array to store current query
    $_SESSION['query'] = array();
    // cycle through GET param to add to session
    foreach ($_GET AS $key => $value) {
      // array values added to one general array to make selecting checkbox easier
      if (is_array($value)) {
        foreach ($value as $box) {
          $_SESSION['query']['checkbox'][$box] = 'true';
        }
      } else {
        $_SESSION['query'][$key] = $value;
      }
    }
    // if no past query array in session, make it
    if (!isset($_SESSION['past_query'])) $_SESSION['past_query'] = array();
    // get current GET param and corresponding url
    $current = array('terms'=>$_GET,'url'=>$_SERVER['REQUEST_URI']);
    // add to front, remove any previous entries
    if (($key = array_search($current, $_SESSION['past_query'])) !== false) unset($_SESSION['past_query'][$key]); 
    array_unshift($_SESSION['past_query'],$current);             
    // if >5 past queries, remove oldest
    if (count($_SESSION['past_query'])>5) array_pop($_SESSION['past_query']);
  }
}

/*
 * Unsets empty or invalid GET parameters
 */
function get_search_history() {
  // check for search history in session
  if (isset($_SESSION['past_query'])) {
    echo '<b>Search history:</b><br>';
    // create link for each past search
    foreach($_SESSION['past_query'] as $query) {
      echo '<a href="'.$query['url'].'">'.displayParam($query['terms']).'</a>';
      if ($query !== end($_SESSION['past_query'])) { echo '<br>'; }
    }
  }
}

/*
 * Creates select options for form using array values
 *
 * @param $list, list of value pairs to use as value and text of option
 */
function options(array $list) {
  foreach ($list as $code => $name) {
    echo '<option value="'.$code.'">'.$name.'</option>';
  }
}

/*
 * Creates checkboxes with given attributes from strings and values from array
 *
 * @param $list, list of value pairs to use as value and text of checkbox
 * @param $attr, first half of checkbox form code with specific attributes  
 */
function checkboxes($attr,array $list) {
  foreach ($list as $code => $name) {
    echo $attr.'value="'.$code.'"';
    // if there is a current query, check used values
    if (!empty($_GET) and isset($_SESSION['query']['checkbox'][$code])) {
      echo ' checked="checked"';
    }
    echo '>'.$name.'</label>';
  }
}

function getSearchResults(array $param) {
  global $dbh;
  
  filter_fields($param);
  if (isset($param['course_number'])) $param['course_number'] = format_course_number($param['course_number']);

  /* Remove if set to non-existent field
   * Set sort_by to department by default
   * Remove sort_by from term parsing as it will be added at the end
  */
  $sortFields = array('term_code','subject_code','course_number','instructors','startend1');
  
  if (isset($param['sort_by']) and in_array($param['sort_by'],$sortFields)) { $sort_by = $param['sort_by']; }
  else { $sort_by = 'subject_code'; }
  unset($param['sort_by']);
  
  $where_field = '';
  $search_terms = array();
  
  $text_field = array('subject_code','instructors');
  
  foreach ($param as $key => $value) {
    // select fields have pre-filled one-choice
    if ($key == 'term_code') {
      $where_field .= $key . ' = ?';      
      array_push($search_terms,$value);
    }
    // user-inputted info, might have multiples by commas
    else if (in_array($key,$text_field)) {
      $where_field .= '('.$key . ' REGEXP ?)';        
      $term = str_replace('*','(.*)',trim($value));
      array_push($search_terms,str_replace(',','|',$term));
    }
    else if ($key == 'course_number') {
      $where_field .= '(';
      foreach ($value as $q) {                    
        $where_field .= '(subject_code REGEXP ? AND course_number REGEXP ?)';
        if ($q !== end($value)) { $where_field .= ' OR '; }
      }
      $where_field .= ')';
      
      foreach ($value as $query) {
        $query = str_replace('*','(.*)',$query);
        $query = explode(' ',$query);
        foreach ($query as $q) {
          $q = explode('+',$q);
          foreach ($q as &$dept) {
            if (!empty($dept)) $dept = '^'.$dept;
          }
          $q = implode('|',$q);
          // ", math 1*" gave "","math 1*" when broken up
          if(!empty($q)) array_push($search_terms,$q);
        }
      }
    }
    else if ($key == 'keyword') {
      $keywords = explode(',',$value);
      $where_field .= '(';
      foreach ($keywords as $word) {
        $where_field .= '(course_title LIKE ? OR course_description LIKE ?)';
        if ($word !== end($keywords)) { $where_field .= ' OR '; }
      }
      $where_field .= ')';
      
      $keywords = explode(',',$value);
      foreach ($keywords as $word) {
        $term = '%'.trim($word).'%';
        array_push($search_terms,$term,$term);
      }
    }
    else if ($key == 'days') {
      $where = array();       
      $days = array();
      foreach ($value as $day) {
        // 'T' needs special case to avoid showing 'Th'
        if ($day == 'T') {
          $where[] = 'concat(days1,days2,days3) REGEXP BINARY ?';
          array_push($search_terms,'('.$day."$|".$day."[[:upper:]])");
        }
        else {
          $where[] = "LOCATE(?,concat(days1,days2,days3)) > 0";
          array_push($search_terms,$day);
        }
      }      
      $where = '('.implode(' AND ',$where).')';
      $where_field .= $where;
    }
    else if ($key == 'startend1') {
      $where_field .= '(startend1 = ? OR startend2 = ? OR startend3 = ?)';array_push($search_terms,$value,$value,$value);
    }
    else if ($key == 'start1') {
      $where_field .= $key . ' >= ?';        
      array_push($search_terms,$value);
    }
    else if ($key == 'end1') {
      $where_field .= $key . ' <= ?';      
      array_push($search_terms,$value);
    }
    else if ($key == 'distrib') {
      $where_field .= '(';
      foreach ($value as $distrib) {
        $where_field .= ' ? IN (distribution1,distribution2,distribution3)';
        if ($distrib !== end($value)) { $where_field .= ' OR '; }        
        array_push($search_terms,getDistrib($distrib,false));
      }
      $where_field .= ')';
    }
    else if ($key == 'hasLab') { $where_field .= "course_title LIKE '%with%Lab%'"; }
    
    end($param); // move to last element to check if at end of array
    if ($key !== key($param)) { $where_field .= ' AND '; }
  }
  
  $query = 'SELECT term_code,crn,concat(subject_code," ",course_number) as course,course_title,instructors,days1,startend1,days2,startend2,days3,startend3,distribution1,distribution2,distribution3 FROM courses';
  
  if (!empty($where_field)) $query .= ' WHERE ' . $where_field;
  
  // add sort_by to end of query
  $query .= ' ORDER BY ?';
  array_push($search_terms,$sort_by);
  
  /*print_r($query);
  echo '<p>';
  print_r($search_terms);*/
  
  $resultset = prepared_query($dbh, $query, $search_terms);
  
  if(PEAR::isError($resultset)) {
    die('Failed to issue query, error message : ' . $resultset->getMessage());
  }
  
  if ($resultset->numRows() == 0) {
      echo "No results found for ".displayParam($param);
  } else {               
    echo '<h4>Results</h4>';
    echo $resultset->numRows() . ' result found for '.displayParam($param);
    global $sortList;
    echo ', sorted by ' . strtolower($sortList[$sort_by]);
    while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      formatCourse($row);		
    }             
  }
}

/*
 * Unsets empty or invalid GET parameters
 */
function filter_fields($param) {
  // list all possible search fields to avoid errors from unused keys in GET
  $fields = array('term_code','subject_code','course_number','keyword','instructors','days','startend1','start1','end1','distrib','hasLab','sort_by');
  
  // remove empty and non-standard GET variables
  foreach ($param as $key => $value) {
    if (empty($value) or !in_array($key,$fields)) { unset($param[$key]); }            
  }
}

/*
 * Converts user inputted course numbers to array
 */
function format_course_number($course) {
  // convert user-inputted course_number to appropriate fields
  $course_input = preg_replace('/\s{2,}/',' ',$course);
  $courses = explode(',',trim($course_input));
  foreach ($courses as $key => $value) {
    $value = trim($value);
    if (empty($value)) unset($courses[$key]);
    // double check for spaces in case of manually inputted url
    if (!preg_match('/.\s./',$value)) {
      echo 'Please include a space between the department and the course number.';
      return;
    }
  }
  return $courses;
}

/*
 * Displays current array in readable way (pulls from form variables)
 *
 * @param $array, array of search parameters
 * @return $terms, string of search terms separated by commas
 */
function displayParam($param) {
  global $termList,$distribList,$startend1List,$end1List;
  
  // do not want to list sort by value
  if(isset($param['sort_by'])) unset($param['sort_by']);
  
  $terms = '';
  foreach ($param as $key => $value) {
    // append more information before time variables for context
    if($key == 'start1' and !isset($param['end1'])) $terms .= ' after ';
    if($key == 'end1' and !isset($param['start1'])) $terms .= ' before ';
    
    // separate array values by comma except for days
    if(is_array($value) and $key != 'days') {
      foreach ($value as $term) {
        $terms .= ($key == 'distrib' ? $distribList[$term] : $term);
        if ($term != end($value)) $terms .= ', ';
      }
    }
    // days are shown in a block with no comma
    else if($key == 'days') {
      foreach ($value as $day) { $terms .= $day; }
    }
    // term code and times are translated using arrays used to create the form
    else if ($key == 'term_code') $terms .= $termList[$value];
    else if ($key == 'startend1') $terms .= $startend1List[$value];
    else if ($key == 'start1' or $key == 'end1') $terms .= $end1List[$value];
    else $terms .= $value;
    
    // if have start and end time, separate by 'to' instead of comma for context
    if($key == 'start1' and isset($param['end1'])) $terms .= ' to ';
    else if ($value != end($param)) $terms .= ', ';
  }
  if (empty($terms)) $terms = 'All Terms, All Subjects';
  return $terms;
}

/*
 * Creates html for individual course result
 *
 * @param $row, individual MySQL result row for course
 * @return $course, string of all html for given course
 */
function formatCourse($row) {
  // collect all days and times
  $time = '';
  if ($row['days1']) $time .= $row['days1'].' ' . $row['startend1'] . '<br>';
  if ($row['days2']) $time .= $row['days2'].' ' . $row['startend2'] . '<br>';
  if ($row['days3']) $time .= $row['days3'].' ' . $row['startend3'] . '<br>';

  $distrib = array();
  // collect all distribution fields
  foreach (array($row['distribution1'],$row['distribution2'],$row['distribution3']) as $distribField) {
    // add acronym for distribution to array
    if (getDistrib($distribField)) $distrib[] = getDistrib($distribField);
  }
  // use array to create comma-separated list of distributions
  $distrib = implode(', ',$distrib);

  $course = <<<EOF
  <div id="{$row['crn']}" class="course well">
  <div class="right">        
  {$time}
  {$distrib}<br>
  <small><a href="javascript:void(0);" onclick="getDetail({$row['term_code']},{$row['crn']})">More info</a></small>
  </div>
  <div>        
  {$row['course']}<br>
  {$row['course_title']}<br>
  {$row['instructors']}<br>
  </div>        
  </div>
EOF;
  echo $course;
}

/*
 * Translates distribution variable to/from acronym
 *
 * @param $value, given distribution string in acronym or full text form
 * @param $getAcro, determines whether acronym (default) or full text is returned
 * @return $distrib, acronym or full text version of given distribution
 */    
function getDistrib($value,$getAcro=true) {
  $full = array('ars'=>'Arts, Music, Theatre, Film, Video','ll'=>'Language and Literature','sba'=>'Social and Behavioral Analysis','ec'=>'Epistemology and Cognition','rep'=>'Religion, Ethics, and Moral Philosophy','hs'=>'Historical Studies','mm'=>'Mathematical Modeling','nps'=>'Natural and Physical Science','qr'=>'Fulfills the Quantitative Reasoning overlay course requirement');
  // flip previous array
  $acronym = array_flip($full);
  // check if given param is valid
  if (!in_array($value,$full) and !in_array($value,$acronym)) return;
  $getAcro ? $distrib = strtoupper($acronym[$value]) : $distrib = $full[$value];
  return $distrib;
}
?>
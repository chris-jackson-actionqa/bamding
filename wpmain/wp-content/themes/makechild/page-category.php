<?php
require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');

get_header();  

// don't show to non-members
if (current_user_can("access_s2member_level1"))
  {
    DisplayCategory::show();
  } 
else 
  {
  // redirect non-members to pay up!
  header('Location: ' . Site::getBaseURL() . '/prices');
  exit();
  } 

  get_footer();


<?php
require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');

get_header();  

// don't show venues to non-members
if (current_user_can("access_s2member_level1"))
  {
  DisplayForms::editVenue(Site::getBaseURL() . '/myvenues/', (int)$_GET['venue_id']);
  } 
else 
  {
  // redirect non-members to pay up!
  header('Location: ' . Site::getBaseURL() . '/prices/');
  exit();
  } 

  get_footer();


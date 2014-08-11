<?php
require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');

get_header();  

// don't show venues to non-members
if (current_user_can("access_s2member_level1"))
  {
  DisplayForms::addNewVenue(Site::getBaseURL() . '/myvenues/');
  } 
else 
  {
  // redirect non-members to pay up!
  header('Location: ' . Site::getBaseURL() . '/prices/');
  exit();
  } 

  get_footer();


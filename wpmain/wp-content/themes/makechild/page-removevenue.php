<?php
require_once(ABSPATH. '/wp-content/tardis/Venues.php');
require_once(ABSPATH. '/wp-content/tardis/Venue.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayForms.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayData.php');
require_once(ABSPATH. '/wp-content/tardis/ProcessForms.php');

get_header();  

// don't show to non-members
if (current_user_can("access_s2member_level1"))
  {
  DisplayForms::confirmRemoveVenues();
  } 
else 
  {
  // redirect non-members to pay up!
  header('Location: http://bamding.com/prices');
  exit();
  } 

  get_footer();


<?php
require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');

get_header();  

// don't show venues to non-members
if (current_user_can("access_s2member_level1"))
  {
  // Process any new venue submissions
  ProcessForms::AddNewVenue($_POST);
  ProcessForms::removeVenues($_POST);
  
  // Display the venues
  echo '<h1>My Venues</h1>';
  echo '  <a href="'. Site::getBaseURL() . '/addvenue/" id="bdAddMyVenueLink">'
    . 'Add A Venue</a><br />';
  DisplayData::displayMyVenues(get_user_field('user_login'));
  } 
else 
  {
  // redirect non-members to pay up!
  header('Location: ' . Site::getBaseURL() . '/prices');
  exit();
  } 

  get_footer();


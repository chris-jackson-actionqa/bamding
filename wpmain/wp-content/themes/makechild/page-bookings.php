<?php
require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');

// don't show venues to non-members
if (!current_user_can("access_s2member_level1"))
  {
  header('Location: ' . Site::getBaseURL() . '/prices/');
  exit();
  } 

get_header();  

$oBookings = new Bookings(get_user_field('user_login'));
$oBookings->getAllBookings();

DisplayForms::displayBookings(get_user_field('user_login'));

get_footer();



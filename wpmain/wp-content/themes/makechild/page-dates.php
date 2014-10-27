<?php
/**
 * Let users edit and view their dates and timeframes for their bookings.
 * @author Chris Jackson
 * @package BamDing
 */
require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');

// don't show to non-members
if (!current_user_can("access_s2member_level1"))
  {
  header('Location: ' . Site::getBaseURL() . '/prices/');
  exit();
  } 

get_header();  

// display dates and timeframes
DisplayDates::displayDatesTimeFrames(get_user_field('user_login'));

get_footer();

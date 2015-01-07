<?php
if(!defined('ABSPATH') && preg_match('/xampp/i', $_SERVER['DOCUMENT_ROOT']))
{
  define('ABSPATH', $_SERVER['DOCUMENT_ROOT'] . '/bamding/wpmain');
}
elseif (!defined('ABSPATH'))
{
  define('ABSPATH', $_SERVER['DOCUMENT_ROOT'] . '/..');
}

require_once(ABSPATH. '/wp-content/tardis/Database.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayData.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayForms.php');
require_once(ABSPATH. '/wp-content/tardis/ProcessForms.php');
require_once(ABSPATH. '/wp-content/tardis/Site.php');
require_once(ABSPATH. '/wp-content/tardis/Venue.php');
require_once(ABSPATH. '/wp-content/tardis/Venues.php');
require_once(ABSPATH. '/wp-content/tardis/Bookings.php');
require_once(ABSPATH. '/wp-content/tardis/BookingDates.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayDates.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayCategory.php');
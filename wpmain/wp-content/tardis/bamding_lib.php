<?php
if(empty($_SERVER['DOCUMENT_ROOT']))
{
  $_SERVER['DOCUMENT_ROOT'] = 'c://xampp//htdocs';
}

if(!defined('ABSPATH') && preg_match('/xampp/i', $_SERVER['DOCUMENT_ROOT']))
{
  define('ABSPATH', $_SERVER['DOCUMENT_ROOT'] . '/bamding/wpmain');
}
elseif (!defined('ABSPATH'))
{
  define('ABSPATH', $_SERVER['DOCUMENT_ROOT']);
}

require_once(ABSPATH. '/wp-content/tardis/Database.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayData.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayForms.php');
require_once(ABSPATH. '/wp-content/tardis/ProcessForms.php');
require_once(ABSPATH. '/wp-content/tardis/Site.php');
require_once(ABSPATH. '/wp-content/tardis/Venue.php');
require_once(ABSPATH. '/wp-content/tardis/Venues.php');
require_once(ABSPATH. '/wp-content/tardis/BandDetails.php');
require_once(ABSPATH. '/wp-content/tardis/Bookings.php');
require_once(ABSPATH. '/wp-content/tardis/BookingDates.php');
require_once(ABSPATH. '/wp-content/tardis/BookingTemplates.php');
require_once(ABSPATH. '/wp-content/tardis/BookingTemplate.php');
require_once(ABSPATH. '/wp-content/tardis/Display.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayDates.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayCategory.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayBandDetails.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayBookings.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayFrequency.php');
require_once(ABSPATH. '/wp-content/tardis/DisplaySetTemplate.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayTemplates.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayEditTemplate.php');
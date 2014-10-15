<?php

if(preg_match('/xampp/i', $_SERVER['DOCUMENT_ROOT']))
{
  define('ABSPATH', $_SERVER['DOCUMENT_ROOT'] . '/bamding/wpmain');
  define('ADMINPATH', $_SERVER['DOCUMENT_ROOT'] . '/bamding/admin');
}
 else 
{
  define('ABSPATH', $_SERVER['DOCUMENT_ROOT'] . '/..');
  define('ADMINPATH', $_SERVER['DOCUMENT_ROOT'] . '/../admin');
}

require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');
require_once(ADMINPATH . '/lib/AdminDates.php');
require_once(ADMINPATH . '/lib/AdminDisplay.php');
require_once(ADMINPATH . '/lib/AdminDisplayBookings.php');
require_once(ADMINPATH . '/lib/AdminBookings.php');
require_once(ADMINPATH . '/lib/AdminReminders.php');
require_once(ADMINPATH . '/lib/AdminUsers.php');
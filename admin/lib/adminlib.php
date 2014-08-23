<?php

if(preg_match('/xampp/i', $_SERVER['DOCUMENT_ROOT']))
{
  define('ABSPATH', $_SERVER['DOCUMENT_ROOT'] . '/bamding/wpmain');
}
 else 
{
  define('ABSPATH', $_SERVER['DOCUMENT_ROOT']);
}

require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');
require_once('lib/AdminDisplay.php');
require_once('lib/AdminBookings.php');
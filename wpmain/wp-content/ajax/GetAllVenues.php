<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once './../tardis/bamding_lib.php';

if(empty($_REQUEST['user_login']))
{
  echo '';
}
else if ($_REQUEST['type'] === 'venues')
{
  // get user's venue data and store in javascript
  $oUserVenues = new Venues('my_venues', $_REQUEST['user_login']);
  $venues = $oUserVenues->getAllMyVenues();
}
else if($_REQUEST['type'] === 'bookings')
{
  $bookings = new Bookings($_REQUEST['user_login']);
  $venues = $bookings->getAllBookings();
}

$sJSON = \json_encode($venues);
echo $sJSON;

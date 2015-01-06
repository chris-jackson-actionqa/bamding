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
else
{
  // get user's venue data and store in javascript
  $oUserVenues = new Venues('my_venues', $_REQUEST['user_login']);
  $aUserVenues = $oUserVenues->getAllMyVenues();
  $sJSON = \json_encode($aUserVenues);
  echo $sJSON;
}

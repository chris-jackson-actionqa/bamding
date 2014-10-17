<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once './../lib/adminlib.php';

if(empty($_POST['user_login']))
{
  echo '';
}

// get user's venue data and store in javascript
$oUserVenues = new Venues('my_venues', $_POST['user_login']);
$aUserVenues = $oUserVenues->getAllMyVenues();
$sJSON = \json_encode($aUserVenues);
echo $sJSON;

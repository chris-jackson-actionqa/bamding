<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once './../lib/adminlib.php';

if(!key_exists('user_login', $_REQUEST))
{
  echo 'ERROR: user_login required' . "\n";
}
else
{
  $sHTML = AdminDisplay::displayDatesTimeFramesInnerHTML($_REQUEST['user_login']);
  echo $sHTML;
}

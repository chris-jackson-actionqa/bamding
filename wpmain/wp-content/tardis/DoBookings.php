<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if(!defined('ABSPATH'))
{
  $sAbsPath = $_SERVER['CONTEXT_DOCUMENT_ROOT'];
  if(preg_match('/\/$/', $sAbsPath))
  {
    $sAbsPath = preg_replace('/\/$/', '', $sAbsPath);
  }
  define('ABSPATH', $sAbsPath);
}
require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');

switch($_POST['action'])
{
  case 'startBooking':
    ProcessForms::processBookings($_POST);
    break;
  case 'setPause':
    ProcessForms::processBookings($_POST);
    break;
  case 'getHTMLTableNotContacted':
    DisplayForms::displayBookingsNotContacted($_POST['user_login']);
    break;
  case 'getHTMLTableScheduled':
    DisplayForms::displayBookingsScheduled($_POST['user_login']);
    break;
  case 'getHTMLTableActive':
    DisplayForms::displayBookingsActivePaused($_POST['user_login'], TRUE);
    break;
  case 'getHTMLTablePaused':
    DisplayForms::displayBookingsActivePaused($_POST['user_login'], FALSE);
    break;
  default:
    throw new Exception("Undefined action: " . $_POST['action']);
    break;
}

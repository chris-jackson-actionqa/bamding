<?php

require_once('./lib/adminlib.php');

$sUserLogin = '';
if(key_exists('user_login', $_GET))
{
  $sUserLogin = $_GET['user_login'];
}
AdminDisplay::getHeader("Dates", "initDatesDatePickers();initUserVenues('$sUserLogin');");
AdminDisplay::getMenu();
AdminDisplay::showDatesForm($_GET, $_POST);
AdminDisplay::getFooter();
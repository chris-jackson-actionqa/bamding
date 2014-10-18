<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../lib/adminlib.php';

$sUser = (key_exists('user_login', $_REQUEST)) ? 
        trim($_REQUEST['user_login']) : '';
$nVenueRange = (int)$_REQUEST['venue_range'];
$nDateType = (int)$_REQUEST['date_type'];
$sCountry = (key_exists('country', $_REQUEST)) ?
        trim($_REQUEST['country']) : '';
$sState = (key_exists('state', $_REQUEST)) ?
        trim($_REQUEST['state']) : '';
$sCity = (key_exists('city', $_REQUEST)) ?
        trim($_REQUEST['city']) : '';
$nVenueID = (int)$_REQUEST['venue_id'];

$oAdminDates = new AdminDates($sUser);
$sVenueRange = $oAdminDates->getVenueRangeFromID($nVenueRange);
$oAdminDates->deleteBookingDates($sVenueRange, $sCountry, $sState, $sCity, $nVenueID);
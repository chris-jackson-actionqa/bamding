<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('./lib/adminlib.php');

AdminDisplay::getHeader("Bookings");
AdminDisplay::getMenu();
AdminDisplayBookings::processBookingUpdate();
AdminDisplayBookings::upcomingBookings();
AdminDisplay::showH1User();
AdminDisplayBookings::showBookingsScript();
AdminDisplayBookings::showSubForms();
AdminDisplayBookings::showUpdateBookings();
AdminDisplayBookings::showBookedEmail();
AdminDisplay::getFooter();
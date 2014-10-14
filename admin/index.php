<?php
require_once('./lib/adminlib.php');

AdminDisplay::getHeader();
AdminDisplay::getMenu();
AdminDisplay::getReminders();
AdminDisplay::getTodaysBookings();
AdminDisplay::clearBoth();
AdminDisplay::bookingsForm('admin-bookings.php', 'bookings', $_POST);
AdminDisplay::displayBookingsTable($_POST);
AdminDisplay::getFooter();

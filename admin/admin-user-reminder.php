<?php
require_once('./lib/adminlib.php');

AdminDisplay::getHeader();
AdminDisplay::getMenu();
AdminDisplay::getReminders();
AdminDisplay::clearBoth();
AdminDisplay::getUserReminderData($_GET, $_POST);
AdminDisplay::getFooter();


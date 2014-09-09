<?php
require_once('./lib/adminlib.php');

AdminDisplay::getHeader("Reminders", "initReminderDatePickers()");
AdminDisplay::getMenu();
$sMessage = AdminReminders::updateReminderData($_POST);
AdminDisplay::showMessage($sMessage);
AdminDisplay::getReminders();
AdminDisplay::clearBoth();
AdminDisplay::getUpdateReminderSentForm($_GET);
AdminDisplay::getUserReminderData($_GET);
AdminDisplay::getFooter();


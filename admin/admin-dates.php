<?php

require_once('./lib/adminlib.php');

AdminDisplay::getHeader("Dates", 'initDatesDatePickers()');
AdminDisplay::getMenu();
AdminDisplay::showDatesForm($_GET, $_POST);
AdminDisplay::getFooter();
<?php

require_once('./lib/adminlib.php');

AdminDisplay::getHeader();
AdminDisplay::getMenu();
AdminDisplay::showDatesForm($_GET, $_POST);
AdminDisplay::getFooter();
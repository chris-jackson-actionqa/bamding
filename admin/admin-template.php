<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('./lib/adminlib.php');
ini_set('max_execution_time', 300); //TODO: debug why timeout occurs

AdminDisplay::getHeader("Template");
$template = new AdminTemplate();
$template->doPage();
AdminDisplay::getFooter();
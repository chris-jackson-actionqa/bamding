<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$to = $_REQUEST['to'];
$from = $_REQUEST['from'];
$subject = $_REQUEST['subject'];
$body = $_REQUEST['body'];

// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'From: BamDing <seth@bamding.com>' . "\r\n";

$result = mail($to, $subject, $body, $headers);

echo $result;
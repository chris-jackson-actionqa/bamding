<?php

require_once '../bamding_lib.php';

$oDB = new Database();
$oConn = $oDb->connect();

$sql = <<<SQL
DELETE * FROM booking-templates
WHERE user_login='test_user'
SQL;

$result = $oConn->query($sql);
$oConn->close();
if(FALSE === $result)
{
    throw new RuntimeException("Error deleting booking templates.");
}

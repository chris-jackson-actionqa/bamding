<?php

require_once 'c:/xampp/htdocs/bamding/wpmain/wp-content/tardis/bamding_lib.php';

$oDB = new Database();
$oConn = $oDB->connect();

$sql = <<<SQL
DELETE FROM booking_templates
WHERE user_login='test_user'
SQL;

$result = $oConn->query($sql);
$oConn->close();
if(FALSE === $result)
{
    throw new RuntimeException("Error deleting booking templates.");
}

<?php

require_once 'c:/xampp/htdocs/bamding/wpmain/wp-content/tardis/bamding_lib.php';

$oDB = new Database();
$oConn = $oDB->connect();

$sql = <<<SQL
DELETE FROM band_details
WHERE user_login='test_user'
SQL;

$result = $oConn->query($sql);
$error = $oConn->error;
$oConn->close();
if(FALSE === $result)
{
    throw new RuntimeException("Error deleting booking templates.\n".$error);
}

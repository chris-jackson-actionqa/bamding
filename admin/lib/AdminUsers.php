<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminUsers
 *
 * @author Seth
 */
class AdminUsers
{
  public static function getAllUsers()
  {
    $oDB = new Database();
    $oConn = $oDB->connect();
    
    $sSQL = <<<SQL
SELECT DISTINCT user_login FROM my_venues
SQL;
    
    $mResult = $oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new Exception("Could not get users from my_venues database.");
    }
    
    $aUsers = array();
    while($hRow = $mResult->fetch_assoc())
    {
      array_push($aUsers, $hRow['user_login']);
    }
    
    return $aUsers;
  }
}

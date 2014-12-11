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
  
  /**
   * Get the user's email address
   * 
   * @param string $user_login
   * @return string the user's email
   * @throws Exception
   */
  public static function getEmail($user_login)
  {
    $oDB = new Database();
    $oConn = $oDB->connect(true);
    
    $sSQL = "SELECT user_email FROM wp_users WHERE user_login='$user_login'";
    $mResult = $oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new RuntimeException("Could not get $user_login's email.");
    }
    
    $hRow = $mResult->fetch_assoc();
    return trim($hRow['user_email']);
  }
}

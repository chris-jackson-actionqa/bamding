<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminBookings
 *
 * @author Seth
 */
class AdminBookings {
  private $oConn = null;
  
  public function __construct()
  {
    $oDB = new Database();
    $this->oConn = $oDB->connect();
  }
  public function getAllUsers()
  {
    $sSQL = <<<SQL
SELECT DISTINCT user_login FROM bookings
SQL;
    
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new Exception("Could not get users from bookings database.");
    }
    
    $aUsers = array();
    while($hRow = $mResult->fetch_assoc())
    {
      array_push($aUsers, $hRow['user_login']);
    }
    
    return $aUsers;
  }
  
  public function getColumnHeadings()
  {
    $sSQL = <<<SQL
SELECT `COLUMN_NAME` 
FROM `INFORMATION_SCHEMA`.`COLUMNS` 
WHERE `TABLE_SCHEMA`=DATABASE() 
    AND `TABLE_NAME`='bookings';
SQL;
   
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new Exception("Could not get users from bookings database.");
    }
    
    return Database::fetch_all($mResult);
  }
  
  public function getBookings($sUser)
  {
    $hBookings = array();
    if('all' == $sUser)
    {
      $aUsers = $this->getAllUsers();
      foreach($aUsers as $sUser)
      {
        $oBookings = new Bookings($sUser);
        $hTempBookings = $oBookings->getAllBookings();
        $hBookings = array_merge($hBookings, $hTempBookings);
      }
    }
    else 
    {
      $oBookings = new Bookings($sUser);
      $hBookings = $oBookings->getAllBookings();
    }
    
    return $hBookings;
  }
  
  public function filterBookings($hBookings, $hPostData)
  {
    $sNextContactMin = "";
    if(array_key_exists('next_contact_min', $hPostData))
    {
      $sNextContactMin = trim($hPostData['next_contact_min']);
    }
    
    $hFilteredBookings = array();
    foreach($hBookings as $hRow)
    {
      if(!empty($sNextContactMin))
      {
        if($sNextContactIs == $hRow['next_contact'])
        {
          array_push($hFilteredBookings, $hRow);
        }
      }
    }
    
    return $hFilteredBookings;
  }
  
  public function getTodaysBookings()
  {
    $sSQL = <<<SQL
SELECT DISTINCT user_login, next_contact
FROM bookings
WHERE next_contact<=CURDATE() AND
      pause=0
SQL;
    
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new InvalidArgumentException("Could not get today's bookings from the database.");
      error_log($sSQL);
      error_log($this->oConn->error);
    }
    
    return Database::fetch_all($mResult);
  }

}

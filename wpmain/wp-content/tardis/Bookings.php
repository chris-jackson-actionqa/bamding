<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');

/**
 * Description of Bookings
 *
 * @author Seth
 */
class Bookings
{
  const BOOKINGS_TABLE = 'bookings';
  
  private $oConn;
  private $sUserLogin;
  
  public function __construct($sUserLogin)
  {
    $oDB = new Database();
    $this->oConn = $oDB->connect();
    
    $this->sUserLogin = $sUserLogin;
  }
  
  public function getAllBookings()
  {
    $sTable = Bookings::BOOKINGS_TABLE;
    $sSQL = <<<SQL
SELECT * FROM $sTable
WHERE user_login='{$this->sUserLogin}'
SQL;

    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new Exception("SQL query didn't work");
    }
    
    return $mResult->fetch_all(MYSQLI_ASSOC);
  }
  
  public function updateBooking()
  {
    
  }
  
  public function addBooking()
  {
    
  }
}

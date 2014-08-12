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
SELECT $sTable.*, my_venues.name, my_venues.city, my_venues.state, my_venues.country 
FROM $sTable
INNER JOIN my_venues
ON bookings.venue_id=my_venues.id
WHERE $sTable.user_login='{$this->sUserLogin}' 
ORDER BY my_venues.country, my_venues.state, my_venues.city, my_venues.name
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

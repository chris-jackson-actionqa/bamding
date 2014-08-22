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
  
  private function getBookingsSQL($sWhere = '')
  {
    $sWhere = (!empty($sWhere)) ? "AND $sWhere" : "";
    $sTable = Bookings::BOOKINGS_TABLE;
    $sSQL = <<<SQL
SELECT $sTable.*, my_venues.name, my_venues.city, my_venues.state, my_venues.country 
FROM $sTable
INNER JOIN my_venues
ON bookings.venue_id=my_venues.id
WHERE $sTable.user_login='{$this->sUserLogin}' $sWhere
ORDER BY my_venues.country, my_venues.state, my_venues.city, my_venues.name
SQL;
    
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new Exception("Bookings query didn't work: $sSQL");
    }
    
    return $mResult;
  }
  
  private function fetch_all($mResult)
  {
    $hAllRows = array();
    while ($hRow = $mResult->fetch_assoc())
    {
      array_push($hAllRows, $hRow);
    }
    return $hAllRows;
  }
  
  public function getAllBookings()
  {
    $mResult = $this->getBookingsSQL();
    
    return $this->fetch_all($mResult);
  }
  
  public function getActive()
  {
    $sWhere = "bookings.pause<>TRUE AND "
            . "(bookings.last_contacted IS NOT NULL AND bookings.last_contacted <> '0000-00-00')";
    $mResult = $this->getBookingsSQL($sWhere);
    return $this->fetch_all($mResult);
  }
  
  public function getNotContacted()
  {
    $sWhere = "(bookings.last_contacted IS NULL OR bookings.last_contacted='0000-00-00') AND "
            . "bookings.pause=TRUE";
    $mResult = $this->getBookingsSQL($sWhere);
    return $this->fetch_all($mResult);
  }
  
  public function getStarted()
  {
    $sWhere = "(bookings.last_contacted IS NULL OR bookings.last_contacted='0000-00-00') AND "
            . "bookings.pause=FALSE";
    $mResult = $this->getBookingsSQL($sWhere);
    return $this->fetch_all($mResult);
  }
  
  public function getPaused()
  {
    $sWhere = "bookings.pause=TRUE AND "
            . "(bookings.last_contacted IS NOT NULL AND bookings.last_contacted <> '0000-00-00')";
    $mResult = $this->getBookingsSQL($sWhere);
    return $this->fetch_all($mResult);
  }
  
  public function setPause($nVenueID, $bPause)
  {
    $nVenueID = (int)$nVenueID;
    
    // if string 'true' or 'false' passed in, change to boolean
    if(!is_bool($bPause))
    {
      if('true' == $bPause)
      {
        $bPause = TRUE;
      }
      else if( 'false' == $bPause)
      {
        $bPause = FALSE;
      }
      else 
      {
        throw new InvalidArgumentException("Value should be true or false. bPause = $bPause");
      }
    }
    $sPause = $bPause ? 'TRUE' : 'FALSE';
    
    $sSQL = <<<SQL
UPDATE bookings
SET pause=$sPause, timestamp=NOW()
WHERE user_login='{$this->sUserLogin}' AND venue_id=$nVenueID
SQL;
    
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new Exception("Could not pause venue: $sSQL");
    }
  }
  
  public function addNewBooking($nVenueID)
  {
    $sSQL = <<<SQL
INSERT INTO bookings (user_login, venue_id, frequency_num, freq_type, pause, timestamp)
VALUES ('{$this->sUserLogin}', '$nVenueID', '2', 'W', TRUE, NOW())
SQL;
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new Exception("Could not insert venue into booking table." . $this->oConn->error);
    }
  }
  
  public function removeBooking($nVenueID)
  {
    $sSQL = <<<SQL
DELETE FROM bookings
WHERE venue_id='$nVenueID' AND user_login='{$this->sUserLogin}'
SQL;

    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new Exception("Could not delete venue from booking table.");
    }
  }
}

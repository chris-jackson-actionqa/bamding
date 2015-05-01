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
SELECT $sTable.*, my_venues.name, my_venues.city, my_venues.state, my_venues.country, my_venues.category,
       booking_templates.title
FROM $sTable
INNER JOIN my_venues
ON bookings.venue_id=my_venues.id
LEFT JOIN booking_templates
ON booking_templates.template_id=bookings.template_id
WHERE $sTable.user_login='{$this->sUserLogin}' $sWhere
ORDER BY bookings.pause, my_venues.country, my_venues.state, my_venues.city, my_venues.name
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
      throw new RuntimeException("Could not pause venue: $sSQL");
    }
  }
  
  /**
   * Get the booking info for the venue
   * @param int $venueID
   * @return array of key/value pairs for the booking
   * @throws RuntimeException
   */
  public function getBooking($venueID)
  {
    $venueID = (int)$venueID;
    
    $sql = <<<SQL
SELECT * FROM bookings WHERE venue_id=$venueID
SQL;
    
    $result = $this->oConn->query($sql);
    if(FALSE === $result)
    {
      throw new RuntimeException($this->oConn->error);
    }
    
    $rows = Database::fetch_all($result);
    return $rows[0];
  }
  
  /**
   * Set the next contact date
   * @param int $venueID The venue id
   * @param date $nextContact The next contact date. Format: mm/dd/yyyy
   * @throws RuntimeException if SQL fails
   */
  public function setNextContact($venueID, $nextContact)
  {
    $venueID = (int)$venueID;
    
    $bookingInfo = $this->getBooking($venueID);
    
    $dt = new DateTime($nextContact);
    $next = $dt->format('Y-m-d');
    
    $sSQL = <<<SQL
UPDATE bookings
SET next_contact='$next', timestamp=NOW()
WHERE user_login='{$this->sUserLogin}' AND venue_id=$venueID
SQL;

    $result = $this->oConn->query($sSQL);
    if(FALSE === $result)
    {
      $message = $this->oConn->error;
      throw new RuntimeException($message);
    }
  }
  
  /**
   * Is the next contact date valid?
   * 
   * @param type $venueID
   * @param type $nextContact
   * @return type
   */
  public function isValidNextContact($venueID, $nextContact)
  {
    $bookingInfo = $this->getBooking((int)$venueID);
    $next = strtotime($nextContact);
    
    // Not contacted yet. Tomorrow or later is acceptable next contact
    if('0000-00-00' === $bookingInfo['last_contacted'])
    {
      $tomorrow = strtotime("tomorrow");
      return $tomorrow <= $next;
    }
    
    // Otherwise, needs to be at least one week from last contact
    $last = strtotime($bookingInfo['last_contacted']);
    $week = 7 * 24 * 60 * 60;
    
    return ($last + $week) <= $next;
  }
  
  /**
   * Get the next contact that is safe for the user to set
   * @param type $venueID
   * @return string yyyy-mm-dd
   * @throws InvalidArgumentException
   */
  public function getSafeNextContact($venueID)
  {
    $bookingInfo = $this->getBooking((int)$venueID);
    
    if('0000-00-00' === $bookingInfo['last_contacted'])
    {
      $safeNext = date('Y-m-d', strtotime("tomorrow"));
    }
    else
    {
      $frequency_num = (int)$bookingInfo['frequency_num'];
      $frequency_type = $bookingInfo['freq_type'];
      $last = new DateTime($bookingInfo['last_contacted']);
      
      switch($frequency_type)
      {
        case 'D':
          $interval = 'P'.$frequency_num.'D';
          break;
        case 'W':
          $interval = 'P'.$frequency_num.'W';
          break;
        case 'M':
          $interval = 'P'.$frequency_num.'M';
          break;
        default:
          throw new InvalidArgumentException("Invalid frequency type");
      }
      
      $next = $last->add(new DateInterval($interval));
      $safeNext = $next->format('Y-m-d');
    }
    
    return $safeNext;
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
  
  public function setFrequency($venueID, $frequencyNum, $frequencyType)
  {
    $venueID = (int)$venueID;
    $frequencyNum = (int)$frequencyNum;
    $frequencyType = strtoupper($frequencyType);
    
    if($frequencyType == 'D' && $frequencyNum < 7)
    {
      throw new InvalidArgumentException('Need at least 7 days between contacts');
    }
    elseif($frequencyNum <= 0)
    {
      throw new InvalidArgumentException('Must be at least 1');
    }
    elseif($frequencyNum > 365)
    {
      throw new InvalidArgumentException('365 is the max');
    }
    
    $sql = <<<SQL
UPDATE bookings
SET frequency_num=$frequencyNum,
    freq_type='$frequencyType'
WHERE user_login='{$this->sUserLogin}' AND
      venue_id=$venueID
SQL;
    
    $result = $this->oConn->query($sql);
    if($result === FALSE)
    {
      throw new RuntimeException($this->oConn->error);
    }
    
    $row = $this->getBooking($venueID);
    $nextContact = $this->calculateNextContactFromFrequency(
            $row['last_contacted'], $frequencyNum, $frequencyType);
    $this->setNextContact($venueID, $nextContact);
  }
  
  public function calculateNextContactFromFrequency($lastContact, $frequencyNum, $frequencyType)
  {
    $nextContact = '';
    $tomorrow = date('Y-m-d', strtotime("+1 day"));
    $lastContactTimeStamp = strtotime($lastContact);
    
    // if last contact never happened, set next contact to tomorrow
    if('0000-00-00' === $lastContact)
    {
      $nextContact = $tomorrow;
    }
    // if frequency number is invalid, set next contact to 2 weeks
    elseif( 0 >= (int)$frequencyNum)
    {
      $nextContact = date('Y-m-d', strtotime("+2 week", $lastContactTimeStamp));
    }
    // if frequency type is invalid, set next contact to  2 weeks
    elseif( 'D' != $frequencyType && 'W' != $frequencyType && 'M' != $frequencyType)
    {
      $nextContact = date('Y-m-d', strtotime("+2 week", $lastContactTimeStamp));
    }
    // if less than a week, set to one week
    elseif(7 > (int)$frequencyNum && 'D' === $frequencyType)
    {
      $nextContact = date('Y-m-d', strtotime("+1 week", $lastContactTimeStamp));
    }
    // otherwise, calculate next contact
    else
    {
      $interval = 'day';
      if('W' === $frequencyType)
      {
        $interval = 'week';
      }
      elseif('M' === $frequencyType)
      {
        $interval = 'month';
      }
      
      $nextContact = date('Y-m-d', 
              strtotime("+$frequencyNum $interval", $lastContactTimeStamp));
    }
    // return next contact
    return $nextContact;
  }
  
  public function setTemplate($venueID, $templateID)
  {
    $venueID = (int)$venueID;
    $templateID = (int)$templateID;
    
    // verify venue id belongs to user
    // verify template id belongs to user
    $sql = <<<SQL
UPDATE bookings
SET template_id=$templateID
WHERE user_login='{$this->sUserLogin}' AND venue_id=$venueID
SQL;

    $result = $this->oConn->query($sql);
    if( FALSE === $result)
    {
      throw new RuntimeException($oConn->error);
    }
  }
}

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

  public function getUpcomingBookings()
  {
    $sSQL = <<<SQL
SELECT DISTINCT user_login, next_contact
FROM bookings
WHERE pause=0
ORDER BY next_contact
SQL;
    
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new InvalidArgumentException("Could not get upcoming bookings from the database.");
      error_log($sSQL);
      error_log($this->oConn->error);
    }
    
    return Database::fetch_all($mResult);
  }
  
  public function getUserTodayEmailBookings($sUser)
  {
    if(empty($sUser))
    {
      throw new InvalidArgumentException('User name cannot be empty');
    }
    
    $sSQL = <<<SQL
SELECT
  my_venues.category,
  my_venues.email,
  my_venues.booker_fname,
  my_venues.name,
  my_venues.id,
  my_venues.country,
  my_venues.state,
  my_venues.city,
  booking_templates.title,
  booking_templates.template_id
FROM `bookings`
INNER JOIN my_venues
ON my_venues.id=bookings.venue_id
LEFT JOIN booking_templates
ON bookings.template_id=booking_templates.template_id
WHERE
  bookings.next_contact<=CURDATE() AND
  bookings.user_login='$sUser' AND
  bookings.pause=0 AND 
  my_venues.email IS NOT NULL AND
  my_venues.email<>''
ORDER BY booking_templates.title, my_venues.category, my_venues.country, 
  my_venues.state, my_venues.city, my_venues.name;
SQL;
    
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new InvalidArgumentException("Could not get bookings from the database: " . $this->oConn->error);
    }
    
    return Database::fetch_all($mResult);
  }
  
  public function getUserTodaySubFormBookings($sUser)
  {
    if(empty($sUser))
    {
      throw new InvalidArgumentException('User name cannot be empty');
    }
    
    $sSQL = <<<SQL
SELECT
  my_venues.category,
  my_venues.subform,
  my_venues.booker_fname,
  my_venues.name,
  my_venues.country,
  my_venues.state,
  my_venues.city
FROM `bookings`
INNER JOIN my_venues
ON my_venues.id=bookings.venue_id
WHERE
  bookings.next_contact<=CURDATE() AND
  bookings.user_login='$sUser' AND
  bookings.pause=0 AND 
  (my_venues.email IS NULL OR my_venues.email='')
ORDER BY my_venues.category, my_venues.country, my_venues.state, 
  my_venues.city, my_venues.name;
SQL;
    
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new InvalidArgumentException("Could not get bookings from the database.");
    }
    
    return Database::fetch_all($mResult);
  }
  
  /**
   * Update the last_contacted and next_contact dates
   * 
   * @param string $sUser
   * @return type
   * @throws InvalidArgumentException
   */
  public function updateBookings($sUser)
  {
    if(empty($sUser))
    {
      return;
    }
    
    // Select distinct frequency and frequency types for this user
    $sSQL = <<<SQL
SELECT DISTINCT frequency_num, freq_type
FROM bookings
WHERE user_login='$sUser'
SQL;
    
    $mResult = $this->oConn->query($sSQL);
    if(FALSE === $mResult)
    {
      throw new InvalidArgumentException("Could not update bookings from the database.");
    }
    
    $hFrequencyTypes['D'] = 'DAY';
    $hFrequencyTypes['W'] = 'WEEK';
    $hFrequencyTypes['M'] = 'MONTH';
    $hFrequencyTypes['Y'] = 'YEAR';
    
    while ($hRow = $mResult->fetch_assoc())
    {
      $num_interval = (int)trim($hRow['frequency_num']);
      $type_freq = trim($hRow['freq_type']);
      $type_interval = $hFrequencyTypes[$type_freq];
      $sSQL = <<<SQL
UPDATE bookings
SET last_contacted=CURDATE(), next_contact=DATE_ADD(CURDATE(),INTERVAL $num_interval $type_interval)
WHERE user_login='$sUser' and 
      pause=0 and 
      next_contact<=CURDATE() and 
      frequency_num=$num_interval AND freq_type='$type_freq';
SQL;
      
      $mUpdateResult = $this->oConn->query($sSQL);
    
      if(FALSE === $mUpdateResult)
      {
        throw new InvalidArgumentException("Could not update bookings from the database.");
      }
    }
  }
  
  public function getUserVenuesContacted($sUser)
  {
    if(empty($sUser))
    {
      return array();
    }
    
    $sSQL = <<<SQL
SELECT 
  my_venues.name AS Venue, 
  my_venues.city AS City, 
  my_venues.state AS State, 
  DATE_FORMAT( bookings.last_contacted, '%m/%d/%Y' ) AS Contacted, 
  DATE_FORMAT( bookings.next_contact, '%m/%d/%Y' ) AS 'Next Contact'
FROM `bookings`
INNER JOIN my_venues ON my_venues.id = bookings.venue_id
WHERE 
  bookings.pause=0 AND 
  bookings.user_login='$sUser' AND 
  last_contacted=CURDATE()
ORDER BY my_venues.country, my_venues.state, my_venues.city, my_venues.name;
SQL;
    
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new InvalidArgumentException("Could not get contacted venues from the database.");
    }
    
    return Database::fetch_all($mResult);
  }
}

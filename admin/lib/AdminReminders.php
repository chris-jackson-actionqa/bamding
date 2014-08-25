<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminReminders
 *
 * @author Seth
 */
class AdminReminders
{
  private $oConn;
  public function __construct()
  {
    $oDB = new Database();
    $this->oConn = $oDB->connect();
  }
  
  public function getTodaysReminders()
  {
    $oSQL = <<<SQL
SELECT DISTINCT user_login, next_contact
FROM bookings
WHERE next_contact<=CURDATE()+3 AND
      bookings.next_contact<>'0000-00-00' AND
      reminder_sent<>next_contact-3 AND
      pause=0
SQL;
    
    $mResult = $this->oConn->query($oSQL);
    
    if(FALSE === $mResult)
    {
      error_log($oSQL);
      error_log($this->oConn->error);
      throw new InvalidArgumentException('Problem with getting reminders from database.');
    }
    
    return Database::fetch_all($mResult);
  }
  
  public function getUserReminderVenues($sUser)
  {
    if(empty($sUser))
    {
      throw new InvalidArgumentException('Need to specify a user to get reminder info.');
    }
    
    $oSQL = <<<SQL
SELECT 
  my_venues.name AS Venue, 
  my_venues.city AS City, 
  my_venues.state AS State, 
  DATE_FORMAT( bookings.last_contacted, '%m/%d/%Y' ) AS Contacted, 
  DATE_FORMAT( bookings.next_contact, '%m/%d/%Y' ) AS 'Next Contact'
FROM `bookings`
INNER JOIN my_venues ON my_venues.id = bookings.venue_id
WHERE 
  bookings.pause =0 AND
  bookings.user_login = '$sUser' AND
  bookings.next_contact<>'0000-00-00' AND
  bookings.next_contact<=CURDATE()+3 AND
  bookings.reminder_sent<>bookings.next_contact-3
ORDER BY my_venues.state, my_venues.city;
SQL;
    
    $mResult = $this->oConn->query($oSQL);
    
    if(FALSE === $mResult)
    {
      error_log($oSQL);
      error_log($this->oConn->error);
      throw new InvalidArgumentException('Problem with getting reminders from database.');
    }
    
    return Database::fetch_all($mResult);
  }
}

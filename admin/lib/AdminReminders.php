<?php
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
      reminder_sent < next_contact-3 AND
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
  
  public function getUpcomingReminders()
  {
    $oSQL = <<<SQL
SELECT DISTINCT user_login, next_contact
FROM bookings
WHERE bookings.next_contact<>'0000-00-00' AND
      (DATEDIFF(next_contact,reminder_sent)>3 OR reminder_sent='0000-00-00') AND
      pause=0
ORDER BY bookings.next_contact
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
  
  public function getUserReminderVenues($sUser, $sNextContact = '')
  {
    // should never get here without user
    if(empty($sUser))
    {
      throw new InvalidArgumentException('Need to specify a user to get reminder info.');
    }
    
    // by default, use today's date + 3 for the next contact. 
    if(empty($sNextContact))
    {
      $sNextContact = date('Y-m-d', strtotime("+3 days"));
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
  bookings.next_contact<='$sNextContact' AND
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
  
  function updateReminders($sUser, $reminderSent, $nextContact)
  {
    if(empty($sUser))
    {
      throw new InvalidArgumentException('User is empty');
    }
    
    if(empty($reminderSent))
    {
      throw new InvalidArgumentException('Reminder is empty');
    }
    
    if(empty($nextContact))
    {
      throw new InvalidArgumentException('Next contact is empty');
    }
    
    // update reminder sent dates for given next contacts
    $sSQL = <<<SQL
UPDATE bookings
SET reminder_sent='$reminderSent'
WHERE 
  user_login='$sUser' AND
  next_contact='$nextContact' AND
  pause=0
SQL;
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      error_log($sSQL);
      error_log($this->oConn->error);
      throw new InvalidArgumentException('Could not update reminder dates.');
    }
  }
  
  public static function convertDateToSQL($sDatePicker)
  {
    $aDate = split('/', $sDatePicker);
    $sSQLDate = $aDate[2] . '-' . $aDate[0] . '-' . $aDate[1];
    return $sSQLDate;
  }
  
  public static function updateReminderData($hPost)
  {
    $sMessage = '';
    
    //update reminder sents
    if(array_key_exists('ACTION', $hPost))
    {
      if($hPost['ACTION'] == 'UPDATE_REMINDER')
      {
        $oAdminReminders = new AdminReminders();
        $sUser = $hPost['user_login'];
        $reminderSent = AdminReminders::convertDateToSQL($hPost['reminder_sent']);
        $nextContact = AdminReminders::convertDateToSQL($hPost['next_contact']);
        //echo $reminderSent . '   ' . $nextContact . '<br/>';
        $oAdminReminders->updateReminders($sUser, $reminderSent, $nextContact);
        $sMessage = "Updated reminder sent dates for $sUser. Reminder: $reminderSent, Next Contact: $nextContact";
      }
    }
    
    return $sMessage;
  }
}

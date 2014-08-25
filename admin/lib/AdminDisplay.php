<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminDisplay
 *
 * @author Seth
 */
class AdminDisplay 
{
  public static function getHeader()
  {
    $sHTML = <<<HTM
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="admin-style.css">
</head>
<body>
HTM;
    echo $sHTML;
  }
  
  public static function getFooter()
  {
    $sHTML = <<<HTM
</body>
</html>
HTM;
    echo $sHTML;
  }
  
  public static function getMenu()
  {
    $sHTML = <<<HTM
<div id="admin-menu">
  <a href="admin-bookings.php">Bookings</a>
</div>
HTM;
    echo $sHTML;
  }
  
  public static function clearBoth()
  {
    echo '<div id="clear"></div>';
  }
  
  public static function getReminders()
  {
    $oReminders = new AdminReminders();
    $hReminders = $oReminders->getTodaysReminders();
    echo '<div id="reminders">';
    echo '<h2>Reminders</h2>';
    foreach($hReminders as $hRow)
    {
      echo $hRow['user_login'] . 
           ', Next Contact: ' . 
           $hRow['next_contact'] . 
           '<a href="admin-user-reminder.php?user_login=' . 
           $hRow['user_login'] .
           '">Go!</a>' .
            '<br />';
    }
    echo '</div>';
  }
  
  public static function getTodaysBookings()
  {
    $oAdminBookings = new AdminBookings();
    $hTodayBookings = $oAdminBookings->getTodaysBookings();
    
    echo '<div id="today_bookings">';
    echo '<h2>Today Bookings</h2>';
    foreach($hTodayBookings as $hRow)
    {
      echo $hRow['user_login'] . ', Next Contact: ' . $hRow['next_contact'] . '<br />';
    }
    echo '</div>';
  }
  
  public static function bookingsForm($sAction, $sTable, $hPostData)
  {
    $aUsers = array();
    if($sTable = 'bookings')
    {
      $oAdminBookings = new AdminBookings();
      $aUsers = $oAdminBookings->getAllUsers();
    }
    array_push($aUsers, 'all');
    
    $sSelectedUser = NULL;
    if(array_key_exists('users', $hPostData))
    {
      $sSelectedUser = $hPostData['users'];
    }
    
    echo '<form action="' . $sAction . '" method="POST">';
    
    //---------------Choose User
    echo '<label>Users</label>';
    echo '<select name="users">';
    foreach($aUsers as $sUser)
    {
      echo '<option ';
      if( $sSelectedUser == $sUser )
      {
        echo 'selected';
      }
      echo ' value="' . $sUser . '">' . $sUser . '</option>';
    }
    
    echo '</select>';
    echo '<br />';
    
    //--------------Next Contact
    $sDateValue = "";
    if(array_key_exists('next_contact_min', $hPostData))
    {
      $sDateValue = 'value="' . $hPostData['next_contact_min'] . '"';
    }
    echo '<label>Next Contact Min: </label>';
    echo '<input type="text" name="next_contact_min"'. $sDateValue . '>';
    echo '<br />';
    echo '<input type="submit">';
    echo '</form>';
  }
  
  public static function displayBookingsTable($hPostData)
  {
    $hBookings = array();
    if(array_key_exists('users', $hPostData))
    {
      $oAdminBookings = new AdminBookings();
      $hBookings = $oAdminBookings->getBookings($hPostData['users']);
      //$hBookings = $oAdminBookings->filterBookings($hBookings, $hPostData);
    }
    
    
    
    echo '<table>';
    echo '<tr>';
    echo '<th>User</th>';
    echo '<th>Venue ID</th>';
    echo '<th>Venue</th>';
    echo '<th>City</th>';
    echo '<th>State</th>';
    echo '<th>Country</th>';
    echo '<th>Contacted</th>';
    echo '<th>Next Contact</th>';
    echo '<th>Paused</th>';
    echo '<th>Freq#</th>';
    echo '<th>Freq Type</th>';
    echo '<th>Updated</th>';
    echo '</tr>';
    
    foreach($hBookings as $hRow)
    {
      echo '<tr>';
      echo '<td>'. $hRow['user_login'] .'</td>';
      echo '<td>'. $hRow['venue_id'] .'</td>';
      echo '<td>'. $hRow['name'] .'</td>';
      echo '<td>'. $hRow['city'] .'</td>';
      echo '<td>'. $hRow['state'] .'</td>';
      echo '<td>'. $hRow['country'] .'</td>';
      echo '<td>'. $hRow['last_contacted'] .'</td>';
      echo '<td>'. $hRow['next_contact'] .'</td>';
      echo '<td>'. $hRow['pause'] .'</td>';
      echo '<td>'. $hRow['frequency_num'] .'</td>';
      echo '<td>'. $hRow['freq_type'] .'</td>';
      echo '<td>'. $hRow['timestamp'] .'</td>';
      echo '</tr>';
    }
    
    echo '</table>';
  }
  
  public static function getUserReminderData($hGet, $hPost)
  {
    $sUser = '';
    if(array_key_exists('user_login', $hGet))
    {
      $sUser = $hGet['user_login'];
    }
    
    if(empty($sUser))
    {
      throw new InvalidArgumentException('Could not get user info');
    }
    
    $oAdminReminders = new AdminReminders();
    $hReminderVenues = $oAdminReminders->getUserReminderVenues($sUser);
    $hTableHeaders = array_keys($hReminderVenues[0]);
    
    echo '<div id="user_reminder">';
    echo "<h2>$sUser's Reminder</h2>";
    
    echo '<h3>Subject:</h3>';
    echo "Reminder: Venues will be contacted on<br />";
    echo '<h3>Body:</h3>';
    echo 'The following venues will be contacted:</br>';
    echo '<table>';
    
    // table header row
    echo '<tr>';
    foreach($hTableHeaders as $sHeader)
    {
      echo "<th>$sHeader</th>";
    }
    echo '</tr>';
    
    // table rows
    foreach($hReminderVenues as $hRow)
    {
      echo '<tr>';
      foreach($hTableHeaders as $sKey)
      {
        echo '<td>'. $hRow[$sKey] . '</td>';
      }
      echo '</tr>';
    }
    echo '</table>';
    
    // dates and time frames
    echo '<br />';
    echo 'Dates/Time-Frames:<br />';
    echo '<br />';
    echo "If you'd like to submit more venues:<br/>";
    echo '<a href="http://BamDing.com/myvenues/">My Venues</a><br />';
    echo '<br />';
    echo "If you'd like to view, pause, or resume your bookings:<br />";
    echo '<a href="http://BamDing.com/bookings">Bookings</a><br />';
    echo '<br />';
    echo 'If you have any questions, feel free to reply here.<br />';
    echo '<br />';
    echo 'Thanks,</br />';

    echo '<div>';
    
  }
}

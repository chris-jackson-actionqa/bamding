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
  public static function getHeader($sTitle = '', $sOnLoadFunc = '')
  {
    $sHTML = <<<HTM
<!DOCTYPE html>
<html>
<head>
  <title>$sTitle</title>
  <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/smoothness/jquery-ui.css" />
  <link rel="stylesheet" type="text/css" href="admin-style.css">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
  <script src="js/admin.js"></script>
</head>
<body onload="$sOnLoadFunc">
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
  &nbsp; &nbsp;
  <a href="admin-user-reminder.php">Reminders</a>
  &nbsp; &nbsp;
  <a href="admin-dates.php">Dates</a>
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
    $hReminders = $oReminders->getUpcomingReminders();
    echo '<div id="reminders">';
    echo '<h2>Upcoming Reminders</h2>';
    foreach($hReminders as $hRow)
    {
      echo $hRow['user_login'] . 
           ', Next Contact: ' . 
           $hRow['next_contact'] . 
           '<a href="admin-user-reminder.php?' . 
              'user_login=' . $hRow['user_login'] .
              '&next_contact=' . $hRow['next_contact'] .
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
  
  public static function getUserReminderData($hGet)
  {
    $sUser = '';
    if(array_key_exists('user_login', $hGet))
    {
      $sUser = $hGet['user_login'];
    }
    
    if(empty($sUser))
    {
      return;
    }
    
    $sNextContact = '';
    if(array_key_exists('next_contact', $hGet))
    {
      $sNextContact = $hGet['next_contact'];
    }
    
    $oAdminReminders = new AdminReminders();
    $hReminderVenues = $oAdminReminders->getUserReminderVenues($sUser, $sNextContact);
    if(empty($hReminderVenues))
    {
      return;
    }
    
    $hTableHeaders = array_keys($hReminderVenues[0]);
    
    // Need inline styles for copy/paste to Gmail
    $sTable = '<table style="border: 1px solid black;border-collapse: collapse;">';
    $sTD = '<td style="border: 1px solid black;">';
    $sTH = '<th style="border: 1px solid black;">';
    
    echo '<div id="user_reminder">';
    echo "<h2>$sUser's Reminder</h2>";
    
    echo '<h3>Reminder email</h3>';
    echo '<h4>Subject:</h4>';
    echo "Reminder: Venues will be contacted on ". $hReminderVenues[0]['Next Contact']."<br />";
    echo '<h4>Body:</h4>';
    echo 'The following venues will be contacted:</br>';
    //echo '<style>table {border-collapse: collapse;}table, td, th {border: 1px solid black;}</style>';
    echo $sTable;
    
    // table header row
    echo '<tr>';
    foreach($hTableHeaders as $sHeader)
    {
      echo "$sTH$sHeader</th>";
    }
    echo '</tr>';
    
    // table rows
    foreach($hReminderVenues as $hRow)
    {
      echo '<tr>';
      foreach($hTableHeaders as $sKey)
      {
        echo $sTD . $hRow[$sKey] . '</td>';
      }
      echo '</tr>';
    }
    echo '</table>';
    
    // dates and time frames
    AdminDisplay::displayDatesTimeFrames($sUser);
    
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
  
  public static function displayDatesTimeFrames($sUser)
  {
    // dates and time frames
    $oAdminDates = new AdminDates();
    $hDates = $oAdminDates->getDatesTimeframes($sUser);
    $hTimeframeGroups = $oAdminDates->getTimeframeGroups($hDates);
    
    echo '<br />';
    echo 'Dates/Time-Frames:<br />';
    $hTimeFrames = array_keys($hTimeframeGroups);
    if( 1 == count($hTimeFrames))
    {
      $aDatesKeys = array_keys($hDates);
      echo $oAdminDates->dateToMonth($hDates[$aDatesKeys[0]]['month_from']);
      if( !empty($hDates[$aDatesKeys[0]]['month_to']))
      {
        echo ' to ' . $oAdminDates->dateToMonth($hDates[$aDatesKeys[0]]['month_to']) . '<br />';
      }
    }
    echo '<br />';
  }
  
  public static function getUpdateReminderSentForm($hGet)
  {
    $sUser = '';
    if(array_key_exists('user_login', $hGet))
    {
      $sUser = $hGet['user_login'];
    }
    
    if(empty($sUser))
    {
      return;
    }
    
    // update reminder
    echo '<h3>Update reminder sent</h3>';
    echo '<form method="post" action="admin-user-reminder.php">';
    echo '<input type="hidden" name="ACTION" value="UPDATE_REMINDER">';
    echo '<input type="hidden" name="user_login" value="' . $sUser . '">';
    echo '<label>Update Reminder Sent</label>';
    echo '<input type="text" name="reminder_sent" id="reminderSent" value="'. date('m/d/Y') .'">';
    echo '<br />';
    echo '<label>for Next Contact=</label>';
    echo '<input type="text" name="next_contact" id="nextContact" value="'. date('m/d/y', strtotime($hGet['next_contact'])) .'">';
    echo '<br />';
    echo '<input type="submit">';
    echo '</form>';
  }
  
  public static function showMessage($sMessage)
  {
    if(empty($sMessage))
    {
      return;
    }
    
    echo '<div id="message">';
    echo $sMessage;
    echo '</div>';
  }
  
  /**
   * Select user form. 
   * Creates a dropdown form with all users.
   * Customize the method and the action of the form.
   * Allows setting the selected user.
   * 
   * @param string $sMethod get or post
   * @param string $sAction the action/page to go to
   * @param string $sDefaultUser (optional) selected user
   */
  public static function selectUserForm($sMethod, $sAction, $sDefaultUser = '')
  {
    // select user
    echo '<form method="'.$sMethod.'" action="'.$sAction.'">';
    $aUsers = AdminUsers::getAllUsers();
    echo '<select name="user_login">';
    foreach($aUsers as $sUser)
    {
      $sSelected = '';
      if($sUser == $sDefaultUser)
      {
        $sSelected = 'selected';
      }
      echo '<option value="' . $sUser . '"' . $sSelected .'>' . $sUser . '</option>';
    }
    echo '</select>';
    echo '<input type="submit">';
    echo '</form>';
  }
  
  public static function datesVenueRangeSelect($sDefaultRange='')
  {
    echo '<label>Update dates/timeframes for </label>';
    echo '<select name="venue_range">';
    echo '<option value="ALL" checked>All</option>';
    echo '<option value="country">Country</option>';
    echo '<option value="state">State</option>';
    echo '<option value="city">City</option>';
    echo '<option value="venue">Venue</option>';
    echo '</select>';
  }
  
  public static function datesInputTimeFrame($sDefaultRange='', $sFrom = '', $sTo = '')
  {
    if('' == $sDefaultRange)
    {
      return;
    }
    
    echo '<br />';
    echo '<input type="radio" name="update_type" value="timeframe" checked>Time-Frame';
    echo '<br />';
    echo '<label>Month From: </label>';
    echo '<select name="month_from">';
    echo '<option value="January">January</option>';
    echo '<option value="February">February</option>';
    echo '<option value="March">March</option>';
    echo '<option value="April">April</option>';
    echo '<option value="May">May</option>';
    echo '<option value="June">June</option>';
    echo '<option value="July">July</option>';
    echo '<option value="August">August</option>';
    echo '<option value="September">September</option>';
    echo '<option value="October">October</option>';
    echo '<option value="November">November</option>';
    echo '<option value="December">December</option>';
    echo '</select>';
    
    echo '<label>Month To: </label>';
    echo '<select name="month_to">';
    echo '<option value="" checked></option>';
    echo '<option value="January">January</option>';
    echo '<option value="February">February</option>';
    echo '<option value="March">March</option>';
    echo '<option value="April">April</option>';
    echo '<option value="May">May</option>';
    echo '<option value="June">June</option>';
    echo '<option value="July">July</option>';
    echo '<option value="August">August</option>';
    echo '<option value="September">September</option>';
    echo '<option value="October">October</option>';
    echo '<option value="November">November</option>';
    echo '<option value="December">December</option>';
    echo '</select>';
    
  }
  
  public static function showDatesForm($hGet, $hPost)
  {
    $sUserLogin = '';
    if(key_exists('user_login', $hGet))
    {
      $sUserLogin = $hGet['user_login'];
      self::selectUserForm('get', 'admin-dates.php', $sUserLogin);
    }
    else
    {
      self::selectUserForm('get', 'admin-dates.php');
      return;
    }
    
    echo '<h2>' . $sUserLogin . '</h2>';
    
    // form for applying dates
    echo '<form method="post" action="admin-dates.php?user_login='. $hGet['user_login'] . '">';
    
    $sVenueRange = '';
    if(key_exists('venue_range', $hPost))
    {
      $sVenueRange = $hPost['venue_range'];
    }
    
    self::datesVenueRangeSelect($sVenueRange);
    self::datesInputTimeFrame($sVenueRange);
    
    
    /*
    echo '<br />---------   OR   ----------<br />';
    echo '<label>Date from: </label>';
    echo '<input type="text" name="date_from">';
    echo '<label>Date to: </label>';
    echo '<input type="text" name="date_to">';
    echo '<br />';
    
    echo '<input type="radio" name="update_type" value="dates">Dates';
    echo '<br />';
    echo '<label>Add date:</label>';
    echo '<input type="text">';
    echo '<button>Add date</button>';
    echo '<br />';
    echo '<div id="dates"></div>';
    echo '<br />';
    
    echo '<input type="radio" name="update_type" value="college">Quarter/Semester';
    echo '<br />';
    echo '<label>From: </label>';
    echo '<select name="quarter_from">';
    echo '<option value="fall">Fall</option>';
    echo '<option value="winter">Winter</option>';
    echo '<option value="spring">Spring</option>';
    echo '<option value="summer">Summer</option>';
    echo '</select>';
    echo '<label>To (Optional): </label>';
    echo '<select name="quarter_from">';
    echo '<option value=""></option>';
    echo '<option value="fall">Fall</option>';
    echo '<option value="winter">Winter</option>';
    echo '<option value="spring">Spring</option>';
    echo '<option value="summer">Summer</option>';
    echo '</select>';
     * 
     */
    echo '<br />';
    echo '<input type="submit" value="Update">';
    echo '</form>';
  }
  
  const ALL = 'all';
  
  /**
   * Process the request data sent from the admin-dates form
   * 
   * @param map $hGet $_GET
   * @param map $hPost $_POST
   * @return no type. just returns early if no data to process
   * @throws InvalidArgumentException
   */
  public static function processDatesForm($hGet, $hPost)
  {
    // user_login needs to exist
    if(!key_exists('user_login', $hGet))
    {
      return;
    }
    
    //$oAdminDates = new AdminDates();
    //$oAdminDates->updateDatesTimeFrames($sUser, $updateType, $updateChoice, $aDates);
  }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('adminlib.php');

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
  <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum=scale=1" />
  <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/smoothness/jquery-ui.css" />
  <link rel="stylesheet" type="text/css" href="admin-style.css">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
  <script src="js/admin.js"></script>
</head>
<body onload="$sOnLoadFunc;">
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
  <a href="index.php">Dashboard</a>
  &nbsp; &nbsp;
  <a href="admin-user-reminder.php">Reminders</a>
  &nbsp; &nbsp;
  <a href="admin-bookings.php">Bookings</a>
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
    
    $user_email = AdminUsers::getEmail($sUser);
    
    ?>
    <div id="user_reminder">
      <h2><?php echo $sUser;?>'s Reminder</h2>
        <h3>Reminder email</h3>
          <h4>To:</h4>
            <a id="reminder_email_user_email" 
               href="mailto:<?php echo $user_email; ?>">
               <?php echo $user_email; ?></a>
          <h4>Subject:</h4>
          <div contenteditable="true" id="reminder_email_subject">
            Reminder: Venues will be contacted on 
            <?php echo $hReminderVenues[0]['Next Contact']; ?>
          </div>
          <br />
          <h4>Body:</h4>
          <div id="reminder_email_body" contenteditable="true">
            The following venues will be contacted:</br>
    
    <?php
    // display venues, dates/timeframes, and email standard ending
    AdminDisplay::displayInlineTable($hReminderVenues);
    AdminDisplay::displayDatesTimeFrames($sUser);
    AdminDisplay::standardEmailEnding();
    ?>
      </div>
      <button id="reminder_send_mail_button">Send Email</button>
    <?php
  }
  
  public static function displayInlineTable($hAssocArray)
  {
    if(empty($hAssocArray) || 0 == count($hAssocArray))
    {
      return;
    }
    
    // Get the keys that will be used for the headers and accessing row data
    $hTableHeaders = array_keys($hAssocArray[0]);
    
    // Need inline styles for copy/paste to Gmail
    $sTable = '<table style="border: 1px solid black;border-collapse: collapse;">';
    $sTD = '<td style="border: 1px solid black;">';
    $sTH = '<th style="border: 1px solid black;">';
    
    echo $sTable;
    
    // table header row
    if(0 != count($hTableHeaders))
    {
      echo '<tr>';
      foreach($hTableHeaders as $sHeader)
      {
        echo "$sTH$sHeader</th>";
      }
      echo '</tr>';
    }
    
    // table rows
    foreach($hAssocArray as $hRow)
    {
      echo '<tr>';
      foreach($hTableHeaders as $sKey)
      {
        echo $sTD . $hRow[$sKey] . '</td>';
      }
      echo '</tr>';
    }
    echo '</table>';
    echo '<br />';
  }
  
  public static function standardEmailEnding()
  {
    echo "If you'd like to submit more venues:<br/>";
    echo '<a href="http://BamDing.com/myvenues/">My Venues</a><br />';
    echo '<br />';
    echo "If you'd like to view, pause, or resume your bookings:<br />";
    echo '<a href="http://BamDing.com/bookings">Bookings</a><br />';
    echo '<br />';
    echo 'If you have any questions, feel free to reply here.<br />';
    echo '<br />';
    echo 'Thanks,<br />';
    echo 'Seth Jackson (Founder of BamDing)<br />';
    echo 'http://BamDing.com<br />';
  }
  
  /**
   * Displays user friendly dates and timeframes
   * @param string $sUser The username for the venues
   */
  public static function displayDatesTimeFrames($sUser)
  {
    $sHTML = '';
    
    // dates and time frames
    $sHTML .= "<div id='datesAndTimes'>\n";
    $sHTML .= self::displayDatesTimeFramesInnerHTML($sUser);
    $sHTML .= "</div>\n";
    echo $sHTML;
  }
  
  /**
   * The inner html for user friendly dates and timeframes.
   * Intended to be used with an AJAX call.
   * @param string $sUser username's dates and timeframes to display
   * @return string the inner html. 
   */
  public static function displayDatesTimeFramesInnerHTML($sUser)
  {
    
    $sHTML = '';
    
    // dates and time frames
    $sHTML .= "\t<strong>Dates and TimeFrames:</strong>\n<br />\n";
    
    $oAdminDates = new AdminDates($sUser);
    $hDates = $oAdminDates->getDatesTimeframes($sUser);
    if(0 == count($hDates))
    {
      $sHTML .= "\t" . 'No dates or time frames.<br />' . "\n";
      return $sHTML;
    }
    $aKeys = array_keys($hDates[0]);
    
    // Display 'all' venues timeframe
    $nCount = count($hDates);
    $nAllVenueIndex = -1;
    $nALLVenues = $oAdminDates->getVenueRangeID(AdminDates::ALL);
    $nCountry = $oAdminDates->getVenueRangeID(AdminDates::COUNTRY);
    $nState = $oAdminDates->getVenueRangeID(AdminDates::STATE);
    $nCity = $oAdminDates->getVenueRangeID(AdminDates::CITY);
    $nVenue = $oAdminDates->getVenueRangeID(AdminDates::VENUE);
    for($nIndex = 0; $nIndex < $nCount; ++$nIndex)
    {
      if($hDates[$nIndex]['venue_range'] === $nALLVenues)
      {
        $sHTML .= "\tAll Venues:<br />\n";
      }
      else if($hDates[$nIndex]['venue_range'] === $nCountry)
      {
        $sHTML .= "\t" . $hDates[$nIndex]['country'] . ":<br />";
      }
      else if($hDates[$nIndex]['venue_range'] === $nState)
      {
        $sHTML .= "\t" . $hDates[$nIndex]['state'] . ", " . 
             $hDates[$nIndex]['country'] .
             ":<br />\n";
      }
      else if($hDates[$nIndex]['venue_range'] === $nCity)
      {
        $sHTML .= $hDates[$nIndex]['city'] . ", " . 
             $hDates[$nIndex]['state'] . ", " . 
             $hDates[$nIndex]['country'] .
             ":<br />\n";
      }
      else if($hDates[$nIndex]['venue_range'] === $nVenue)
      {
        $oVenues = new Venues('my_venues', $sUser);
        $oVenue = $oVenues->getVenue((int)$hDates[$nIndex]['venue_id']);
        $sHTML .= $oVenue->getName() . ", " . 
             $hDates[$nIndex]['city'] . ", " . 
             $hDates[$nIndex]['state'] . ", " . 
             $hDates[$nIndex]['country'] .
             ":<br />\n";
      }
      $sHTML .= self::datesDisplayDatesTimeFramesForVenueRange($sUser, $hDates[$nIndex], true);
    }
    $sHTML .= '<br />' . "\n";
    return $sHTML;
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
  
  public static function datesVenueRangeSelect($sVenueRange='')
  {
    // hash map of option's values and text
    $hOptions = array(
        'ALL'=>'All venues',
        'COUNTRY'=>'Country',
        'STATE'=>'State',
        'CITY'=>'City',
        'VENUE'=>'Venue'
    );
    
    // if POST contains venue_range, get the value to check against
    // in the for loop
    $sRangeSelected = (!empty($sVenueRange)) ? $sVenueRange : 'ALL';
    
    // Begin the venue range selection
    echo '<label>Update dates/timeframes for </label>';
    echo '<select id="selectVenueRange" name="venue_range">';
    
    // echo out the options
    // default checked the appropriate option
    foreach($hOptions as $sRange=>$sText)
    {
      $sSelected = '';
      if($sRange == $sRangeSelected)
      {
        $sSelected = 'selected';
      }
      
      echo "<option value='$sRange' $sSelected>$sText</option>";
    }
    
    // end the selection
    echo '</select>';
  }
  
  public static function datesInputTimeFrame($sDefaultRange='', $sFrom = '', $sTo = '')
  {
    if(empty($sDefaultRange))
    {
      return;
    }
    
    $aMonths = array(
        1=>'January', 2=>'February', 3=>'March', 4=>'April', 
        5=>'May', 6=>'June', 7=>'July', 8=>'August', 
        9=>'September', 10=>'October', 11=>'November', 12=>'December'
    );
    
    echo '<br />';
    echo '<input type="radio" name="date_type" value="TIMEFRAME" checked>Time-Frame';
    echo '<br />';
    echo '<label>Month From: </label>';
    echo '<select name="month_from">';
    $aKeys = array_keys($aMonths);
    foreach($aKeys as $sKey)
    {
      $sChecked = '';
      $sMonth = $aMonths[$sKey];
      if(!empty($sFrom) && $sMonth == $aMonths[$sFrom])
      {
        $sChecked = 'selected';
      }
      echo '<option value="'.$sKey.'" '.$sChecked.'>'.$sMonth.'</option>';
    }
    echo '</select>';
    
    echo '<br />';
    
    echo '<label>Month To: </label>';
    echo '<select name="month_to">';
    // insert the empty option to this array
    array_unshift($aKeys, 0);
    $aMonths[0] = '';
    foreach($aKeys as $sKey)
    {
      $sChecked = '';
      $sMonth = $aMonths[$sKey];
      if(!empty($sTo) && $sMonth == $aMonths[$sTo])
      {
        $sChecked = 'selected';
      }
      echo '<option value="'.$sKey.'" '.$sChecked.'>'.$sMonth.'</option>';
    }
    echo '</select>';
    echo '<br />';
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
    
    $sVenueRange = (key_exists('venue_range', $hPost))
      ? $hPost['venue_range'] : AdminDates::ALL;
    
    $aVenueRangeValues = array();
    if(key_exists('value_range_country', $hPost))
    {
      $aVenueRangeValues['country'] = $hPost['value_range_country'];
    }
    
    $sTimeFrameFrom = (key_exists('month_from', $hPost))
      ? $hPost['month_from'] : '';
    $sTimeFrameTo = (key_exists('month_to', $hPost))
      ? $hPost['month_to'] : '';
    $sCustomFrom = (key_exists('custom_from', $hPost))
      ? $hPost['custom_from'] : '';
    $sCustomTo = (key_exists('custom_to', $hPost))
      ? $hPost['custom_to'] : '';
    $sQuarterFrom = (key_exists('quarter_from', $hPost))
      ? $hPost['quarter_from'] : '';
    $sQuarterTo = (key_exists('quarter_to', $hPost))
      ? $hPost['quarter_to'] : '';
    
    echo '<h2>' . $sUserLogin . '</h2>';
    
    $sMessage = self::processDatesForm($hGet,$hPost);
    if(!empty($sMessage))
    {
      echo "<h3>Message:</h3>" . $sMessage . '<br /><br />';
    }
    
    // reset venue range to all on success
    if( $sMessage === 'Successfully updated.')
    {
      $sVenueRange = 'ALL';
    }
    
    // displays the dates and timeframes user already has set
    self::displayDatesTimeFrames($sUserLogin);
    AdminDisplayDates::showEditDatesTimeframes();
    
    // form for applying dates
    echo '<h3>Update:</h3>';
    echo '<form method="post" action="admin-dates.php?user_login='. $hGet['user_login'] . '">';
    
    self::datesVenueRangeSelect($sVenueRange);
    self::datesValueRangeValues($sVenueRange, $aVenueRangeValues);
    self::datesInputTimeFrame($sVenueRange);
    self::datesInputCustomRange($sVenueRange, $sCustomFrom, $sCustomTo);
    self::datesInputQuarterRange($sVenueRange, $sQuarterFrom, $sQuarterTo);
    self::datesInputDates($sVenueRange);
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
    if( !key_exists('user_login', $hGet) || 
        !key_exists('venue_range', $hPost) ||
        !key_exists('date_type', $hPost))
    {
      return '';
    }
    
    $sVenueRange = $hPost['venue_range'];
    
    // if venue range isn't all, but country doesn't exist, throw exception
    if(AdminDates::ALL != $sVenueRange && !key_exists('venue_range_country', $hPost))
    {
      return 'ERROR: Country not selected.';
    }
    
    // if venue range is state, state range needs to exist
    if((AdminDates::STATE === $sVenueRange || AdminDates::CITY === $sVenueRange || 
        AdminDates::VENUE === $sVenueRange) && 
        !key_exists('venue_range_state', $hPost))
    {
      return 'ERROR: State not selected';
    }
    
    // if venue range is city or venue, city range needs to exist
    if((AdminDates::CITY === $sVenueRange || AdminDates::VENUE === $sVenueRange) && 
        !key_exists('venue_range_city', $hPost))
    {
      return 'ERROR: City not selected';
    }
    
    // if venue range is venue, venue range needs to exist
    if(AdminDates::VENUE === $sVenueRange && !key_exists('venue_range_venue', $hPost))
    {
      return 'ERROR: Venue not selected';
    }
    
    // add range values to the array
    $aRangeValue = array();
    if(key_exists('venue_range_country', $hPost))
    {
      $aRangeValue['country'] = $hPost['venue_range_country'];
    }
    
    if(key_exists('venue_range_state', $hPost))
    {
      $aRangeValue['state'] = $hPost['venue_range_state'];
    }
    
    if(key_exists('venue_range_city', $hPost))
    {
      $aRangeValue['city'] = $hPost['venue_range_city'];
    }
    
    if(key_exists('venue_range_venue', $hPost))
    {
      $aRangeValue['venue'] = $hPost['venue_range_venue'];
    }
    
    // get date type
    $sDateType = key_exists('date_type',$hPost) ? $hPost['date_type'] : '';
    
    $aDates = array();
    
    switch($sDateType)
    {
      case AdminDates::TIMEFRAME:
        if(!key_exists('month_from', $hPost))
        {
          return 'ERROR: "Month From" is missing.';
        }
        
        // get month_from,
        $nMonthFrom = (int)$hPost['month_from'];
        
        // get today's month and year
        $aToday = date_parse_from_format('Y-m-d', date('Y-m-d'));
        $nYearFrom = $aToday['year'];
        // if less than this month, bump up to next year
        if($nMonthFrom < $aToday['month'])
        {
          $nYearFrom++;
        }
        $sMonth = ($nMonthFrom<10) ? "0$nMonthFrom" : "$nMonthFrom";
        array_push($aDates, "$nYearFrom-$sMonth-01");
        
        // check month to
        $nMonthTo = (key_exists('month_to', $hPost)) ? (int)$hPost['month_to'] : 0;
        $nYearTo = $nYearFrom;
        if(0 != $nMonthTo)
        {
          // month_to is set
          if($nMonthTo <= $nMonthFrom)
          {
            $nYearTo++;
          }
          
          $sMonth = ($nMonthTo<10) ? "0$nMonthTo" : "$nMonthTo";
          array_push($aDates, "$nYearTo-$sMonth-01");
        }
        break;
        
      case AdminDates::CUSTOMRANGE:
        if(!key_exists('custom_from', $hPost) || !key_exists('custom_to', $hPost))
        {
          return 'ERROR: Need both custom range dates.';
        }
        
        $sDateFrom = $hPost['custom_from'];
        $sDateTo = $hPost['custom_to'];
        if(empty($sDateFrom) || empty($sDateTo))
        {
          return 'ERROR: Need both custom range dates.';
        }
        
        // make sure the 'from' date is in the future
        $oDateToday = new DateTime();
        $oDateFrom = new DateTime($sDateFrom);
        if($oDateFrom <= $oDateToday)
        {
          return 'ERROR: Date "from" needs to be in the future.';
        }
        
        // make sure the to date comes after the from date
        $oDateTo = new DateTime($sDateTo);
        if($oDateFrom >= $oDateTo)
        {
          return 'ERROR: The "to" date needs to be later than the "from" date.';
        }
        
        // convert dates to mysql friendly format
        // add to dates array
        array_push($aDates, $oDateFrom->format('Y-m-d'));
        array_push($aDates, $oDateTo->format('Y-m-d'));
        break;
      case AdminDates::QUARTERRANGE:
        if(!key_exists('quarter_from', $hPost))
        {
          return 'ERROR: "Quarter From" is required.';
        }
        
        // "Quarter From" is required and can't be empty
        // if not empty, push on to dates array
        $sQuarterFrom = $hPost['quarter_from'];
        if(empty($sQuarterFrom))
        {
          return 'ERROR: "Quarter From" cannot be empty';
        }
        else
        {
          array_push($aDates, $sQuarterFrom);
        }
        
        // "Quarter To" is optional
        $sQuarterTo = (key_exists('quarter_to', $hPost))
                ?$hPost['quarter_to'] : "";
        if(!empty($sQuarterTo))
        {
          array_push($aDates, $sQuarterTo);
        }
        break;
        
      case AdminDates::DATES:
        if(!key_exists('dates_list', $hPost) || empty($hPost['dates_list']))
        {
          return "ERROR: The list of dates is missing or empty.";
        }
        
        // convert the string of dates into a dates array
        $aDates = split(",",$hPost['dates_list']);
        break;
      
      default:
        throw new InvalidArgumentException("Unrecognized venue range: " + sDateType);
        break;
    }
    
    try
    {
    $oAdminDates = new AdminDates($hGet['user_login']);
    $oAdminDates->updateDatesTimeFrames($hPost['venue_range'], $aRangeValue, $sDateType, $aDates);
    }
    catch(Exception $ex)
    {
      return $ex->getMessage();
    }
    
    return 'Successfully updated.';
  }
  
  public static function datesInputCustomRange($sDefaultRange, $sFrom, $sTo)
  {
    if(empty($sDefaultRange))
    {
      return;
    }
    
    echo '<br />';
    echo '<input type="radio" name="date_type" value="CUSTOMRANGE">Custom Range';
    echo '<br />';
    echo '<label>Date From:</label>';
    echo '<input type="text" name="custom_from" id="customFrom">';
    echo '<br />';
    echo '<label>Date To:</label>';
    echo '<input type="text" name="custom_to" id="customTo">';
    echo '<br />';
  }
  
  public static function datesInputQuarterRange($sVenueRange, $sFrom, $sTo)
  {
    if(empty($sVenueRange))
    {
      return;
    }
    
    $aQuarters = array(
        AdminDates::FALL=>'Fall',
        AdminDates::WINTER=>'Winter',
        AdminDates::SPRING=>'Spring',
        AdminDates::SUMMER=>'Summer'
    );
    
    echo '<br />';
    echo '<input type="radio" name="date_type" value="QUARTERRANGE">College Quarter/Semester Range';
    echo '<br />';
    echo '<label>Quarter From:</label>';
    echo '<select name="quarter_from">';
    $aKeys = array_keys($aQuarters);
    foreach($aKeys as $sKey)
    {
      echo "<option value='$sKey'>$aQuarters[$sKey]</option>";
    }
    echo '</select>';
    
    echo '<br />';
    
    echo '<label>Quarter To:</label>';
    echo '<select name="quarter_to">';
    echo '<option value="0000-00-00"></option>';
    foreach($aKeys as $sKey)
    {
      echo "<option value='$sKey'>$aQuarters[$sKey]</option>";
    }
    echo '</select>';
    echo '<br />';
  }
  
  public static function datesInputDates($sVenueRange)
  {
    if(empty($sVenueRange))
    {
      return;
    }
    
    echo '<br />';
    echo '<input type="radio" name="date_type" value="DATES">Date(s)';
    echo '<br />';
    echo '<label>Add Date</label><br />';
    echo '<input type="text" name="add_date" id="addDate">';
    echo '<button type="button" onclick="addDateToList();">Add Date</button>';
    echo '<br />';
    echo '<label>Dates</label>';
    echo '<input type="hidden" name="dates_list" id="hiddenDatesList">';
    echo '<div id="listOfDates"></div>';
  }
  
  public static function datesValueRangeValues($sVenueRange='', $aValues=array())
  {
    $sStyle = '';
    // if venue range is empty or 'ALL', return
    if(empty($sVenueRange) || AdminDates::ALL === $sVenueRange)
    {
      $sStyle = 'style="display: none;"';
    }
    
    // create dropdowns for country, state, city, and venues
    // hide the ones that aren't applicable
    // example, if 'country' is the range value, no need to show state, city, or
    // venues
    // if venues is selected, display all the dropboxes

    // if 'COUNTRY'
    // get all the venues' countries
    // display them in a drop-down box
    
    echo '<fieldset id="fieldsetChooseVenueRangeValues" '. $sStyle . '>';
    echo '<legend>Choose country, state, city, or venue.</legend>';
    
    // Country
    echo '<label id="labelCountry">Country</label>';
    echo '<select id="selectCountry" name="venue_range_country">';
    if(key_exists('country', $aValues))
    {
      echo '<option value="' . 
              $aValues['country'] . 
              '" selected>' . 
              $aValues['country'] . 
              "</option>";
    }
    echo '</select>';
    echo '<br />';
    
    // State
    echo '<label class="state">State</label>';
    echo '<select class="state" id="selectState" name="venue_range_state">';
    echo '</select>';
    echo '<br />';
    
    // City
    echo '<label class="city">City</label>';
    echo '<select class="city" id="selectCity" name="venue_range_city">';
    echo '</select>';
    echo '<br />';
    
    // Venue
    echo '<label class="venue">Venue</label>';
    echo '<select class="venue" id="selectVenue" name="venue_range_venue">';
    echo '</select>';
    echo '<br />';
    
    echo '</fieldset>';
  }
  
  const INDENT='&nbsp;&nbsp;&nbsp;&nbsp;';  
  
  /**
   * Display the dates/timeframes.
   * Displays timeframes and individual dates.
   * Doesn't display things like "All Venues", country, state, city, etc.
   * 
   * @param string $sUser username to init the AdminDates object. TODO: remove
   * @param hash $hDates map of dates/timeframes
   * @param boolean $bIndent indent the listings?
   * @throws InvalidArgumentException unknown date/timeframe type
   * @todo refactor the switch cases. Move to seperate functions
   */
  public static function 
    datesDisplayDatesTimeFramesForVenueRange(
            $sUser, $hDates, $bIndent = false, $sDatesNewLine = "<br />\n")
  {
    $sHTML = '';
    $sIndent = $bIndent ? self::INDENT : '';
    
    // get date type
    $oAdminDates = new AdminDates($sUser);
    $nTimeframeID = $oAdminDates->getDateTypeID(AdminDates::TIMEFRAME);
    $nCustomRangeID = $oAdminDates->getDateTypeID(AdminDates::CUSTOMRANGE);
    $nCollegeID = $oAdminDates->getDateTypeID(AdminDates::QUARTERRANGE);
    $nDatesID = $oAdminDates->getDateTypeID(AdminDates::DATES);

    // display timeframe
    switch ($hDates['date_type'])
    {
      case $nTimeframeID:
        // get from month
        $nTimestamp = strtotime($hDates['date_from']);
        $sMonth = date('F', $nTimestamp);
        $sHTML .= "\t" . $sIndent . $sMonth;

        // get to month
        if('0000-00-00' !== $hDates['date_to'])
        {
          $nTimestamp = strtotime($hDates['date_to']);
          $sMonth = date('F', $nTimestamp);
          $sHTML .= ' through ' . $sMonth;
        }
        $sHTML .= '<br />' . "\n";
        break;
      case $nCustomRangeID:
        // get from month
        $nTimestamp = strtotime($hDates['date_from']);
        $sMonth = date('F j, Y', $nTimestamp);
        $sHTML .= $sIndent . $sMonth;

        // get to month
        if('0000-00-00' !== $hDates['date_to'])
        {
          $nTimestamp = strtotime($hDates['date_to']);
          $sMonth = date('F j, Y', $nTimestamp);
          $sHTML .= ' through ' . $sMonth;
        }
        $sHTML .= '<br />' . "\n";
        break;
      case $nCollegeID:
        // get 'from' quarter
        $sQuarter = '';
        switch($hDates['date_from'])
        {
          case AdminDates::FALL:
            $sQuarter = 'Fall';
            break;
          case AdminDates::WINTER:
            $sQuarter = 'Winter';
            break;
          case AdminDates::SPRING:
            $sQuarter = 'Spring';
            break;
          case AdminDates::SUMMER:
            $sQuarter = 'Summer';
            break;
        }
        $sHTML .= "\t" . $sIndent . $sQuarter;

        // get to month
        if('0000-00-00' !== $hDates['date_to'])
        {
          $sQuarter = '';
          switch($hDates['date_to'])
          {
            case AdminDates::FALL:
              $sQuarter = 'Fall';
              break;
            case AdminDates::WINTER:
              $sQuarter = 'Winter';
              break;
            case AdminDates::SPRING:
              $sQuarter = 'Spring';
              break;
            case AdminDates::SUMMER:
              $sQuarter = 'Summer';
              break;
          }
          $sHTML .= ' through ' . $sQuarter;
        }
        $sHTML .= '<br />' . "\n";
        break;
      case $nDatesID:
        $sDates = $hDates['dates'];
        // split the string to an array of dates
        $aDates = split(',', $sDates);

        // find all the months to group by
        $aMonths = array();
        foreach($aDates as $sDate)
        {
          // get month of entry
          $sMonth = date('F', strtotime($sDate));
          array_push($aMonths, $sMonth);
        }
        // make sure no duplicate months
        $aMonths = array_unique($aMonths);

        // for each month, print the dates belonging to that month
        foreach($aMonths as $sMonth)
        {
          $sHTML .= "$sIndent$sMonth: ";
          $sDay = '';
          foreach($aDates as $sDate)
          {
            $sMonthOfDate = date('F', strtotime($sDate));
            if(strtoupper($sMonth) === strtoupper($sMonthOfDate))
            {
              $sDay .= date('jS', strtotime($sDate)) . ", ";
            }
          }
          $sDay = chop($sDay, ', ');
          $sHTML .= $sDay;
          $sHTML .= $sDatesNewLine;
        }
        break;
      default:
        throw new InvalidArgumentException("Unknown date type in dates");
        break;
    }
    
    return $sHTML;
  }
  
  public static function 
    datesDisplayOnlyTimeFrameFromVenueRange($sUser, $hDates, $bIndent = false)
  {
    $sHTML = '';
    $sIndent = $bIndent ? self::INDENT : '';
    
    // get date type
    $oAdminDates = new AdminDates($sUser);
    $nTimeframeID = $oAdminDates->getDateTypeID(AdminDates::TIMEFRAME);
    $nCustomRangeID = $oAdminDates->getDateTypeID(AdminDates::CUSTOMRANGE);
    $nCollegeID = $oAdminDates->getDateTypeID(AdminDates::QUARTERRANGE);
    $nDatesID = $oAdminDates->getDateTypeID(AdminDates::DATES);

    // display timeframe
    switch ($hDates['date_type'])
    {
      case $nTimeframeID:
        // get from month
        $nTimestamp = strtotime($hDates['date_from']);
        $sMonth = date('F', $nTimestamp);
        $sHTML .= $sIndent . $sMonth;

        // get to month
        if('0000-00-00' !== $hDates['date_to'])
        {
          $nTimestamp = strtotime($hDates['date_to']);
          $sMonth = date('F', $nTimestamp);
          $sHTML .= ' through ' . $sMonth;
        }
        break;
      case $nCustomRangeID:
        // get from month
        $nTimestamp = strtotime($hDates['date_from']);
        $sMonth = date('F j, Y', $nTimestamp);
        $sHTML .= $sIndent . $sMonth;

        // get to month
        if('0000-00-00' !== $hDates['date_to'])
        {
          $nTimestamp = strtotime($hDates['date_to']);
          $sMonth = date('F j, Y', $nTimestamp);
          $sHTML .= ' through ' . $sMonth;
        }
        break;
      case $nCollegeID:
        // get 'from' quarter
        $sQuarter = '';
        switch($hDates['date_from'])
        {
          case AdminDates::FALL:
            $sQuarter = 'Fall';
            break;
          case AdminDates::WINTER:
            $sQuarter = 'Winter';
            break;
          case AdminDates::SPRING:
            $sQuarter = 'Spring';
            break;
          case AdminDates::SUMMER:
            $sQuarter = 'Summer';
            break;
        }
        $sHTML .= $sIndent . $sQuarter;

        // get to month
        if('0000-00-00' !== $hDates['date_to'])
        {
          $sQuarter = '';
          switch($hDates['date_to'])
          {
            case AdminDates::FALL:
              $sQuarter = 'Fall';
              break;
            case AdminDates::WINTER:
              $sQuarter = 'Winter';
              break;
            case AdminDates::SPRING:
              $sQuarter = 'Spring';
              break;
            case AdminDates::SUMMER:
              $sQuarter = 'Summer';
              break;
          }
          $sHTML .= ' through ' . $sQuarter;
        }
        break;
      case $nDatesID:
        $sDates = $hDates['dates'];
        // split the string to an array of dates
        $aDates = split(',', $sDates);

        // find all the months to group by
        $aMonths = array();
        foreach($aDates as $sDate)
        {
          // get month of entry
          $sMonth = date('F', strtotime($sDate));
          array_push($aMonths, $sMonth);
        }
        // make sure no duplicate months
        $aMonths = array_unique($aMonths);

        // from the first month to the last month in the array
        $sHTML .= $aMonths[0];
        if( 1 != count($aMonths))
        {
          $sHTML .= ' through ' . $aMonths[count($aMonths)-1];
        }
        break;
      default:
        throw new InvalidArgumentException("Unknown date type in dates");
        break;
    }
    
    return $sHTML;
  }
  
  public static function showH1User()
  {
    $sUser = (key_exists('user_login', $_GET)) ? $_GET['user_login'] : '';
    if(empty($sUser))
    {
      return;
    }
    
    echo '<h1>'.$sUser.'</h1>';
  }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminDisplayBookings
 *
 * @author Seth
 */
class AdminDisplayBookings extends AdminDisplay
{
  public static function 
  upcomingBookings()
  {
    $oAdminBookings = new AdminBookings();
    $hTodayBookings = $oAdminBookings->getUpcomingBookings();
    
    echo '<div">';
    echo '<h2>Upcoming Bookings</h2>';
    foreach($hTodayBookings as $hRow)
    {
      echo $hRow['user_login'] . ', Next Contact: ' . $hRow['next_contact'] . 
           '<a href="admin-bookings.php?' . 
           'user_login=' . $hRow['user_login'] .
           '&next_contact=' . $hRow['next_contact'] .
           '">Go!</a>' .'<br />';
    }
    echo '</div>';
  }
  
  const DELIM = '>>';
  
  /**
   * Booking input script for TourBooking.php
   * @return nothing
   */
  public static function 
  showBookingsScript()
  {
    $sUser = '';
    if(key_exists('user_login', $_GET))
    {
      $sUser = $_GET['user_login'];
    }
    
    if(empty($sUser))
    {
      return;
    }
    
    echo '<h2>Bookings.txt</h2>';
    
    $oBookings = new AdminBookings();
    $hBookings = $oBookings->getUserTodayEmailBookings($sUser);
    $oAdminDates = new AdminDates($sUser);
    
    $category = 'NOT_SET_YET_BRO';
    $cur_template = 'NOT_SET_YET_BRO';
    
    foreach($hBookings as $hRow)
    {
      $row_template = $hRow['title'];
      if( $cur_template !== $row_template)
      {
        $cur_template = $row_template;
        echo "<h3>Template: $row_template</h3>";
      }
      
      $row_category = strtoupper(trim($hRow['category']));
      echo "# Category: $row_category<br />";
      $sEmail = $hRow['email'];
      $sBookerFName = (empty($hRow['booker_fname'])) ? '' : $hRow['booker_fname'];
      $sVenueName = $hRow['name'];
      
      $sDates = trim($oAdminDates->getDatesFromVenueID($hRow['id']));
      $sTimeFrame = trim($oAdminDates->getDatesFromVenueID($hRow['id'], AdminDates::TIMEFRAME_FORMAT));
      if('NO_DATES' !== $sDates)
      {
        // remove trailing new line characters
        $sDates = trim(str_replace("<br />", '', $sDates));
        $sDates = trim($sDates);
      }
      else
      {
          $sDates = $sTimeFrame = '';
      }
      
      $sTimeFrame = str_replace("&nbsp;", '', $sTimeFrame);
            
      echo '#' . $hRow['country'] . ', ' . $hRow['state'] . ', ' . $hRow['city'];
      echo '<br />';
      echo $sEmail . self::DELIM . 
           $sBookerFName . self::DELIM .
           $sVenueName . self::DELIM . 
           $sDates . self::DELIM .
           $sTimeFrame;
      echo '<br />';
    }
  }
  
  public static function 
  showSubForms()
  {
    $sUser = '';
    if(key_exists('user_login', $_GET))
    {
      $sUser = $_GET['user_login'];
    }
    
    if(empty($sUser))
    {
      return;
    }
    
    echo '<h2>Submission Forms</h2>';
    
    $oBookings = new AdminBookings();
    $hBookings = $oBookings->getUserTodaySubFormBookings($sUser);
    
    $category = 'NOT_SET_YET_BRO';
    
    foreach($hBookings as $hRow)
    {
      $row_category = strtoupper(trim($hRow['category']));
      if( $category !== $row_category)
      {
        $category = $row_category;
        echo "<h3>Category: $row_category</h3>";
      }
      $sWebsite = $hRow['subform'];
      $sBookerFName = (empty($hRow['booker_fname'])) ? '' : $hRow['booker_fname'];
      $sVenueName = $hRow['name'];
      
      echo '<a href="' . $sWebsite . '" target="_BLANK">' . $sVenueName . '</a>' .
           '&nbsp;&nbsp;Booker: ' . $sBookerFName;
      echo '<br />';
    }
  }
  
  public static function 
  showUpdateBookings()
  {
    $sUserLoginGET = '';
    if(key_exists('user_login', $_GET))
    {
      $sUserLoginGET = '?user_login=' . $_GET['user_login'];
    }
    
    if( empty($sUserLoginGET))
    {
      return;
    }
    
    echo '<h2>Update Bookings</h2>';
    echo '<form method="POST" action="admin-bookings.php' . $sUserLoginGET . '">';
    echo '<input type="hidden" name="update_bookings" value="update">';
    echo '<input type="submit" value="Update Bookings">';
    echo '</form>';
  }
  
  public static function
  processBookingUpdate()
  {
    if(!key_exists('user_login', $_GET))
    {
      return;
    }
    
    if(!key_exists('update_bookings', $_POST))
    {
      return;
    }
    
    $sUser = $_GET['user_login'];
    $oBookings = new AdminBookings();
    $oBookings->updateBookings($sUser);
  }
  
  public static function showBookedEmail()
  {
    $sUser = (key_exists('user_login', $_GET)) ? $_GET['user_login'] : '';
    if(empty($sUser))
    {
      return;
    }
    
    $user_email = AdminUsers::getEmail($sUser);
    $oBookings = new AdminBookings();
    $hVenuesContacted = $oBookings->getUserVenuesContacted($sUser);
    ?>
    <h2>Venues Booked Email</h2>
      <h3>To:</h3>
        <a id="email_address" href="mailto:<?php echo $user_email; ?>"><?php echo $user_email; ?></a>
      <h3>Subject</h3>
      <div id="email_subject">Venues contacted</div><br />
      <h3>Body</h3>
      <div id="email_body" contenteditable="true">
        The following venues were contacted:<br />
    
    <?php
    //list venues table
    AdminDisplayBookings::displayInlineTable($hVenuesContacted);
    // Dates and timeframes
    AdminDisplay::displayDatesTimeFrames($sUser);
    AdminDisplayBookings::standardEmailEnding();
    ?>
      </div>
      <button id="email_send" onclick="BAMDING.ADMIN.BOOKINGS.sendContactedEmail();">
        Send Email
      </button>
    <?php
  }
  
  /**
   * Display the template links
   */
  public static function displayTemplates()
  {
      $sUser = (key_exists('user_login', $_REQUEST)) ? $_REQUEST['user_login'] : '';
        if(empty($sUser))
        {
          return;
        }
      ?>
<h2>Templates</h2>
      <?php
    $bookingTemplates = new BookingTemplates($sUser);
    $templates = $bookingTemplates->getTemplates();
    
    foreach($templates as $template)
    {
      $user = urlencode(stripslashes($template['user_login']));
      $id = (int)$template['template_id'];
      $get = "user_login=$user&id=$id";
      $title = stripslashes($template['title']);
      ?>
<a href="admin-template.php?<?php echo $get; ?>" target="_blank">
  <?php echo $title; ?>
</a><br />
      <?php
    }
  }
}

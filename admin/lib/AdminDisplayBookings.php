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
    
    foreach($hBookings as $hRow)
    {
      $sEmail = $hRow['email'];
      $sBookerFName = (empty($hRow['booker_fname'])) ? '' : $hRow['booker_fname'];
      $sVenueName = $hRow['name'];
      
      echo $sEmail . self::DELIM . 
           $sBookerFName . self::DELIM .
           $sVenueName;
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
    
    foreach($hBookings as $hRow)
    {
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
  
  public static function
  showBookedEmail($sUser)
  {
    if(empty($sUser))
    {
      return;
    }
    
    echo '<h2>Venues Booked Email</h2>';
    echo '<h3>Subject</h3>';
    echo 'Venues contacted<br />';
    echo '<h3>Body</h3>';
    echo 'The following venues were contacted:<br />';
    //list venues table
    echo '<br />';
    // Dates and timeframes
    echo '<br />';
    // add more venues
    // view, pause, resume bookings
    
  }
}

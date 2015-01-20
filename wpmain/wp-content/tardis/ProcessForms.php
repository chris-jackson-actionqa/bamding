<?php
require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProcessForms
 *
 * @author Seth
 */
class ProcessForms
{
  /**
   * Processes the add new venue submission.
   * Displays errors in divs if couldn't add the venue.
   * Otherwise, it displays a success message and adds the venue to the
   * customer venue database.
   * 
   * @param type $hPostData $_POST variable from submission
   * @todo 
   */
  public static function doVenue($hPostData)
  {
    // No post data to process. No form submitted.
    if(empty($hPostData))
    {
      return;
    }
    
    $nBehavior = (int)$hPostData['bd_venue_method'];
    switch($nBehavior)
    {
      case DisplayForms::ADD_VENUE:
      case DisplayForms::EDIT_VENUE:
        break;
      default:
        return; //do nothing
    }
    
    $oVenue = new Venue();
    
    //User login
    $sUserLogin = "";
    if( array_key_exists('bd_user_login', $hPostData) 
            && !empty($hPostData['bd_user_login']) )
    {
      $sUserLogin = $hPostData['bd_user_login'];
    }

    // Venue name
    if( array_key_exists('bd_venue_name', $hPostData) 
            && !empty($hPostData['bd_venue_name']) )
    {
      $oVenue->setName($hPostData['bd_venue_name']);
    }
    else
    {
      // Display error and return
      echo '<div class="bdFormError">ERROR: Venue name is required.</div>';
      return;
    }
    
    // Email
    if( array_key_exists('bd_venue_email', $hPostData) 
            && !empty($hPostData['bd_venue_email']) )
    {
      $oVenue->setEmail($hPostData['bd_venue_email']);
    }
    
    // contact form url
    if( array_key_exists('bd_venue_contact_url', $hPostData) 
            && !empty($hPostData['bd_venue_contact_url']) )
    {
      $oVenue->setContactForm($hPostData['bd_venue_contact_url']);
    }
    
    // Booker's First Name
    if( array_key_exists('bd_venue_booker_fname', $hPostData) 
            && !empty($hPostData['bd_venue_booker_fname']) )
    {
      $oVenue->setBookerFirstName($hPostData['bd_venue_booker_fname']);
    }
    
    // Booker's Last Name
    if( array_key_exists('bd_venue_booker_lname', $hPostData) 
            && !empty($hPostData['bd_venue_booker_lname']) )
    {
      $oVenue->setBookerLastName($hPostData['bd_venue_booker_lname']);
    }
    
    // Street Address 1
    if( array_key_exists('bd_venue_address1', $hPostData) 
            && !empty($hPostData['bd_venue_address1']) )
    {
      $oVenue->setAddress1($hPostData['bd_venue_address1']);
    }
    
    // Street Address 1
    if( array_key_exists('bd_venue_address2', $hPostData) 
            && !empty($hPostData['bd_venue_address2']) )
    {
      $oVenue->setAddress2($hPostData['bd_venue_address2']);
    }
    
    // City
    if( array_key_exists('bd_venue_city', $hPostData) 
            && !empty($hPostData['bd_venue_city']) )
    {
      $oVenue->setCity($hPostData['bd_venue_city']);
    }
    else
    {
      // Display error and return
      echo '<div class="bdFormError">ERROR: The city is required.</div>';
      return;
    }
    
    // State
    if( array_key_exists('bd_venue_state', $hPostData) 
            && !empty($hPostData['bd_venue_state']) )
    {
      $oVenue->setState($hPostData['bd_venue_state']);
    }
    else
    {
      // Display error and return
      echo '<div class="bdFormError">ERROR: The state is required.</div>';
      return;
    }
    
    // Zip/Postal code
    if( array_key_exists('bd_venue_zip', $hPostData) 
            && !empty($hPostData['bd_venue_zip']) )
    {
      $oVenue->setZip($hPostData['bd_venue_zip']);
    }
    
    // Country
    if( array_key_exists('bd_venue_country', $hPostData) 
            && !empty($hPostData['bd_venue_country']) )
    {
      $oVenue->setCountry($hPostData['bd_venue_country']);
    }
    else
    {
      // Display error and return
      echo '<div class="bdFormError">ERROR: The country is required.</div>';
      return;
    }
    
    // Website
    if( array_key_exists('bd_venue_website', $hPostData) 
            && !empty($hPostData['bd_venue_website']) )
    {
      $oVenue->setWebsite($hPostData['bd_venue_website']);
    }
    
    if( array_key_exists('bd_venue_note', $hPostData))
    {
      $oVenue->setNote($hPostData['bd_venue_note']);
    }
    
    // Error if both email and submission form are empty.
    $sContactForm = $oVenue->getContactForm();
    $sEmail = $oVenue->getEmail();
    if(empty($sContactForm) && empty($sEmail))
    {
      // Display error and return
      echo '<div class="bdFormError">ERROR: Must provide either an '
      . 'email contact or submission form for the venue.</div>';
      return;
    }
    
    // TODO: verify email by regex
    // TODO: verify submission form by regex
    // TODO: test submission form link
    
    try
    {
      $oVenues = new Venues('my_venues', $sUserLogin);
      $sSuccessMessage = '';
      switch($nBehavior)
      {
        case DisplayForms::ADD_VENUE:
        {
          $oVenues->addVenue($oVenue);
          $nVenueID = $oVenues->getVenueID($oVenue);
          if(-1 == $nVenueID)
          {
            throw new Exception("Can't find the venue's id that was just added.");
          }
          $oBookings = new Bookings($sUserLogin );
          $oBookings->addNewBooking($nVenueID);
          $sSuccessMessage = "Venue " . $oVenue->getName() . " added.";
        }
        break;
        case DisplayForms::EDIT_VENUE:
          $nVenueID = (int)$hPostData['bd_venue_id'];
          $oVenues->updateVenue($oVenue, $nVenueID);
          $sSuccessMessage = 'Venue ' . $oVenue->getName() . ' updated.';
          break;
      }
      

      // Display success message
      echo '<div class="bdFormSuccess">' . $sSuccessMessage .'</div>';
      ProcessForms::mailOnVenue($sUserLogin, $oVenue->getName());
    }
    catch(Exception $oException)
    {
      echo '<div class="bdFormError">Error: Could not add/update venue.'
      . '<br />'
      . $oException->getMessage()
      . '<br /></div>';
      self::mailOnError($sUserLogin, "Problem adding/updating venue", $oException);
    }
  }
  
  public static function removeVenues($hPostData)
  {
    // No post data to process. No form submitted.
    if(empty($hPostData))
    {
      return;
    }
    
    if($hPostData['bd_venue_method'] != 'remove')
    {
      return;
    }
    
    // for each entry, delete the venue
    $sResultHTML = "";
    try 
    {  
      $oVenues = new Venues('my_venues', get_user_field('user_login'));
      $oBookings = new Bookings(get_user_field('user_login'));
      
      foreach($hPostData as $nVenueID=>$sVenue)
      {
        if('remove' == $nVenueID || !is_numeric($nVenueID))
        {
          continue;
        }
        
        $oVenues->removeVenue($nVenueID);
        $oBookings->removeBooking($nVenueID);
        ProcessForms::mailOnVenue(get_user_field('user_login'), $sVenue, 'removed');
      }
      $sResultHTML = '<div class="bdFormSuccess">Successfully removed venue(s).</div>';
      
    }
    catch(Exception $oEx)
    {
      $sResultHTML = '<div class="bdFormError">Error: ' . $oEx->getMessage() . '</div>';
      self::mailOnError(get_user_field('user_login'), 'Error removing venue:' . $nVenueID, $oEx);
    }
    
    echo $sResultHTML;
  }
  
  public static function mailOnVenue($sUserID, $sVenue, $sAction = 'added' )
  {
    $sTo = "seth@bamding.com";
    $sSubject = "Venue $sAction for $sUserID";
    $sMessage = "User: $sUserID, Venue: $sVenue";
    mail($sTo, $sSubject, $sMessage);
  }
  
  /**
   * Mail on bulk bookings
   * @param type $action
   * @param type $message
   */
  public static function mailOnBulk($action, $message)
  {
    $user = get_user_field('user_login');
    $sTo = "seth@bamding.com";
    $sSubject = "Venue(s) $action for $user";
    mail($sTo, $sSubject, $message);
  }
  
  public static function mailOnError($sUserID, $sDescription, $oException)
  {
    $sTo = "seth@bamding.com";
    $sSubject = "Error processing form for $sUserID";
    $sMessage = "User: $sUserID\r\n" .
            "Description: $sDescription\r\n" .
            "Exception:\r\n" . $oException;
    mail($sTo, $sSubject, $sMessage);
  }
  
  public static function processBookings($hPostData)
  {
    if(!key_exists('user_login', $hPostData))
    {
      throw new Exception('Cannot process bookings because user is not defined.');
    }
    
    $sUserLogin = $hPostData['user_login'];
    $oBookings = new Bookings($sUserLogin);
    
    switch($hPostData['action'])
    {
      case 'startBooking':
        $oBookings->setPause((int)$hPostData['venue_id'], FALSE);
        break;
      case 'setPause':
        $oBookings->setPause($hPostData['venue_id'], $hPostData['pause']);
        break;
    }
    $oVenues = new Venues('my_venues', $sUserLogin);
    $oVenue = $oVenues->getVenue($hPostData['venue_id']);
    $sVenueInfo = $oVenue->getName() . ", " . 
            $oVenue->getCity() . ", " .
            $oVenue->getState() . ", " .
            $oVenue->getCountry();
    if(array_key_exists('pause', $hPostData))
    {
      $sVenueInfo .= ", PAUSE='" . $hPostData['pause'] . "' ";
    }
    self::mailOnVenue($sUserLogin, $sVenueInfo , $hPostData['action']);
  }
  
  /**
   * Process bulk action from Bookings page
   * @throws RuntimeException
   */
  public static function processMultipleBookings()
  {
    $bookings = new Bookings(get_user_field('user_login'));
   
    $action = $_REQUEST['bd_bookings_bulk_action_top'];
    foreach($_REQUEST as $key => $value)
    {
      if(strpos($key, "venue_") === FALSE)
      {
        continue;
      }
      
      switch($action)
      {
        case 'start':
          $bookings->setPause((int)$value, FALSE);
          break;
        case 'pause':
          $bookings->setPause((int)$value, TRUE);
          break;
        default:
          throw new RuntimeException("Unrecognized action: $action");
      }
    }
    
    self::mailOnBulk($action, "");
  }
  
  public static function setVenueCategory()
  {
    // return if venue method isn't to set the category
    if( empty($_REQUEST['bd_venue_method']) ||
        'set_category' !== $_REQUEST['bd_venue_method'])
    {
      return;
    }
    
    // Get the category
    if( empty($_REQUEST['category']))
    {
      return;
    }
    
    $category = $_REQUEST['category'];
    $category = trim($category);
    
    // get the venue ids
    $venue_ids = array();
    foreach($_REQUEST as $key => $entry)
    {
      if( 1 === preg_match('/venue_(\d+)/', $key))
      {
        array_push($venue_ids, intval($entry));
      }
    }
    
    // add the category to the venues
    foreach($venue_ids as $venue_id)
    {
      $venues = new Venues('my_venues', get_user_field('user_login'));
      $venues->setCategoryForVenue($venue_id, $category);
    }
  }
}

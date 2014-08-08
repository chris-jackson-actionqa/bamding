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
        throw new InvalidArgumentException('Not a valid behavior to perform on a venue.');
    };
    
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
          $oVenues->addVenue($oVenue);
          $sSuccessMessage = "Venue " . $oVenue->getName() . " added.";
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
      foreach($hPostData as $nVenueID=>$sVenue)
      {
        if('remove' == $nVenueID || !is_numeric($nVenueID))
        {
          continue;
        }
        
        $oVenues->removeVenue($nVenueID);
        ProcessForms::mailOnVenue(get_user_field('user_login'), $sVenue, 'removed');
      }
      $sResultHTML = '<div class="bdFormSuccess">Successfully removed venue(s).</div>';
      
    }
    catch(InvalidArgumentException $oEx)
    {
      $sResultHTML = '<div class="bdFormError">Error: ' . $oEx->getMessage() . '</div>';
    }
    catch(RuntimeException $oEx)
    {
      $sResultHTML = '<div class="bdFormError">Error: ' . $oEx->getMessage() . '</div>';
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
}

<?php
require_once(ABSPATH. '/wp-content/tardis/Venues.php');
require_once(ABSPATH. '/wp-content/tardis/Venue.php');

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
  public static function AddNewVenue($hPostData)
  {
    // Venue name
    $sVenueName = "";
    if( array_key_exists('bd_venue_name', $hPostData) 
            && !empty($hPostData['bd_venue_name']) )
    {
      $sVenueName = $hPostData['bd_venue_name'];
    }
    else
    {
      // Display error and return
      echo '<div class="bdFormError">ERROR: Venue name is required.</div>';
      return;
    }
    
    // Email
    $sEmail = "";
    if( array_key_exists('bd_venue_email', $hPostData) 
            && !empty($hPostData['bd_venue_email']) )
    {
      $sVenueName = $hPostData['bd_venue_email'];
    }
    
    // contact form url
    $sContactURL = "";
    if( array_key_exists('bd_venue_contact_url', $hPostData) 
            && !empty($hPostData['bd_venue_contact_url']) )
    {
      $sVenueName = $hPostData['bd_venue_contact_url'];
    }
    
    // Booker's First Name
    $sBookerFName = "";
    if( array_key_exists('bd_venue_booker_fname', $hPostData) 
            && !empty($hPostData['bd_venue_booker_fname']) )
    {
      $sVenueName = $hPostData['bd_venue_booker_fname'];
    }
    
    // Booker's Last Name
    $sBookerLName = "";
    if( array_key_exists('bd_venue_booker_lname', $hPostData) 
            && !empty($hPostData['bd_venue_booker_lname']) )
    {
      $sVenueName = $hPostData['bd_venue_booker_lname'];
    }
    
    // Street Address 1
    $sAddress1 = "";
    if( array_key_exists('bd_venue_address1', $hPostData) 
            && !empty($hPostData['bd_venue_address1']) )
    {
      $sVenueName = $hPostData['bd_venue_address1'];
    }
    
    // Street Address 1
    $sAddress2 = "";
    if( array_key_exists('bd_venue_address2', $hPostData) 
            && !empty($hPostData['bd_venue_address2']) )
    {
      $sVenueName = $hPostData['bd_venue_address2'];
    }
    
    // City
    $sCity = "";
    if( array_key_exists('bd_venue_city', $hPostData) 
            && !empty($hPostData['bd_venue_city']) )
    {
      $sVenueName = $hPostData['bd_venue_city'];
    }
    else
    {
      // Display error and return
      echo '<div class="bdFormError">ERROR: The city is required.</div>';
      return;
    }
    
    // State
    $sState = "";
    if( array_key_exists('bd_venue_state', $hPostData) 
            && !empty($hPostData['bd_venue_state']) )
    {
      $sVenueName = $hPostData['bd_venue_state'];
    }
    else
    {
      // Display error and return
      echo '<div class="bdFormError">ERROR: The state is required.</div>';
      return;
    }
    
    // Zip/Postal code
    $sZip = "";
    if( array_key_exists('bd_venue_zip', $hPostData) 
            && !empty($hPostData['bd_venue_zip']) )
    {
      $sVenueName = $hPostData['bd_venue_zip'];
    }
    
    // Country
    $sCountry = "";
    if( array_key_exists('bd_venue_country', $hPostData) 
            && !empty($hPostData['bd_venue_country']) )
    {
      $sVenueName = $hPostData['bd_venue_country'];
    }
    else
    {
      // Display error and return
      echo '<div class="bdFormError">ERROR: The country is required.</div>';
      return;
    }
    
    // Website
    $sWebsite = "";
    if( array_key_exists('bd_venue_website', $hPostData) 
            && !empty($hPostData['bd_venue_website']) )
    {
      $sVenueName = $hPostData['bd_venue_website'];
    }
    
    // Additional Info
    $sInfo = "";
    if( array_key_exists('bd_venue_info', $hPostData) 
            && !empty($hPostData['bd_venue_info']) )
    {
      $sVenueName = $hPostData['bd_venue_info'];
    }
    
    $oVenue = new Venue();
    $oVenue->setName($sVenueName);

    /*
    $oVenues = new Venues();
    $oVenues->addVenue($oVenue);
    */
    
    // Display success message
    echo '<div class="bdFormSuccess">Venue ' . $oVenue->getName() . ' added.</div>';
  }
}

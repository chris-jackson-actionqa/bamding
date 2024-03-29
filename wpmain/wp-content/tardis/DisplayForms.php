<?php
require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');

class DisplayForms
{
  const ADD_VENUE = 1;
  const VIEW_VENUE = 2;
  const EDIT_VENUE = 3;
  
  public static function displayInput($sLabel, $sType, $sName, $sValue = '', $sAttributes = '')
  {
    if( $sType != 'hidden')
    {
      echo "<label>$sLabel</label>";
      echo '<br />';
    }
    echo '<input type="' . $sType . 
            '" name="' . $sName . 
            '" value="' . $sValue . 
            '" ' . $sAttributes . '>';
    
    if( $sType != 'hidden')
    {
      echo '<br />';
    }
  }
  
  /**
   * Displays a <textarea>
   * @param string $sLabel for the <label>
   * @param string $sName name attribute for <textarea>
   * @param string $sValue content for <textarea>
   * @param string $sAttributes optional attributes
   */
  public static function displayTextArea($sLabel, $sName, $sValue = '', $sAttributes = '')
  {
    ?>
<label><?php echo $sLabel; ?></label>
<br />
<textarea name="<?php echo $sName; ?>" <?php echo $sAttributes; ?> >
<?php echo $sValue; ?>
</textarea>
<br />
    <?php 
  }
  
  public static function displayVenueForm($sAction, $nBehavior, $nVenueID = -1)
  {
    // Can't have an empty action url for the form
    if(empty($sAction))
    {
      throw new InvalidArgumentException('Form requires an action url.');
    }
    
    // The behavior needs to be a known one
    if(DisplayForms::ADD_VENUE != $nBehavior && 
      DisplayForms::VIEW_VENUE != $nBehavior &&
      DisplayForms::EDIT_VENUE != $nBehavior)
    {
      throw new InvalidArgumentException('Not a recognized behavior for venues');
    }
    
    // Get user
    $sUserLogin = get_user_field('user_login');
    
    // Init venue object. 
    $oVenues = new Venues('my_venues', $sUserLogin);
    $oVenue = $oVenues->getVenue($nVenueID);
    
    // Display the form
    echo '<b>* indicates a required field</b>';
    echo '<form id="bdAddNewVenueForm" name="addNewVenue" action="' . $sAction . '" method="post">';
    DisplayForms::displayInput
            ('', 'hidden', 'bd_user_login', $sUserLogin, 'required');
    DisplayForms::displayInput
            ('', 'hidden', 'bd_venue_method', $nBehavior);
    
    // venue id for editing
    if( DisplayForms::EDIT_VENUE == $nBehavior)
    {
      DisplayForms::displayInput('', 'hidden', 'bd_venue_id', $nVenueID);
    }
    
    DisplayForms::displayInput
            ("Venue's Name:*", 'text', 'bd_venue_name', $oVenue->getName(), 'required');
    DisplayForms::displayInput
            ("Venue's Booking Email:", 'email', 'bd_venue_email', $oVenue->getEmail());
    DisplayForms::displayInput
            ('Contact Form (Requires online submission form plan):', 'url', 'bd_venue_contact_url', $oVenue->getContactForm());
    DisplayForms::displayInput
            ("Booker's First Name:", 'text', 'bd_venue_booker_fname', $oVenue->getBookerFirstName());
    DisplayForms::displayInput
            ("Booker's Last Name:", 'text', 'bd_venue_booker_lname', $oVenue->getBookerLastName());
    DisplayForms::displayInput
            ('Address:', 'text', 'bd_venue_address1', $oVenue->getAddress1());
    DisplayForms::displayInput
            ('Address 2:', 'text', 'bd_venue_address2', $oVenue->getAddress2());
    DisplayForms::displayInput
            ('City:*', 'text', 'bd_venue_city', $oVenue->getCity(), 'required');
    DisplayForms::displayInput
            ('State:*', 'text', 'bd_venue_state', $oVenue->getState(), 'maxlength="2" required');
    DisplayForms::displayInput
            ('Zip/Postal Code:', 'text', 'bd_venue_zip', $oVenue->getZip());
    
    // Default to United States
    $sCountry = $oVenue->getCountry();
    $sCountry = empty($sCountry) ? 'United States' : $sCountry;
    DisplayForms::displayInput
            ('Country:*', 'text', 'bd_venue_country', $sCountry, 'required');
    
    DisplayForms::displayInput
            ('Website:', 'url', 'bd_venue_website', $oVenue->getWebsite());
    
    DisplayForms::displayTextArea
            ('Note:', 'bd_venue_note', $oVenue->getNote(),
            'cols="50" maxlength="65535"'
            );
    
    // Submit button
    $sSubmitText = (DisplayForms::EDIT_VENUE == $nBehavior) ? 'Update Venue' : 'Add Venue';
    
    echo '<br />';
    echo '<input type="submit" value="' . $sSubmitText . '" '
            . 'id="bd_venue_add_button">';
    echo '</form>';
    
  }
  
  /**
   * addNewVenue displays a add new venue form to the user
   * 
   * @param string $sAction The submit action url for this form
   */
  public static function addNewVenue($sAction)
  {
    DisplayForms::displayVenueForm($sAction, DisplayForms::ADD_VENUE);
  }
  
  public static function editVenue($sAction, $nVenueID)
  {
    DisplayForms::displayVenueForm($sAction, DisplayForms::EDIT_VENUE, $nVenueID);
  }
  
  public static function confirmRemoveVenues()
  {
    $sMyVenuesURI = Site::getBaseURL() . '/myvenues/';

    // If no venues selected
    if(0 == count($_POST))
    {
      echo 'No venues were selected to remove.';
      echo '<br />';
      echo '<a href="'. $sMyVenuesURI . '">Back to my venues.</a>';
      return;
    }
    
    // list the venues with a Yes or No confirm button to remove them
    echo '<b>Remove the following venue(s)?</b><br />';
    echo '<form name="bdRemoveMyVenues" action="' . $sMyVenuesURI . '" method="post">';
    
    echo '<input type="hidden" name="bd_venue_method" value="remove">';
    
    // Display the venues to be removed
    foreach($_POST as $sVenue=>$nID)
    {
      // skip the bulk action selection and main checkbox
      if(strtolower(trim('bd_venues_bulk_action_top')) === $sVenue ||
         strtolower(trim('bd_select_all_venues')) === $sVenue ||
         strtolower(trim('bd_venues_bulk_action_bottom')) === $sVenue)
      {
        continue;
      }
      echo '<input type="text" name="' . (int)$nID . '" value="' . $sVenue . '" readonly>';
      echo '<br />';
    }
    
    // No
    echo '<a href="' . $sMyVenuesURI . '"><b>No! Take me back to my venues!</b></a>';
    
    // Yes
    echo '<input type="submit" value="Yes, remove them." id="bd_btn_remove">';
    
    echo '</form>';
    
  }
}

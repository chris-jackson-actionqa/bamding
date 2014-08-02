<?php


class DisplayForms
{
  const ADD_VENUE = 1;
  const VIEW_VENUE = 2;
  const EDIT_VENUE = 3;
  
  public static function displayVenueForm($sAction, $nBehavior, $nVenueID)
  {
    
    $sHTML = <<<HTM
    <b>* indicates a required field</b>
    <form id="bdAddNewVenueForm" name='addNewVenue' action="<?php echo $sAction; ?>" method="post">
      <input type="hidden" name="bd_user_email" value="<?php echo get_user_field('user_email'); ?>" required>
      <input type="hidden" name="bd_user_login" value="<?php echo get_user_field('user_login'); ?>" required>
      <input type="hidden" name="bd_venue_method" value="add">
      <label>Venue's Name:*</label>
      <br />
      <input type="text" name="bd_venue_name" required>
      <br />
      
      <label>Venue's Booking Email:</label>
      <br />
      <input type="email" name="bd_venue_email">
      <br />
      
      <label>Contact Form (Requires online submission form plan.):</label>
      <br />
      <input type="url" name="bd_venue_contact_url">
      <br />
      
      <label>Booker's First Name:</label>
      <br />
      <input type="text" name="bd_venue_booker_fname">
      <br />
      
      <label>Booker's Last Name:</label>
      <br />
      <input type="text" name="bd_venue_booker_lname">
      <br />
      
      <label>Address:</label>
      <br />
      <input type="text" name="bd_venue_address1">
      <br />
      <label>Address2:</label>
      <br />
      <input type="text" name="bd_venue_address2">
      <br />
      <label>City:*</label>
      <br />
      <input type="text" name="bd_venue_city" required>
      <br />
      <label>State:*</label>
      <br />
      <input type="text" name="bd_venue_state" maxlength="2" required>
      <br />
      <label>Zip/Postal Code:</label>
      <br />
      <input type="text" name="bd_venue_zip">
      <br />
      <label>Country:*</label>
      <br />
      <input type="text" name="bd_venue_country" value="United States" required>
      <br />
      <label>Website:</label>
      <br />
      <input type="url" name="bd_venue_website">
      <br />
      <br />
      <input type="submit" value="Add Venue">
    </form>
HTM;
    
    echo $sHTML;
  }
  
  /**
   * addNewVenue displays a add new venue form to the user
   * 
   * @param string $sAction The submit action url for this form
   */
  public static function addNewVenue($sAction)
  {
    ?>
    <b>* indicates a required field</b>
    <form id="bdAddNewVenueForm" name='addNewVenue' action="<?php echo $sAction; ?>" method="post">
      <input type="hidden" name="bd_user_email" value="<?php echo get_user_field('user_email'); ?>" required>
      <input type="hidden" name="bd_user_login" value="<?php echo get_user_field('user_login'); ?>" required>
      <input type="hidden" name="bd_venue_method" value="add">
      <label>Venue's Name:*</label>
      <br />
      <input type="text" name="bd_venue_name" required>
      <br />
      
      <label>Venue's Booking Email:</label>
      <br />
      <input type="email" name="bd_venue_email">
      <br />
      
      <label>Contact Form (Requires online submission form plan.):</label>
      <br />
      <input type="url" name="bd_venue_contact_url">
      <br />
      
      <label>Booker's First Name:</label>
      <br />
      <input type="text" name="bd_venue_booker_fname">
      <br />
      
      <label>Booker's Last Name:</label>
      <br />
      <input type="text" name="bd_venue_booker_lname">
      <br />
      
      <label>Address:</label>
      <br />
      <input type="text" name="bd_venue_address1">
      <br />
      <label>Address2:</label>
      <br />
      <input type="text" name="bd_venue_address2">
      <br />
      <label>City:*</label>
      <br />
      <input type="text" name="bd_venue_city" required>
      <br />
      <label>State:*</label>
      <br />
      <input type="text" name="bd_venue_state" maxlength="2" required>
      <br />
      <label>Zip/Postal Code:</label>
      <br />
      <input type="text" name="bd_venue_zip">
      <br />
      <label>Country:*</label>
      <br />
      <input type="text" name="bd_venue_country" value="United States" required>
      <br />
      <label>Website:</label>
      <br />
      <input type="url" name="bd_venue_website">
      <br />
      <br />
      <input type="submit" value="Add Venue">
    </form>

    <?php
  }
  
  public static function confirmRemoveVenues()
  {
    // If no venues selected
    if(0 == count($_POST))
    {
      echo 'No venues were selected to remove.';
      echo '<br />';
      echo '<a href="http://bamding.com/myvenues/">Back to my venues.</a>';
      return;
    }
    
    // list the venues with a Yes or No confirm button to remove them
    echo '<b>Remove the following venue(s)?</b><br />';
    echo '<form name="bdRemoveMyVenues" action="http://bamding.com/myvenues/" method="post">';
    
    echo '<input type="hidden" name="bd_venue_method" value="remove">';
    
    // Display the venues to be removed
    foreach($_POST as $sVenue=>$nID)
    {
      echo '<input type="text" name="' . (int)$nID . '" value="' . $sVenue . '" readonly>';
      echo '<br />';
    }
    
    // No
    echo '<a href="http://bamding.com/myvenues/"><b>No! Take me back to my venues!</b></a>';
    
    // Yes
    echo '<input type="submit" value="Yes, remove them.">';
    
    echo '</form>';
    
  }
}

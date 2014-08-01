<?php


class DisplayForms
{
  /**
   * addNewVenue displays a add new venue form to the user
   * 
   * @param string $sAction The submit action url for this form
   */
  public static function addNewVenue($sAction)
  {
    ?>
    <form id="bdAddNewVenueForm" name='addNewVenue' action="<?php echo $sAction; ?>" method="post">
      <input type="hidden" name="bd_user_email" value="<?php echo get_user_field('user_email'); ?>" required>
      <input type="hidden" name="bd_user_login" value="<?php echo get_user_field('user_login'); ?>" required>
      <label>Venue's Name:</label>
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
      <label>City:</label>
      <br />
      <input type="text" name="bd_venue_city" required>
      <br />
      <label>State:</label>
      <br />
      <input type="text" name="bd_venue_state" required>
      <br />
      <label>Zip/Postal Code:</label>
      <br />
      <input type="text" name="bd_venue_zip">
      <br />
      <label>Country:</label>
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
}

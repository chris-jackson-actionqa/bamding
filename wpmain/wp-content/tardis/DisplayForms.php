<?php


class DisplayForms
{
  public static function addNewVenue($sAction)
  {
    ?>
    <form id="bdAddNewVenueForm" name='addNewVenue' action="<?php echo $sAction; ?>" method="post">
      <label>Venue's Name:</label>
      <br />
      <input type="text" name="bd_venue_name" required>
      <br />
      
      <label>Email:</label>
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
      <label>Additional Info:</label>
      <br />
      <textarea name="bd_venue_info"></textarea>
      <br />
      <br />
      <input type="submit" value="Add Venue">
    </form>

    <?php
  }
}
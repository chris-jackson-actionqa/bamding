<?


class DisplayForms
{
  static public addNewVenue($sAction)
  {
    ?>
    <form name='addNewVenue' action="<?php echo $sAction; ?>">
      <label>Venue's Name:</label>
      Email:
      Contact Form:
      Booker's First Name:
      Booker's Last Name:
      City:
      State:
      Country:
      Website:
      Additional Info:
    </form>

    <?php
  }
};

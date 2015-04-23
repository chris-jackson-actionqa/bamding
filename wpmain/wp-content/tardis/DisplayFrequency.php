<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DisplayFrequency
 *
 * @author Seth
 */
class DisplayFrequency extends Display {

  private $oBookings = null;
  private $sUserLogin = "";

  /**
   * Constructor. Initialize the bookings object
   * 
   * @param string $sUserLogin user name
   * @throws InvalidArgumentException
   */
  public function __construct($sUserLogin) {
    $this->sUserLogin = trim($sUserLogin);
    if (empty($this->sUserLogin)) {
      $message = "User name can't be empty.";
      throw new InvalidArgumentException($message);
    }

    $this->oBookings = new Bookings($this->sUserLogin);
  }

  public function doPage() {
    $bookingsURL = Site::getBaseURL() . '/bookings/';
    $this->insertBookingsScript();
    $this->beginForm('edit_frequency_form', 'post', $bookingsURL);
    $this->beginDiv('edit_frequency_div', 'center_div');
    $this->frequency();
    $this->beginDiv('frequency_spacer', 'frequency_spacer');
    $this->endDiv();
    $this->submit();
    $this->cancel();
    $this->endDiv();
    $this->insertHiddenVenueInputs();
    $this->endForm();
  }

  public function frequency() {
    ?>
    <input type="number" 
           id="frequency_number"
           name="frequency_number" 
           value="2" 
           style="width: 70px;"
           max="365"
           min="1"
           onchange="BAMDING.EDIT_FREQUENCY.makeFrequencyNumberValid();">
    <select name="frequency_type" id="frequency_type" class="freq_input_height"
            onchange="BAMDING.EDIT_FREQUENCY.makeFrequencyNumberValid();">
      <option value="D">Days</option>
      <option value="W" selected>Weeks</option>
      <option value="M">Months</option>
    </select>
    <br />
    <?php

  }

  public function submit() {
    ?>
    <input type="submit" value="Submit" class="bd_float_right">
    <?php

  }

  public function cancel() {
    ?>
    <input type="submit" name="cancel" value="Cancel" class="bd_float_left">
    <?php

  }

  public function insertHiddenVenueInputs() {
    // echo out frequency type
    $format = '<input type="hidden" name="%s" value="%s">';
    echo sprintf($format, 'bd_bookings_bulk_action_top', 'frequency' );
    
    // echo out venues to be updated
    foreach ($_REQUEST as $key => $value) {
      if (strpos($key, "venue_") === FALSE) {
        continue;
      }

      echo sprintf($format, $key, $value);
    }
  }

}

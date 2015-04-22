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
class DisplayFrequency extends Display
{
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
  
  public function doPage()
  {
    $bookingsURL = Site::getBaseURL() . '/bookings/';
    $this->beginForm('edit_frequency_form', 'post', $bookingsURL);
    $this->beginDiv('edit_frequency_div', 'center_div');
    $this->frequency();
    $this->submit();
    $this->cancel();
    $this->endDiv();
    $this->endForm();
  }
  
  public function frequency()
  {
    ?>
<input type="number" name="frequency_number" value="2" style="width: 70px;">
<select name="frequency_type">
  <option value="D">Days</option>
  <option value="W" selected>Weeks</option>
  <option value="M">Months</option>
</select>
<br />
<?php
  }
  
  public function submit()
  {
    ?>
<input type="submit" value="Submit">
<?php
  }
  
  public function cancel()
  {
    ?>
<input type="submit" name="cancel" value="Cancel">
<?php
  }
}

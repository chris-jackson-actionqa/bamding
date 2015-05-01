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
class DisplaySetTemplate extends Display {

  private $oTemplates = null;
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

    $this->oTemplates = new BookingTemplates($this->sUserLogin);
  }

  public function doPage() {
    $bookingsURL = Site::getBaseURL() . '/bookings/';
    $this->insertBookingsScript();
    $this->beginForm('set_template_form', 'post', $bookingsURL);
    $this->beginDiv('set_template_div', 'center_div');
    $this->templates();
    $this->beginDiv('frequency_spacer', 'frequency_spacer');
    $this->endDiv();
    $this->submit();
    $this->cancel();
    $this->endDiv();
    $this->insertHiddenVenueInputs();
    $this->endForm();
  }

  public function templates()
  {
    $templates = $this->oTemplates->getTemplates();
    ?>
<select name="template_id">
  <?php
  foreach($templates as $template)
  {
    echo '<option value="'.$template['template_id'].'">'.$template['title'].'</option>'."\r\n";
  }
  ?>
</select>
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
    echo sprintf($format, 'bd_bookings_bulk_action_top', 'template' );
    
    // echo out venues to be updated
    foreach ($_REQUEST as $key => $value) {
      if (strpos($key, "venue_") === FALSE) {
        continue;
      }

      echo sprintf($format, $key, $value);
    }
  }

}

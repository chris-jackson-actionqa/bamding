<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DisplayBandDetails
 *
 * @author Seth
 */
class DisplayBandDetails {
    
    /**
     * Display the page
     */
    public static function doPage()
    {
        self::startForm();
        
        self::beginDiv("required_info", "bd_float_left");
        self::requiredFields();
        self::endDiv();
        
        self::beginDiv("optional_info", "bd_float_left");
        self::optionalFields();
        self::endDiv();
        
        self::beginDiv("", "bd_float_clear");
        self::endDiv();
        
        self::submit();
        
        self::endForm();
    }
    
    /**
     * Start the form
     */
    public static function startForm()
    {
        ?>
<form method="post" action="">
        <?php 
    }
    
    /**
     * End the form
     */
    public static function endForm()
    {
        ?>
</form>
        <?php
    }
    
    /**
     * Band details submit button
     */
    public static function submit()
    {
        ?>
  <input type="submit" id="band_details_submit">
        <?php
    }
    
    /**
     * Begin the div tag
     * @param string $id id for the div
     * @param string $class class for the div
     */
    public static function beginDiv($id = "", $class = "")
    {
        ?>
  <div id="<?php echo $id;?>" class="<?php echo $class;?>">
        <?php
    }
    
    /**
     * End the div tag
     */
    public static function endDiv()
    {
        ?>
  </div>
        <?php
    }
    
    /**
     * Display the required input fields for the band details form
     */
    public static function requiredFields()
    {
        ?>
  <fieldset>
      <legend>Required Info</legend>
      <label>Solo Project? Check here:</label>
      <input type="checkbox" name="band_details_solo">
      <br />
      <label>Band's Name:</label>
      <br />
      <input type="text" name="band_details_name">
      <br />
      <label>Main Genre of Music:</label>
      <br />
      <input type="text" name="band_details_genre">
      <br />
      <label>What popular bands do you sound like?</label>
      <br />
      <input type='text' name="band_details_sounds_like">
      <br />
      <label>Email used for booking:</label>
      <br />
      <input type="text" name="band_details_email">
      <br />
      <label>Main Website:</label>
      <br />
      <input type="text" name="band_details_website">
      <br />
      <label>Where To Hear Your Music?</label>
      <br />
      <input type="text" name="band_details_music">
      <br />
  </fieldset>
        <?php
    }
    
    /**
     * Optional fields for band details
     */
    public static function optionalFields()
    {
        ?>
  <fieldset>
      <legend>Optional Info</legend>
      <label>Band's Booking Phone Number:</label>
      <br />
      <input type="text" name="band_details_phone">
      <br />
      <label>What's your local draw?</label>
      <br />
      <input type="text" name="band_details_draw">
      <br />
      <label>Where are your live videos? (Optional, but highly recommended.)</label>
      <br />
      <input type="text" name="band_details_video">
      <br />
      <label>Booking calendar or show list</label>
      <br />
      <input type="text" name="band_details_calendar">
      <br />
      <label>Additional social media or relevant sites for your band.</label>
      <br />
      <textarea name="band_details_sites"></textarea>
      <br />
  </fieldset>
        <?php
    }
}

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
    public static function doPage($status)
    {
        self::insertScript();
        
        self::statusMessage($status);
        
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
<form method="post" action="" id="band_details_form">
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
<input type="submit" id="band_details_submit" class="btn_disabled" disabled 
       value="Submit">
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
        $bandDetails = new BandDetails(get_user_field('user_login'));
        ?>
  <fieldset>
      <legend>Required Info</legend>
      <label>Solo Project? Check here:</label>
      <input type="checkbox" name="band_details_solo"
             <?php if($bandDetails->getSolo()){ echo "checked"; } ?>>
      <br />
      <label>Band's Name:</label>
      <input type="text" name="band_details_name" maxlength="255" 
             onkeyup="BAMDING.BANDDETAILS.toggleSubmit();"
             value="<?php echo $bandDetails->getBandName();?>">
      <label>Main Genre of Music:</label>
      <input type="text" name="band_details_genre" maxlength="255"
             onkeyup="BAMDING.BANDDETAILS.toggleSubmit();"
             value="<?php echo $bandDetails->getGenre();?>">
      <label>What popular bands do you sound like?</label>
      <input type='text' name="band_details_sounds_like" maxlength="255"
             onkeyup="BAMDING.BANDDETAILS.toggleSubmit();"
             value="<?php echo $bandDetails->getSoundsLike();?>">
      <label>Email used for booking:</label>
      <input type="text" name="band_details_email" maxlength="255"
             onkeyup="BAMDING.BANDDETAILS.toggleSubmit();"
             value="<?php echo $bandDetails->getEmail();?>">
      <label>Main Website:</label>
      <input type="text" name="band_details_website" maxlength="255"
             onkeyup="BAMDING.BANDDETAILS.toggleSubmit();"
             value="<?php echo $bandDetails->getWebsite();?>">
      <label>Where To Hear Your Music?</label>
      <input type="text" name="band_details_music" maxlength="255"
             onkeyup="BAMDING.BANDDETAILS.toggleSubmit();"
             value="<?php echo $bandDetails->getMusic();?>">
  </fieldset>
        <?php
    }
    
    /**
     * Optional fields for band details
     */
    public static function optionalFields()
    {
        $bandDetails = new BandDetails(get_user_field('user_login'));
        ?>
  <fieldset>
      <legend>Optional Info</legend>
      <label>Band's Booking Phone Number:</label>
      <input type="text" name="band_details_phone" maxlength="255"
             onkeyup="BAMDING.BANDDETAILS.toggleSubmit();"
             value="<?php echo $bandDetails->getPhone(); ?>">
      <label>What's your local draw?</label>
      <input type="text" name="band_details_draw" maxlength="255"
             onkeyup="BAMDING.BANDDETAILS.toggleSubmit();"
             value="<?php echo $bandDetails->getDraw();?>">
      <label>Where are your live videos? (Optional, but highly recommended.)</label>
      <input type="text" name="band_details_video" maxlength="255"
             onkeyup="BAMDING.BANDDETAILS.toggleSubmit();"
             value="<?php echo $bandDetails->getVideo();?>">
      <label>Booking calendar or show list</label>
      <input type="text" name="band_details_calendar" maxlength="255"
             onkeyup="BAMDING.BANDDETAILS.toggleSubmit();"
             value="<?php echo $bandDetails->getCalendar();?>">
      <label>Additional social media or relevant sites for your band.</label>
      <textarea name="band_details_sites"
                onkeyup="BAMDING.BANDDETAILS.toggleSubmit();"><?php echo $bandDetails->getSites();?></textarea>
  </fieldset>
        <?php
    }
    
    public static function insertScript()
    {
        ?>
<script src="<?php echo Site::getBaseURL(); ?>/wp-content/js/bookings.js"></script>
        <?php
    }
    
    /**
     * Display a status message if the submit form worked or not.
     * Display nothing if there was no submit.
     * @param string $status "success", "error", or empty. Empty doesn't display
     * the div.
     * @return type
     */
    public static function statusMessage($status)
    {
        $status = strtolower(trim($status));
        if($status === "")
        {
            return;
        }
        
        $class = "band_details_success";
        $message = "Successfully updated your band details. <br />You rock!!!";
        
        if("success" !== $status)
        {
            $class = "band_details_error";
            $message = "Error updating your details. Try again or email " . 
                    '<a href="mailto:seth@bamding.com">seth@bamding.com</a>';
        }
        
        ?>
<div class="<?php echo $class;?>" id="band_details_status">
<?php echo $message; ?>
</div>
        <?php
    }
}

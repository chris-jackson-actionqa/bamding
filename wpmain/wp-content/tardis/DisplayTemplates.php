<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DisplayTemplates
 *
 * @author Seth
 */
class DisplayTemplates 
{
    /**
     * Display the UI of the page
     */
    public static function doPage()
    {
        if(self::areBandDetailsEntered())
        {
          self::doForm();
        }
        else
        {
          self::doNoBandDetails();
        }
    }
    
    /**
     * Display the templates list form
     */
    public static function doForm()
    {
        self::startForm();
        self::addNewTemplate();
        self::submit();
        self::endForm();
    }
    
    public static function doNoBandDetails()
    {
      ?>
<div id="templates_error" class="error_message">
  Your band details have not been entered yet.<br />
  Please go to the following page to enter those details first:<br/>
  <a href="./band-details/">Band Details</a><br/>
  <br />
  If you have any questions, feel free to contact 
  <a href="mailto:seth@bamding.com">seth@bamding.com</a>.
</div>
      <?php
    }
    
    /**
     * Start the form
     */
    public static function startForm()
    {
        ?>
<form method="post" action="" id="booking_templates_form">
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
<input type="submit" id="booking_templates_submit" class="btn_disabled" disabled 
       value="Submit">
        <?php
    }
    
    public static function addNewTemplate()
    {
        ?>
<a href="<?php echo Site::getBaseURL(); ?>/edit-template/" 
   id="booking_template_add_button">
   Add New Template
</a>
<br />
        <?php
    }
    
    /**
     * Are the band details entered for the user?
     * @return boolean true if entered, false otherwise
     */
    public static function areBandDetailsEntered()
    {
      $bandDetails = new BandDetails(get_user_field('user_login'));
      $detailsEntered = true;
      if(empty($bandDetails->getBandName()))
      {
        $detailsEntered = false;
      }
      
      return $detailsEntered;
    }
}

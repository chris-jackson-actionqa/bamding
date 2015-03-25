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
    public static function doPage($result)
    {
        if(self::areBandDetailsEntered())
        {
            self::doMessage($result);
          self::doForm();
        }
        else
        {
          self::doNoBandDetails();
        }
    }
    
    public static function doMessage($result)
    {
        $message = "Success!<br />\r\n".
                "Your template has been saved.";
        $class = "band_details_success";
        if(empty($result))
        {
            return;
        }
        elseif('error' === $result)
        {
            $message = "Error!<br />\r\n".
                    "An error occurred with your template.<br />\r\n".
                    "Please, try again.<br />\r\n".
                    "If you keep having problems, contact ".
                    '<a href="mailto:seth@bamding.com">seth@bamding.com</a>.';
            $class = "band_details_error";
        }
        
        ?>
<div id="template_message" class="status_message <?php echo $class; ?>">
    <?php echo $message; ?>
</div>
        <?php
    }
    
    /**
     * Display the templates list form
     */
    public static function doForm()
    {
        self::startForm();
        self::addNewTemplate();
        self::listTemplates();
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
<a href="<?php echo Site::getBaseURL(); ?>/edit-template/?taction=add_new" 
   id="booking_template_add_button"
   class='add_new'>
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
    
    /**
     * list the templates table
     */
    public static function listTemplates()
    {
        $bookingTemplates = new BookingTemplates(get_user_field('user_login'));
        $templates = $bookingTemplates->getTemplates();
        if(empty($templates))
        {
            echo "<br />No templates.<br />";
            return;
        }
        ?>
<table>
    <tr>
        <th>Title</th>
    </tr>
    <?php
    foreach($templates as $row)
    {
        echo '<tr><td><a href="'.
                Site::getBaseURL().
                '/edit-template/?taction=edit&id='.
                $row['template_id']
                .'">'.
                $row['title']
                .'</a></td></tr>'.
                "\r\n";
    }
    ?>
</table>
        <?php
    }
}

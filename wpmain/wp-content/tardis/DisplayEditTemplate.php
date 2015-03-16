<?php

class DisplayEditTemplate 
{
    /**
     * Display the edit template page
     */
    public static function doPage()
    {
        $bandDetails = new BandDetails(get_user_field('user_login'));
        
        self::startForm();
        self::templateName();
        self::fromEmail($bandDetails);
        self::fromName($bandDetails);
        self::subject($bandDetails);
        self::message($bandDetails);
        self::spacer();
        self::save();
        self::cancel();
        self::endForm();
    }
    
    /**
     * Begin the form
     */
    public static function startForm()
    {
        $action = Site::getBaseURL() . '/booking-templates/';
        ?>
<form method="post" action="<?php echo $action; ?>" id="edit_template_form">
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
     * Save the form
     */
    public static function save()
    {
       ?>
<input type="submit" name="template_save" value="Save">
       <?php
    }
    
    public static function cancel()
    {
        ?>
<input type="submit" name="template_cancel" value="Cancel">
        <?php
    }
    
    /**
     * Display the From email for bookings
     * Not editable
     * Email pulled from band details.
     */
    public static function fromEmail(BandDetails $bandDetails)
    {
        $email = $bandDetails->getEmail();
        ?>
<label>Booking Email:</label>
<br />
<input type="email" name="booking_template_email"  disabled
       class="input_max_width" value="<?php echo $email; ?>">
<br />
        <?php
    }
    
    /**
     * The friendly name bookers will see when the email is sent
     * 
     */
    public static function fromName(BandDetails $bandDetails)
    {
        $name = $bandDetails->getBandName();
        ?>
<label>From Name:</label>
<br />
<input type="text" maxlength=255 name="booking_template_from_name"
       class="input_max_width" value="<?php echo $name; ?>">
<br />
        <?php
    }
    
    /**
     * Subject line
     */
    public static function subject(BandDetails $bandDetails)
    {
        // StoneAge Thriller is seeking shows for [[timeframe]]. (Original Rock)
        $name = $bandDetails->getBandName();
        $isSolo = $bandDetails->getSolo();
        $is = $isSolo ? "is" : "are";
        $genre = $bandDetails->getGenre();
        
        $subject_format = "%s %s seeking shows for [[timeframe]]. (%s)";
        $subject = sprintf($subject_format, $name, $is, $genre);
        ?>
<label>Subject:</label>
<br />
<input type="text" maxlength=255 name="booking_template_subject"
       class="input_max_width" value="<?php echo $subject;?>">
<br />
        <?php
    }
    
    /**
     * Message
     */
    public static function message(BandDetails $bandDetails)
    {
        $isSolo = $bandDetails->getSolo();
        
        
        ?>
<label>Message:</label>
<br />
<textarea name="booking_template_message" 
          class="input_max_width message_height">
Hello[[, booker_first_name]],

Stuff
    
    
</textarea>
<br />
        <?php
    }
    
    public static function spacer()
    {
        ?>
<div class="template_spacer"></div>
        <?php
    }
    
    public static function templateName()
    {
        ?>
<input type="text" maxlength="255" name="template_title"
       class="input_max_width template_title"
       placeholder="Untitled Template">
<br />
        <?php
    }
}

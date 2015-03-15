<?php

class DisplayEditTemplate 
{
    /**
     * Display the edit template page
     */
    public static function doPage()
    {
        self::startForm();
        self::templateName();
        self::fromEmail();
        self::fromName();
        self::subject();
        self::message();
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
    public static function fromEmail()
    {
        ?>
<label>Booking Email:</label>
<br />
<input type="email" name="booking_template_email"  disabled
       class="input_max_width">
<br />
        <?php
    }
    
    /**
     * The friendly name bookers will see when the email is sent
     * 
     */
    public static function fromName()
    {
        ?>
<label>From Name:</label>
<br />
<input type="text" maxlength=255 name="booking_template_from_name"
       class="input_max_width">
<br />
        <?php
    }
    
    /**
     * Subject line
     */
    public static function subject()
    {
        ?>
<label>Subject:</label>
<br />
<input type="text" maxlength=255 name="booking_template_subject"
       class="input_max_width">
<br />
        <?php
    }
    
    /**
     * Message
     */
    public static function message()
    {
        ?>
<label>Message:</label>
<br />
<textarea name="booking_template_message" 
          class="input_max_width message_height">
    
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

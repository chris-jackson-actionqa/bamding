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
    public static function doPage()
    {
        self::startForm();
        self::addNewTemplate();
        self::submit();
        self::endForm();
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
}

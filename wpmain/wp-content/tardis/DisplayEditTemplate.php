<?php

class DisplayEditTemplate 
{
    const ADD_NEW = "add_new";
    const EDIT = 'edit';
    
    private $bandDetails = null;
    private $action = '';
    private $template = null;
    
    public function __construct()
    {
        $this->bandDetails = new BandDetails(get_user_field('user_login'));
        $this->action = strtolower(trim(filter_input(INPUT_GET, 'taction')));
        $this->assertValidAction();
        
        if(self::EDIT === $this->action)
        {
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if(empty($id))
            {
                throw new InvalidArgumentException("Invalid id");
            }
            
            $this->template = new BookingTemplate(
                    get_user_field('user_login'), 
                    $id);
        }
    }
    
    /**
     * Throw an exception if action isn't expected
     * @throws InvalidArgumentException
     */
    private function assertValidAction()
    {
        switch($this->action)
        {
            case self::ADD_NEW:
            case self::EDIT:
                break;
            default:
                throw new InvalidArgumentException();
        }
    }
    
    /**
     * Display the edit template page
     */
    public function doPage()
    {
        $this->help();
        $this->startForm();
        $this->templateName();
        $this->templateID();
        $this->fromEmail();
        $this->fromName();
        $this->subject();
        $this->message();
        $this->spacer();
        $this->save();
        $this->cancel();
        $this->endForm();
    }
    
    /**
     * Display help on using templates
     */
    public function help()
    {
        ?>
<div id="help_message">
    <h3>Keys filled in by BamDing:</h3>
    <ul>
        <li><strong>[[, booker_first_name]]</strong>: Booker's first name. 
        If no name was entered, just leaves this blank.
        </li>
        <li>
            <strong>[[timeframe]]</strong>:  Displays general months for booking requests.<br />
            Example 1: May<br />
            Example 2: May through July
        </li>
        <li>
            <strong>[[dates]]</strong>: Your specific dates for booking requests. <br />
            Example: May: 17th, 20th, 31st
        </li>
        <li>
            <strong>[[venue]]</strong>: Fills in the venue name.
        </li>
    </ul>
</div>
        <?php
    }
    
    /**
     * Begin the form
     */
    public function startForm()
    {
        $action = Site::getBaseURL() . '/booking-templates/';
        ?>
<form method="post" action="<?php echo $action; ?>" id="edit_template_form">
        <?php
    }
    
    /**
     * End the form
     */
    public function endForm()
    {
        ?>
</form>
        <?php
    }
    
    /**
     * Save the form
     */
    public function save()
    {
       ?>
<input type="submit" name="template_save" value="Save">
       <?php
    }
    
    public function cancel()
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
    public function fromEmail()
    {
        $email = $this->bandDetails->getEmail();
        ?>
<label>Booking Email:</label>
<br />
<input type="email" name="booking_template_email"  readonly
       class="input_max_width" value="<?php echo $email; ?>">
<br />
        <?php
    }
    
    /**
     * The friendly name bookers will see when the email is sent
     * 
     */
    public function fromName()
    {
        switch($this->action)
        {
            case self::ADD_NEW:
                $name = $this->bandDetails->getBandName();
                break;
            case self::EDIT:
                $name = $this->template->getFromName();
                break;
            default:
                $name = '';
                break;
        }
        
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
    public function subject()
    {
        switch($this->action)
        {
            case self::ADD_NEW:
                $subject = $this->genericSubject();
                break;
            case self::EDIT:
                $subject = $this->template->getSubject();
                break;
            default:
                $subject = '';
                break;
        }
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
    public function message()
    {
        switch($this->action)
        {
            case self::ADD_NEW:
                $message = $this->genericTemplate();
                break;
            case self::EDIT:
                $message = $this->template->getMessage();
                break;
            default:
                $message = '';
        }
        ?>
<label>Message:</label>
<br />
<textarea name="booking_template_message" 
          class="input_max_width message_height">
<?php echo $message; ?>
    
</textarea>
<br />
        <?php
    }
    
    public function spacer()
    {
        ?>
<div class="template_spacer"></div>
        <?php
    }
    
    public function templateName()
    {
        $value = '';
        if(self::EDIT == $this->action)
        {
            $value = $this->template->getTitle();
        }
        ?>
<input type="text" maxlength="255" name="template_title"
       class="input_max_width template_title"
       placeholder="Untitled Template"
       value="<?php echo $value;?>">
<br />
        <?php
    }
    
    public function templateID()
    {
        $id = -1;
        if(self::EDIT == $this->action)
        {
            $id = $this->template->getID();
        }
        ?>
<input type="hidden" name="template_id" value=<?php echo $id; ?>>
        <?php
    }
    
    /**
     * Retrieve a generic booking template
     * @global type $current_user
     * @return string generic template message
     */
    public function genericTemplate()
    {
        $isSolo = $this->bandDetails->getSolo();
        $name = $this->bandDetails->getBandName();
        $genre = $this->bandDetails->getGenre();
        $draw_num = $this->bandDetails->getDraw();
        $sounds_like = $this->bandDetails->getSoundsLike();
        $music = $this->bandDetails->getMusic();
        $liveVideo = $this->bandDetails->getVideo();
        $calendar = $this->bandDetails->getCalendar();
        $phone = $this->bandDetails->getPhone();
        $website = $this->bandDetails->getWebsite();
        $other_sites = $this->bandDetails->getSites();
        
        // Get user's first and last name
        global $current_user;
        get_currentuserinfo();
        $first_name = $current_user->user_firstname;
        $last_name = $current_user->user_lastname;
        
        $Im = $isSolo ? "I'm" : "We're";
        $im = $isSolo ? "I'm" : "we're";
        $I_am = $isSolo ? "I am" : "We are";
        $I = $isSolo ? "I" : "We";
        $i = $isSolo ? "I" : "we";
        $my = $isSolo ? "my" : "our";
        $My = $isSolo ? "My" : "Our";
        $ill = $isSolo ? "I'll" : "we'll";
        
        $message = <<<MSG
Hello[[, booker_first_name]],

$Im $name, a $genre act, looking to book a show at [[venue]]. $I_am seeking the following dates:
[[dates]]


MSG;
        
        if(!empty($draw_num))
        {
        $message .=  <<<MSG
$My draw has been around $draw_num people per show.


MSG;
        }
        
        $message .= <<<MSG
$I sound similar to the following bands:
$sounds_like

To hear $my music, click here:
$music


MSG;
    
        if(!empty($liveVideo))
        {
            $message .= <<<VID
To see $my live videos, click here:
$liveVideo


VID;
        }
    
        if(!empty($calendar))
        {
            $message .= <<<LAST
$I_am available to play other dates and would love to help out if you need a last minute band. Just check $my booking calendar to see if $im available:
$calendar


LAST;
        }
    
    
        $message .= <<<MSG
If $i don’t hear back from you, $ill send another email in a couple of weeks.

Talk to you soon!
- $first_name $last_name

MSG;
    
        if(!empty($phone))
        {
            $message .= <<<MSG
$phone

MSG;
        }

        $message .= <<<MSG
$website


MSG;
    
        if(!empty($other_sites))
        {
            $message .= <<<MSG
$other_sites
MSG;
        }
        
        return $message;
    }
    
    /**
     * Get the generic subject for new templates
     * @return type
     */
    public function genericSubject()
    {
        // StoneAge Thriller is seeking shows for [[timeframe]]. (Original Rock)
        $name = $this->bandDetails->getBandName();
        $isSolo = $this->bandDetails->getSolo();
        $is = $isSolo ? "is" : "are";
        $genre = $this->bandDetails->getGenre();
        
        $subject_format = "%s %s seeking shows for [[timeframe]]. (%s)";
        $subject = sprintf($subject_format, $name, $is, $genre);
        
        return $subject;
    }
    
    
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminTemplate
 *
 * @author Seth
 */
class AdminTemplate 
{
    //put your code here
    private $oConn = null;
    private $template = null;
  
    public function __construct()
    {
        $this->template = new BookingTemplate(
                $_GET['user_login'], 
                (int)$_GET['id']);
    }
    
    public function doPage()
    {
        $this->defines();
        $this->subject();
        $this->message();
    }
    
    public function defines()
    {
        ?>
[[begindefines]]<br />
fromName=<?php echo $this->template->getFromName();?><br />
fromEmail=<?php echo $this->template->getEmail();?><br />
[[enddefines]]<br />
<br />
        <?php
    }
    
    public function subject()
    {
        $subject = $this->template->getSubject();
        ?>
[[beginsubject]]<br />
<?php echo $subject;?><br />
[[endsubject]]<br />
<br />
        <?php
    }
    
    public function message()
    {
        $message = nl2br($this->template->getMessage());
        ?>
[[beginmessage]]<br />
<?php echo $message; ?><br />
[[endmessage]]<br />
<br />
        <?php
    }
}

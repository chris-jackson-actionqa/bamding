<?php

class BookingTemplate 
{
    private $oConn = null;
    private $id = -1;
    private $sUserLogin = '';
    private $email = '';
    private $title = '';
    private $name = '';
    private $subject = '';
    private $message = '';
    
    /**
     * Constructor. Connects to the database. 
     * @param string $sUserLogin
     * @throws InvalidArgumentException
     */
    public function __construct($sUserLogin, $templateID = -1)
    {
      if(empty($sUserLogin))
      {
        throw new InvalidArgumentException(
                'BookingTemplate requires valid user login');
      }
      
      $this->sUserLogin = $sUserLogin;
      
      $oDB = new Database();
      $this->oConn = $oDB->connect();
      
      $this->id = (int)$templateID;
      if( 0 < $this->id)
      {
          $this->loadTemplate();
      }
    }
    
    /**
     * Destructor. Closes database connection.
     */
    public function __destruct() {
        $this->oConn->close();
    }
    
    /**
     * Load the template via its ID
     */
    private function loadTemplate()
    {
        $sql = <<<SQL
SELECT * FROM booking_templates
WHERE template_id={$this->getID()}
SQL;

        $result = $this->oConn->query($sql);
        if(empty($result))
        {
            throw new RuntimeException($this->oConn->error);
        }
        
        $rows = Database::fetch_all($result);
        $row = $rows[0];
        
        $this->title = $row['title'];
        $this->email = $row['booking_email'];
        $this->name = $row['from_name'];
        $this->subject = $row['subject'];
        $this->message = $row['message'];
    }
    
    /**
     * Save the template
     */
    public function saveTemplate()
    {
        if(0 > $this->id)
        {
            $this->addNewTemplate();
        }
        else 
        {
            $this->updateTemplate();
        }
    }
    
    private function addNewTemplate()
    {
        $sql = <<<SQL
INSERT INTO booking_templates
    (
    user_login,
    title,
    booking_email,
    from_name,
    subject,
    message,
    created,
    modified
    )
VALUES
    (
    '{$this->sUserLogin}',
    '{$this->title}',
    '{$this->email}',
    '{$this->name}',
    '{$this->subject}',
    '{$this->message}',
    NOW(),
    NOW()
    )
SQL;
    
        $result = $this->oConn->query($sql);
        if(empty($result))
        {
            throw new RuntimeException($this->oConn->error);
        }
    }
    
    private function updateTemplate()
    {
        $sql = <<<SQL
UPDATE booking_templates
SET 
    user_login='{$this->sUserLogin}',
    title='{$this->title}',
    booking_email='{$this->email}',
    from_name='{$this->name}',
    subject='{$this->subject}',
    message='{$this->message}',
    modified=NOW()
WHERE 
    user_login='{$this->sUserLogin}' AND
    template_id={$this->id}
SQL;
    
        $result = $this->oConn->query($sql);
        if(empty($result))
        {
            throw new RuntimeException($this->oConn->error);
        }
    }
    
    /**
     * Get template id
     * @return int id
     */
    public function getID()
    {
        return (int)$this->id;
    }
    
    /**
     * Get booking email
     * @return string email
     */
    public function getEmail()
    {
        return stripslashes($this->email);
    }
    
    /**
     * Set the booking email address
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $this->trimAndEscape($email);
    }
    
    /**
     * Get the title of the template
     * @return string title of template
     */
    public function getTitle()
    {
        return stripslashes($this->title);
    }
    
    /**
     * Set the title of the template
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $this->trimAndEscape($title);
    }
    
    /**
     * Friendly from name 
     * @return string 
     */
    public function getFromName()
    {
        return stripslashes($this->name);
    }
    
    /**
     * Set friendly from name
     * @param string $name
     */
    public function setFromName($name)
    {
        $this->name = $this->trimAndEscape($name);
    }
    
    /**
     * Get subject
     * @return string 
     */
    public function getSubject()
    {
        return stripslashes($this->subject);
    }
    
    /**
     * Set the subject line
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $this->trimAndEscape($subject);
    }
    
    /**
     * Get the message body
     * @return string
     */
    public function getMessage()
    {
        return stripslashes($this->message);
    }
    
    /**
     * Set the message body
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $this->trimAndEscape($message);
    }
    
    /**
     * Helper function for sanitizing variables
     * @param string $var
     * @return string
     */
    private function trimAndEscape($var)
    {
        return mysql_real_escape_string(trim($var));
    }
}

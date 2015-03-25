<?php

class BookingTemplates 
{
    private $sUserLogin = '';
    private $oConn = '';
    
    /**
     * Constructor. Connects to the database. 
     * @param string $sUserLogin
     * @throws InvalidArgumentException
     */
    public function __construct($sUserLogin)
    {
      if(empty($sUserLogin))
      {
        throw new InvalidArgumentException(
                'Booking Templates requires valid user login');
      }

      $this->sUserLogin = $sUserLogin;
      $oDB = new Database();
      $this->oConn = $oDB->connect();
    }
    
    /**
     * Destructor. Closes database connection.
     */
    public function __destruct() {
        $this->oConn->close();
    }
    
    /**
     * Get all the templates for the user
     * @return array rows of templates
     * @throws RuntimeException if sql query fails
     */
    public function getTemplates()
    {
        $sql = <<<SQL
SELECT * FROM booking_templates
WHERE user_login='{$this->sUserLogin}'
SQL;
        
        $result = $this->oConn->query($sql);
        if(empty($result))
        {
            throw new RuntimeException($this->oConn->error);
        }
        
        return Database::fetch_all($result);
    }
}

<?php

class BookingTemplates 
{
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
                'Band Details requires valid user login');
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
}

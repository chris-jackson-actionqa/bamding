<?php

class Database
{
  private $oConn;

  // connect
  public function connect()
  {
    $oConnection = new mysqli("localhost", "sethalic_cust", "Snad2co1", "sethalic_bamding");

    if ($oConnection->connect_error) 
    {
      throw new Exception('Connect Error (' . $oConnection->connect_errno . ') '
            . $mysqli->connect_error);
    }
    
    $this->oConn = $oConnection;
    return $oConnection;
  }
};

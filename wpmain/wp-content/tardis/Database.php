<?php

class Database
{
  private $oConn;

  // connect
  public function connect()
  {
    $sSettingsFile = $_SERVER['DOCUMENT_ROOT'] . '/../config.ini';
    $hDB = parse_ini_file($sSettingsFile);
    if(FALSE === $hDB)
    {
      throw new Exception('Could not get database settings.');
    }
    
    $oConnection = new mysqli(
            $hDB['db_host'], 
            $hDB['db_user'], 
            $hDB['db_pwd'], 
            $hDB['db_database']);

    if ($oConnection->connect_error) 
    {
      throw new Exception('Connect Error (' . $oConnection->connect_errno . ') '
            . $mysqli->connect_error);
    }
    
    $this->oConn = $oConnection;
    return $oConnection;
  }
};

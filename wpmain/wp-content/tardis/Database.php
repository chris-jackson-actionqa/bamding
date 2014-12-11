<?php

class Database
{
  private $oConn;

  // connect
  public function connect($wordpress=false)
  {
    $sSettingsFile = $_SERVER['DOCUMENT_ROOT'] . '/../config.ini';
    if('/home2/sethalic/public_html/admin' == $_SERVER['DOCUMENT_ROOT'])
    {
      $sSettingsFile = $_SERVER['DOCUMENT_ROOT'] . '/../../config.ini';
    }
    $hDB = parse_ini_file($sSettingsFile);
    
    if(FALSE === $hDB)
    {
      throw new Exception('Could not get database settings.');
    }
    
    $host = $wordpress ? $hDB['wp_host'] : $hDB['db_host'];
    $user = $wordpress ? $hDB['wp_user'] : $hDB['db_user'];
    $pwd = $wordpress ? $hDB['wp_pwd'] : $hDB['db_pwd'];
    $database = $wordpress ? $hDB['wp_database'] : $hDB['db_database'];
    $oConnection = new mysqli(
            $host, 
            $user, 
            $pwd, 
            $database);

    if ($oConnection->connect_error) 
    {
      throw new Exception('Connect Error (' . $oConnection->connect_errno . ') '
            . $mysqli->connect_error);
    }
    
    $this->oConn = $oConnection;
    return $oConnection;
  }
  
  public static function fetch_all($mResult)
  {
    $hAllRows = array();
    while ($hRow = $mResult->fetch_assoc())
    {
      array_push($hAllRows, $hRow);
    }
    return $hAllRows;
  }
};

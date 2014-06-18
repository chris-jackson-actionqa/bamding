<?php
function db_connect()
{
  $result = new mysqli('localhost', 'root','Snad2co1', 'bamding');
  if( !$result )
  {
    throw new Exception("Could not connect to database server.");
  }
  else
  {
    return $result;
  }
}

function get_user_id($sEmail)
{
  $conn = db_connect();
  $result = $conn->query(
    "select userid from user " .
    "where email='$sEmail'"
  );

  if( $result === FALSE )
  {
    throw new Exception("Could not find user.");
  }
  else if ($result->num_rows != 1 )
  {
    throw new Exception("Error: $sEmail returned more than one user id.");
  }
  
  $aRow = $result->fetch_row();
  
  return $aRow[0];
}

function is_user_paid($nUserId)
{
  $oConn = db_connect();
  $oResult = $oConn->query(
          "SELECT paid_accounts from account " .
          "WHERE userid='$nUserId'"
          );
  
  if(FALSE === $oResult || $oResult->num_rows != 1)
  {
    throw new Exception("Error: Could not find user's account.");
  }
  
  $aRow = $oResult->fetch_row();
  return $aRow[0] != 0;
}

function createUserAccount($nUserId)
{
  // if the account exists already, this function should not
  // have been called. Something is wrong.
  if(accountExists($nUserId))
  {
    throw new Exception("Error: User account already exists. Can't create it.");
  }
  
  // create the user's account
  $oConn = db_connect();
  $oResult = $oConn->query(
          "insert into account " .
          "(userid,paid_accounts)" .
          " values ('$nUserId','0')"
          );
  
  // throw if result sucks
  if( $oResult === FALSE )
  {
    throw new Exception("ERROR: Could not add user's account.");
  }
}

function accountExists($nUserId)
{
  $oConn = db_connect();
  $oResult = $oConn->query(
          "SELECT paid_accounts from account " .
          "WHERE userid='$nUserId'"
          );
  return ($oResult->num_rows != 0);
}
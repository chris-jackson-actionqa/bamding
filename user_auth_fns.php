<?php
function register($email, $password)
{
  //register new person with db
  //return true or error message
  //connect to db
  $conn = db_connect();

  //check if username is unique
  $result = $conn->query(
    "select * from user where email='" .
    $email . "'");

  if( !$result )
  {
    throw new Exception('Could not execute query: ' . $conn->error);
  }

  if($result->num_rows > 0  )
  {
    throw new Exception(
      'That username is taken - go back and choose another one.');
  }

  //if ok, put in db
  $shapwd = sha1($password);
  $result = $conn->query(
    "insert into user " .
    "(email,password)" .
    " values ('$email','$shapwd')");

  if( !$result )
  {
    throw new Exception(
      'Could not register you in the database. ' .
      'Please try again.');
  }
  
  $nUserId = get_user_id($email);
  createUserAccount($nUserId);

  return TRUE;
}

function login($email, $password)
{
  //check email and password with db
  //if yes, return true
  //else throw exception
  //connect to db
  $conn = db_connect();

  //check if email is unique
  $result = $conn->query(
    "select * from user 
    where email='$email'" .
    " and password= sha1('$password')");

  if(!$result)
  {
    throw new Exception('Could not log you in. Bad password or email?');
  }

  if($result->num_rows > 0 )
  {
    return true;
  }
  else
  {
    throw new Exception('Could not log you in. Email or password not found.');
  }
}

function check_valid_user()
{
  $userEmail = NULL;

  // see if somebody is logged in and notify them if not
  if(isset($_SESSION['valid_user']))
  {
    $userEmail = $_SESSION['valid_user'];
  }
  
  return $userEmail;
}

function logout()
{
  session_start();
  session_destroy();
}

<?php
require_once('bamding_fns.php');
$email=$_POST['email'];
$passwd=$_POST['password'];
$passwd2=$_POST['password2'];

session_start();
try
{
  //check forms filled in
  if(!filled_out($_POST))
  {
    throw new Exception(
      "You have not filled the form out correctly" .
      "-- Please go back and try again.");
  }

  //email address not valid
  if( !valid_email($email))
  {
    throw new Exception(
      "That is not a valid email address." .
      " Please go back and try again.");
  }

  //passwords not the same
  if( $passwd != $passwd2 )
  {
    throw new Exception(
      "The passwords you entered do not match. " .
      "Go back and try again.");
  }

  //check if password length is okay
  //okay if username truncates, but passwords will get munged
  //if too long
  if( (strlen($passwd)) < 6 || (strlen($passwd)) > 16 )
  {
    throw new Exception(
      "Your password must be between 6 and 16 characters. " .
      "Go back and try again.");
  }

  //attempt to register
  //this function can also throw an exception
  register($email, $passwd);

  //register session variable
  $_SESSION['valid_user']= $email;

  //provide link to members page
  do_html_header('Registration successful');
  echo "Your registration was successful. Go to the members page to start.";
  do_html_url("member.php", "Go to members page");

  do_html_footer();
}
catch(Exception $e)
{
  do_html_header("Problem: ");
  echo $e->getMessage();
  do_html_footer();
  exit;
}

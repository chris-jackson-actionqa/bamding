<?php
require_once('bamding_fns.php');

session_start();

//TODO: verify and sanitize.
//TODO: might not be coming from a form, so these vars might not exist
//TODO: if they are not defined, don't use them
$email = $_POST['email'];
$passwd = $_POST['password'];

if( $email && $passwd )
{
  //they have just tried logging in
  try
  {
    login($email, $passwd);

    // if they are in the database register the user id
    $_SESSION['valid_user'] = $email;
  }
  catch( Exception $e )
  {
    // unsuccessful login
    do_html_header('Problem:');
    echo 'You could not be logged in. You must be logged in to use this page.<br />';
    do_html_url('index.php', 'Login');
    do_html_footer();
    exit;
  }
}

do_html_header('Home');
check_valid_user();

// get user content
echo "Welcome, member.<br />";

//Paid member?
//If not paid, display paypal options
if( is_paid_member($_SESSION['valid_user']) )
{
  echo "You are paid member<br />";
}
//else, display menu
else
{
  display_payment_form();
}

do_html_footer();

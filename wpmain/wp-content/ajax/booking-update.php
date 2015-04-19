<?php
/**
 * Updates the next contact date or the frequency of the bookings for a venue.
 */
require_once './../tardis/bamding_lib.php';

header("content-type:application/json");

// Get the user login
$user = filter_input(INPUT_POST, 'user_login', FILTER_SANITIZE_STRING);

$bookings = new Bookings($user);

// Get the action to perform
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

// If updating the next contact, 
// get the next contact date
if('next_contact' === $action)
{
  $nextContactDate = filter_input(INPUT_POST, 'next_contact', FILTER_SANITIZE_STRING);
  $venueID = filter_input(INPUT_POST, 'venue_id', FILTER_SANITIZE_NUMBER_INT);
  $success = TRUE;
  $message = $nextContactDate;
  $next = '';
  try
  {
    if( !$bookings->isValidNextContact($venueID, $nextContactDate))
    {
      throw new RuntimeException('Invalid date');
    }
    
    $bookings->setNextContact($venueID, $nextContactDate);
  } 
  catch (RuntimeException $ex) 
  {
    $success = FALSE;
    $message = $ex->getMessage();
    $next = (new DateTime($bookings->getSafeNextContact($venueID)))->format('m/d/Y');
  }
}

// Validate the request input
$response = array(
    'success' => $success,
    'message' => $message,
    'next' => $next
);
echo json_encode($response);

exit();
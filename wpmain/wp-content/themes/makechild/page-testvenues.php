<?php
require_once(ABSPATH. '/wp-content/tardis/Venues.php');
require_once(ABSPATH. '/wp-content/tardis/Venue.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayForms.php');
require_once(ABSPATH. '/wp-content/tardis/ProcessForms.php');
get_header();  
?>

<h1>Wooohooo!</h1>
<?php
  $user_email = get_user_field ("user_email");
  echo "<h2>$user_email</h2>";
  ProcessForms::AddNewVenue($_POST);
  DisplayForms::addNewVenue('http://bamding.com/testvenues/');
  get_footer();


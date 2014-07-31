<?php
require_once(ABSPATH. '/wp-content/tardis/Venues.php');
require_once(ABSPATH. '/wp-content/tardis/Venue.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayForms.php');
get_header();  
?>

<h1>Wooohooo!</h1>
<?php
  $user_email = get_user_field ("user_email");
echo "<h2>$user_email</h2>";


// if 'get' data, add to database
if( array_key_exists('bd_venue_name', $_POST) && !empty($_POST['bd_venue_name']) )
{
  $oVenue = new Venue();
  $oVenue->setName($_POST['bd_venue_name']);
  
  $oVenues = new Venues();
  $oVenues->addVenue($oVenue);
  echo 'Venue ' . $oVenue->getName() . ' added.<br />';
}

?>

<?php
DisplayForms::addNewVenue('http://bamding.com/testvenues/');
?>

<br>
<br>
<?php 

get_footer();


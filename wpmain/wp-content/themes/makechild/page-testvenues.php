<?php
require_once(ABSPATH. '/wp-content/tardis/Venues.php');
require_once(ABSPATH. '/wp-content/tardis/Venue.php');
get_header();  
?>

<h1>Wooohooo!</h1>
<?php
  $user_email = get_user_field ("user_email");
echo "<h2>$user_email</h2>";


// if 'get' data, add to database
if( array_key_exists('bd_venue_name', $_GET) && !empty($_GET['bd_venue_name']) )
{
  $oVenue = new Venue();
  $oVenue->setName($_GET['bd_venue_name']);
  
  $oVenues = new Venues();
  $oVenues->addVenue($oVenue);
  echo 'Venue ' . $oVenue->getName() . ' added.<br />';
}

?>

<form name='addVenue' action='http://bamding.com/testvenues/' method='get'>
Venue: <input type="text" name="bd_venue_name"><br />
<input type="submit" value="Submit">
</form>
<br>
<br>
<?php 

get_footer();


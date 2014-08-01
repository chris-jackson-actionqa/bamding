<?php
require_once(ABSPATH. '/wp-content/tardis/Venues.php');
require_once(ABSPATH. '/wp-content/tardis/Venue.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayForms.php');
require_once(ABSPATH. '/wp-content/tardis/ProcessForms.php');
get_header();  
?>
<?php if (current_user_can("access_s2member_level1")){ ?>
<h1>Wooohooo!</h1>
<?php
  $user_email = get_user_field ("user_email");
  echo "<h2>$user_email</h2>";
  ProcessForms::AddNewVenue($_POST);
  DisplayForms::addNewVenue('http://bamding.com/testvenues/');
  ?>

<?php } else { ?>
<h1>Who are you?</h1>
<?php } ?>
<?php
  get_footer();


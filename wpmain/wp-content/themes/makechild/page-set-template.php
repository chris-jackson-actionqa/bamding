<?php
require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');

// don't show venues to non-members
if (!current_user_can("access_s2member_level1"))
  {
  header('Location: ' . Site::getBaseURL() . '/prices/');
  exit();
  } 

get_header();  
$template = new DisplaySetTemplate(get_user_field('user_login'));
$template->doPage();
get_footer();

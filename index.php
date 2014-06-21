<?php
require_once('bamding_fns.php');

if(isset($_POST['logout']))
{
  logout();
}
do_html_header("Home");
display_site_info();
display_login_form();
do_html_footer();
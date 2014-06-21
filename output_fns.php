<?php
function do_html_header($title)
{
?>
<html>
  <head>
    <title><?php echo "BamDing | $title"; ?></title>
  </head>
  <body>
    <h1>BamDing</h1>
<?php
}

function display_site_info()
{
}

function display_login_form()
{
?>
<a href="register_form.php">Not a member?</a>
<form action="member.php" method="post">
  E-mail: <input type="text" name="email"><br>
  Password: <input type="password" name="password"><br>
  <input type="submit" value="Log in">
</form>
<a href="">Forgot your password?</a>

<?php
}

function display_registration_form()
{
?>
<form action="register_new.php" method="post">
  E-mail: <input type="text" name="email"><br>
  Password: <input type="password" name="password"><br>
  Confirm Password: <input type="password" name="password2"><br>
  <input type="submit" value="Register">
</form>
<?php
}

function do_html_footer()
{
?>
  </body>
</html>
<?php 
}

function do_html_url($url, $text)
{
  echo "<a href=\"$url\">$text</a>";
}

function display_payment_form()
{
  ?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="U2E5C8LRTY72G">
<table>
<tr><td><input type="hidden" name="on0" value=""></td></tr><tr><td><select name="os0">
	<option value="Monthly">Monthly : $10.00 USD - monthly</option>
	<option value="Yearly (3 Months Free)">Yearly (3 Months Free) : $90.00 USD - yearly</option>
</select> </td></tr>
</table>
<input type="hidden" name="currency_code" value="USD">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<?php
}

function display_member_menu()
{
  ?>
<div id='member_menu'>
  <form method="post" action="index.php">
    <input type="hidden" name="logout" value="true">
    <input type="submit" name="Submit" value="Log Out">
  </form>
</div>
  
  <?php
}
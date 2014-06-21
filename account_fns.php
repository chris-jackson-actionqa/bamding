<?php
function is_paid_member($sEmail)
{
  // get userid for email
  $mUserId = get_user_id($sEmail);
  
  // find if user is paid
  return is_user_paid($mUserId);
}

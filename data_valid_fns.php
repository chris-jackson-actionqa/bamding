<?php
function filled_out($form_vars)
{
  //test that each variable has a value
  foreach( $form_vars as $key => $value )
  {
    if( (!isset($key)) || ($value == '') )
    {
      return FALSE;
    }
  }

  return TRUE;
}

function valid_email($address)
{
  // check an email address is possibly valid
  if( ereg('^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$',
    $address ) )
  {
    return TRUE;
  }
  else
  {
    return FALSE;
  }
}

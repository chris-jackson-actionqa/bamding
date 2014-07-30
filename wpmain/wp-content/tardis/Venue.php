<?php

class Venue
{
  private $sName = '';
  public function getName()
  {
    return $this->sName;
  }
  
  public function setName( $sVenueName )
  {
    if(empty($sVenueName) )
    {
      throw InvalidArgumentException( "Venue name can't be empty" );
    }

    if(255 < strlen( $sVenueName ) )
    {
      throw LengthException( 'Venue name is too long. 255 characters allowed.' );
    }

    $this->sName = $sVenueName;
  }
};

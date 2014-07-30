<?php
/**
 * The Venue object. 
 * Abstract the interaction with the database through easy to use
 * calls. 
 * You can get all the databases or filter them down by city.
 * Get what venues a musician is booking at.
 */
require_once(ABSPATH. '/wp-content/tardis/Database.php');
require_once(ABSPATH. '/wp-content/tardis/Venue.php');

class Venues
{
  private $oConn;

  // constructor
  public function __construct()
  {
    $oDB = new Database();
    $this->oConn = $oDB->connect();
  }

  // add a venue
  public function addVenue(Venue $oVenue)
  {
    if(empty($oVenue))
    {
      throw InvalidArgumentException('Venue object is empty');
    }

    $sSQL = "INSERT INTO venues (name) VALUES ('" . 
      $oVenue->getName() . "')";
    $mResult = $this->oConn->query($sSQL);

    if(TRUE !== $mResult )
    {
      throw new Exception("Unable to add venue to database: " .
        $this->oConn->error );
    }
  }

  // get venues

  // remove a venue
  
  // update venue
};

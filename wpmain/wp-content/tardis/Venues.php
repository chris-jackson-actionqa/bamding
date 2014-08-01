<?php
/**
 * The Venues object. 
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
  private $sTable;
  private $sUserID;
  private $sActID;

  // constructor
  public function __construct($sTable = "", $sUserID = "", $sActID = "")
  {
    $oDB = new Database();
    $this->oConn = $oDB->connect();
    
    if(empty($sTable))
    {
      $this->sTable = 'venues';
    }
    else
    {
     $this->sTable = $sTable;
    }
    
    $this->sUserID = $sUserID;
    $this->sActID = $sActID;
  }

  // add a venue
  public function addVenue(Venue $oVenue)
  {
    if(empty($oVenue))
    {
      throw InvalidArgumentException('Venue object is empty');
    }
    
    //TODO: Detect duplicate venues.

    $sSQL = "";
    
    if( 'venues' == $this->sTable)
    {
      $sSQL = "INSERT INTO $this->sTable (name) VALUES ('" . 
        $oVenue->getName() . "')";
    }
    else if('my_venues' == $this->sTable)
    {
      $sSQL = <<<SQL
INSERT INTO {$this->sTable}  
  ( 
  name,
  email,
  city,
  state,
  booker_fname,
  booker_lname,
  subform,
  address1,
  address2,
  country,
  postalcode,
  user_login
  )  
VALUES (
  '{$oVenue->getName()}',
  '{$oVenue->getEmail()}',
  '{$oVenue->getCity()}',
  '{$oVenue->getState()}',
  '{$oVenue->getBookerFirstName()}',
  '{$oVenue->getBookerLastName()}',
  '{$oVenue->getContactForm()}',
  '{$oVenue->getAddress1()}',
  '{$oVenue->getAddress2()}',
  '{$oVenue->getCountry()}',
  '{$oVenue->getZip()}',
  '{$this->sUserID}'
  )
SQL;
  
    }
    $mResult = $this->oConn->query($sSQL);

    if(TRUE !== $mResult )
    {
      throw new Exception("Unable to add venue to database: " .
        $this->oConn->error );
    }
  }

  // get venues
  public function getAllMyVenues()
  {
    $sSQL = "SELECT * FROM my_venues WHERE user_login='$this->sUserID'";
    
    $mResult = $this->oConn->query($sSQL);

    if(FALSE === $mResult )
    {
      throw new Exception("Could not get venues: " .
        $this->oConn->error );
    }
    
    $aVenues = array();
    
    while( $row = $mResult->fetch_assoc())
    {
      array_push($aVenues, $row);
    }
    
    $mResult->free();
    
    return $aVenues;
  }

  // remove a venue
  
  // update venue
};

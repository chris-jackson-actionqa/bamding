<?php
/**
 * The Venues object. 
 * Abstract the interaction with the database through easy to use
 * calls. 
 * You can get all the databases or filter them down by city.
 * Get what venues a musician is booking at.
 */
require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');

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
    $sSQL = "SELECT * FROM my_venues WHERE user_login='$this->sUserID'"
            . " ORDER BY country,state,city";
    
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
  
  public function getVenue($nVenueID)
  {
    $nVenueID = (int)$nVenueID;
    $sUserID = get_user_field('user_login');
    $sSQL = "SELECT * FROM my_venues WHERE user_login='$sUserID' AND id='$nVenueID'";
    $mResult = $this->oConn->query($sSQL);
    
    $oVenue = NULL;
    
    if(FALSE !== $mResult)
    {
      $hVenue = $mResult->fetch_assoc();
      
      $oVenue = new Venue();
      
      $sVal = (is_null($hVenue['name'])) ? '' : $hVenue['name'];
      $oVenue->setName($sVal);
      
      $sVal = (is_null($hVenue['email'])) ? '' : $hVenue['email'];
      $oVenue->setEmail($sVal);
      
      $sVal = (is_null($hVenue[''])) ? '' : $hVenue['email'];
      $oVenue->setEmail($sVal);
      
    }
    
    return $oVenue;
  }

  // remove a venue
  public function removeVenue($nVenueID)
  {
    // make sure venue id is set
    if(!isset($nVenueID))
    {
      throw new InvalidArgumentException("Venue id isn't set or wasn't provided.");
    }
    
    // make sure the user id is set
    if(empty($this->sUserID))
    {
      throw new InvalidArgumentException("A valid user id is required to remove a venue.");
    }
    
    // sanitize venue id
    $nVenueID = (int)$nVenueID;
    
    // does the venue exist?
    if( !$this->doesVenueExist($nVenueID))
    {
      throw new InvalidArgumentException('Venue not in database: ' . $nVenueID);
    }
    
    // verify venue belongs to user
    // really bad if not.
    if(!$this->doesVenueBelongToUser($nVenueID))
    {
      throw new InvalidArgumentException("Venue doesn't belong to user: $nVenueID");
    }
    
    // remove venue from the database
    $sSQL = <<<SQL
DELETE FROM my_venues
WHERE 
  id='$nVenueID' AND
  user_login='{$this->sUserID}'
SQL;
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new RuntimeException("Failed to remove venue.");
    }
    
  }
  
  public function doesVenueBelongToUser($nVenueID)
  {
    // make sure the venue and user id are set
    if(!isset($nVenueID) || empty($this->sUserID))
    {
      return FALSE;
    }
    
    // sanitize venue id
    $nVenueID = (int)$nVenueID;
    
    //get venue
    $sSQL = 'SELECT id, user_login FROM my_venues '
            . "WHERE id='" . $nVenueID . "' AND "
            . "user_login='" . $this->sUserID . "'";
    $mResult = $this->oConn->query($sSQL);
    
    if( FALSE === $mResult)
    {
      return FALSE;
    }
    
    if( 0 == $mResult->num_rows)
    {
      return FALSE;
    }
    
    return TRUE;
  }
  
  public function doesVenueExist($nVenueID)
  {
    // make sure the venue and user id are set
    if(!isset($nVenueID) || empty($this->sUserID))
    {
      return FALSE;
    }
    
    // sanitize venue id
    $nVenueID = (int)$nVenueID;
    
    //get venue
    $sSQL = 'SELECT id FROM my_venues '
            . "WHERE id='" . $nVenueID . "'";
    $mResult = $this->oConn->query($sSQL);
    
    if( FALSE === $mResult)
    {
      return FALSE;
    }
    
    if( 0 == $mResult->num_rows)
    {
      return FALSE;
    }
    
    return TRUE;
  }
  
  // update venue
};

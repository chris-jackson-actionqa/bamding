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
  private $sTableNotes = 'my_venues_notes';
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
  
  public function __destruct()
  {
    $this->oConn->close();
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
  website,
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
  '{$oVenue->getWebsite()}',
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
    
    $this->addVenueNote($this->getVenueID($oVenue), $oVenue->getNote());
}

public function addVenueNote($nVenueID, $note)
{
  $venue_id = (int)$nVenueID;
  
  $sSQL = <<<SQL
INSERT INTO $this->sTableNotes
(
  user_login,
  venue_id,
  note
)
VALUES
(
'{$this->sUserID}',
'$venue_id',
'$note'
)
SQL;
  $mResult = $this->oConn->query($sSQL);

  if(TRUE !== $mResult )
  {
    throw new Exception("Unable to add venue note to database: " .
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
  
  /**
   * Get the venue object from the id
   * @param int $nVenueID
   * @return \Venue or null if doesn't exist
   */
  public function getVenue($nVenueID)
  {
    $nVenueID = (int)$nVenueID;
    $sUserID = $this->sUserID;
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
      
      $sVal = (is_null($hVenue['city'])) ? '' : $hVenue['city'];
      $oVenue->setCity($sVal);
      
      $sVal = (is_null($hVenue['state'])) ? '' : $hVenue['state'];
      $oVenue->setState($sVal);
      
      $sVal = (is_null($hVenue['booker_fname'])) ? '' : $hVenue['booker_fname'];
      $oVenue->setBookerFirstName($sVal);
      
      $sVal = (is_null($hVenue['booker_lname'])) ? '' : $hVenue['booker_lname'];
      $oVenue->setBookerLastName($sVal);
      
      $sVal = (is_null($hVenue['subform'])) ? '' : $hVenue['subform'];
      $oVenue->setContactForm($sVal);
      
      $sVal = (is_null($hVenue['address1'])) ? '' : $hVenue['address1'];
      $oVenue->setAddress1($sVal);
      
      $sVal = (is_null($hVenue['address2'])) ? '' : $hVenue['address2'];
      $oVenue->setAddress2($sVal);
      
      $sVal = (is_null($hVenue['country'])) ? '' : $hVenue['country'];
      $oVenue->setCountry($sVal);
      
      $sVal = (is_null($hVenue['postalcode'])) ? '' : $hVenue['postalcode'];
      $oVenue->setZip($sVal);
      
      $sVal = (is_null($hVenue['website'])) ? '' : $hVenue['website'];
      $oVenue->setWebsite($sVal);
      
      $oVenue->setNote($this->getVenueNote($nVenueID));
      
      $mResult->close();
    }
    
    return $oVenue;
  }
  
  /**
   * Return the venues note
   * @param int $nVenueID
   * @return string note
   */
  public function getVenueNote($nVenueID)
  {
    $nVenueID = (int)$nVenueID;
    $sUserID = $this->sUserID;
    $sSQL = "SELECT * FROM my_venues_notes "
            . "WHERE user_login='$sUserID' AND venue_id='$nVenueID'";
    $mResult = $this->oConn->query($sSQL);
    
    $note = '';
    if(FALSE !== $mResult)
    {
      $hNoteRow = $mResult->fetch_assoc();
      $note = $hNoteRow['note'];
      $mResult->close();
    }
    
    return $note;
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
    
    $this->removeVenueNote($nVenueID);
  }
  
  /**
   * Delete the note row from the database.
   * @param int $nVenueID
   * @throws RuntimeException
   */
  public function removeVenueNote($nVenueID)
  {
    $sSQL = "DELETE FROM my_venues_notes WHERE venue_id=$nVenueID";
    $mResult = $this->oConn->query($sSQL);
    if(FALSE === $mResult)
    {
      throw new RuntimeException("Could not delete venue's notes.");
    }
  }
  
  
  /**
   * Updates an existing venue
   * 
   * @param Venue $oVenue valid Venue object
   * @param int $nVenueID the id number for the venue
   * @throws InvalidArgumentException if an invalid venue, an invalid id, or if the venue doesn't belong to the user
   * @throws RuntimeException if the MySQL update failed
   */
  public function updateVenue(Venue $oVenue, $nVenueID)
  {
    if(empty($oVenue))
    {
      throw new InvalidArgumentException('Need to provide a venue to update');
    }
    
    if(!$this->doesVenueExist($nVenueID))
    {
      throw new InvalidArgumentException('This venue does not exist. Venue id = ' . $nVenueID);
    }
    
    if(!$this->doesVenueBelongToUser($nVenueID))
    {
      throw new InvalidArgumentException("This venue does not belong to you, so you cannot edit it. Venue id = $nVenueID");
    }
    
    $sSQL = <<<SQL
UPDATE my_venues
SET
  name='{$oVenue->getName()}',
  email='{$oVenue->getEmail()}',
  subform='{$oVenue->getContactForm()}',
  booker_fname='{$oVenue->getBookerFirstName()}',
  booker_lname='{$oVenue->getBookerLastName()}',
  address1='{$oVenue->getAddress1()}',
  address2='{$oVenue->getAddress2()}',
  city='{$oVenue->getCity()}',
  state='{$oVenue->getState()}',
  postalcode='{$oVenue->getZip()}',
  country='{$oVenue->getCountry()}',
  website='{$oVenue->getWebsite()}'
WHERE id='$nVenueID'
SQL;
  
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new RuntimeException("Failed to update venue.");
    }
    
    $this->updateVenueNote($nVenueID, $oVenue->getNote());
  }
  
  /**
   * Update the venue note. Create a new note entry if the venue's note doesn't
   * exist.
   * @param int $nVenueID
   * @param string $note
   * @throws RuntimeException
   */
  public function updateVenueNote($nVenueID, $note)
  {
    // if venue id doesn't exist in note db
    if(!$this->doesVenueNoteExist($nVenueID))
    {
      // add new venue note
      $this->addVenueNote($nVenueID, $note);
    }
    else
    {
      // update venue note
      $sSQL = <<<SQL
UPDATE my_venues_notes
SET note='$note'
WHERE user_login='{$this->sUserID}' AND venue_id=$nVenueID
SQL;
      $mResult = $this->oConn->query($sSQL);
      
      if(FALSE === $mResult)
      {
        throw new RuntimeException("Could not update venue's note.");
      }
    }
  }
  
  /**
   * Check if the note exists for the venue id
   * @param int $nVenueID
   * @return bool
   */
  public function doesVenueNoteExist($nVenueID)
  {
    $nVenueID = (int)$nVenueID;
    
    $sSQL = "SELECT * FROM my_venues_notes WHERE venue_id=$nVenueID";
    $mResult = $this->oConn->query($sSQL);
    $ret = FALSE;
    if(FALSE !== $mResult && 0 !== $mResult->num_rows)
    {
      $ret = TRUE;
    }
    return $ret;
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
  
  public function getVenueID(Venue $oVenue)
  {
    $sSQL = <<<SQL
SELECT id FROM my_venues
WHERE 
  user_login='{$this->sUserID}' AND 
  name='{$oVenue->getName()}' AND
  city='{$oVenue->getCity()}' AND
  state='{$oVenue->getState()}' AND
  country='{$oVenue->getCountry()}'
SQL;
  
    $mResult = $this->oConn->query($sSQL);
    
    if( FALSE === $mResult)
    {
      return -1;
    }
    
    if( 1 != $mResult->num_rows)
    {
      return -1;
    }
    
    $aVenue = $mResult->fetch_array();
    return $aVenue[0];
  }
  
  const ALL = 'all';
  
  /**
   * Get venues for a user by its type (all, country, state, city, or venue)
   * 
   * @param string $sType (all, country, state, etc)
   * @return array results of SQL query
   * @throws InvalidArgumentException (if invalid sType)
   * @throws Exception (if bad SQL query)
   */
  public function getVenuesBy($sType)
  {
    $sSQL = '';
    
    switch($sType)
    {
      case self::ALL:
        $sSQL = <<<SQL
SELECT id FROM my_venues
WHERE user_login='{$this->sUserID}'
SQL;
        break;
      default:
        throw new InvalidArgumentException(
                'Invalid request type for venues: ' . $sType);
        break;
    }
    
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new Exception('SQL query failed.');
    }
    
    return Database::fetch_all($mResult);
  }
  
  public function getCategories()
  {
    return ['Libraries', 'Pre-Schools', 'Festivals'];
  }
  
  public function addCategory($sCategory)
  {
    
  }
}

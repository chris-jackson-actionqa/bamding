<?php

/**
 * @todo Add validation of setters
 * @todo Add utility functions like isValid, error message, etc
 */
class Venue
{
  private $sName = '';
  private $sEmail = '';
  private $sSubForm = '';
  
  private $sBookerFName = '';
  private $sBookerLName = '';
  
  private $sWebsite = '';
  
  // location
  private $sAddress1 = '';
  private $sAddress2 = '';
  private $sCity = '';
  private $sState = '';
  private $sZip = '';
  private $sCountry = '';
  
  
  //-------GETTERS AND SETTERS
  /**
   * 
   * @return string Name of the venue
   */
  public function getName()
  {
    return $this->sName;
  }
  
  /**
   * 
   * @param string $sVenueName
   * @throws InvalidArgumentException Invalid venue name (empty, null, etc)
   * @throws LengthException Venue name is too long
   */
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
  
  public function getEmail()
  {
    return $this->sEmail;
  }
  
  public function setEmail($sEmail)
  {
    //TODO: validate email regex
    $this->sEmail = $sEmail;
  }
  
  public function getContactForm()
  {
    return $this->sSubForm;
  }
  
  public function setContactForm($sContactFormURL)
  {
    //TODO: validate url valid
    $this->sSubForm = $sContactFormURL;
  }
  
  public function getBookerFirstName()
  {
    return $this->sBookerFName;
  }
  
  public function setBookerFirstName($sBookerFirstName)
  {
    $this->sBookerFName = $sBookerFirstName;
  }
  
  public function getBookerLastName()
  {
    return $this->sBookerLName;
  }
  
  public function setBookerLastName($sBookerLastName)
  {
    $this->sBookerLName = $sBookerLastName;
  }
  
  public function getWebsite()
  {
    return $this->sWebsite;
  }
  
  public function setWebsite($sWebsite)
  {
    $this->sWebsite = $sWebsite;
  }
  
  public function getAddress1()
  {
    return $this->sAddress1;
  }
  
  public function setAddress1($sAddress1)
  {
    $this->sAddress1 = $sAddress1;
  }
  
  public function getAddress2()
  {
    return $this->sAddress2;
  }
  
  public function setAddress2($sAddress2)
  {
    $this->sAddress2 = $sAddress2;
  }
  
  public function getCity()
  {
    return $this->sCity;
  }
  
  public function setCity($sCity)
  {
    $this->sCity = $sCity;
  }
  
  public function getState()
  {
    return $this->sState;
  }
  
  public function setState($sState)
  {
    $this->sState = $sState;
  }
  
  public function getZip()
  {
    return $this->sZip;
  }
  
  public function setZip($sZip)
  {
    $this->sZip = $sZip;
  }
  
  public function getCountry()
  {
    return $this->sCountry;
  }
  
  public function setCountry($sCountry)
  {
    $this->sCountry = $sCountry;
  }
};

<?php
require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');

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
  
  // information
  private $category = '';
  private $note = '';
  
  
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
   */
  public function setName( $sVenueName )
  {
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
  
  /**
   * Get the venue's notes
   * @return string
   */
  public function getNote()
  {
    return $this->note;
  }
  
  /**
   * Set the note
   * @param string $note
   * @throws InvalidArgumentException
   */
  public function setNote($note)
  {
    if(strlen($note) > 65535)
    {
      throw new InvalidArgumentException("Notes must be less than 65,535 characters long.");
    }
    $this->note = $note;
  }
  
  /**
   * Get the category
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  
  /**
   * Set category
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
}

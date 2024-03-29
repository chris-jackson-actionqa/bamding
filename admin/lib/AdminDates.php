<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminDates
 *
 * @author Seth
 */
class AdminDates
{
  private $oConn = null;
  private $sUserLogin = '';
  
  public function __construct($sUserLogin)
  {
    if(empty($sUserLogin))
    {
      throw new InvalidArgumentException('AdminDates requires valid user login');
    }
    
    $this->sUserLogin = $sUserLogin;
    $oDB = new Database();
    $this->oConn = $oDB->connect();
  }
  
  /**
   * Get all the entries for dates and timeframes for the user
   * @return associative array all user's dates
   * @throws Exception SQL error
   */
  public function getDatesTimeframes()
  {
    $sSQL = <<<SQL
SELECT * FROM booking_dates
WHERE user_login='{$this->sUserLogin}'
SQL;
    
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new Exception("Could not get dates and timeframes: " . $this->oConn->error);
    }
    
    //results for above query
    $hAll = Database::fetch_all($mResult);
    $mResult->close();
    return $hAll;
  }
  
  /**
   * The database has only one date per venue. Merge that together into a
   * new array keyed on the venue and the dates stored in an array.
   * 
   * @param map $hDatesAndTimeFrames results returned from database
   * @return map results keyed on venue id and dates stored in array
   *   result[venue_id#](dates, country, city, name, month_from, month_to)
   */
  private function mergeDifferentDates($hDatesAndTimeFrames)
  {
    $hMergedDatesTimes = array();
    $aKeys = array_keys($hDatesAndTimeFrames[0]);
    
    // for each venue, push all the dates into an array
    foreach($hDatesAndTimeFrames as $hRow)
    {
      $sVenueID = $hRow['id'];
      
      // create an array for dates if doesn't exist already
      if(!array_key_exists("$sVenueID", $hMergedDatesTimes))
      {
        $hMergedDatesTimes["$sVenueID"]['dates'] = array();
      }
      
      // if date is not empty, push the date
      if( !empty($hRow['date']))
      {
        array_push($hMergedDatesTimes["$sVenueID"]['dates'], $hRow['date']);
      }
      
      // copy over keys except for id and date
      foreach($aKeys as $sKey)
      {
        if('id' == $sKey || 'date' == $sKey)
        {
          continue;
        }
        $hMergedDatesTimes["$sVenueID"][$sKey] = $hRow[$sKey]; 
      }
    }
    
    return $hMergedDatesTimes;
  }
  
  public function getTimeframeGroups($hDatesTimeFrames)
  {
    $hTimeFrameGroups = array();
    
    foreach($hDatesTimeFrames as $sVenueID=>$hRow)
    {
      $sKey = $hRow['month_from'] . ' ' . $hRow['month_to'];
      
      foreach($hRow['dates'] as $sDate)
      {
        $sKey .= " $sDate";
      }
      
      if( !array_key_exists($sKey, $hTimeFrameGroups))
      {
        $hTimeFrameGroups[$sKey] = array();
      }
      array_push(
        $hTimeFrameGroups[$sKey],
        $sVenueID);
    }
    
    return $hTimeFrameGroups;
  }
  
  /**
   * Winter string representation
   */
  const WINTER = '0001-01-01';
  /**
   * Spring string representation
   */
  const SPRING = '0002-01-01';
  /**
   * Summer string representation
   */
  const SUMMER = '0003-01-01';
  /**
   * Fall string representation
   */
  const FALL = '0004-01-01';
  
  /**
   * Returns the month string (January, February,etc)
   * for the provided date.
   * Checks for Winter, Spring, Summer, and Fall, as well.
   * @param type $sDate date string like 2004-11-30
   * For Winter, '0001-01-01'
   * For Spring, '0002-01-01'
   * For Summer, '0003-01-01'
   * For Fall, '0004-01-01'
   * @return string the month or the college quarter
   */
  public function dateToMonth($sDate)
  {
    if(self::WINTER == $sDate)
    {
      return 'Winter';
    }
    
    if(self::SPRING == $sDate)
    {
      return 'Spring';
    }
    
    if(self::SUMMER == $sDate)
    {
      return 'Summer';
    }
    
    if(self::FALL == $sDate)
    {
      return 'Fall';
    }
    return date('F', strtotime($sDate));
  }
  
  const ALL = 'ALL';
  const COUNTRY = 'COUNTRY';
  const STATE = 'STATE';
  const CITY = 'CITY';
  const VENUE = 'VENUE';
  
  public function updateDatesTimeFrames(
          $sVenueRange, $aRangeValue, $sDateType, $aDates )
  {
    //TODO: save the current listings for the venues
    // ..this is to restore them if a problem occurs
    $nVenueRangeID = $this->getVenueRangeID($sVenueRange);
    $sCountry = (key_exists('country', $aRangeValue) && $sVenueRange != self::ALL) ? 
            trim($aRangeValue['country']) : '';
    $sState = (key_exists('state', $aRangeValue) && 
                ($sVenueRange === self::STATE || 
                 $sVenueRange === self::CITY || 
                 $sVenueRange === self::VENUE)
              ) ?
            trim($aRangeValue['state']) : '';
    $sCity = (key_exists('city', $aRangeValue) && 
               ($sVenueRange === self::CITY || 
                $sVenueRange === self::VENUE)
              ) ?
            trim($aRangeValue['city']) : '';
    $nVenueID = (key_exists('venue', $aRangeValue) && 
                $sVenueRange === self::VENUE
              ) ?
            (int)$aRangeValue['venue'] : -1;
    
    $nDateTypeID = (int)$this->getDateTypeID($sDateType);
    $sDateFrom = trim($this->getDateFrom($sDateType, $aDates));
    $sDateTo = trim($this->getDateTo($sDateType, $aDates));
    $sDates = trim($this->getDates($sDateType, $aDates));
    
    // delete conflicting bookings
    $this->deleteBookingDates($sVenueRange, $sCountry, $sState, $sCity, $nVenueID);
    
    $sSQL =<<<SQL
INSERT INTO booking_dates (
  user_login,
  venue_range,
  date_type,
  country,
  state,
  city,
  venue_id,
  date_from,
  date_to,
  dates
) VALUES (
  '{$this->sUserLogin}',
  '$nVenueRangeID',
  '$nDateTypeID',
  '$sCountry',
  '$sState',
  '$sCity',
  '$nVenueID',
  '$sDateFrom',
  '$sDateTo',
  '$sDates'
)
SQL;

    $mResult = $this->oConn->query($sSQL);
    if(FALSE === $mResult)
    {
      //TODO: restore old bookings
      //error_log($sSQL);
      //error_log('Trying to update booking_dates');
      throw new RuntimeException('Invalid SQL query: ' . $this->oConn->error . '\n' . $sSQL);
    }
    
    // delete the current listings for these venues from the database
    
    // insert the new values
    // get the type of update (timeframe, dates, or quarters)
    // get the appropriate values for 
    // timeframe
    // ..verify timeframe 'from' field
    // ..is 'to' field set?
    // ....verify 'to' field
    
    // on failure, restore the original values
    
  }
  
  public function deleteBookingDates(
          $sVenueRange, 
          $sCountry = '', 
          $sState = '', 
          $sCity = '', 
          $nVenueID = -1)
  {
    $mResult = null;
    $nVenueID = (int)$nVenueID; // sanitize venue id
    
    switch($sVenueRange)
    {
      case self::ALL:
        $sSQL = "DELETE FROM booking_dates WHERE user_login='$this->sUserLogin'";
        break;
      case self::COUNTRY:
        if(empty($sCountry))
        {
          throw new InvalidArgumentException("Country can't be an empty string.");
        }
        $sSQL = "DELETE FROM booking_dates WHERE user_login='$this->sUserLogin' AND " .
                "UPPER(TRIM(country))=UPPER(TRIM('$sCountry'))";
        break;
      case self::STATE:
        if(empty($sCountry) || empty($sState))
        {
          throw new InvalidArgumentException("Country or state can't be empty.");
        }
        $sSQL = "DELETE FROM booking_dates WHERE user_login='$this->sUserLogin' AND " .
              "UPPER(TRIM(country))=UPPER(TRIM('$sCountry')) AND " .
              "UPPER(TRIM(state))=UPPER(TRIM('$sState'))";
        break;
      case self::CITY:
        if(empty($sCountry) || empty($sState) || empty($sCity))
        {
          throw new InvalidArgumentException("Country, state, and city are required.");
        }
        $sSQL = "DELETE FROM booking_dates WHERE user_login='$this->sUserLogin' AND " .
              "UPPER(TRIM(country))=UPPER(TRIM('$sCountry')) AND " .
              "UPPER(TRIM(state))=UPPER(TRIM('$sState')) AND " .
              "UPPER(TRIM(city))=UPPER(TRIM('$sCity'))";
        break;
      case self::VENUE:
        if(empty($sCountry) || empty($sState) || empty($sCity) || empty($nVenueID))
        {
          throw new InvalidArgumentException("Country, state, city, and venue id are required.");
        }
        $sSQL = "DELETE FROM booking_dates WHERE user_login='$this->sUserLogin' AND " .
              "UPPER(TRIM(country))=UPPER(TRIM('$sCountry')) AND " .
              "UPPER(TRIM(state))=UPPER(TRIM('$sState')) AND " .
              "UPPER(TRIM(city))=UPPER(TRIM('$sCity')) AND " .
              "venue_id=$nVenueID";
        break;
      default:
        throw new InvalidArgumentException("Invalid venue range: $sVenueRange");
        break;
    }
    
    $mResult = $this->oConn->query($sSQL);
    if(FALSE === $mResult)
    {
      //error_log($sSQL);
      //error_log('Trying to delete booking_dates');
      throw new RuntimeException('Unexpected SQL error: ' . $this->oConn->error);
    }
  }
  
  public function getVenueRangeID($sVenueRange)
  {
    $sSQL = "SELECT id FROM venue_range WHERE value='$sVenueRange'";
    $mResult = $this->oConn->query($sSQL);
    if(FALSE === $mResult)
    {
      throw new RuntimeException('Unknown SQL error: ' . $this->oConn->error);
    }
    
    if( 1 != $mResult->num_rows)
    {
      throw new InvalidArgumentException('Invalid venue range: ' . $sVenueRange);
    }
    
    $aRow = $mResult->fetch_row();
    $mResult->close();
    return $aRow[0];
  }
  
  /**
   * Retrieves the string value of the venue range given the ID
   * @param int $nVenueRange valid venue range ID
   * @return string Venue range string
   * @throws RuntimeException on SQL error
   * @throws InvalidArgumentException if no results found for nVenueRange
   */
  public function getVenueRangeFromID($nVenueRange)
  {
    $nVenueRange = (int)$nVenueRange;
    $sSQL = "SELECT value FROM venue_range WHERE id=$nVenueRange";
    $mResult = $this->oConn->query($sSQL);
    if(FALSE === $mResult)
    {
      throw new RuntimeException('Unknown SQL error: ' . $this->oConn->error);
    }
    
    if( 1 != $mResult->num_rows)
    {
      throw new InvalidArgumentException('Invalid venue range id: ' . $nVenueRange);
    }
    
    $aRow = $mResult->fetch_row();
    return $aRow[0];
  }
  
  public function getDateTypeID($sDateType)
  {
    $sSQL = "SELECT id FROM date_type WHERE type='$sDateType'";
    $mResult = $this->oConn->query($sSQL);
    if(FALSE === $mResult)
    {
      throw new RuntimeException('Unknown SQL error: ' . $this->oConn->error);
    }
    
    if( 1 != $mResult->num_rows)
    {
      throw new InvalidArgumentException('Invalid date type: ' . $sDateType);
    }
    
    $aRow = $mResult->fetch_row();
    return $aRow[0];
  }
  
  const TIMEFRAME='TIMEFRAME';
  const CUSTOMRANGE='CUSTOMRANGE';
  const QUARTERRANGE='QUARTERRANGE';
  const DATES='DATES';
  
  private function getDateFrom($sDateType, $aDates)
  {
    $this->throwOnEmpty($sDateType, "Date Type can't be empty");
    $this->throwOnEmpty($aDates, "Dates array can't be empty.");
    
    $sFrom = '';
    
    switch($sDateType)
    {
      case self::TIMEFRAME:
        // verify month isn't in the past
        // floor date to beginning of month
        $sFlooredDate = $this->floorDateToMonth($aDates[0]);
        
        // floor today's date to beginning of the month
        $sThisMonth = $this->floorDateToMonth(date('Y-m-d'));
        
        // if the passed in date is less than this month,
        // throw invalid argument exception
        // else, set the from value with the date
        $oDateFloored = new DateTime($sFlooredDate);
        $oThisMonth = new DateTime($sThisMonth);
        if( $oDateFloored < $oThisMonth)
        {
          throw new InvalidArgumentException("'From' Month is in the past: " . $sFlooredDate);
        }
        $sFrom = $sFlooredDate;
        break;
      case self::CUSTOMRANGE:
        if(2 != count($aDates))
        {
          throw new InvalidArgumentException('Need both from and to dates');
        }
        $oToday = new DateTime();
        $oFrom = new DateTime($aDates[0]);
        if($oToday >= $oFrom)
        {
          throw new InvalidArgumentException('from date is in the past');
        }
        
        $sFrom = $oFrom->format('Y-m-d');
        break;
      case self::QUARTERRANGE:
        // array of dates must be one or two dates exactly
        if(1 != count($aDates) && 2 != count($aDates))
        {
          throw new InvalidArgumentException("Need only one or two dates for the Quarter range.");
        }
        
        // TODO: verify dates in proper "QUARTER" format
        // return from quarter range
        $sFrom = $aDates[0];
        break;
      case self::DATES:
        $sFrom = '';
        break;
      default:
        throw new InvalidArgumentException("Not valid date type: $sDateType");
        break;
    }
    
    return $sFrom;
  }
  
  private function getDateTo($sDateType, $aDates)
  {
    $this->throwOnEmpty($sDateType, "Date Type can't be empty");
    $this->throwOnEmpty($aDates, "Dates array can't be empty.");
    
    $sTo = '';
    
    if(1 == count($aDates))
    {
      return $sTo;
    }
    
    switch($sDateType)
    {
      case self::TIMEFRAME:
        // verify month isn't less than month 'from'
        // floor date to beginning of month
        $sFlooredDateTo = $this->floorDateToMonth($aDates[1]);
        
        // get month 'from'
        $sFromMonth = $this->getDateFrom($sDateType, $aDates);
        
        $oDateTo = new DateTime($sFlooredDateTo);
        $oDateFrom = new DateTime($sFromMonth);
        if( $oDateTo < $oDateFrom)
        {
          throw new InvalidArgumentException("'To' Month is before the 'From' Month: " . $sFlooredDateTo);
        }
        $sTo = $sFlooredDateTo;
        break;
      case self::CUSTOMRANGE:
        if(2 != count($aDates))
        {
          throw new InvalidArgumentException('Need both from and to dates');
        }
        
        $oFrom = new DateTime($aDates[0]);
        $oTo = new DateTime($aDates[1]);
        if($oTo <= $oFrom)
        {
          throw new InvalidArgumentException('The to date needs to be later than the from date');
        }
        
        $sTo = $oTo->format('Y-m-d');
        break;
      case self::QUARTERRANGE:
        if(2 != count($aDates))
        {
          break;
        }
        //TODO: verify date is in proper "quarter" format
        $sTo = $aDates[1];
        break;
      case self::DATES:
        $sTo = '';
        break;
      default:
        throw new InvalidArgumentException("Not valid date type: $sDateType");
        break;
    }
    
    return $sTo;
  }
  
  /**
   * Floors a date to the beginning of the month.
   * For example, 2014-09-30 would be floored to 2014-09-01.
   * 
   * @param string $sDate Date to floor. Needs to be in format YYYY-MM-DD
   * @return string Date floored to beginning of month in format YYYY-MM-DD
   * @throws InvalidArgumentException Date paramater needs to be valid.
   */
  private function floorDateToMonth($sDate)
  {
    $this->throwOnEmpty($sDate, "Need date to floor.");
    if(!preg_match('/\d\d\d\d-\d\d-\d\d/', $sDate))
    {
      throw new InvalidArgumentException("Invalid date format: " . $sDate);
    }
    
    $aFormat = date_parse_from_format( "Y-m-d", $sDate);
    return $aFormat['year'] . '-' . $aFormat['month'] . '-01';
  }
  
  private function throwOnEmpty($mParam, $sErrorMessage, $sExceptionType = 'InvalidArgumentException')
  {
    if(empty($mParam))
    {
      throw new $sExceptionType($sErrorMessage);
    }
  }
  
  /**
   * Converts the date array into a single, comma-delimited string
   * 
   * @param string $sDateType Date type. If not AdminDates::DATES, just returns.
   * @param array $aDates array of dates
   * @return string of dates
   */
  private function getDates($sDateType, $aDates)
  {
    $this->throwOnEmpty($sDateType, "Date type can't be empty");
    $this->throwOnEmpty($aDates, "Dates array can't be empty");
    
    if( $sDateType != self::DATES)
    {
      return '';
    }
    
    $sDates = "";
    foreach($aDates as $sDate)
    {
      if(empty($sDates))
      {
        $sDates = $sDate;
      }
      else
      {
        $sDates .= ",$sDate";
      }
    }
    
    return $sDates;
  }
  
  public function getExistingDates($sVenueRange, $aRangeValue, $sDateType)
  {
    $this->throwOnEmpty($sVenueRange, "Venue range can't be empty");
    $this->throwOnEmpty($sDateType, "Date type can't be empty");
    
    if(self::DATES == $sDateType)
    {
      throw new InvalidArgumentException("Date type not implemented.");
    }
    
    switch($sVenueRange)
    {
      case self::ALL:
        break;
      default:
        throw new InvalidArgumentException("Venue range not implemented.");
        break;
    }
    
    $nVenueRangeID = $this->getVenueRangeID($sVenueRange);
    $nDateType = $this->getDateTypeID($sDateType);
    
    $sSQL=<<<SQL
SELECT dates 
FROM booking_dates
WHERE 
  user_login='{$this->sUserLogin}' AND
  venue_range=$nVenueRangeID AND
  date_type=$nDateType
SQL;
  
    $mResult = $this->oConn->query($sSQL);
    if(FALSE === $mResult)
    {
      throw new RuntimeException("Bad sql query.");
    }
    
    $aRow = $mResult->fetch_assoc();
    return split(",", $aRow['dates']);
  }
  
  const NEWLINE = 'NEWLINE';
  const DATES_FORMAT = 'DATES_FORMAT';
  const TIMEFRAME_FORMAT = 'TIMEFRAME_FORMAT';
  
  /**
   * Return a 'booking script' friendly dates format complete with NEWLINE 
   * for multiple dates.
   * @param int $nVenueID
   * @return string either the date string or empty string if not found
   * @todo more elegant solution. Can do one for loop instead of multiple
   */
  public function getDatesFromVenueID($nVenueID, $sDateOrTimeFrame = self::DATES_FORMAT)
  {
    $hDatesTimeFrames = $this->getDatesTimeframes();
    
    // start from most specific to least
    // if venue id specified, return those dates
    foreach($hDatesTimeFrames as $hRow)
    {
      if($hRow['venue_range'] == $this->getVenueRangeID(self::VENUE)&&
         $nVenueID === $hRow['venue_id'])
      {
        if( self::DATES_FORMAT === $sDateOrTimeFrame)
        {
          return AdminDisplay::datesDisplayDatesTimeFramesForVenueRange(
                  $this->sUserLogin,
                  $hRow,
                  self::NEWLINE);
        }
        else if( self::TIMEFRAME_FORMAT === $sDateOrTimeFrame)
        {
          return AdminDisplay::datesDisplayOnlyTimeFrameFromVenueRange(
                  $this->sUserLogin,
                  $hRow);
        }
      }
    }
    
    // get the venue's details
    $oVenues = new Venues('my_venues', $this->sUserLogin);
    $oVenue = $oVenues->getVenue($nVenueID);
    $sCity = strtoupper(trim($oVenue->getCity()));
    $sState = strtoupper(trim($oVenue->getState()));
    $sCountry = strtoupper(trim($oVenue->getCountry()));
    
    // if city specified, return those dates
    foreach($hDatesTimeFrames as $hRow)
    {
      $sRowCity = strtoupper(trim($hRow['city']));
      $sRowState = strtoupper(trim($hRow['state']));
      $sRowCountry = strtoupper(trim($hRow['country']));
      if($hRow['venue_range'] == $this->getVenueRangeID(self::CITY)&&
         $sCountry === $sRowCountry &&
         $sState === $sRowState &&
         $sCity === $sRowCity)
      {
        if( self::DATES_FORMAT === $sDateOrTimeFrame)
        {
          return AdminDisplay::datesDisplayDatesTimeFramesForVenueRange(
                  $this->sUserLogin,
                  $hRow,
                  self::NEWLINE);
        }
        else if( self::TIMEFRAME_FORMAT === $sDateOrTimeFrame)
        {
          return AdminDisplay::datesDisplayOnlyTimeFrameFromVenueRange(
                  $this->sUserLogin,
                  $hRow);
        }
      }
    }
    
    // if state specified, return those dates
    foreach($hDatesTimeFrames as $hRow)
    {
      $sRowState = strtoupper(trim($hRow['state']));
      $sRowCountry = strtoupper(trim($hRow['country']));
      if($hRow['venue_range'] == $this->getVenueRangeID(self::STATE)&&
         $sCountry === $sRowCountry &&
         $sState === $sRowState)
      {
        if( self::DATES_FORMAT === $sDateOrTimeFrame)
        {
          return AdminDisplay::datesDisplayDatesTimeFramesForVenueRange(
                  $this->sUserLogin,
                  $hRow,
                  self::NEWLINE);
        }
        else if( self::TIMEFRAME_FORMAT === $sDateOrTimeFrame)
        {
          return AdminDisplay::datesDisplayOnlyTimeFrameFromVenueRange(
                  $this->sUserLogin,
                  $hRow);
        }
      }
    }
    
    // if country specified, return those dates
    foreach($hDatesTimeFrames as $hRow)
    {
      $sRowCountry = strtoupper(trim($hRow['country']));
      if($hRow['venue_range'] == $this->getVenueRangeID(self::COUNTRY)&&
         $sCountry === $sRowCountry)
      {
        if( self::DATES_FORMAT === $sDateOrTimeFrame)
        {
          return AdminDisplay::datesDisplayDatesTimeFramesForVenueRange(
                  $this->sUserLogin,
                  $hRow,
                  self::NEWLINE);
        }
        else if( self::TIMEFRAME_FORMAT === $sDateOrTimeFrame)
        {
          return AdminDisplay::datesDisplayOnlyTimeFrameFromVenueRange(
                  $this->sUserLogin,
                  $hRow);
        }
      }
    }
    
    // if all venues specified, return those dates
    foreach($hDatesTimeFrames as $hRow)
    {
      if($hRow['venue_range'] == $this->getVenueRangeID(self::ALL))
      {
        if( self::DATES_FORMAT === $sDateOrTimeFrame)
        {
          return AdminDisplay::datesDisplayDatesTimeFramesForVenueRange(
                  $this->sUserLogin,
                  $hRow);
        }
        else if( self::TIMEFRAME_FORMAT === $sDateOrTimeFrame)
        {
          return AdminDisplay::datesDisplayOnlyTimeFrameFromVenueRange(
                  $this->sUserLogin,
                  $hRow,
                  self::NEWLINE);
        }
      }
    }
    
    return 'NO_DATES';
  }
  
  /**
   * Return a 'booking script' friendly timeframe format.
   * If the venue has dates like October 17 and November 13th,
   * this function will return 'October through November'.
   * 
   * @param type $nVenueID
   * @return type
   */
  public function getTimeFrameFromVenueID($nVenueID)
  {
    return $sTimeFrame;
  }
}

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
  
  public function __construct()
  {
    $oDB = new Database();
    $this->oConn = $oDB->connect();
  }
  
  public function getDatesTimeframes($sUser)
  {
    //verify user
    if(empty($sUser))
    {
      throw new InvalidArgumentException('Need a valid user');
    }
    
    //drop the view if not previously dropped
    $this->oConn->query('DROP VIEW IF EXISTS DatesAndTimeframes');
    
    //create a view of a join on the venues and the dates/timeframes tables
    $sSQL = <<<SQL
CREATE VIEW DatesAndTimeframes AS
SELECT 
  my_venues.user_login, 
  my_venues.id, 
  my_venues.country,
  my_venues.state,
  my_venues.city, 
  my_venues.name,
  booking_dates.month_from,
  booking_dates.month_to, 
  booking_dates.date
FROM my_venues
INNER JOIN booking_dates
ON booking_dates.venue_id=my_venues.id
WHERE my_venues.user_login='$sUser'
ORDER BY my_venues.country,my_venues.state,my_venues.city,my_venues.name;
SQL;
    
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new Exception("Could not create view of dates and timeframes");
    }
    
    //get country, state, city's dates and timeframes
    $sSQL = <<<SQL
SELECT country,state,city,id,name, month_from,month_to,date
FROM DatesAndTimeframes
INNER JOIN bookings ON bookings.venue_id=DatesAndTimeframes.id
WHERE bookings.pause=0
ORDER BY country, state, city, name;
SQL;
    
    $mResult = $this->oConn->query($sSQL);
    
    if(FALSE === $mResult)
    {
      throw new Exception("Could not get dates and timeframes");
    }
    
    //results for above query
    $hResults = Database::fetch_all($mResult);
    
    //drop the view
    $this->oConn->query('DROP VIEW IF EXISTS DatesAndTimeframes');
    
    return $this->mergeDifferentDates($hResults);
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
  
  const ALL = 'all';
  
  public function updateDatesTimeFrames(
          $sUser, $sVenueRange, $sDateType, $aDates )
  {
    if(empty($sUser))
    {
      return;
    }
    
    // save the current listings for the venues
    // ..this is to restore them if a problem occurs
    
    switch($sVenueRange)
    {
      case self::ALL:
        //delete all current listings for the user
        //
        break;
      default:
        break;
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
}

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
    
    //get distinct country, state, city's dates and timeframes
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
    
    return $hResults;
  }
  
  public function getTimeframeGroups($hDatesTimeFrames)
  {
    $hTimeFrameGroups = array();
    
    foreach($hDatesTimeFrames as $hRow)
    {
      $sKey = $hRow['month_from'] . ' ' . $hRow['month_to'];
      if( !array_key_exists($sKey, $hTimeFrameGroups))
      {
        $hTimeFrameGroups[$sKey] = array();
      }
      array_push(
        $hTimeFrameGroups[$sKey],
        $hRow['id']);
    }
    
    return $hTimeFrameGroups;
  }
}

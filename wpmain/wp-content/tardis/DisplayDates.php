<?php

/**
 * Displays the html for the dates page.
 *
 * @author Chris Jackson
 */
class DisplayDates {
  public static function showDatesForm($sUserLogin){
    $sVenueRange = (key_exists('venue_range', $hPost))
      ? $hPost['venue_range'] : BookingDates::ALL;
    
    $aVenueRangeValues = array();
    if(key_exists('value_range_country', $hPost))
    {
      $aVenueRangeValues['country'] = $hPost['value_range_country'];
    }
    
    $sTimeFrameFrom = (key_exists('month_from', $hPost))
      ? $hPost['month_from'] : '';
    $sTimeFrameTo = (key_exists('month_to', $hPost))
      ? $hPost['month_to'] : '';
    $sCustomFrom = (key_exists('custom_from', $hPost))
      ? $hPost['custom_from'] : '';
    $sCustomTo = (key_exists('custom_to', $hPost))
      ? $hPost['custom_to'] : '';
    $sQuarterFrom = (key_exists('quarter_from', $hPost))
      ? $hPost['quarter_from'] : '';
    $sQuarterTo = (key_exists('quarter_to', $hPost))
      ? $hPost['quarter_to'] : '';
    
    echo '<h2>' . $sUserLogin . '</h2>';
    
//    $sMessage = self::processDatesForm($hGet,$hPost);
//    if(!empty($sMessage))
//    {
//      echo "<h3>Message:</h3>" . $sMessage . '<br /><br />';
//    }
//    
//    // reset venue range to all on success
//    if( $sMessage === 'Successfully updated.')
//    {
//      $sVenueRange = 'ALL';
//    }
    
    // displays the dates and timeframes user already has set
    self::displayDatesTimeFrames($sUserLogin);
    //AdminDisplayDates::showEditDatesTimeframes();
    
    // form for applying dates
//    echo '<h3>Update:</h3>';
//    echo '<form method="post" action="admin-dates.php?user_login='. $hGet['user_login'] . '">';
//    
//    self::datesVenueRangeSelect($sVenueRange);
//    self::datesValueRangeValues($sVenueRange, $aVenueRangeValues);
//    self::datesInputTimeFrame($sVenueRange);
//    self::datesInputCustomRange($sVenueRange, $sCustomFrom, $sCustomTo);
//    self::datesInputQuarterRange($sVenueRange, $sQuarterFrom, $sQuarterTo);
//    self::datesInputDates($sVenueRange);
  }
  
  public static function displayDatesTimeFrames($sUser)
  {
    // dates and time frames
    echo '<div id="datesAndTimeFrames" <h2>Dates and TimeFrames:</h2><br />';
    
    $oBookingDates = new BookingDates($sUser);
    $hDates = $oBookingDates->getDatesTimeframes($sUser);
    if(0 == count($hDates))
    {
      echo 'No dates or time frames.<br />'."\n";
      return;
    }
    $aKeys = array_keys($hDates[0]);
    
    // Display 'all' venues timeframe
    $nCount = count($hDates);
    $nAllVenueIndex = -1;
    $nALLVenues = $oBookingDates->getVenueRangeID(BookingDates::ALL);
    $nCountry = $oBookingDates->getVenueRangeID(BookingDates::COUNTRY);
    $nState = $oBookingDates->getVenueRangeID(BookingDates::STATE);
    $nCity = $oBookingDates->getVenueRangeID(BookingDates::CITY);
    $nVenue = $oBookingDates->getVenueRangeID(BookingDates::VENUE);
    for($nIndex = 0; $nIndex < $nCount; ++$nIndex)
    {
      if($hDates[$nIndex]['venue_range'] === $nALLVenues)
      {
        echo "<span class='venueRange'>All Venues:</span><br />\n";
      }
      else if($hDates[$nIndex]['venue_range'] === $nCountry)
      {
        echo "<span class='venueRange'>".$hDates[$nIndex]['country'] . 
                ":</span><br />\n";
      }
      else if($hDates[$nIndex]['venue_range'] === $nState)
      {
        echo "<span class='venueRange'>".
             $hDates[$nIndex]['state'] . ", " . 
             $hDates[$nIndex]['country'] .
             ":</span><br />\n";
      }
      else if($hDates[$nIndex]['venue_range'] === $nCity)
      {
        echo "<span class='venueRange'>".
             $hDates[$nIndex]['city'] . ", " . 
             $hDates[$nIndex]['state'] . ", " . 
             $hDates[$nIndex]['country'] .
             ":</span><br />\n";
      }
      else if($hDates[$nIndex]['venue_range'] === $nVenue)
      {
        $oVenues = new Venues('my_venues', $sUser);
        $oVenue = $oVenues->getVenue((int)$hDates[$nIndex]['venue_id']);
        echo "<span class='venueRange'>".
             $oVenue->getName() . ", " . 
             $hDates[$nIndex]['city'] . ", " . 
             $hDates[$nIndex]['state'] . ", " . 
             $hDates[$nIndex]['country'] .
             ":</span><br />\n";
      }
      self::displayDatesTimeFramesForVenueRange($sUser, $hDates[$nIndex], true);
    }
    echo '</div><br />'."\n";
  }
  
  const INDENT='&nbsp;&nbsp;&nbsp;&nbsp;';  
  
  /**
   * Display the dates/timeframes.
   * Displays timeframes and individual dates.
   * Doesn't display things like "All Venues", country, state, city, etc.
   * 
   * @param string $sUser username to init the BookingDates object. TODO: remove
   * @param hash $hDates map of dates/timeframes
   * @param boolean $bIndent indent the listings?
   * @throws InvalidArgumentException unknown date/timeframe type
   * @todo refactor the switch cases. Move to seperate functions
   */
  public static function 
    displayDatesTimeFramesForVenueRange($sUser, $hDates, $bIndent = false)
  {
    $sIndent = $bIndent ? self::INDENT : '';
    
    // get date type
    $oBookingDates = new BookingDates($sUser);
    $nTimeframeID = $oBookingDates->getDateTypeID(BookingDates::TIMEFRAME);
    $nCustomRangeID = $oBookingDates->getDateTypeID(BookingDates::CUSTOMRANGE);
    $nCollegeID = $oBookingDates->getDateTypeID(BookingDates::QUARTERRANGE);
    $nDatesID = $oBookingDates->getDateTypeID(BookingDates::DATES);

    // display timeframe
    switch ($hDates['date_type'])
    {
      case $nTimeframeID:
        // get from month
        $nTimestamp = strtotime($hDates['date_from']);
        $sMonth = date('F', $nTimestamp);
        echo $sIndent . $sMonth;

        // get to month
        if('0000-00-00' !== $hDates['date_to'])
        {
          $nTimestamp = strtotime($hDates['date_to']);
          $sMonth = date('F', $nTimestamp);
          echo ' through ' . $sMonth;
        }
        echo '<br />';
        break;
      case $nCustomRangeID:
        // get from month
        $nTimestamp = strtotime($hDates['date_from']);
        $sMonth = date('F j, Y', $nTimestamp);
        echo $sIndent . $sMonth;

        // get to month
        if('0000-00-00' !== $hDates['date_to'])
        {
          $nTimestamp = strtotime($hDates['date_to']);
          $sMonth = date('F j, Y', $nTimestamp);
          echo '<br />'.$sIndent.'through <br /> ' . 
                  $sIndent . $sMonth;
        }
        echo '<br />';
        break;
      case $nCollegeID:
        // get 'from' quarter
        $sQuarter = '';
        switch($hDates['date_from'])
        {
          case BookingDates::FALL:
            $sQuarter = 'Fall';
            break;
          case BookingDates::WINTER:
            $sQuarter = 'Winter';
            break;
          case BookingDates::SPRING:
            $sQuarter = 'Spring';
            break;
          case BookingDates::SUMMER:
            $sQuarter = 'Summer';
            break;
        }
        echo $sIndent . $sQuarter;

        // get to month
        if('0000-00-00' !== $hDates['date_to'])
        {
          $sQuarter = '';
          switch($hDates['date_to'])
          {
            case BookingDates::FALL:
              $sQuarter = 'Fall';
              break;
            case BookingDates::WINTER:
              $sQuarter = 'Winter';
              break;
            case BookingDates::SPRING:
              $sQuarter = 'Spring';
              break;
            case BookingDates::SUMMER:
              $sQuarter = 'Summer';
              break;
          }
          echo ' through ' . $sQuarter;
        }
        echo '<br />';
        break;
      case $nDatesID:
        $sDates = $hDates['dates'];
        // split the string to an array of dates
        $aDates = split(',', $sDates);

        // find all the months to group by
        $aMonths = array();
        foreach($aDates as $sDate)
        {
          // get month of entry
          $sMonth = date('F', strtotime($sDate));
          array_push($aMonths, $sMonth);
        }
        // make sure no duplicate months
        $aMonths = array_unique($aMonths);

        // for each month, print the dates belonging to that month
        foreach($aMonths as $sMonth)
        {
          echo "$sIndent<strong>$sMonth</strong>:<br /><span class='seriesOfDates'>\n";
          $sDay = '';
          $iDays = 0;
          foreach($aDates as $sDate)
          {
            $sMonthOfDate = date('F', strtotime($sDate));
            if(strtoupper($sMonth) === strtoupper($sMonthOfDate))
            {
              $sDay .= date('jS', strtotime($sDate)) . ", ";
            }
          }
          $sDay = chop($sDay, "<br />$sIndent");
          $sDay = chop($sDay, ', ');
          echo $sDay;
          echo "</span><br />";
          
        }
        break;
      default:
        throw new InvalidArgumentException("Unknown date type in dates");
        break;
    }
  }
}

<?php

/**
 * Displays the html for the dates page.
 *
 * @author Chris Jackson
 */
class DisplayDates {
  /**
   * show the dates form
   * Shows the user friendly dates.
   * Allows user to add their own dates.
   * @param string $sUserLogin logged in user
   */
  public static function showDatesForm($sUserLogin, $hGet, $hPost){
    ?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
<script src="<?php echo Site::getBaseURL()?>/wp-content/js/bookings.js"></script>
<script>
  $( window ).load(
  $(function() {
    initDatesDatePickers();
    }));
</script>
    <?php
    
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
    
    $sMessage = self::processDatesForm($hGet,$hPost);
    if(!empty($sMessage))
    {
      echo "<h3>Message:</h3>" . $sMessage . '<br /><br />';
    }
    
    // reset venue range to all on success
    if( $sMessage === 'Successfully updated.')
    {
      $sVenueRange = 'ALL';
    }
    
    // displays the dates and timeframes user already has set
    self::displayDatesTimeFrames($sUserLogin);
    //AdminDisplayDates::showEditDatesTimeframes();
    
    // form for applying dates
    echo '<h3>Update:</h3>';
    echo '<form id="datesForm" method="post" action="admin-dates.php?user_login='. $sUserLogin . '">';
    
    self::datesVenueRangeSelect($sVenueRange);
    self::datesValueRangeValues($sVenueRange, $aVenueRangeValues);
    self::datesInputTimeFrame($sVenueRange);
    self::datesInputCustomRange($sVenueRange, $sCustomFrom, $sCustomTo);
    self::datesInputQuarterRange($sVenueRange, $sQuarterFrom, $sQuarterTo);
    self::datesInputDates($sVenueRange);
    echo '<br />';
    echo '<input type="submit" value="Update">';
    echo '</form>';
  }
  
  public static function displayDatesTimeFrames($sUser)
  {
    // dates and time frames
    echo '<div id="datesAndTimeFrames"> <h2>Dates and TimeFrames:</h2><br />';
    
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
  
  /**
   * Process the request data sent from the admin-dates form
   * 
   * @param map $hGet $_GET
   * @param map $hPost $_POST
   * @return no type. just returns early if no data to process
   * @throws InvalidArgumentException
   */
  public static function processDatesForm($hGet, $hPost)
  {
    // user_login needs to exist
    if( !key_exists('user_login', $hGet) || 
        !key_exists('venue_range', $hPost) ||
        !key_exists('date_type', $hPost))
    {
      return '';
    }
    
    $sVenueRange = $hPost['venue_range'];
    
    // if venue range isn't all, but country doesn't exist, throw exception
    if(BookingDates::ALL != $sVenueRange && !key_exists('venue_range_country', $hPost))
    {
      return 'ERROR: Country not selected.';
    }
    
    // if venue range is state, state range needs to exist
    if((BookingDates::STATE === $sVenueRange || BookingDates::CITY === $sVenueRange || 
        BookingDates::VENUE === $sVenueRange) && 
        !key_exists('venue_range_state', $hPost))
    {
      return 'ERROR: State not selected';
    }
    
    // if venue range is city or venue, city range needs to exist
    if((BookingDates::CITY === $sVenueRange || BookingDates::VENUE === $sVenueRange) && 
        !key_exists('venue_range_city', $hPost))
    {
      return 'ERROR: City not selected';
    }
    
    // if venue range is venue, venue range needs to exist
    if(BookingDates::VENUE === $sVenueRange && !key_exists('venue_range_venue', $hPost))
    {
      return 'ERROR: Venue not selected';
    }
    
    // add range values to the array
    $aRangeValue = array();
    if(key_exists('venue_range_country', $hPost))
    {
      $aRangeValue['country'] = $hPost['venue_range_country'];
    }
    
    if(key_exists('venue_range_state', $hPost))
    {
      $aRangeValue['state'] = $hPost['venue_range_state'];
    }
    
    if(key_exists('venue_range_city', $hPost))
    {
      $aRangeValue['city'] = $hPost['venue_range_city'];
    }
    
    if(key_exists('venue_range_venue', $hPost))
    {
      $aRangeValue['venue'] = $hPost['venue_range_venue'];
    }
    
    // get date type
    $sDateType = key_exists('date_type',$hPost) ? $hPost['date_type'] : '';
    
    $aDates = array();
    
    switch($sDateType)
    {
      case BookingDates::TIMEFRAME:
        if(!key_exists('month_from', $hPost))
        {
          return 'ERROR: "Month From" is missing.';
        }
        
        // get month_from,
        $nMonthFrom = (int)$hPost['month_from'];
        
        // get today's month and year
        $aToday = date_parse_from_format('Y-m-d', date('Y-m-d'));
        $nYearFrom = $aToday['year'];
        // if less than this month, bump up to next year
        if($nMonthFrom < $aToday['month'])
        {
          $nYearFrom++;
        }
        $sMonth = ($nMonthFrom<10) ? "0$nMonthFrom" : "$nMonthFrom";
        array_push($aDates, "$nYearFrom-$sMonth-01");
        
        // check month to
        $nMonthTo = (key_exists('month_to', $hPost)) ? (int)$hPost['month_to'] : 0;
        $nYearTo = $nYearFrom;
        if(0 != $nMonthTo)
        {
          // month_to is set
          if($nMonthTo <= $nMonthFrom)
          {
            $nYearTo++;
          }
          
          $sMonth = ($nMonthTo<10) ? "0$nMonthTo" : "$nMonthTo";
          array_push($aDates, "$nYearTo-$sMonth-01");
        }
        break;
        
      case BookingDates::CUSTOMRANGE:
        if(!key_exists('custom_from', $hPost) || !key_exists('custom_to', $hPost))
        {
          return 'ERROR: Need both custom range dates.';
        }
        
        $sDateFrom = $hPost['custom_from'];
        $sDateTo = $hPost['custom_to'];
        if(empty($sDateFrom) || empty($sDateTo))
        {
          return 'ERROR: Need both custom range dates.';
        }
        
        // make sure the 'from' date is in the future
        $oDateToday = new DateTime();
        $oDateFrom = new DateTime($sDateFrom);
        if($oDateFrom <= $oDateToday)
        {
          return 'ERROR: Date "from" needs to be in the future.';
        }
        
        // make sure the to date comes after the from date
        $oDateTo = new DateTime($sDateTo);
        if($oDateFrom >= $oDateTo)
        {
          return 'ERROR: The "to" date needs to be later than the "from" date.';
        }
        
        // convert dates to mysql friendly format
        // add to dates array
        array_push($aDates, $oDateFrom->format('Y-m-d'));
        array_push($aDates, $oDateTo->format('Y-m-d'));
        break;
      case BookingDates::QUARTERRANGE:
        if(!key_exists('quarter_from', $hPost))
        {
          return 'ERROR: "Quarter From" is required.';
        }
        
        // "Quarter From" is required and can't be empty
        // if not empty, push on to dates array
        $sQuarterFrom = $hPost['quarter_from'];
        if(empty($sQuarterFrom))
        {
          return 'ERROR: "Quarter From" cannot be empty';
        }
        else
        {
          array_push($aDates, $sQuarterFrom);
        }
        
        // "Quarter To" is optional
        $sQuarterTo = (key_exists('quarter_to', $hPost))
                ?$hPost['quarter_to'] : "";
        if(!empty($sQuarterTo))
        {
          array_push($aDates, $sQuarterTo);
        }
        break;
        
      case BookingDates::DATES:
        if(!key_exists('dates_list', $hPost) || empty($hPost['dates_list']))
        {
          return "ERROR: The list of dates is missing or empty.";
        }
        
        // convert the string of dates into a dates array
        $aDates = split(",",$hPost['dates_list']);
        break;
      
      default:
        throw new InvalidArgumentException("Unrecognized venue range: " + sDateType);
        break;
    }
    
    try
    {
    $oBookingDates = new BookingDates($hGet['user_login']);
    $oBookingDates->updateDatesTimeFrames($hPost['venue_range'], $aRangeValue, $sDateType, $aDates);
    mail( "seth@bamding.com", 
          "Dates changed for ".$hGet['user_login'], 
          "Dates changed for ".$hGet['user_login']);
    }
    catch(Exception $ex)
    {
      return $ex->getMessage();
    }
    
    return 'Successfully updated.';
  }

  /**
   * Venue range selector
   * @param string $sVenueRange
   */
  public static function datesVenueRangeSelect($sVenueRange='')
  {
    // hash map of option's values and text
    $hOptions = array(
        'ALL'=>'All venues',
        'COUNTRY'=>'Country',
        'STATE'=>'State',
        'CITY'=>'City',
        'VENUE'=>'Venue'
    );
    
    // if POST contains venue_range, get the value to check against
    // in the for loop
    $sRangeSelected = (!empty($sVenueRange)) ? $sVenueRange : 'ALL';
    
    // Begin the venue range selection
    echo '<label>Update dates/timeframes for </label>';
    echo '<select id="selectVenueRange" name="venue_range">';
    
    // echo out the options
    // default checked the appropriate option
    foreach($hOptions as $sRange=>$sText)
    {
      $sSelected = '';
      if($sRange == $sRangeSelected)
      {
        $sSelected = 'selected';
      }
      
      echo "<option value='$sRange' $sSelected>$sText</option>";
    }
    
    // end the selection
    echo '</select>';
  }

  public static function datesValueRangeValues($sVenueRange='', $aValues=array())
  {
    $sStyle = '';
    // if venue range is empty or 'ALL', return
    if(empty($sVenueRange) || BookingDates::ALL === $sVenueRange)
    {
      $sStyle = 'style="display: none;"';
    }
    
    // create dropdowns for country, state, city, and venues
    // hide the ones that aren't applicable
    // example, if 'country' is the range value, no need to show state, city, or
    // venues
    // if venues is selected, display all the dropboxes

    // if 'COUNTRY'
    // get all the venues' countries
    // display them in a drop-down box
    
    echo '<fieldset id="fieldsetChooseVenueRangeValues" '. $sStyle . '>';
    echo '<legend>Choose country, state, city, or venue.</legend>';
    
    // Country
    echo '<label id="labelCountry">Country</label>';
    echo '<select id="selectCountry" name="venue_range_country">';
    if(key_exists('country', $aValues))
    {
      echo '<option value="' . 
              $aValues['country'] . 
              '" selected>' . 
              $aValues['country'] . 
              "</option>";
    }
    echo '</select>';
    echo '<br />';
    
    // State
    echo '<label class="state">State</label>';
    echo '<select class="state" id="selectState" name="venue_range_state">';
    echo '</select>';
    echo '<br />';
    
    // City
    echo '<label class="city">City</label>';
    echo '<select class="city" id="selectCity" name="venue_range_city">';
    echo '</select>';
    echo '<br />';
    
    // Venue
    echo '<label class="venue">Venue</label>';
    echo '<select class="venue" id="selectVenue" name="venue_range_venue">';
    echo '</select>';
    echo '<br />';
    
    echo '</fieldset>';
  }
  
  public static function datesInputTimeFrame($sDefaultRange='', $sFrom = '', $sTo = '')
  {
    if(empty($sDefaultRange))
    {
      return;
    }
    
    $aMonths = array(
        1=>'January', 2=>'February', 3=>'March', 4=>'April', 
        5=>'May', 6=>'June', 7=>'July', 8=>'August', 
        9=>'September', 10=>'October', 11=>'November', 12=>'December'
    );
    
    echo '<br />';
    echo '<input type="radio" name="date_type" value="TIMEFRAME" checked>Time-Frame';
    echo '<br />';
    echo '<label>Month From: </label>';
    echo '<select name="month_from">';
    $aKeys = array_keys($aMonths);
    foreach($aKeys as $sKey)
    {
      $sChecked = '';
      $sMonth = $aMonths[$sKey];
      if(!empty($sFrom) && $sMonth == $aMonths[$sFrom])
      {
        $sChecked = 'selected';
      }
      echo '<option value="'.$sKey.'" '.$sChecked.'>'.$sMonth.'</option>';
    }
    echo '</select>';
    
    echo '<br />';
    
    echo '<label>Month To: </label>';
    echo '<select name="month_to">';
    // insert the empty option to this array
    array_unshift($aKeys, 0);
    $aMonths[0] = '';
    foreach($aKeys as $sKey)
    {
      $sChecked = '';
      $sMonth = $aMonths[$sKey];
      if(!empty($sTo) && $sMonth == $aMonths[$sTo])
      {
        $sChecked = 'selected';
      }
      echo '<option value="'.$sKey.'" '.$sChecked.'>'.$sMonth.'</option>';
    }
    echo '</select>';
    echo '<br />';
  }
  
  public static function datesInputCustomRange($sDefaultRange, $sFrom, $sTo)
  {
    if(empty($sDefaultRange))
    {
      return;
    }
    
    echo '<br />';
    echo '<input type="radio" name="date_type" value="CUSTOMRANGE">Custom Range';
    echo '<br />';
    echo '<label>Date From:</label>';
    echo '<input type="text" name="custom_from" id="customFrom">';
    echo '<br />';
    echo '<label>Date To:</label>';
    echo '<input type="text" name="custom_to" id="customTo">';
    echo '<br />';
  }
  
  public static function datesInputQuarterRange($sVenueRange, $sFrom, $sTo)
  {
    if(empty($sVenueRange))
    {
      return;
    }
    
    $aQuarters = array(
        BookingDates::FALL=>'Fall',
        BookingDates::WINTER=>'Winter',
        BookingDates::SPRING=>'Spring',
        BookingDates::SUMMER=>'Summer'
    );
    
    echo '<br />';
    echo '<input type="radio" name="date_type" value="QUARTERRANGE">College Quarter/Semester Range';
    echo '<br />';
    echo '<label>Quarter From:</label>';
    echo '<select name="quarter_from">';
    $aKeys = array_keys($aQuarters);
    foreach($aKeys as $sKey)
    {
      echo "<option value='$sKey'>$aQuarters[$sKey]</option>";
    }
    echo '</select>';
    
    echo '<br />';
    
    echo '<label>Quarter To:</label>';
    echo '<select name="quarter_to">';
    echo '<option value="0000-00-00"></option>';
    foreach($aKeys as $sKey)
    {
      echo "<option value='$sKey'>$aQuarters[$sKey]</option>";
    }
    echo '</select>';
    echo '<br />';
  }
  
  public static function datesInputDates($sVenueRange)
  {
    if(empty($sVenueRange))
    {
      return;
    }
    
    echo '<br />';
    echo '<input type="radio" name="date_type" value="DATES">Date(s)';
    echo '<br />';
    echo '<label>Add Date</label><br />';
    echo '<input type="text" name="add_date" id="addDate">';
    echo '<button type="button" onclick="addDateToList();">Add Date</button>';
    echo '<br />';
    echo '<label>Dates</label>';
    echo '<input type="hidden" name="dates_list" id="hiddenDatesList">';
    echo '<div id="listOfDates"></div>';
  }
  
}

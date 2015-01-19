<?php
require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');

class DisplayForms
{
  const ADD_VENUE = 1;
  const VIEW_VENUE = 2;
  const EDIT_VENUE = 3;
  
  public static function displayInput($sLabel, $sType, $sName, $sValue = '', $sAttributes = '')
  {
    if( $sType != 'hidden')
    {
      echo "<label>$sLabel</label>";
      echo '<br />';
    }
    echo '<input type="' . $sType . 
            '" name="' . $sName . 
            '" value="' . $sValue . 
            '" ' . $sAttributes . '>';
    
    if( $sType != 'hidden')
    {
      echo '<br />';
    }
  }
  
  /**
   * Displays a <textarea>
   * @param string $sLabel for the <label>
   * @param string $sName name attribute for <textarea>
   * @param string $sValue content for <textarea>
   * @param string $sAttributes optional attributes
   */
  public static function displayTextArea($sLabel, $sName, $sValue = '', $sAttributes = '')
  {
    ?>
<label><?php echo $sLabel; ?></label>
<br />
<textarea name="<?php echo $sName; ?>" <?php echo $sAttributes; ?> >
<?php echo $sValue; ?>
</textarea>
<br />
    <?php 
  }
  
  public static function displayVenueForm($sAction, $nBehavior, $nVenueID = -1)
  {
    // Can't have an empty action url for the form
    if(empty($sAction))
    {
      throw new InvalidArgumentException('Form requires an action url.');
    }
    
    // The behavior needs to be a known one
    if(DisplayForms::ADD_VENUE != $nBehavior && 
      DisplayForms::VIEW_VENUE != $nBehavior &&
      DisplayForms::EDIT_VENUE != $nBehavior)
    {
      throw new InvalidArgumentException('Not a recognized behavior for venues');
    }
    
    // Get user
    $sUserLogin = get_user_field('user_login');
    
    // Init venue object. 
    $oVenues = new Venues('my_venues', $sUserLogin);
    $oVenue = $oVenues->getVenue($nVenueID);
    
    // Display the form
    echo '<b>* indicates a required field</b>';
    echo '<form id="bdAddNewVenueForm" name="addNewVenue" action="' . $sAction . '" method="post">';
    DisplayForms::displayInput
            ('', 'hidden', 'bd_user_login', $sUserLogin, 'required');
    DisplayForms::displayInput
            ('', 'hidden', 'bd_venue_method', $nBehavior);
    
    // venue id for editing
    if( DisplayForms::EDIT_VENUE == $nBehavior)
    {
      DisplayForms::displayInput('', 'hidden', 'bd_venue_id', $nVenueID);
    }
    
    DisplayForms::displayInput
            ("Venue's Name:*", 'text', 'bd_venue_name', $oVenue->getName(), 'required');
    DisplayForms::displayInput
            ("Venue's Booking Email:", 'email', 'bd_venue_email', $oVenue->getEmail());
    DisplayForms::displayInput
            ('Contact Form (Requires online submission form plan):', 'url', 'bd_venue_contact_url', $oVenue->getContactForm());
    DisplayForms::displayInput
            ("Booker's First Name:", 'text', 'bd_venue_booker_fname', $oVenue->getBookerFirstName());
    DisplayForms::displayInput
            ("Booker's Last Name:", 'text', 'bd_venue_booker_lname', $oVenue->getBookerLastName());
    DisplayForms::displayInput
            ('Address:', 'text', 'bd_venue_address1', $oVenue->getAddress1());
    DisplayForms::displayInput
            ('Address 2:', 'text', 'bd_venue_address2', $oVenue->getAddress2());
    DisplayForms::displayInput
            ('City:*', 'text', 'bd_venue_city', $oVenue->getCity(), 'required');
    DisplayForms::displayInput
            ('State:*', 'text', 'bd_venue_state', $oVenue->getState(), 'maxlength="2" required');
    DisplayForms::displayInput
            ('Zip/Postal Code:', 'text', 'bd_venue_zip', $oVenue->getZip());
    
    // Default to United States
    $sCountry = $oVenue->getCountry();
    $sCountry = empty($sCountry) ? 'United States' : $sCountry;
    DisplayForms::displayInput
            ('Country:*', 'text', 'bd_venue_country', $sCountry, 'required');
    
    DisplayForms::displayInput
            ('Website:', 'url', 'bd_venue_website', $oVenue->getWebsite());
    
    DisplayForms::displayTextArea
            ('Note:', 'bd_venue_note', $oVenue->getNote(),
            'cols="50" maxlength="65535"'
            );
    
    // Submit button
    $sSubmitText = (DisplayForms::EDIT_VENUE == $nBehavior) ? 'Update Venue' : 'Add Venue';
    
    echo '<br />';
    echo '<input type="submit" value="' . $sSubmitText . '" '
            . 'id="bd_venue_add_button">';
    echo '</form>';
    
  }
  
  /**
   * addNewVenue displays a add new venue form to the user
   * 
   * @param string $sAction The submit action url for this form
   */
  public static function addNewVenue($sAction)
  {
    DisplayForms::displayVenueForm($sAction, DisplayForms::ADD_VENUE);
  }
  
  public static function editVenue($sAction, $nVenueID)
  {
    DisplayForms::displayVenueForm($sAction, DisplayForms::EDIT_VENUE, $nVenueID);
  }
  
  public static function confirmRemoveVenues()
  {
    $sMyVenuesURI = Site::getBaseURL() . '/myvenues/';

    // If no venues selected
    if(0 == count($_POST))
    {
      echo 'No venues were selected to remove.';
      echo '<br />';
      echo '<a href="'. $sMyVenuesURI . '">Back to my venues.</a>';
      return;
    }
    
    // list the venues with a Yes or No confirm button to remove them
    echo '<b>Remove the following venue(s)?</b><br />';
    echo '<form name="bdRemoveMyVenues" action="' . $sMyVenuesURI . '" method="post">';
    
    echo '<input type="hidden" name="bd_venue_method" value="remove">';
    
    // Display the venues to be removed
    foreach($_POST as $sVenue=>$nID)
    {
      // skip the bulk action selection and main checkbox
      if(strtolower(trim('bd_venues_bulk_action_top')) === $sVenue ||
         strtolower(trim('bd_select_all_venues')) === $sVenue ||
         strtolower(trim('bd_venues_bulk_action_bottom')) === $sVenue)
      {
        continue;
      }
      echo '<input type="text" name="' . (int)$nID . '" value="' . $sVenue . '" readonly>';
      echo '<br />';
    }
    
    // No
    echo '<a href="' . $sMyVenuesURI . '"><b>No! Take me back to my venues!</b></a>';
    
    // Yes
    echo '<input type="submit" value="Yes, remove them." id="bd_btn_remove">';
    
    echo '</form>';
    
  }
  
  public static function displayBookings($sUserLogin)
  {
    $oBookings = new Bookings($sUserLogin);
    $hBookingInfo = $oBookings->getAllBookings();
    if(is_null($hBookingInfo) || 0 == count($hBookingInfo))
    {
      ?>
<h1>Bookings</h1>
<h2>No venues added</h2>
      <?php
      return;
    }
    ?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="<?php echo Site::getBaseURL(); ?>/wp-content/js/bookings.js"></script>
<h1>Bookings</h1>
<form>
  <select name="bd_bookings_bulk_action_top" 
                  id="bd_bookings_bulk_action_top"
                  onchange="BAMDING.MYVENUES.changeBulkActionSelection(this);">
    <option value="bulk">Bulk Action</option>
    <option value="start">Start Booking</option>
    <option value="pause">Pause Booking</option>
  </select>
  <input type='submit' 
         value='Apply' 
         id="btn_myven_apply_top"
         class="btn_disabled"
         disabled>
          
  <select id="filter_venues_select">
    <option>Filter: All</option>
    <option>Filter: Paused</option>
    <option>Filter: Active</option>
    <option>Filter: Name</option>
    <option>Filter: State</option>
    <option>Filter: City</option>
    <option>Filter: Country</option>
    <option>Filter: Category</option>
  </select>
  <input id="filter_venues_input" 
         onkeyup="BAMDING.MYVENUES.filterVenues();">
          
<table id="bookings_table">
  <tr>
    <th>
      <input name="bd_select_all_bookings" 
             type="checkbox" 
             id="bookings_header_checkbox"
             onchange="BAMDING.BOOKINGS.toggleAllBookingsCheckboxes(this);">
    </th>
    <th>Status</th>
    <th>Venue</th>
    <th>City</th>
    <th>State</th>
    <th>Last Contact</th>
    <th>Next Contact</th>
    <th>Every</th>
    <th>Interval</th>
    <th>Category</th>
  </tr>
  
  <?php
  foreach($hBookingInfo as $row)
  {
    ?>
  <tr id="<?php echo $row['venue_id']; ?>">
    <td>
      <input type="checkbox"
             onchange="BAMDING.BOOKINGS.uncheckSelectAll();">
    </td>
    <td>
      <?php
      if((int)$row['pause'] === 1)
      {
        echo "Paused";
      }
      else
      {
        echo "Active";
      }
      ?>
    </td>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['city']; ?></td>
    <td><?php echo $row['state']; ?></td>
    <td><?php echo $row['last_contacted']; ?></td>
    <td><?php echo $row['next_contact']; ?></td>
    <td><?php echo $row['frequency_num']; ?></td>
    <td><?php echo $row['category']; ?></td>
    <td>
      <?php echo self::getFriendlyFrequencyType($row['freq_type'], 
            $row['frequency_num']); ?>
    </td>
  </tr>
    <?php
  }
  ?>
</table>
  <select name="bd_venues_bulk_action_bottom"
                    id="bd_bookings_bulk_action_bottom"
                    onchange="BAMDING.MYVENUES.changeBulkActionSelection(this);">
    <option value="bulk">Bulk Action</option>
    <option value="start">Start Booking</option>
    <option value="pause">Pause Booking</option>
  </select>
  <input type='submit' 
         value='Apply' 
         id="btn_myven_apply_bottom"
         class="btn_disabled"
         disabled>
</form>
    <?php
  }
  
  public static function displayBookingsNotContacted($sUserLogin)
  {
    $oBookings = new Bookings($sUserLogin);
    $hBookingInfo = $oBookings->getNotContacted();
    if(is_null($hBookingInfo) || 0 == count($hBookingInfo))
    {
      return;
    }
    
    $sTable = "";
    $sTable .= '<h2>Not Yet Contacted</h2>';
    $sTable .= '  <table id="not_contacted">';
    $sTable .= '    <tr>';
    $sTable .= '      <th>Start<br />Booking!</th>';
    $sTable .= '      <th>Venue</th>';
    $sTable .= '      <th>City</th>';
    $sTable .= '      <th>State</th>';
    $sTable .= '      <th>Last Contact</th>';
    $sTable .= '      <th>Next Contact</th>';
    $sTable .= '      <th>Every</th>';
    $sTable .= '      <th>D/W/M</th>';
    $sTable .= '    </tr>';
    
    foreach($hBookingInfo as $aRow)
    {
      $sTable .= '    <tr id="row_'. $aRow['venue_id'] .'">';
      $sTable .= '      <td>'
              . '<button type="button" onclick="startBookings('. 
              "'$sUserLogin', " .
              $aRow['venue_id'] .')">Start' . 
              '</button>' . 
              '</td>';
      $sTable .= '      <td>' . $aRow['name'] . '</td>';
      $sTable .= '      <td>' . $aRow['city'] . '</td>';
      $sTable .= '      <td>' . $aRow['state'] . '</td>';
      $sTable .= '      <td>' . $aRow['last_contacted'] . '</td>';
      $sTable .= '      <td>' . $aRow['next_contact'] . '</td>';
      $sTable .= '      <td>' . $aRow['frequency_num'] . '</td>';
      
      // Display user friendly frequency type
      $sFriendlyType = self::getFriendlyFrequencyType($aRow['freq_type'], $aRow['frequency_num']);
      
      $sTable .= '      <td>' . $sFriendlyType . '</td>';
      $sTable .= '    </tr>';
    }
    
    $sTable .= '  </table>';
    echo $sTable;
  }
  
  public static function displayBookingsScheduled($sUserLogin)
  {
    $oBookings = new Bookings($sUserLogin);
    $hBookingInfo = $oBookings->getStarted();
    if(is_null($hBookingInfo) || 0 == count($hBookingInfo))
    {
      return;
    }
    
    $sTable = "";
    $sTable .= '<h2>Scheduled to be contacted</h2>';
    $sTable .= '  <table id="scheduled">';
    $sTable .= '    <tr>';
    $sTable .= '      <th>Venue</th>';
    $sTable .= '      <th>City</th>';
    $sTable .= '      <th>State</th>';
    $sTable .= '      <th>Last Contact</th>';
    $sTable .= '      <th>Next Contact</th>';
    $sTable .= '      <th>Every</th>';
    $sTable .= '      <th>D/W/M</th>';
    $sTable .= '    </tr>';
    
    foreach($hBookingInfo as $aRow)
    {
      $sTable .= '    <tr>';
      $sTable .= '      <td>' . $aRow['name'] . '</td>';
      $sTable .= '      <td>' . $aRow['city'] . '</td>';
      $sTable .= '      <td>' . $aRow['state'] . '</td>';
      $sTable .= '      <td>' . $aRow['last_contacted'] . '</td>';
      $sTable .= '      <td>' . $aRow['next_contact'] . '</td>';
      $sTable .= '      <td>' . $aRow['frequency_num'] . '</td>';
      
      // Display user friendly frequency type
      $sFriendlyType = self::getFriendlyFrequencyType($aRow['freq_type'], $aRow['frequency_num']);
      
      $sTable .= '      <td>' . $sFriendlyType . '</td>';
      $sTable .= '    </tr>';
    }
    
    $sTable .= '  </table>';
    echo $sTable;
  }
  
  public static function displayBookingsActivePaused($sUserLogin, $bActive)
  {
    $bActive = (bool)$bActive;
    
    $sButtonText = $bActive ? 'Pause' : 'Resume';
    $sH2 = $bActive ? 'Active Venues' : 'Paused Venues';
    $sOnClickParam = ($bActive) ? 'true' : 'false';
    
    $oBookings = new Bookings($sUserLogin);
    $hBookingInfo = $bActive ? $oBookings->getActive() : $oBookings->getPaused();
    if(is_null($hBookingInfo) || 0 == count($hBookingInfo))
    {
      return;
    }
    
    $sTable = "";
    $sTable .= "<h2>$sH2</h2>";
    $sTable .= '  <table>';
    $sTable .= '    <tr>';
    $sTable .= "      <th>$sButtonText<br/>Booking</th>";
    $sTable .= '      <th>Venue</th>';
    $sTable .= '      <th>City</th>';
    $sTable .= '      <th>State</th>';
    $sTable .= '      <th>Last Contact</th>';
    $sTable .= '      <th>Next Contact</th>';
    $sTable .= '      <th>Every</th>';
    $sTable .= '      <th>D/W/M</th>';
    $sTable .= '    </tr>';
    
    foreach($hBookingInfo as $aRow)
    {
      $sTable .= '    <tr id="row_' . $aRow['venue_id'] . '">';
      $sTable .= '      <td>' . 
              '<button type="button" id="button_'.  $aRow['venue_id'] .'" onclick="setPaused('. 
              "'$sUserLogin', " .
              $aRow['venue_id'] . ', ' .
              $sOnClickParam
              . ')">' . $sButtonText .
              '</button>' . 
              '</td>';
      $sTable .= '      <td>' . $aRow['name'] . '</td>';
      $sTable .= '      <td>' . $aRow['city'] . '</td>';
      $sTable .= '      <td>' . $aRow['state'] . '</td>';
      $sTable .= '      <td>' . $aRow['last_contacted'] . '</td>';
      $sTable .= '      <td>' . $aRow['next_contact'] . '</td>';
      $sTable .= '      <td>' . $aRow['frequency_num'] . '</td>';
      
      // Display user friendly frequency type
      $sFriendlyType = self::getFriendlyFrequencyType($aRow['freq_type'], $aRow['frequency_num']);
      
      $sTable .= '      <td>' . $sFriendlyType . '</td>';
      $sTable .= '    </tr>';
    }
    
    $sTable .= '  </table>';
    echo $sTable;
    
  }
  
  public static function getFriendlyFrequencyType($sFrequencyType, $sFrequencyNumber)
  {
    $sFriendlyType = '';
    switch($sFrequencyType)
    {
      case 'D':
        $sFriendlyType = 'Day';
        break;
      case 'W':
        $sFriendlyType = 'Week';
        break;
      case 'M':
        $sFriendlyType = 'Month';
        break;
      default:
        $sFriendlyType = '<strong>ERROR</strong>';
        break;
    }
      
    if( 1 < $sFrequencyNumber)
    {
      $sFriendlyType .= 's';
    }
    
    return $sFriendlyType;
  }
}

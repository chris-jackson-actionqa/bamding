<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DisplayBookings
 *
 * @author Seth
 */
class DisplayBookings {

  private $oBookings = null;
  private $sUserLogin = "";

  /**
   * Constructor. Initialize the bookings object
   * 
   * @param string $sUserLogin user name
   * @throws InvalidArgumentException
   */
  public function __construct($sUserLogin) {
    $this->sUserLogin = trim($sUserLogin);
    if (empty($this->sUserLogin)) {
      $message = "User name can't be empty.";
      throw new InvalidArgumentException($message);
    }

    $this->oBookings = new Bookings($this->sUserLogin);
  }

  /**
   * Display bookings
   * TODO: Break into smaller functions
   * @param type $sUserLogin
   * @return type
   */
  public function displayBookings() {
    $this->oBookings = new Bookings($this->sUserLogin);
    $hBookingInfo = $this->oBookings->getAllBookings();
    if (is_null($hBookingInfo) || 0 == count($hBookingInfo)) {
      ?>
      <h1>Bookings</h1>
      <h2>No venues added</h2>
      <?php
      return;
    }

    $this->includeJQueryUI();
    ?>
    <script src="<?php echo Site::getBaseURL(); ?>/wp-content/js/bookings.js"></script>
    <script>
      BAMDING.MYVENUES.getAllVenues(
              "<?php echo get_user_field('user_login'); ?>",
              "bookings");
    </script>
    <h1>Bookings</h1>
    <form action="" method="post" id="bookings_form">
      <?php 
      $this->bulkAction('top'); 
      $this->displayBookingsFilter(); 
      ?>

      <table id="bookings_table">
        <tr>
          <th>
            <input name="bd_select_all_bookings" 
                   type="checkbox" 
                   id="bookings_header_checkbox"
                   onchange="BAMDING.BOOKINGS.toggleAllBookingsCheckboxes(this);
                           BAMDING.BOOKINGS.toggleBulkApply();">
          </th>
          <th>Status</th>
          <th>Venue</th>
          <th>City</th>
          <th>State</th>
          <th>Last Contact</th>
          <th>Next Contact</th>
          <th>Every</th>
          <th>Category</th>
        </tr>

        <?php
        foreach ($hBookingInfo as $row) {
          if ((int) $row['pause'] === 1) {
            $row_class = "row_paused";
          } else {
            $row_class = "row_active";
          }
          ?>
          <tr class="<?php echo $row_class; ?>" id="<?php echo $row['venue_id']; ?>">
            <td>
              <input type="checkbox"
                     name="venue_<?php echo $row['venue_id']; ?>"
                     value="<?php echo $row['venue_id']; ?>"
                     onchange="BAMDING.BOOKINGS.uncheckSelectAll();
                               BAMDING.BOOKINGS.toggleBulkApply();">
            </td>
            <td>
              <?php
              if ((int) $row['pause'] === 1) {
                echo "Paused";
              } else {
                echo "Active";
              }
              ?>
            </td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['city']; ?></td>
            <td><?php echo $row['state']; ?></td>
            <td><?php echo $row['last_contacted']; ?></td>
            <td>
              <?php
              $this->nextContactField($row);
              ?>
            </td>
            <!-- Every  -->
            <td>
              <?php 
              $frequency_number = (int)$row['frequency_num'];
              $selected = array(
                  'D' => '',
                  'W' => '',
                  'M' => ''
              );
              switch($row['freq_type'])
              {
                case 'D':
                  $selected['D'] = 'selected';
                  break;
                case 'W':
                  $selected['W'] = 'selected';
                  break;
                case 'M':
                  $selected['M'] = 'selected';
                  break;
              }
              ?>
              <input type="number"
                     min="1"
                     max="365"
                     value="<?php echo $frequency_number;?>"
                     style="width: 70px;">
              <select>
                <option value="D" <?php echo $selected['D'];?>>Days</option>
                <option value="W" <?php echo $selected['W'];?>>Weeks</option>
                <option value="M" <?php echo $selected['M'];?>>Months</option>
              </select>
            </td>
            <td><?php echo $row['category']; ?></td>
          </tr>
          <?php
        }
        ?>
      </table>
      <?php $this->bulkAction('bottom'); ?>
    </form>
    <?php
  }

  /**
   * Display bookings filter
   * Filter select and input to control the venues shown in the bookings
   * venues table
   */
  public function displayBookingsFilter() {
    ?>
    <select id="filter_bookings_select">
      <option>Filter: All</option>
      <option>Filter: Name</option>
      <option>Filter: State</option>
      <option>Filter: City</option>
      <option>Filter: Category</option>
    </select>
    <input id="filter_bookings_input"
           onkeyup="BAMDING.BOOKINGS.filterVenues();">
           <?php
         }

  /**
   * Takes the input abbreviation for the frequency type and returns a 
   * user friendly string.
   * 
   * Examples:
   * W, 1 returns Week
   * D, 5 returns Days
   * 
   * @param string $sFrequencyType D, W, M
   * @param int $sFrequencyNumber if 1 or less, returns singular. More, plural.
   * @return string friendly frequency or '<strong>ERROR</strong>'
   */
  public static function getFriendlyFrequencyType($sFrequencyType, $sFrequencyNumber) {
    $sFriendlyType = '';
    switch ($sFrequencyType) {
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

    if (1 < $sFrequencyNumber) {
      $sFriendlyType .= 's';
    }

    return $sFriendlyType;
  }

  /**
   * display the next contact field with an interactive datepicker
   * @param type $bookingInfo
   */
  private function nextContactField($bookingInfo) {
    $user = 
    $venue_id = $bookingInfo['venue_id'];
    $nextContactDate = $bookingInfo['next_contact'];
    $last_contacted = $bookingInfo['last_contacted'];
    $isPaused = $bookingInfo['pause'];
    
    $unique_id = 'datepicker_' . $venue_id;

    $displayDate = $nextContactDate == "0000-00-00" ?
            date('m/d/Y', time() + (24 * 60 * 60)) : 
            date("m/d/Y", strtotime($nextContactDate));
    
    // Need to disable dates less than 7 days after last contacted.
    // Prevent users from unnecessary spamming!
    $mindate = ('0000-00-00' == $last_contacted) ?
            date_parse(date('Y-m-d', time())) :
            date_parse(date('Y-m-d', strtotime($last_contacted) + (7 * 24 * 60 * 60)));
    $y_m_d = $mindate['year'].', '.$mindate['month'].' - 1, '.$mindate['day'];
    ?>
    <script>
      $(function () {
        $("#<?php echo $unique_id; ?>").datepicker(
                { minDate: new Date(<?php echo $y_m_d; ?>)});
      });
    </script>
    <input type="text" 
           id="<?php echo $unique_id; ?>" 
           value="<?php echo $displayDate; ?>"
           class="date_width"
           onchange="BAMDING.BOOKINGS.updateNextContact(
                     this, 
                     '<?php echo get_user_field('user_login'); ?>',
                     <?php echo $venue_id; ?>
                     );">
    </script>
    <?php
  }

  /**
   * inject the scripts for jQuery and jQuery UI
   */
  public function includeJQueryUI() {
    ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
    <?php
  }
  
  /**
   * Bulk action select and apply inputs
   * @param string $location top or bottom
   */
  public function bulkAction($location)
  {
    $name = "bd_bookings_bulk_action_$location";
    $selectID = "bd_bookings_bulk_action_$location";
    $buttonID = "btn_bookings_apply_$location";
    ?>
    <select name="<?php echo $name;?>" 
              id="<?php echo $selectID;?>"
              onchange="BAMDING.BOOKINGS.changeBulkActionSelection(this);">
        <option value="bulk">Bulk Action</option>
        <option value="start">Start Booking</option>
        <option value="pause">Pause Booking</option>
        <option value="frequency">Set How Often To Contact</option>
      </select>
      <input type='submit' 
             value='Apply' 
             id="<?php echo $buttonID;?>"
             class="btn_disabled"
             disabled
             onclick="BAMDING.BOOKINGS.displayPop('<?php echo $selectID;?>');">
      <?php
  }
}

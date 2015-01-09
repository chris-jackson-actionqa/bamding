<?php
require_once(ABSPATH . '/wp-content/tardis/bamding_lib.php');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Access the database and display data to the user.
 * Things like the user's venues and such.
 *
 * @author Seth
 */
class DisplayData {

    public static function displayMyVenues($sUserID) {
        $script_location = Site::getBaseURL() . '/wp-content/js/bookings.js';
        ?>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src='<?php echo $script_location; ?>'></script>
        <script>
          BAMDING.MYVENUES.getAllVenues(
            "<?php echo get_user_field('user_login'); ?>");
        </script>
        <?php
        $oVenues = new Venues('my_venues', $sUserID);
        $aAllMyVenues = $oVenues->getAllMyVenues();
        if (0 == count($aAllMyVenues)) {
            echo '<div>No venues found for you.</div>';
            return;
        }
        ?>
        
        <form name="bdVenueList" 
              id="bdVenueList"
              action="" 
              method="post" 
              onsubmit="applyMyVenuesForm(this);">
          <select name="bd_venues_bulk_action_top" 
                  id="bd_venues_bulk_action_top"
                  onchange="BAMDING.MYVENUES.changeBulkActionSelection(this);">
                <option value="bulk">Bulk Action</option>
                <option value="remove">Remove</option>
                <option value="category">Set Category</option>
            </select>
          <input type='submit' 
                 value='Apply' 
                 id="btn_myven_apply_top"
                 class="btn_disabled"
                 disabled>
          
        <select id="filter_venues_select">
          <option>Filter: All</option>
          <option>Filter: Name</option>
          <option>Filter: State</option>
          <option>Filter: City</option>
          <option>Filter: Country</option>
          <option>Filter: Category</option>
        </select>
          <input id="filter_venues_input" 
                 onkeyup="BAMDING.MYVENUES.filterVenues();">

            <table id="venues_table">
                <tr>
                  <th>
                    <input name="bd_select_all_venues" 
                           type="checkbox" 
                           id="my_venues_header_checkbox"
                           onchange="toggleAllMyVenuesCheckBoxes(this); BAMDING.MYVENUES.toggleBulkApply();">
                  </th>
                  <th>Venue</th>
                  <th>City</th>
                  <th>State</th>
                  <th>Country</th>
                  <th>Category</th>
                </tr>

        <?php
        foreach ($aAllMyVenues as $aRow) {
            ?>
                    <tr>
                        <!-- NAME -->
                        <td>
                            <input type='checkbox' 
                                   name="<?php echo $aRow['name']; ?>" 
                                   value="<?php echo $aRow['id']; ?>"
                                   onchange="uncheckMyVenuesHeaderCheckbox(); BAMDING.MYVENUES.toggleBulkApply();">
                        </td>
                        <!-- Venue -->
                        <td>
                            <a href="<?php echo Site::getBaseURL() . '/editvenue?venue_id=' . $aRow['id']; ?>">
            <?php echo $aRow['name']; ?>
                            </a>
                        </td>
                        <!-- City -->
                        <td><?php echo $aRow['city']; ?></td>
                        <!-- State -->
                        <td><?php echo $aRow['state']; ?></td>
                        <!-- Country -->
                        <td><?php echo $aRow['country']; ?></td>
                        <td><?php echo $aRow['category']; ?></td>
                    </tr>
            <?php
        }
        ?>
            </table>
            <select name="bd_venues_bulk_action_bottom"
                    id="bd_venues_bulk_action_bottom"
                    onchange="BAMDING.MYVENUES.changeBulkActionSelection(this);">
                <option value="bulk">Bulk Action</option>
                <option value="remove">Remove</option>
                <option value="category">Set Category</option>
            </select>
            <input type='submit' 
                   value='Apply' 
                   id="btn_myven_apply_bottom"
                   class="btn_disabled"
                   disabled>
        </form>
        <?php
    }
}

//XXXXX||||==-------------==||||XXXXX
//XXX||||=---,e*@@@@@@@@*e,--=||||XXX
//XX|||=---e@@@@@@@@@@@@@@@@e--=|||XX
//X|||=--,@@@@@@@@@@@@@@@@@@@@,-=||XX
//X||=--,@@@@^"   ''::||XXX@@@@--=|XX
//||=---@@@@'        ::;||XX@@@b--||X
//||=--d@@@P       .'::::||XX@@@,-=|X
//|=---@@@@'    _,;,'  '::||XY@@b--||
//|=--.@@@P    '~~~'  .:+^^+|X@@@--=|
//|--.~'@@    ,c@X;  .:|o@XxXX@@@--=|
//|--:: 7@      ~"   :|  ""::X0P|---|
//|--:',g@           '|:  ':||0:|---|
//|---.'*             :|: :||XX|'--=|
//|=---e,,       .|" ;e*;:||XX7----=|
//|=---@@@       '  :'':|||XXX'----||
//||=--7@@      ..,ee*o,:||XXP----=||
//X||=--7@          :||T|||X7'---=||X
//X||=---Y        .'::X|||XY'----=||X
//XX||=---  .        .:||XX'----=||XX
//XX|||=-- .:::....:;;|e*@@----=|||XX
//XXX|||=-.:::::::|X@@@@@@T,--=|||XXX
//XXXX|||e;::::::|||X@@PXXd@,||||XXXX
//XXXXXd@@@e;::||||X@@XXXd@@@e||XXXXX
//XXXd@@@@@@@@e||XXXXXXe@@@@@@@*e,^XX
//e@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@e
<?php
require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');
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
class DisplayData 
{
  public static function displayMyVenues($sUserID)
  {
    $oVenues = new Venues('my_venues', $sUserID);
    $aAllMyVenues = $oVenues->getAllMyVenues();
    if(0 == count($aAllMyVenues))
    {
      echo '<div>No venues found for you.</div>';
      return;
    }
    $sAction = Site::getBaseURL() . '/removevenue/';
    echo '<form name="bdVenueList" action="' . $sAction . '" method="post">';
    
    ?>
<select name="bd_venues_bulk_action">
  <option value="bulk">Bulk Action</option>
  <option value="remove">Remove</option>
  <option value="category">Set Category</option>
</select>
<input type='submit' value='Apply'>
<table>
  <tr>
    <th><input name="bd_select_all_venues" type="checkbox"></th>
    <th>Venue</th>
    <th>City</th>
    <th>State</th>
    <th>Country</th>
    <th>Category</th>
  </tr>
    
  <?php
    
    foreach($aAllMyVenues as $aRow)
    {
      echo "<tr>";
      // check box with venue id
      echo "  <td><input type='checkbox' name='" .
              $aRow['name'] . "' value='" . $aRow['id'] . "'>" .
              "</td>";
      // Venue
      echo "  <td><a href='" .
              Site::getBaseURL() . '/editvenue' .
              '?venue_id=' . $aRow['id'] .
              "'>" .
              $aRow['name'] . "</a></td>";
      // City
      echo "  <td>" . $aRow['city'] . "</td>";
      // State
      echo "  <td>" . $aRow['state'] . "</td>";
      // Country
      echo "  <td>" . $aRow['country'] . "</td>";
      
      ?>
    <td><?php echo $aRow['category'];?></td>
  </tr>
<?php
    }
?>
</table>
<select name="bd_venues_bulk_action">
  <option value="bulk">Bulk Action</option>
  <option value="remove">Remove</option>
  <option value="category">Set Category</option>
</select>
<input type='submit' value='Apply'>
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
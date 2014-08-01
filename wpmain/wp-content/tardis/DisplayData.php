<?php
require_once(ABSPATH. '/wp-content/tardis/Venue.php');
require_once(ABSPATH. '/wp-content/tardis/DisplayForms.php');
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
    }
    
    echo '<table>';
    
    //display field names
    ?>
<tr>
  <th>Venue</th>
  <th>City</th>
  <th>State</th>
  <th>Country</th>
  <th>Remove</th>
</tr>
    
  <?php
    
    foreach($aAllMyVenues as $aRow)
    {
      echo "<tr>";
      // Venue
      echo "  <td>" . $aRow['name'] . "</td>";
      // City
      echo "  <td>" . $aRow['city'] . "</td>";
      // State
      echo "  <td>" . $aRow['state'] . "</td>";
      // Country
      echo "  <td>" . $aRow['country'] . "</td>";
      
      // Delete
      echo "  <td>" . 
              DisplayForms::removeVenueCell($aRow['id'], $aRow['name']) . 
              "</td>";
      echo "</tr>";
    }
    echo '</table>';
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
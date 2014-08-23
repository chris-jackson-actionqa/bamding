<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminDisplay
 *
 * @author Seth
 */
class AdminDisplay 
{
  public static function getHeader()
  {
    $sHTML = <<<HTM
<!DOCTYPE html>
<head>
  <link rel="stylesheet" type="text/css" href="admin-style.css">
</head>
<html>
<body>
HTM;
    echo $sHTML;
  }
  
  public static function getFooter()
  {
    $sHTML = <<<HTM
</body>
</html>
HTM;
    echo $sHTML;
  }
  
  public static function getMenu()
  {
    $sHTML = <<<HTM
<div id="admin-menu">
  <a href="admin-bookings.php">Bookings</a>
</div>
HTM;
    echo $sHTML;
  }
  
  public static function chooseUserForm($sAction, $sTable, $hPostData)
  {
    $aUsers = array();
    if($sTable = 'bookings')
    {
      $oAdminBookings = new AdminBookings();
      $aUsers = $oAdminBookings->getAllUsers();
    }
    array_push($aUsers, 'all');
    
    $sSelectedUser = NULL;
    if(array_key_exists('users', $hPostData))
    {
      $sSelectedUser = $hPostData['users'];
    }
    
    echo '<form action="' . $sAction . '" method="POST">';
    echo '<select name="users">';
    foreach($aUsers as $sUser)
    {
      echo '<option ';
      if( $sSelectedUser == $sUser )
      {
        echo 'selected';
      }
      echo ' value="' . $sUser . '">' . $sUser . '</option>';
    }
    
    echo '</select>';
    echo '<input type="submit">';
    echo '</form>';
  }
  
  public static function displayBookingsTable($hPostData)
  {
    $hBookings = array();
    if(array_key_exists('users', $hPostData))
    {
      $oAdminBookings = new AdminBookings();
      $hBookings = $oAdminBookings->getBookings($hPostData['users']);
    }
    
    echo '<table>';
    echo '<tr>';
    echo '<th>User</th>';
    echo '<th>Venue ID</th>';
    echo '<th>Venue</th>';
    echo '<th>City</th>';
    echo '<th>State</th>';
    echo '<th>Country</th>';
    echo '<th>Contacted</th>';
    echo '<th>Next Contact</th>';
    echo '<th>Paused</th>';
    echo '<th>Freq#</th>';
    echo '<th>Freq Type</th>';
    echo '<th>Updated</th>';
    echo '</tr>';
    
    foreach($hBookings as $hRow)
    {
      echo '<tr>';
      echo '<td>'. $hRow['user_login'] .'</td>';
      echo '<td>'. $hRow['venue_id'] .'</td>';
      echo '<td>'. $hRow['name'] .'</td>';
      echo '<td>'. $hRow['city'] .'</td>';
      echo '<td>'. $hRow['state'] .'</td>';
      echo '<td>'. $hRow['country'] .'</td>';
      echo '<td>'. $hRow['last_contacted'] .'</td>';
      echo '<td>'. $hRow['next_contact'] .'</td>';
      echo '<td>'. $hRow['pause'] .'</td>';
      echo '<td>'. $hRow['frequency_num'] .'</td>';
      echo '<td>'. $hRow['freq_type'] .'</td>';
      echo '<td>'. $hRow['timestamp'] .'</td>';
      echo '</tr>';
    }
    
    echo '</table>';
  }
}

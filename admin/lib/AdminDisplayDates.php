<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminDisplayDates
 *
 * @author Seth
 */
class AdminDisplayDates extends AdminDisplay
{
  public static function showEditDatesTimeframes()
  {
    $sUser = (key_exists('user_login', $_GET)) ? $_GET['user_login'] : '';
    if(empty($sUser))
    {
      return;
    }
    
    // get the dates and timeframes for the user
    $oDatesTimeFrames = new AdminDates($sUser);
    $hDates = $oDatesTimeFrames->getDatesTimeframes();
    
    if( 0 === count($hDates))
    {
      return;
    }
    
    $hTableHeaders = array_keys($hDates[0]);
    array_unshift($hTableHeaders, 'Delete');
    
    // display a form to edit these
    echo '<h2>Edit Dates/Timeframes</h2>';
    echo '<form action="admin-dates.php?user_login='.$sUser.'" method="post">';
    echo '<table>';
    // table header
    echo '<tr>';
    foreach($hTableHeaders as $sKey)
    {
      echo '<th>'.$sKey.'</th>';
    }
    echo '</tr>';
    
    // list all entries
    $iIndex = 0;
    $nCount = count($hDates);
    for(;$iIndex < $nCount; ++$iIndex)
    {
      $sUserLogin = $hDates[$iIndex]['user_login'];
      $nVenueRange = (int)$hDates[$iIndex]['venue_range'];
      $nDateType = $hDates[$iIndex]['date_type'];
      $sCountry = (empty($hDates[$iIndex]['country'])) ? 
              '' : trim($hDates[$iIndex]['country']); 
      $sState = (empty($hDates[$iIndex]['state'])) ? 
              '' : trim($hDates[$iIndex]['state']);
      $sCity = (empty($hDates[$iIndex]['city'])) ? 
              '' : trim($hDates[$iIndex]['city']);
      $nVenueID = (int)$hDates[$iIndex]['venue_id'];
      
      echo '<tr id="editDatesRowID'.$iIndex.'">';
      foreach($hTableHeaders as $sKey)
      {
        echo '<td>';
        if('Delete' === $sKey)
        {
          $sDeleteArgs = "'editDatesRowID$iIndex'," .
            "'$sUserLogin'" . ',' .
            $nVenueRange . ',' . 
            $nDateType . ',' .
            "'$sCountry'" . ',' .
            "'$sState'" . ',' .
            "'$sCity'" . ',' .
            $nVenueID;
          echo '<button type="button" onclick="deleteDateTimeframe('.$sDeleteArgs.');">X</button>';
        }
        else
        {
          echo $hDates[$iIndex][$sKey];
        }
        echo '</td>';
      }
      echo '</tr>';
    }
    echo '</table>';
    echo '</form>';
  }
}

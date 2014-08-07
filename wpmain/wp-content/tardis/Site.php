<?php
require_once(ABSPATH. '/wp-content/tardis/bamding_lib.php');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Site provides utility functions for the website (like finding the base url).
 *
 * @author Seth
 */
class Site 
{
  /**
   * Returns the base url of the site.
   * Using this instead of hardcoding urls allows portability of the site
   * for local development
   * @return string the current base url without trailing forward slash
   */
  public static function getBaseURL()
  {
    $sHost = $_SERVER['SERVER_NAME'];  //either bamding.com or localhost
    $sRequestURI = $_SERVER['REQUEST_URI']; // need this to see if we're on a local wordpress install
    
    $sBaseURL = 'http://' . $sHost;
    
    if(preg_match('/^\/wordpress\//i', $sRequestURI))
    {
      $sBaseURL .= '/wordpress';
    }
    
    return $sBaseURL;
  }
}

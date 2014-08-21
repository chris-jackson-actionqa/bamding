/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getBaseURL()
{
  pathArray = window.location.href.split( '/' );
  protocol = pathArray[0];
  host = pathArray[2];
  url = protocol + '//' + host;
  if(url == "http://localhost")
  {
    url = url + "/wordpress";
  }
  return url;
}

function startBookings(userLogin, nVenueID)
{
  $(document).ready(function()
  {
    $("#row_" + nVenueID).fadeOut('slow');
    
    // Sets venue to start booking
    $.post(getBaseURL() + "/wp-content/tardis/DoBookings.php", 
      {
        action:"startBooking",
        venue_id: nVenueID,
        user_login: userLogin
      },
      function()
      {
        // update not contacted table
        $.post(getBaseURL() + "/wp-content/tardis/DoBookings.php", 
        {
          action:"getHTMLTableNotContacted",
          user_login: userLogin
        },
        function (data, status)
        {
          $('#div_not_contacted').html(data);
        });

        // update scheduled table
        $.post(getBaseURL() + "/wp-content/tardis/DoBookings.php", 
        {
          action:"getHTMLTableScheduled",
          user_login: userLogin
        },
        function (data, status)
        {
          $('#div_scheduled').html(data);
        });
      });
  });
};

function setPaused(userLogin, nVenueID, bPause)
{
  $(document).ready(function()
  {
    $("#row_" + nVenueID).fadeOut('slow');
    
    // Sets venue to start booking
    $.post(getBaseURL() + "/wp-content/tardis/DoBookings.php", 
      {
        action:"setPause",
        pause: bPause,
        venue_id: nVenueID,
        user_login: userLogin
      },
      function ()
      {
        $.post(getBaseURL() + "/wp-content/tardis/DoBookings.php", 
        {
          action:"getHTMLTablePaused",
          user_login: userLogin
        },
        function (data, status)
        {
          $('#div_paused').html(data);
        }        
        );
      
        $.post(getBaseURL() + "/wp-content/tardis/DoBookings.php", 
        {
          action:"getHTMLTableActive",
          user_login: userLogin
        },
        function (data, status)
        {
          $('#div_active').html(data);
        }
        );
      }
    );
  });
};




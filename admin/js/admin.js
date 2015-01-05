/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var BAMDING = {
  ADMIN: {
    REMINDERS: {
      sendReminderEmail: function()
      {
        elemUserEmail = document.getElementById("reminder_email_user_email");
        elemSubject = document.getElementById("reminder_email_subject");
        elemBody = document.getElementById("reminder_email_body");
        
        to = "sethalicious@gmail.com";
        from = "seth@bamding.com";
        subject = $elemSubject.innerHTML;
        body = "<html><body>" + elemBody.innerHTML + "</body></html>";
        
        
      }
    }
  }
};

$(document).ready( function(){
  /**
   * Send email (via ajax) for reminder emails
   */
  $("#reminder_send_mail_button").click(function(){
    elemUserEmail = document.getElementById("reminder_email_user_email");
    elemSubject = document.getElementById("reminder_email_subject");
    elemBody = document.getElementById("reminder_email_body");

    to = elemUserEmail.innerHTML;
    from = "seth@bamding.com";
    subject = elemSubject.innerHTML;
    body = "<html><body>" + elemBody.innerHTML + "</body></html>";

    $.post('ajax/SendEmail.php',
    {
      to: to,
      from: from,
      subject: subject,
      body: body
    },
    function(data,status){
      alert("Data: " + data + "\nStatus: " + status);
    });
  });
  });

// initialized by PHP when user selected on AdminDates
var gaVenues;

function initReminderDatePickers()
{
  $(function() {
    $( "#reminderSent" ).datepicker();
    });
    
  $(function() {
    $( "#nextContact" ).datepicker();
    });
}

function initDatesDatePickers()
{
  $(function() {
    $( "#customFrom" ).datepicker();
    });
    
  $(function() {
    $( "#customTo" ).datepicker();
    });
  
  $(function() {
    $( "#addDate" ).datepicker();
    });
}

var aDates = [];

// adds user's selected date to the list of dates
// validates the date before adding it to the list
// adds the dates in the proper sql format to the hidden dates list field
// displays the dates in the date div
function addDateToList()
{
  var oDate = new Date($("#addDate").val());
  $("#addDate").val(""); //clear out field
  
  // not valid date
  if(!isDateValid(oDate))
  {
    alert("Date is not valid: " + oDate.toString());
    return;
  }

  //not in future
  if(!isDateInFuture(oDate))
  {
    alert("Date needs to be in the future.");
    return;
  }
  
  // is already in list?
  if(isInDatesList(oDate))
  {
    alert("Date already chosen.");
    return;
  }
  
  // add to list and sort
  aDates.push(oDate);
  aDates.sort(function(a,b){return a-b;});
  
  //update the hidden field list
  updateHiddenField();
  
  // display dates on screen
  displayDatesInDiv("listOfDates");
}

function isInDatesList(oDate)
{
  var iLength = aDates.length;
  for(var i = 0; i < iLength; ++i)
  {
    if(+(aDates[i]) === +oDate)
    {
      return true;
    }
  }
  return false;
}

function displayDatesInDiv(sDiv)
{
  sDiv = '#' + sDiv;
  var sDivText = "";
  var iLength = aDates.length;
  var nDay, nYear, nMonth;
  for(var i = 0; i < iLength; ++i)
  {
    nYear = aDates[i].getFullYear();
    nMonth = aDates[i].getMonth()+1;
    nDay = aDates[i].getDate();
    
    if(0 !== i)
    {
      sDivText += "&nbsp;";
    }
    
    sDivText += nMonth + "/" + nDay + 
            "<button type='button' onclick='removeDate(" + i + ");'>X</button>";
  }
  
  $(sDiv).html(sDivText);
}

function isDateValid(oDate)
{
  // is object a Date object
  if(oDate.constructor.toString().indexOf("Date") < 0)
  {
    return false;
  }
  
  // is date value valid
  return !isNaN(oDate.getTime());
}

// makes sure the date is in the future (ignores time of day)
function isDateInFuture(oDate)
{
  // get today's date
  var oToday = new Date();
  
  // floor both dates to the beginning of the day
  oToday.setHours(0,0,0,0);
  oDate.setHours(0,0,0,0);
  
  // see if date is in future
  if(oDate > oToday)
  {
    return true;
  }
  
  return false;
}

function removeDate(iDateIndex)
{
  aDates.splice(iDateIndex,1);
  updateHiddenField();
  displayDatesInDiv("listOfDates");
}

function updateHiddenField()
{
  // convert dates in array to sql format
  var aSQLDates = new Array();
  for(var i=0; i < aDates.length; ++i)
  {
    var oDate = new Date(aDates[i]);
    var nYear = oDate.getFullYear();
    var nMonth = oDate.getMonth()+1;
    var nDay = oDate.getDate();
    var sMonth = (nMonth < 10)? "0"+nMonth : nMonth;
    var sDay = (nDay < 10) ? "0" + nDay : nDay;
    
    var sSQLDate = nYear + "-" + sMonth + "-" + sDay;
    aSQLDates.push(sSQLDate);
  }
  
  // replace contents of hidden field
  $("#hiddenDatesList").val(aSQLDates.toString());
}

function initUserVenues(sUserLogin)
{
  $.ajax({
      url: 'ajax/GetUserVenues.php',
      type: 'post',
      data: {'user_login': sUserLogin},
      success: function(data, status) {
        gaUserVenues = JSON.parse(data);
      },
      error: function(xhr, desc, err) {
        console.log(xhr);
        console.log("Details: " + desc + "\nError:" + err);
        alert(xhr.toString());
        alert("Details: " + desc + "\nError:" + err);
      }
    }); // end ajax call
}

/**
 * When venue range selection changes
 * 
 */
$(document).ready(function(){
  $("#selectVenueRange").change(function(){
    // clear previous entries
    $("#selectCountry").html("");
    
    // if the selection is ALL venues, hide the venue range selector area
    // return, no need to fill in countries
    if($("#selectVenueRange").val() == 'ALL')
    {
      $("#fieldsetChooseVenueRangeValues").fadeOut();
      return;
    }
    
    $("#fieldsetChooseVenueRangeValues").fadeIn();
    
    var aCountries = uniqueArray(gaUserVenues, 'country');
    var nCount = aCountries.length;
    var sOption = '';
    var sOptions = '';
    for(var i = 0; i < nCount; ++i)
    {
      sOptions = $("#selectCountry").html();
      sOption += "<option value='" + 
              aCountries[i] + 
              "'>" + 
              aCountries[i] +
              "</option>";
      $("#selectCountry").html(sOptions + sOption);
    }
    
    // fade out fields  and return if venue range isn't one of the following
    if($("#selectVenueRange").val() != 'STATE' &&
       $("#selectVenueRange").val() != 'CITY'  &&
       $("#selectVenueRange").val() != 'VENUE')
    {
      $(".state").fadeOut();
      $(".city").fadeOut();
      $(".venue").fadeOut();
      return;
    }
   
    // STATES
    $(".state").fadeIn();
    updateSelectState();
    
    // fade out fields  and return if venue range isn't one of the following
    if($("#selectVenueRange").val() !== 'CITY'  &&
       $("#selectVenueRange").val() !== 'VENUE')
    {
      $(".city").fadeOut();
      $(".venue").fadeOut();
      return;
    }
    
    // CITY
    $(".city").fadeIn();
    updateSelectCity();
    
    // fade out fields  and return if venue range isn't one of the following
    if($("#selectVenueRange").val() !== 'VENUE')
    {
      $(".venue").fadeOut();
      return;
    }
    
    $(".venue").fadeIn();
    updateSelectVenue();
  });
});

/**
 * Returns all the states matching the country in the array. 
 * This will also return duplicates.
 * @param {string} sCountry
 * @returns {getStatesFromCountry.aStates|Array} array of states
 * @todo refactor to handle states, cities, and venues based on params
 */
function getStatesFromCountry(sCountry)
{
  var aStates = [];
  var nCount = gaUserVenues.length;
  for(var i = 0; i < nCount; ++i)
  {
    if(sCountry.trim().toUpperCase() === gaUserVenues[i]['country'].trim().toUpperCase())
    {
      aStates.push(gaUserVenues[i]['state']);
    }
  }
  
  return aStates;
}

/**
 * Get cities based on country and state
 * @param {string} sCountry
 * @param {string} sState
 * @returns {Array|getCitiesFromCountryState.aCities|undefined} array of cities
 */
function getCitiesFromCountryState(sCountry, sState)
{
  if(sCountry === '' || sState === '')
  {
    alert('Either country or state not provided.');
    return;
  }
  
  var aCities = new Array();
  var nCount = gaUserVenues.length;
  for(var i = 0; i < nCount; ++i)
  {
    if(sCountry.trim().toUpperCase() === gaUserVenues[i]['country'].trim().toUpperCase() && 
       sState.trim().toUpperCase() === gaUserVenues[i]['state'].trim().toUpperCase())
    {
      aCities.push(gaUserVenues[i]['city']);
    }
  }
  
  return aCities;
}

function uniqueArray(aDataRows, sKey)
{
  var aUnique = [];
  
  var nLength = aDataRows.length;
  for(var i = 0; i < nLength; ++i)
  {
    var sValueToAdd = '';
    if('' != sKey)
    {
      sValueToAdd = aDataRows[i][sKey];
    }
    else
    {
      sValueToAdd = aDataRows[i];
    }
    
    if( !isInArray(sValueToAdd, aUnique) )
    {
      aUnique.push(sValueToAdd);
    }
  }
  
  return aUnique;
}

/**
 * If the value is already in the array, returns true.
 * @param {string} sValue the value to search for
 * @param {array} aArray array of the values that will be searched
 * @returns {Boolean} true if value in array. False otherwise.
 */
function isInArray(sValue, aArray)
{
  for(sArrayValue in aArray)
  {
    if(aArray[sArrayValue].trim().toUpperCase() === sValue.trim().toUpperCase())
    {
      return true;
    }
  }
  
  return false;
}

$(document).ready(function(){
  $("#selectCountry").change(function(){
    // update states
    updateSelectState();
    // update cities
    updateSelectCity();
    // update venues
    updateSelectVenue();
  });
});

$(document).ready(function(){
  $("#selectState").change(function(){
    // update cities
    updateSelectCity();
    // update venues
    updateSelectVenue();
  });
});

$(document).ready(function(){
  $("#selectCity").change(function(){
    // update venues
    updateSelectVenue();
  });
});

function updateSelectState()
{
  // get states from country
    var aStates = getStatesFromCountry($("#selectCountry").val());
    aStates = uniqueArray(aStates, '');
    var nCount = aStates.length;
    var sOption = '';
    for(var i = 0; i < nCount; ++i)
    {
      sOption += "<option value='" + 
              aStates[i] + 
              "'>" + 
              aStates[i] +
              "</option>";
      $("#selectState").html(sOption);
    }
}

function updateSelectCity()
{
 // get selected country and state
    var sCountry = $("#selectCountry").val();
    var sState = $("#selectState").val();
    
    // get cities from country and state
    var aCities = getCitiesFromCountryState(sCountry, sState);
    aCities = uniqueArray(aCities, '');
    var nCount = aCities.length;
    var sOption = '';
    for(var i = 0; i < nCount; ++i)
    {
      sOption += "<option value='" + 
              aCities[i] + 
              "'>" + 
              aCities[i] +
              "</option>";
      $("#selectCity").html(sOption);
    } 
}

function updateSelectVenue()
{
  var sCountry = $("#selectCountry").val();
  var sState = $("#selectState").val();
  var sCity = $("#selectCity").val();
  
  var aVenues = getVenuesFromCountryStateCity(sCountry, sState, sCity);
  var nCount = aVenues.length;
  var sOption = '';
  for(var i = 0; i < nCount; ++i)
  {
    sOption += "<option value='" + 
              aVenues[i][0] + 
              "'>" + 
              aVenues[i][1] +
              "</option>";
      $("#selectVenue").html(sOption);
  }
}

function getVenuesFromCountryStateCity(sCountry, sState, sCity)
{
  if(sCountry === '' || sState === '' || sCity === '')
  {
    alert("Country, state, and city must be defined.");
    return;
  }
  
  var aVenues = [];
  var nCount = gaUserVenues.length;
  for(var i = 0; i < nCount; ++i)
  {
    if( sCountry.trim().toUpperCase() === gaUserVenues[i]['country'].trim().toUpperCase() &&
        sState.trim().toUpperCase() === gaUserVenues[i]['state'].trim().toUpperCase() &&
        sCity.trim().toUpperCase() === gaUserVenues[i]['city'].trim().toUpperCase())
    {
      aVenues.push([gaUserVenues[i]['id'],gaUserVenues[i]['name']]);
    }
  }
  
  return aVenues;
}

function deleteDateTimeframe(
  sRowID,
  sUserLogin,
  nVenueRange,
  nDateType,
  sCountry,
  sState,
  sCity,
  nVenueID
  )
{
  $.post( 
    "ajax/DeleteDateTimeframe.php",
    { user_login: sUserLogin, venue_range: nVenueRange, date_type: nDateType,
      country: sCountry, state: sState, city: sCity, venue_id: nVenueID
    },
    function() {
      $('#'+sRowID).fadeOut();
      // update date/timeframe user-friendly div
      $.post(
        "ajax/DisplayDatesTimeFrames.php",
        { user_login: sUserLogin },
        function(data){
          $("#datesAndTimes").html(data);
          }
        );
      }
    );
}


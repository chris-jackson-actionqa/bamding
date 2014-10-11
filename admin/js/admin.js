/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

var aDates = new Array();

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

$(document).ready(function(){
  $("#selectVenueRange").change(function(){
    $("#fieldsetChooseVenueRangeValues").show();
    
    $("#selectCountry").html("");
    
    var nCount = gaUserVenues.length;
    for(var i = 0; i < nCount; ++i)
    {
      sOptions = $("#selectCountry").html();
      sOption = "<option value='" + 
              gaUserVenues[i].country + 
              "'>" + 
              gaUserVenues[i].country +
              "</option>";
      $("#selectCountry").html(sOptions + sOption);
    }
  });
});

function uniqueArray(aDataRows, sKey)
{
  
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
    if(sArrayValue == sValue)
    {
      return true;
    }
  }
  
  return false;
}
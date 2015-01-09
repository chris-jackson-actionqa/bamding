/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var BAMDING = {
    MYVENUES : {
        _venues: null,
        _user_login: "",
        /**
         * toggle the "Apply" button's disabled property
         * Disable if "Bulk Action" is selected.
         * Disable if no venues selected on other settings.
         * Enable if venues are selected and "Bulk Action" isn't selected.
         * 
         * @returns {undefined}
         */
        toggleBulkApply: function()
        {
          top_select = document.getElementById('bd_venues_bulk_action_top');
          option = top_select.options[top_select.options.selectedIndex].value;
          top_apply_button = document.getElementById('btn_myven_apply_top');
          bottom_apply_button = document.getElementById('btn_myven_apply_bottom');
          if('bulk' === option || !BAMDING.MYVENUES.isVenueSelected())
          {
              top_apply_button.disabled = true;
              top_apply_button.className = "btn_disabled";
              
              bottom_apply_button.disabled = true;
              bottom_apply_button.className = "btn_disabled";
          }
          else
          {
              top_apply_button.disabled = false;
              top_apply_button.className = "btn_enabled";
              
              bottom_apply_button.disabled = false;
              bottom_apply_button.className = "btn_enabled";
          }
        },
        
        /**
         * Change the top and bottom select boxes to the same selected option.
         * 
         * @param {string} select_box_id the box that triggered the change event
         */
        coordinateSelectBoxes: function(select_box_id)
        {
          select_box = document.getElementById(select_box_id);
          top_select = document.getElementById('bd_venues_bulk_action_top');
          bottom_select = document.getElementById('bd_venues_bulk_action_bottom');

          top_select.options.selectedIndex = select_box.options.selectedIndex;
          bottom_select.options.selectedIndex = select_box.options.selectedIndex;
        },
        
        /**
         * When the bulk action selection changes, 
         * coordinate the values of both the bulk action boxes and
         * enable or disable the "Apply" button appropriately
         * @param {type} elem
         * @returns {undefined}
         */
        changeBulkActionSelection: function(elem)
        {
          BAMDING.MYVENUES.coordinateSelectBoxes(elem.id);
          BAMDING.MYVENUES.toggleBulkApply();
        },
        
        /**
         * Is there a venue that's selected (checkbox is checked)
         * @returns {Boolean}
         */
        isVenueSelected: function()
        {
          var foundSelected = false;
          
          inputs = document.getElementsByTagName("input");
          var numInputs = inputs.length;
          for(var i = 0; i < numInputs; ++i)
          {
            input = inputs[i];
            if(input.type !== "checkbox")
            {
              continue;
            }
            else if (input.name === "bd_select_all_venues")
            {
              continue;
            }
            else if (input.checked)
            {
              foundSelected = true;
              break;
            }
          }
          
          return foundSelected;
        },
        
        filterVenues: function()
        {
          var myvenues = BAMDING.MYVENUES;
          if( null === myvenues._venues)
          {
            myvenues.getAllVenues(myvenues._user_login);
          }
          
          // delete all rows except header
          myvenues.deleteTableRows();
          
          // get filtered venues
          filtered = BAMDING.MYVENUES.getFilteredVenues();
          
          // build the table
          myvenues.buildTableRows(filtered);
        },
        
        getAllVenues: function(user_login) 
        {
          jQuery(document).ready(function($) 
          {
            $.post( getBaseURL() + "/wp-content/ajax/GetAllVenues.php",
            {
              user_login: user_login
            },
            function(data, status)
            {
              BAMDING.MYVENUES._venues = JSON.parse(data);
              BAMDING.MYVENUES._user_login = user_login;
            });
          });
        },
        
        deleteTableRows: function()
        {
          var table = document.getElementById('venues_table');
          var num_rows = table.rows.length;
          for( var i = num_rows -1; i > 0; --i)
          {
            table.deleteRow(i);
          }
        },
        
        buildTableRows: function(venues)
        {
          var table = document.getElementById('venues_table');
          var num_venues = venues.length;
          for( var i = 0; i < num_venues; ++i)
          {
            var row = table.insertRow(-1);
            
            // checkbox
            var cell = row.insertCell(-1);
            cell.innerHTML = "<input type='checkbox' " +
                             "name=\"" + venues[i].name + "\"" + 
                             "value=\"" + venues[i].id + "\"" +
                             "onchange=\"uncheckMyVenuesHeaderCheckbox(); " + 
                             "BAMDING.MYVENUES.toggleBulkApply();\">";
            
            // Venue name
            cell = row.insertCell(-1);
            cell.innerHTML = "<a href=\"" + 
                    getBaseURL() + "/editvenue?venue_id=" + venues[i].id +
                    "\">" + venues[i].name + "</a>";
            
            // city
            cell = row.insertCell(-1);
            cell.innerHTML = venues[i].city;
            
            // state
            cell = row.insertCell(-1);
            cell.innerHTML = venues[i].state;
            
            // country
            cell = row.insertCell(-1);
            cell.innerHTML = venues[i].country;
            
            // category
            cell = row.insertCell(-1);
            cell.innerHTML = venues[i].category;
          }
        },
        
        getFilteredVenues: function()
        {
          // get filter type
          var filter_select = document.getElementById("filter_venues_select");
          var filter_index = filter_select.options.selectedIndex;
          var filter_type = filter_select.options[filter_index].text;
          
          // get filter
          var search = document.getElementById("filter_venues_input").value;
          search = search.trim().toLowerCase();
          
          // get venues
          var venues = BAMDING.MYVENUES._venues;
          
          // if the search is empty, return all venues
          if( "" === search)
          {
            return venues;
          }
          
          var filtered = [];
          var is_all = "Filter: All" === filter_type;
          var is_city = "Filter: City" === filter_type;
          var is_venue_name = "Filter: Name" === filter_type;
          var is_state = "Filter: State" === filter_type;
          var is_country = "Filter: Country" === filter_type;
          var is_category = "Filter: Category" === filter_type;
          for( var i = 0; i < venues.length; ++i)
          {
            var name = venues[i].name.trim().toLowerCase();
            var match_name = (is_venue_name || is_all) && -1 !== name.search(search);
            
            var city = venues[i].city.trim().toLowerCase();
            var match_city = (is_city || is_all) && -1 !== city.search(search);
            
            var state = venues[i].state.trim().toLowerCase();
            var match_state = (is_state || is_all) && -1 !== state.search(search);
            
            var country = venues[i].country.trim().toLowerCase();
            var match_country = (is_country || is_all) && -1 !== country.search(search);
            
            var category = venues[i].category.trim().toLowerCase();
            var match_category = (is_category || is_all) && -1 !== category.search(search);
            
            if(match_city || match_name || match_state  
                    || match_country || match_category)
            {
              filtered.push(venues[i]);
              continue;
            }
          }
          
          return filtered;
        }
    }
};


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

//----------------------------------
// My Venues
//----------------------------------



/**
 * Toggle all the input checkboxes in the table
 * @param {object} ele the element calling this function
 * @returns {undefined}
 */
function toggleAllMyVenuesCheckBoxes(ele)
{
  var checkboxes = document.getElementsByTagName('input');
    for (var i = 0; i < checkboxes.length; i++) 
    {
        if (checkboxes[i].type === 'checkbox') 
        {
            checkboxes[i].checked = ele.checked;
        }
    }
}

/**
 * Uncheck the header checkbox on My Venues table
 */
function uncheckMyVenuesHeaderCheckbox()
{
    var checkbox = document.getElementById("my_venues_header_checkbox");
    checkbox.checked = false;
}

/**
 * 
 */
function applyMyVenuesForm(form)
{
    select_box = document.getElementById("bd_venues_bulk_action_top");
    option = select_box.options.item(select_box.options.selectedIndex).value;
    
    
    switch(option)
    {
      case 'remove':
        document.bdVenueList.action = getBaseURL() + "/removevenue/";
        break;
      case 'category':
        document.bdVenueList.action = getBaseURL() + "/category/";
        break;
    }
}

function confirmRemoveMyVenues()
{
  document.bdVenueList.action = getBaseURL() + "/removevenue/";
}

function toggleMyVenuesApplyButton()
{
  top_select = document.getElementById('bd_venues_bulk_action_top');
  option = top_select.options[top_select.options.selectedIndex].value;
  top_apply_button = document.getElementById('btn_myven_apply_top');
  bottom_apply_button = document.getElementById('btn_myven_apply_bottom');
  if('bulk' === option)
  {
    
  }
}



//---------------------------------
// CATEGORY
//---------------------------------

/**
 * When selection changes
 * For blank selection, disable submit and "add new".
 * For "add new", disable submit and show add new input.
 * For a legit option, enable submit.
 * @param {type} elem element selected
 * @returns {undefined}
 */
function categorySelected(elem)
{
  selected = elem.options.selectedIndex;
  value = elem.options.item(selected).value;
  
  switch(value)
  {
    case "blank":
      categoryDisableSubmit();
      categoryHideAddNew();
      break;
    case "add_new":
      categoryDisableSubmit();
      categoryShowAddNew();
      break;
    default:
      categoryEnableSubmit();
      categoryHideAddNew();
      break;
  }
}

/**
 * Disable submit button
 * @returns {undefined}
 */
function categoryDisableSubmit()
{
  elem = document.getElementById("btn_category_submit");
  elem.disabled = true;
  elem.style.backgroundColor = "#cccccc";
  elem.style.opacity = 0.5;
}

/**
 * Enable submit button
 * @returns {undefined}
 */
function categoryEnableSubmit()
{
  elem = document.getElementById("btn_category_submit");
  elem.disabled = false;
  elem.style.backgroundColor = "#000000";
  elem.style.opacity = 1.0;
}

/**
 * Show the add new textbox and button
 * @returns {undefined}
 */
function categoryShowAddNew()
{
  textbox = document.getElementById("txt_new_category");
  textbox.style.display = "inline";
  textbox.textContent = "";
  textbox.focus();
  
  button = document.getElementById("btn_add_category");
  button.style.display = "inline";
  button.disabled = true;
  button.style.backgroundColor = "#cccccc";
  button.style.opacity = 0.5;
}

/**
 * Hide the add new textbox and button
 * @returns {undefined}
 */
function categoryHideAddNew()
{
  textbox = document.getElementById("txt_new_category");
  button = document.getElementById("btn_add_category");
  
  textbox.value = "";
  textbox.style.display = "none";
  button.style.display = "none";
}

/**
 * Disable the add new button when the textbox is empty
 * @param {type} textbox
 * @returns {undefined}
 */
function disableAddNewOnEmpty(textbox)
{
  button = document.getElementById("btn_add_category");
  if( textbox.value.trim() === "")
  {
    button.disabled = true;
    button.style.backgroundColor = "#cccccc";
    button.style.opacity = 0.5;
  }
  else
  {
    button.disabled = false;
    button.style.backgroundColor = "#000000";
    button.style.opacity = 1.0;
  }
}

/**
 * Add the "add new" textbox value to the dropdown menu of categories
 * @returns {undefined}
 */
function addCategoryToDropdown()
{
  form = document.getElementById("form_category");
  select = document.getElementById("select_category");
  category = document.getElementById("txt_new_category");
  
  if(doesCategoryAlreadyExist(category.value))
  {
    alert("Category already exists.");
    
    // select the already existing category
    for( var i = 0; i < select.options.length; ++i)
    {
      var option = select.options[i].text;
      option = option.trim().toLowerCase();
      var val = category.value;
      val = val.trim().toLowerCase();
      
      if(val === option)
      {
        select.options.selectedIndex = i;
      }
    }
  }
  else
  {
    select.innerHTML += 
            '<option value="' + 
            category.value.trim() + 
            '">' + 
            category.value.trim() + 
            '</option>';
    select.options.selectedIndex = select.length - 1;
  }
  
  categoryHideAddNew();
  categoryEnableSubmit();
}

/**
 * Check if the new category is already in the options
 * @param {string} category new category to check for
 * @returns {Boolean}
 */
function doesCategoryAlreadyExist(category)
{
  category = category.trim().toLowerCase();
  var select = document.getElementById("select_category");
  for( var i = 0; i < select.options.length; ++i )
  {
    var option = select.options[i].text;
    option = option.trim().toLowerCase();
    if( category === option)
    {
      return true;
    }
  }
  
  return false;
}
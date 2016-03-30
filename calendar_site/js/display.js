months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
daysOfWeek = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
year_dropdown_element = document.getElementsByClassName("year-dropdown")[0];
month_dropdown_element = document.getElementsByClassName("month-dropdown")[0];
table_element = document.getElementsByClassName("calendar-table")[0];
month_in_view = new Date(); // only used to keep track of year and month. date is irrelevant.
first_day = month_in_view; // just to instantiate first_day
cells = new Array(); // holds empty calendar cells with the date of that calendar
id_to_event = {}; // holds events

/* Need to implement/fix;
Year dropdown dynamically changes available years
truncate title with ... if multi-line
*/

function formatAMPM(date) {
  // prints time of date as eg 5:00pm
  var hours = date.getHours();
  var minutes = date.getMinutes();
  var ampm = hours >= 12 ? 'pm' : 'am';
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  minutes = minutes < 10 ? '0'+minutes : minutes;
  var strTime = hours + ':' + minutes + ampm;
  return strTime;
}

function fillYearDropdown(year, yearRange){
  var startYear = year - yearRange;
  var endYear = year + yearRange;
  for (var i = startYear; i <= endYear; i++){
    var newa = document.createElement("a");
    newa.textContent = i;
    var newLi = document.createElement("li");
    newLi.appendChild(newa);
    year_dropdown_element.appendChild(newLi);
  }
  $(year_dropdown_element).siblings(".btn").children(".selected-option")[0].textContent = year;
  $(".btn .selected-option.year").val(year);
}

function fillMonthDropdown(month){
  for (var i = 0; i < months.length; i++){
    var newa = document.createElement("a");
    newa.textContent = months[i];
    var newLi = document.createElement("li");
    newLi.appendChild(newa);
    month_dropdown_element.appendChild(newLi);
  }
  $(month_dropdown_element).siblings(".btn").children(".selected-option")[0].textContent = months[month];
  $(".btn .selected-option.month").val(month);
}

function addDropdownWatchers(){
  $(".color-dropdown li a").click(function(){
    $(".btn .selected-option.color").text($(this).text());
    $(".btn .selected-option.color").val($(this).text());
  });

  $(".year-dropdown li a").click(function(){
    $(".btn .selected-option.year").text($(this).text());
    $(".btn .selected-option.year").val($(this).text());
    month_in_view.setFullYear($(this).text());
    refreshMonthCells();
    getEvents();
  });
  $(".month-dropdown li a").click(function(){
    index = $( ".month-dropdown li a" ).index( this );
    $(".btn .selected-option.month").text($(this).text());
    $(".btn .selected-option.month").val(index);
    month_in_view.setMonth(index);
    refreshMonthCells();
    getEvents();
  });
  $(".prev-month").click(function(){
    month_in_view = new Date(month_in_view.getFullYear(), month_in_view.getMonth()-1, 1);
    $(".btn .selected-option.month").text(months[month_in_view.getMonth()]);
    $(".btn .selected-option.month").val(month_in_view.getMonth());
    $(".btn .selected-option.year").text(month_in_view.getFullYear());
    $(".btn .selected-option.year").val(month_in_view.getFullYear());
    refreshMonthCells();
    getEvents();
  });
    $(".next-month").click(function(){
    month_in_view = new Date(month_in_view.getFullYear(), month_in_view.getMonth()+1, 1);
    $(".btn .selected-option.month").text(months[month_in_view.getMonth()]);
    $(".btn .selected-option.month").val(month_in_view.getMonth());
    $(".btn .selected-option.year").text(month_in_view.getFullYear());
    $(".btn .selected-option.year").val(month_in_view.getFullYear());
    refreshMonthCells();
    getEvents();
  });
}

function buildEmptyCalendar(){
  var tableHead = document.createElement("thead");
  var headRow = document.createElement("tr");
  for (var i = 0; i < daysOfWeek.length; i++){
    var cell = document.createElement("th");
    cell.setAttribute("class", "day-of-week");
    var cellText = document.createTextNode(daysOfWeek[i]);
    cell.appendChild(cellText);
    headRow.appendChild(cell);
  }
  tableHead.appendChild(headRow);
  table_element.appendChild(tableHead);

  var tableBody = document.createElement("tbody");
  var table_cell_id = 0;
  for (var r = 0; r < 6; r++){
    var row = document.createElement("tr");
    for (var c = 0; c < 7; c++) {
      var cell = document.createElement("td");
      $(cell).attr({
        class: "day", 
        id: table_cell_id
      });
      var cellDate = document.createElement("span");
      $(cellDate).attr({
        class: "date"
      });
      var cellEventsTable = document.createElement("table");
      $(cellEventsTable).attr({
        class: "events_in_day"
      });
      table_cell_id++;
      cell.appendChild(cellDate);
      cell.appendChild(cellEventsTable);
      row.appendChild(cell);
    }
    tableBody.appendChild(row);
  }
  table_element.appendChild(tableBody);
}

function cell(id, dt) {
  this.cell_id = id;
  this.date = dt;
}

function refreshMonthCells(){
  // determine what day to start populating the table with
  var dummyDate = new Date(month_in_view.getFullYear(), month_in_view.getMonth(),1);
  var this_month_first_day_of_week = dummyDate.getDay();
  if (this_month_first_day_of_week == 0){
    this_month_first_day_of_week += 7;
  }
  dummyDate.setDate(1-this_month_first_day_of_week); // start date
  first_day = new Date(dummyDate);
  cells = [];
  for (var i = 0; i < 42; i++){
    cells.push(new cell(i, new Date(dummyDate)));
    dummyDate.setDate(dummyDate.getDate()+1);

  }
  populateTable(cells);
}

function populateTable(cells){
  for (var i = 0; i < 42; i++){
    $("#"+i+" > span")[0].textContent = cells[i].date.getDate();
  }
}

function eventTemplate(title, date, body, tag, id) {
  this.title = title;
  this.date = date;
  this.body = body;
  this.tag = tag;
  this.id = id;
}

function getEvents(){
  for (var i = 0; i < 42; i++){
    $("#"+i+" > table")[0].textContent = "";
  }
  if ( $("#get_holiday").hasClass("unclickable") ) {
        getHoliday(event);
    }
  var xmlHttp = new XMLHttpRequest(); // Initialize our XMLHttpRequest instance
  // send month_in_view stuff
  var token = readCookie("token");
  xmlHttp.open("POST", "~/../php/getEvents.php", true); // Starting a POST request (NEVER send passwords as GET variables!!!)
  xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // It's easy to forget this line for POST requests
  xmlHttp.addEventListener("load", getEventsCallback, false); // Bind the callback to the load event
  xmlHttp.send("year="+first_day.getFullYear()+"&month="+(first_day.getMonth()+1)+"&date="+first_day.getDate()+ "&token=" + encodeURIComponent(token));
}

function getEventsCallback(event){
  id_to_event = {};
  // console.log(event.target.responseText);
  var jsonData = JSON.parse(event.target.responseText);
  cellIndex = 0;
  if (!(jQuery.isEmptyObject(jsonData))){
    jQuery.each(jsonData, function() {
      var t = this.datetime.split(/[- :]/);
      var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
      var id = this.event_id;
      myEvent = new eventTemplate(this.title, d, this.body, this.tag, this.id);
      while(myEvent.date.getDate() != cells[cellIndex].date.getDate() 
        || myEvent.date.getMonth() != cells[cellIndex].date.getMonth() 
        || myEvent.date.getFullYear() != cells[cellIndex].date.getFullYear() 
        && cellIndex < 42){
        cellIndex++;
    }
    id_to_event["event_"+this.event_id] = myEvent;
    var eventRow = document.createElement("tr");
    $(eventRow).attr({
      class : "event",
      id : "event_"+id
    });
    $(eventRow).click(function(){
      showEvent(id);
    });
    var timeCol = document.createElement("td");
    var titleCol = document.createElement("td");
    var timeText = document.createTextNode(formatAMPM(myEvent.date));
    var titleText = document.createTextNode(myEvent.title);
    $(timeCol).attr({
      class : myEvent.tag
    });
    $(titleCol).attr({
      class : myEvent.tag
    });
    timeCol.appendChild(timeText);
    eventRow.appendChild(timeCol);
    titleCol.appendChild(titleText);
    eventRow.appendChild(titleCol);
    $("#"+cellIndex +" > table")[0].appendChild(eventRow);
    filterByColor();
    // If session color is set, hide if not the color.
    // see if can hide then append, or must append then hide.
  });
  }

}

function filterByColor(){
  colors = JSON.parse(readCookie("colors"));
  // if it's in the cookie but is not checked, check it
  $(":checkbox").each(function() {
    if ($.inArray(this.value, colors)>-1 && (!(this.checked))){
      $(this).prop('checked', true);
    }
  });
  $.each(id_to_event, function( key, value ) {
    if (colors == null || colors.length === 0){
      $("#"+key).show();
    }
    else if ($.inArray(value.tag, colors)>-1){
      $("#"+key).show();
    }else{
      $("#"+key).hide();
    }
  });
}

// cookie functions are from http://stackoverflow.com/questions/14573223/set-cookie-and-get-cookie-with-javascript
function createCookie(name,value,days) {
  if (days) {
    var date = new Date();
    date.setTime(date.getTime()+(days*24*60*60*1000));
    var expires = "; expires="+date.toGMTString();
  }
  else var expires = "";
  document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}

function eraseCookie(name) {
  createCookie(name,"",-1);
}

function doUponLoading(event){
  fillYearDropdown(month_in_view.getFullYear(), 2);
  fillMonthDropdown(month_in_view.getMonth());
  buildEmptyCalendar();
  addDropdownWatchers();
  refreshMonthCells();
  addSignupAndTagWatchers();
  loginCheck();
  addEvent();
  editEvent();
  deleteEvent();
  displayHoliday();
}


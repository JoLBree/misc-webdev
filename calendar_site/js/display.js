months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
daysOfWeek = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
year_dropdown_element = document.getElementsByClassName("year-dropdown")[0];
month_dropdown_element = document.getElementsByClassName("month-dropdown")[0];
table_element = document.getElementsByClassName("calendar-table")[0];
month_in_view = new Date(); // only used to keep track of year and month. date is irrelevant.

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
      var cellText = document.createTextNode("");
      table_cell_id++;
      cell.appendChild(cellText);
      row.appendChild(cell);
    }
    tableBody.appendChild(row);
  }
  table_element.appendChild(tableBody);
}

function addEventWatchers(){
  $(".year-dropdown li a").click(function(){
    $(".btn .selected-option.year").text($(this).text());
    $(".btn .selected-option.year").val($(this).text());
    month_in_view.setFullYear($(this).text());
    refreshMonthCells();
  });
  $(".month-dropdown li a").click(function(){
    index = $( ".month-dropdown li a" ).index( this );
    $(".btn .selected-option.month").text($(this).text());
    $(".btn .selected-option.month").val(index);
    month_in_view.setMonth(index);
    refreshMonthCells();
      // alert( "Handler for .change() called."+index); // for debugging
    });
}

// 
// move to data.js
// since this part is dynamically dependent
// 

function getEvents() {
  return 1; //FIXME
}

function cell(id, yr, mth, dt) {
  this.cell_id = id;
  this.cell_year = yr;
  this.cell_month = mth;
  this.cell_date = dt;
  this.cell_events = getEvents();
}

function refreshMonthCells(){
  var cells = [];
  var dummyDate = new Date(month_in_view.getFullYear(), month_in_view.getMonth(),1);
  var this_month_first_day_of_week = dummyDate.getDay();
  if (this_month_first_day_of_week == 0){
    this_month_first_day_of_week += 7;
  }
  dummyDate.setDate(1-this_month_first_day_of_week); // start date
  for (var i = 0; i < 42; i++){
    cells.push(new cell(i, dummyDate.getFullYear(), dummyDate.getMonth(), dummyDate.getDate()));
    dummyDate.setDate(dummyDate.getDate()+1);
  }
  populateTable(cells);
}

function populateTable(cells){
  for (var i = 0; i < 42; i++){
    test = $("#"+i).contents().filter(function() {
      return this.nodeType == 3;
    })[0].nodeValue = cells[i].cell_date;
  }
}
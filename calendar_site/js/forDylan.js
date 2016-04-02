
// currently, showEvent is called and passes in the id of the event.
// Go ahead and modify this as you like for the modal. I'll leave it modal stuff up to you so we can keep it consistent
function displayHoliday(){
  $("#get_holiday")[0].addEventListener("click", getHoliday, false);
  $(".move").click(function(){
      $("#get_holiday").removeClass( "unclickable");
  });
}
function getHoliday(event){

if ( $(this).hasClass("unclickable") ) {
        event.preventDefault();
    } else {
        $(this).addClass("unclickable");
  var year = document.getElementById("gyear").value;
  var month = months.indexOf(document.getElementById("gmonth").textContent)+1;
  var index = 0;
  for (var i = 0; i < 42; i++){
       var temp = $("#"+i+" > span")[0].textContent;
    if (temp == '1'){
       index = i;
       break;
    }
  }
  $.getJSON("http://holidayapi.com/v1/holidays?country=US&year="+year+"&month="+month, function(json) {
    if (json.holidays.length>0){
      for (i=0;i<json.holidays.length;i++) {
        var eventRow = document.createElement("tr");
        $(eventRow).attr({
          class : "event holiday",
        });
        var holidayRow = document.createElement("td");
        var holidayText = document.createTextNode(json.holidays[i].name);
        holidayRow.appendChild(holidayText);
        eventRow.appendChild(holidayRow);
        var date = json.holidays[i].date;
        var len  = date.length;
        if(date.charAt(len-2)==0){
          var cellIndex = date.charAt(len-1);
        }
        else{
          var cellIndex = date.charAt(len-2) + date.charAt(len-1);
        }
        index = Number(index) + Number(cellIndex)-1;
        $("#"+index+" > table")[0].appendChild(eventRow);
      }
    }
  else{
    alert("Cannot get holidays");
  }
  });
}
}


function showEvent(id){
  $("#show-event-modal").modal('show');
  $("#show_title").val(id_to_event["event_"+id].title);
  $("#show_description").val(id_to_event["event_"+id].body);
  $("#show_color").text(id_to_event["event_"+id].tag);
  $("#show_color").val(id_to_event["event_"+id].tag);
  $("#show_id").val(id);
  date = id_to_event["event_"+id].date;
  var hours = date.getHours();
  var minutes = date.getMinutes();
  minutes = minutes < 10 ? '0'+minutes : minutes;
  var m = '';
  if(hours>12){
    hours -=12;
    m = 'PM'
  }
  else{
    m = 'AM'
  }
  var add = '0';
  var month = date.getMonth()+1;
  var day = date.getDate();
  month = month < 10 ? '0'+month : month;
  day = day < 10 ? '0'+day : day;
  var strTime = month+'/'+day+'/'+date.getFullYear()+' '+hours + ':' + minutes+' '+m ;
  $(".show_date").val(strTime);
}

function addEvent(){
  $("#event_add")[0].addEventListener("click", add, false);
}

function add(){
  var color = document.getElementById("new_color").value; 
  var title = document.getElementById("new_title").value;
  var date = document.getElementById("datetimepicker2").value;
  var description = document.getElementById("new_description").value;
  var token = readCookie("token");
  var dataString = "new_title=" + encodeURIComponent(title) + "&new_date=" + encodeURIComponent(date)+ "&new_description=" + encodeURIComponent(description)+ "&new_color=" + encodeURIComponent(color) + "&token=" + encodeURIComponent(token);
  var xmlHttp = new XMLHttpRequest();
  xmlHttp.open("POST", "~/../php/addEvent.php", true);
  xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xmlHttp.addEventListener("load", function(event){
    var jsonData = JSON.parse(event.target.responseText);
    if(jsonData.success){
      getEvents();
      $("#add-event-modal").modal('hide');
    }else{
      alert(jsonData.message);
    }
  }, false);
  xmlHttp.send(dataString);
}

function editEvent(){
  $("#event_edit")[0].addEventListener("click", edit, false);

}

function edit(){
  var color = document.getElementById("show_color").value;  
  var title = document.getElementById("show_title").value;
  var date = document.getElementById("datetimepicker1").value;
  var event_id = document.getElementById("show_id").value;
  var description = document.getElementById("show_description").value;
  var token = readCookie("token");
  var dataString = "new_title=" + encodeURIComponent(title) + "&new_date=" + encodeURIComponent(date)+ "&new_description=" + encodeURIComponent(description)+ "&new_color=" + encodeURIComponent(color)+ "&event_id="+encodeURIComponent(event_id) + "&token=" + encodeURIComponent(token);
  var xmlHttp = new XMLHttpRequest();
  xmlHttp.open("POST", "~/../php/editEvent.php", true);
  xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xmlHttp.addEventListener("load", function(event){
    var jsonData = JSON.parse(event.target.responseText);
    if(jsonData.success){
      getEvents();
      $("#show-event-modal").modal('hide');
    }else{
      alert(jsonData.message);
    }
  }, false);
  xmlHttp.send(dataString);
}

function deleteEvent(){
  $("#event_delete")[0].addEventListener("click", deletee, false);
}

function deletee(){
  var event_id = document.getElementById("show_id").value;
  var token = readCookie("token");
  var dataString = "event_id="+encodeURIComponent(event_id) + "&token=" + encodeURIComponent(token);
  var xmlHttp = new XMLHttpRequest();
  xmlHttp.open("POST", "~/../php/deleteEvent.php", true);
  xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xmlHttp.addEventListener("load", function(event){
    var jsonData = JSON.parse(event.target.responseText);
    if(jsonData.success){
      getEvents();
      $("#show-event-modal").modal('hide');
    }else{
      alert(jsonData.message);
    }
  }, false);
  xmlHttp.send(dataString);
}
function addSignupAndTagWatchers(){
  $("#create_btn")[0].addEventListener("click", signup, false);
  var colors = [];
  $(":checkbox").change(function() {
    if(this.checked) {
      colors.push(this.value);
    }else{
      var index = colors.indexOf(this.value);
      colors.splice(index, 1);
    }
    createCookie("colors", JSON.stringify(colors), 1);
    filterByColor();
  });

}

function signup(){
  var username = document.getElementById("new_username").value;
  var password = document.getElementById("new_password").value;
  var dataString = "new_username=" + encodeURIComponent(username) + "&new_password=" + encodeURIComponent(password);
  var xmlHttp = new XMLHttpRequest();
  xmlHttp.open("POST", "~/../php/signup.php", true);
  xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xmlHttp.addEventListener("load", function(event){
    var jsonData = JSON.parse(event.target.responseText);
    if(jsonData.success){
      getEvents();
      refreshUserButtons(true);
      $("#create-user-modal").modal('hide');
      $(".color_selector").show();
    }else{
      alert(jsonData.message);
      refreshUserButtons(false);
    }
  }, false);
  xmlHttp.send(dataString);
}

function loginCheck(){
 var xmlHttp = new XMLHttpRequest();
 xmlHttp.open("POST", "~/../php/logincheck.php", true);
 xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
 xmlHttp.addEventListener("load", function(event){
  var jsonData = JSON.parse(event.target.responseText);
  if(jsonData.loggedin == true){ 
    createCookie("token", jsonData.token, 1);
    refreshUserButtons(jsonData.loggedin);
    getEvents();
    $(".color_selector").show();
  }else{
    refreshUserButtons(jsonData.loggedin);
    getEvents();
    $(".color_selector").hide();
  }
}, false);
 xmlHttp.send("dataString");
}

function login(event){
  var username = document.getElementById("username").value;
  var password = document.getElementById("password").value;
  var dataString = "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password);
  var xmlHttp = new XMLHttpRequest();
  xmlHttp.open("POST", "~/../php/login.php", true);
  xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xmlHttp.addEventListener("load", function(event){
    var jsonData = JSON.parse(event.target.responseText);
    if(jsonData.success){
      createCookie("token", jsonData.token, 1);
      getEvents();
      refreshUserButtons(true);
      $(".color_selector").show();
    }else{
      alert(jsonData.message);
      refreshUserButtons(false);
    }
  }, false);
  xmlHttp.send(dataString);
}

function logout(event){
  var token = readCookie("token");
  var dataString = "token=" + encodeURIComponent(token);
  var xmlHttp = new XMLHttpRequest();
  xmlHttp.open("POST", "~/../php/logout.php", true); 
  xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xmlHttp.addEventListener("load", function(event){
    var jsonData = JSON.parse(event.target.responseText);
    if(jsonData.success){  
      refreshUserButtons(false);
      getEvents();
      $(".color_selector").hide();
      eraseCookie("token");
    }else if (jsonData.success == "done"){
      alert(jsonData.message);      
      refreshUserButtons(false);
     }else{
      alert(jsonData);
       alert("You were not logged out.");
       refreshUserButtons(true);
     }
  }, false);
  xmlHttp.send(dataString);
  // erase cookie
}

function refreshUserButtons(loggedin){
  if (loggedin == true){
    if($("#logout_btn").length==0){ // check if logout buttons are not currently displayed
      // display logout button
      var userDOM = document.getElementById("user_management");
      userDOM.textContent = "";
      var logoutBtn = document.createElement("button");
      $(logoutBtn).attr({
        id : "logout_btn",
        class: "btn btn-default"
      });
      var logoutText = document.createTextNode("Log Out");
      logoutBtn.appendChild(logoutText);
      logoutBtn.addEventListener("click", logout, false);
      userDOM.appendChild(logoutBtn);
    }
  }else{
    if($("#login_btn").length==0){ // check if login buttons are not currently displayed
      // display login fields and buttons
      var userDOM = document.getElementById("user_management");
      userDOM.textContent = "";
      var usernameField = document.createElement("input");
      $(usernameField).attr({
        type : "text ",
        id : "username",
        class: "form-control",
        placeholder : "Username"
      });
      $(passwordField).keypress(function(e) {
        if(e.which == 13) {
          login();
        }
      });
      var passwordField = document.createElement("input");
      $(passwordField).attr({
        type : "password",
        id : "password",
        class: "form-control",
        placeholder : "Password"
      });
      $(passwordField).keypress(function(e) {
        if(e.which == 13) {
          login();
        }
      });
      var loginBtn = document.createElement("button");
      $(loginBtn).attr({
        id : "login_btn",
        class: "btn btn-default"
      });
      var loginText = document.createTextNode("Log In");
      loginBtn.appendChild(loginText);
      loginBtn.addEventListener("click", login, false);

      var createAcc = document.createElement("button");
      $(createAcc).attr({
        "data-toggle" : "modal",
        "data-target" : "#create-user-modal",
        id : "create_user_btn",
        class: "btn btn-default"
      });      
      var createAccText = document.createTextNode("Create an account");
      createAcc.appendChild(createAccText);
      userDOM.appendChild(usernameField);
      userDOM.appendChild(passwordField);
      userDOM.appendChild(loginBtn);
      userDOM.appendChild(createAcc);
    }
  }
}
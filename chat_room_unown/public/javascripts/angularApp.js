'use strict';

var app = angular.module('myApp', [
  'ui.router',
  'filters',
  'ui.bootstrap'
  ]);

angular.module('filters', []).filter('letters', function() {
  return function(input) {
    if (input.length === 1){
      if(input.match(/[a-z]/i)){ // i for case insensitive
        return input.toLowerCase();
      }else if (input.match(/[?]/)){
        return 'qu';
      }
      else if (input.match(/[!]/)){
        return '!';
      }else if (input.match(/[.]/)){
        return 'fs';
      }else if (input.match(/['.']/)){
        return 'ap';
      }
    }
    return '_';
  };
});

app.config(['$stateProvider','$urlRouterProvider',function($stateProvider, $urlRouterProvider) {
  $stateProvider
  .state('home', {
    url: '/home',
    templateUrl: '/home.html',
    controller: 'HomeCrtl',
    onEnter: ['$state', 'auth', function($state, auth){
      console.log("hey");
      if(!auth.isLoggedIn()){
        $state.go('login');
      }
    }],
    resolve: {
      postPromise: ['convos', 'auth',function(convos, auth){
        console.log("therre");
        if(auth.isLoggedIn()){
          return convos.getAll();
        }else{
          return null;
        }
      }]
    }
  })
  .state('convo', {
    url: '/convo/{id}',
    templateUrl: '/convo.html',
    controller: 'ConvoCtrl',
    onEnter: ['$state', 'auth', function($state, auth){
      console.log("hey");
      if(!auth.isLoggedIn()){
        $state.go('login');
      }
    }],
    resolve: {
      convo: ['$stateParams', 'convos','auth', function($stateParams, convos, auth) {
        if(auth.isLoggedIn()){
          return convos.get($stateParams.id);
        }else{
          return null;
        }
      }]
    }
  })
  .state('login', {
    url: '/login',
    templateUrl: '/login.html',
    controller: 'AuthCtrl',
    onEnter: ['$state', 'auth', function($state, auth){
      if(auth.isLoggedIn()){
        $state.go('home');
      }
    }]
  })
  .state('register', {
    url: '/register',
    templateUrl: '/register.html',
    controller: 'AuthCtrl',
    onEnter: ['$state', 'auth', function($state, auth){
      if(auth.isLoggedIn()){
        $state.go('home');
      }
    }]
  });
  $urlRouterProvider.otherwise('home');
}]);

app.factory('convos', ['$http', 'auth',function($http, auth){
 var c = {
  convos: [
  {'users':['Person1', 'Person2', 'Person3'],
  'messages':[
  {'author':'Person1', 'content':'Hi everyone!', 'format':'text', 'time':new Date()}
  ]}
  ]};
  c.getAll = function() {
    return $http.get('/convos',{
      headers: {Authorization: 'Bearer '+auth.getToken()}
    }).success(function(data){
      angular.copy(data, c.convos);
      console.log(c.convos);
    });
  };
  c.create = function(convo) {
    return $http.post('/convos', convo, {
      headers: {Authorization: 'Bearer '+auth.getToken()}
    }).success(function(data){
      c.convos.push(data);
    });
  };
  c.get = function(id) {
    return $http.get(('/convos/' + id),{
      headers: {Authorization: 'Bearer '+auth.getToken()}
    }).then(function(res){
      return res.data;
    });
  };
  c.addMessage = function (id, message){
    return $http.post('/convos/' + id + '/messages', message, {
      headers: {Authorization: 'Bearer '+auth.getToken()}
    });
  };
  c.addUser = function (id, user){
    return $http.post('/convos/' + id + '/users', user, {
      headers: {Authorization: 'Bearer '+auth.getToken()}
    });
  };
  c.leave = function (id, user){
    return $http.post('/convos/' + id + '/leave', user, {
        headers: {Authorization: 'Bearer '+auth.getToken()}
    });
 };
  c.getAvatars = function() { //get all avatars
    return $http.get(('/avatars'),{
      headers: {Authorization: 'Bearer '+auth.getToken()}
    });
  };
  c.getAvatar = function() { //get your avatar
    return $http.get(('/avatar'),{
      headers: {Authorization: 'Bearer '+auth.getToken()}
    });
  };
  c.saveAvatar = function (avatar){
    return $http.post('/avatar', avatar, {
      headers: {Authorization: 'Bearer '+auth.getToken()}
    });
  };
return c;
}]);

app.controller('HomeCrtl', ['$scope', 'convos', 'auth', function($scope, convos, auth) {
  $scope.addConvo = function(){
    if (!$scope.new_convo_user || $scope.new_convo_user === '') { return; }
    convos.create({
      users: $scope.new_convo_user
    });
    $scope.new_convo_user = '';
  };
  $scope.convos =convos.convos;  
  $scope.orderProp = 'time';
  $scope.isLoggedIn = auth.isLoggedIn;
  $scope.xWidth = 5;
  $scope.avatars = 'abcdefghijklmnopqrstuvwxyz'.split('');
  $scope.currentUser = auth.currentUser;
  $scope.getColumns = function() {
    return new Array($scope.xWidth);
  }
  $scope.getRows = function() {
    return new Array(Math.ceil(($scope.avatars.length) / $scope.xWidth));
  }
  // $scope.selectedAvatar = $scope.avatars[0];
   convos.getAvatar().success(function(data){
      $scope.selectedAvatar = data.letter; 
    });
  $scope.setAvatar = function(avatar) {
    $scope.selectedAvatar = avatar;
  };
  $scope.saveAvatar = function() {    
    convos.saveAvatar({avatar:$scope.selectedAvatar}).success(function(letter){
    });
};
}]);

app.controller('ConvoCtrl', ['$scope', '$state', 'convo','convos','auth',function($scope, $state, convo, convos, auth){
  $scope.convo = convo;
  $scope.addUser = function(){
    if (!$scope.new_user || $scope.new_user === '') { return; }
    convos.addUser(convo._id, {user: $scope.new_user})
    .success(function(user) {
      console.log("logging new hser:");
      console.log(user);
      $scope.convo.users.push(user);
    });
    $scope.new_user = '';
  };
  $scope.leave = function(){
    convos.leave(convo._id,
      {user: "bye"})
    .success(function() {
      $state.go('home');
    });
    $scope.new_user = '';
  };
  $scope.addMsg = function(){
    if($scope.message_text === '') { return; }
    convos.addMessage(convo._id, {
      content: $scope.message_text,
      author: 'Gumpkin1',
      format:$scope.selectedFormat,
      time:new Date()
    }).success(function(message) {
      $scope.convo.messages.push(message);
    });
    $scope.message_text = '';
    $scope.isLoggedIn = auth.isLoggedIn;
  };
  $scope.avatars = [];
  convos.getAvatars()
  .success(function(avatars) {
    $scope.avatars=avatars;
    console.log("avatars coming!!")
    // console.log($scope.avatars);
    console.log(avatars);
    // return letter.letter;
  });
  $scope.getAvatar = function(user){
    var letter = 'a';
    for (var i = 0; i < $scope.avatars.length; i++){
      if ($scope.avatars[i].user == user){
        letter = $scope.avatars[i].letter;
      }
    }
    return letter;
  }
  $scope.formats = ["unown", "pkmn", "lol"];
  
  $scope.selectedFormat = $scope.formats[0];
  $scope.setFormat = function(action) {
    $scope.selectedFormat = action;
  };
  $scope.toString = function(format){
    if (format == "unown"){
      return "Unown";
    } else if (format == "pkmn"){
      return "Pokemon";
    }else if (format == "lol"){
      return "League of Legends";
    }
  }

$scope.formatAMPM = function(dateString) { // from the same stackoverflow post we referenced in module 5
  // prints time of date as eg 5:00pm
  var date = new Date(dateString);
  var hours = date.getHours();
  var minutes = date.getMinutes();
  var ampm = hours >= 12 ? 'pm' : 'am';
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  minutes = minutes < 10 ? '0'+minutes : minutes;
  var strTime = hours + ':' + minutes + ampm;
  return strTime;
}

}]);

app.factory('auth', ['$http', '$window', '$state', function($http, $window, $state){
 var auth = {};
 auth.saveToken = function (token){
  $window.localStorage['unown-token'] = token;
};

auth.getToken = function (){
  return $window.localStorage['unown-token'];
}

auth.isLoggedIn = function(){
  var token = auth.getToken();

  if(token){
    var payload = JSON.parse($window.atob(token.split('.')[1]));

    return payload.exp > Date.now() / 1000;
  } else {
    return false;
  }
};

auth.currentUser = function(){
  if(auth.isLoggedIn()){
    var token = auth.getToken();
    var payload = JSON.parse($window.atob(token.split('.')[1]));

    return payload.username;
  }
};

auth.register = function(user){
  return $http.post('/register', user).success(function(data){
    auth.saveToken(data.token);
  });
};

auth.logIn = function(user){
  return $http.post('/login', user).success(function(data){
    auth.saveToken(data.token);
  });
};

auth.logOut = function(){
  $window.localStorage.removeItem('unown-token');
  $state.go('login');
};

auth.saveAvatar = function(avatar){

};

return auth;
}])

app.controller('AuthCtrl', ['$scope','$state','auth',function($scope, $state, auth){
  $scope.user = {};

  $scope.register = function(){
    auth.register($scope.user).error(function(error){
      $scope.error = error;
    }).then(function(){
      $state.go('home');
    });
  };

  $scope.logIn = function(){
    auth.logIn($scope.user).error(function(error){
      $scope.error = error;
    }).then(function(){
      $state.go('home');
    });
  };
}])

app.controller('NavCtrl', ['$scope','$state','auth',function($scope, $state, auth){
  $scope.isLoggedIn = auth.isLoggedIn;
  $scope.currentUser = auth.currentUser;
  $scope.logOut = auth.logOut;
}]);

// app.factory('users', ['$http', 'auth',function($http, auth){
//  var users = {
//   convos: [
//   {'users':['Person1', 'Person2', 'Person3'],
//   'messages':[
//   {'author':'Person1', 'content':'Hi everyone!', 'format':'text', 'time':new Date()}
//   ]}
//   ]};
//   c.getAll = function() {
//     return $http.get('/convos',{
//       headers: {Authorization: 'Bearer '+auth.getToken()}
//     }).success(function(data){
//       angular.copy(data, c.convos);
//       console.log(c.convos);
//     });
//   };

//  return c;
// }]);

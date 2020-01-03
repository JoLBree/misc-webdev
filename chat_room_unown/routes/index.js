// THIS GOES IN ROUTE
var express = require('express');
var router = express.Router();

/* GET home page. */
router.get('/', function(req, res, next) {
	res.render('index', { title: 'Express' });
});

module.exports = router;

var mongoose = require('mongoose');
var passport = require('passport');
var jwt = require('express-jwt');

var auth = jwt({secret: 'SECRET', userProperty: 'payload'});
var Convo = mongoose.model('Convo');
var Message = mongoose.model('Message');
var User = mongoose.model('User');
// var Letter = mongoose.model('Letter');

router.get('/convos', auth, function(req, res, next) { // ADDED AUTH
	Convo.find(function(err, convos, auth){ //FIXME add filter to get convos associated with user
		if(err){ return next(err); }
		var convoCopy = [];
		for(var i = convos.length-1; i >=0 ; i--) {
			console.log(convos[i].users);
			console.log(req.payload.username);
			if (convos[i].users.indexOf(req.payload.username)>=0){
				convoCopy.push(convos[i]);
			}
		}
		res.json(convoCopy);
	});
});

router.post('/convos', auth, function(req, res, next) { // ADDED AUTH
	var convo = new Convo(req.body);
	convo.users.push(req.payload.username); //CHECK IF THIS WORKS FIXME	
	convo.save(function(err, convo){
		if(err){ return next(err); }
		res.json(convo);
	});
});

router.param('convo', function(req, res, next, id) {
	var query = Convo.findById(id);
	query.exec(function (err, convo){
		if (err) { return next(err); }
		if (!convo) { return next(new Error('can\'t find convo')); } //FIXME return user convos only

		req.convo = convo;
		return next();
	});
});

// get all messages in a convo
router.get('/convos/:convo', auth, function(req, res) { // ADDED AUTH
	if (req.convo.users.indexOf(req.payload.username)>=0){
		req.convo.populate('messages', function(err, convo) {
			if (err) { return next(err); }
			res.json(convo);
		});
	}
});

// post message to a convo
router.post('/convos/:convo/messages', auth, function(req, res, next) {// ADDED AUTH
	if (req.convo.users.indexOf(req.payload.username)>=0){
		var message = new Message(req.body);
		message.convo = req.convo;
		message.author = req.payload.username;
		message.save(function(err, message){
			if(err){ return next(err); }
			req.convo.messages.push(message);

			req.convo.save(function(err, convo) {
				if(err){ return next(err); }
				message.convo.populate('messages', function(err, convo) {
					if (err) { return next(err); }
					res.json(message);
				});
			});
		});
	}
});

router.post('/avatar', auth, function(req, res, next) { // FIXME
	console.log("REQQQQQQQQQQQQQQQ");
	console.log(req);
	var letter = req.body.avatar;
	// convo.users.push(req.body.user); //CHECK IF THIS WORKS FIXME	
	// convo.save(function(err, convo){
	// 	if(err){ return next(err); }
	// 	res.json(req.body.user);
	// });
	// var letter = req.body;
	
	User.find(function(err, users, auth){ //FIXME add filter to get convos associated with user
		if(err){ return next(err); }
		var userFound = false;
		for(var i = users.length-1; i >=0 ; i--) {
			if (users[i].username == req.payload.username){
				users[i].letter = letter;
				users[i].save(function(err, user){
					if(err){ return next(err); }
					res.json(req.body.avatar);
				});
				// res.json(users[i].letter);
				// userFound = true;
			}
		}
		// convo.save(function(err, convo){
		// 	if(err){ return next(err); }
		// 	res.json(req.body.user);
		// });
	});
	// if (!userFound){
	// 	res.json({letter:'a'});
	// }
});

// post user to a convo
router.post('/convos/:convo/users', auth, function(req, res, next) {// ADDED AUTH
	var convo = req.convo;
	convo.users.push(req.body.usertoLowerCase()); //CHECK IF THIS WORKS FIXME	
	convo.save(function(err, convo){
		if(err){ return next(err); }
		res.json(req.body.user);
	});
});

router.post('/convos/:convo/leave', auth, function(req, res, next) {// ADDED AUTH
	var convo = req.convo;
	var index = convo.users.indexOf(req.payload.username);
	if (index>=0){
		convo.users.splice(index,1);
	}
	convo.save(function(err, convo){
		if(err){ return next(err); }
		res.json(req.body.user);
	});
});

router.post('/register', function(req, res, next){
	if(!req.body.username || !req.body.password){
		return res.status(400).json({message: 'Please fill out all fields'});
	} 
	else if(req.body.password.length ==1){
		return res.status(400).json({message: 'Password must be at least 8 letters long'});	
	}
	// var letter = new Letter();
	// letter.format = "unown";
	// letter.char = 'a';
	var user = new User();
	user.username = req.body.username;
	user.letter = 'a';
	user.setPassword(req.body.password);
	user.save(function (err){
		if(err){ return next(err); }
		return res.json({token: user.generateJWT()})
	});
});

router.post('/login', function(req, res, next){
	if(!req.body.username || !req.body.password){
		return res.status(400).json({message: 'Please fill out all fields'});
	}

	passport.authenticate('local', function(err, user, info){
		if(err){ return next(err); }

		if(user){
			return res.json({token: user.generateJWT()});
		} else {
			return res.status(401).json(info);
		}
	})(req, res, next);
});



router.get('/avatars', auth, function(req, res, next) { // FIXME
	var avatars = [];
	User.find(function(err, users, auth){ //FIXME add filter to get convos associated with user
		if(err){ return next(err); }
		var letterFound = false;
		for(var i = users.length-1; i >=0 ; i--) {
			avatars.push({user:users[i].username,
				letter:users[i].letter});
		}
		res.json(avatars);	
	});
});

router.get('/avatar', auth, function(req, res, next) { // FIXME
	User.find(function(err, users, auth){ //FIXME add filter to get convos associated with user
		if(err){ return next(err); }
		var letterFound = false;
		for(var i = users.length-1; i >=0 ; i--) {
			if (users[i].username == req.payload.username){
				res.json({letter:users[i].letter});
			}
		}
	});
});
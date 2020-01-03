var mongoose = require('mongoose');

var LetterSchema = new mongoose.Schema({
  format: String,
  char: String,
});

mongoose.model('Letter', LetterSchema);
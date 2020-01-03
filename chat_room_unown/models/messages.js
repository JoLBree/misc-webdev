var mongoose = require('mongoose');

var MessageSchema = new mongoose.Schema({
  author: String,
  content: String,
  format: String,
  time: Date,
  convo: { type: mongoose.Schema.Types.ObjectId, ref: 'Convo' }
});

mongoose.model('Message', MessageSchema);
var mongoose = require('mongoose');

var ConvoSchema = new mongoose.Schema({
  users: [{type: String}],
  messages: [{ type: mongoose.Schema.Types.ObjectId, ref: 'Message' }]
});

mongoose.model('Convo', ConvoSchema);
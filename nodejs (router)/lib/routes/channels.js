var config = require('../../app/config/application.js'),
apiControl = require('../apiControl.js'),
channels = {}

channels.read = function(req,res){
	apiControl.get(config.base_path +'channels/channels/read/'+ req.params.channel_id, req, function(data){
		res.end(data)
	})
}

channels.read_all = function(req,res){
	apiControl.get(config.base_path +'channels/channels/read_all/', req, function(data){
		res.end(data)
	})
}

channels.announcement_channel = function(req,res){
  apiControl.get(config.base_path +'channels/channels/announcement_channel/'+ req.params.channel_id, req, function(data){
    res.end(data)
  })
}

module.exports = channels
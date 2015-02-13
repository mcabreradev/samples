var config = require('../../app/config/application.js'),
apiControl = require('../apiControl.js'),
querystring = require('querystring'),
bios = {}

bios.update = function(req,res){
	apiControl.post(config.base_path +'bios/bios/update', req, function(data){
		res.end(data)
	})
}

bios.read = function(req,res){
	apiControl.get(config.base_path +'bios/bios/read/', req, function(data){
		res.end(data)
	})
}

bios.userBio = function(req,res){
	apiControl.get(config.base_path +'bios/bios/read/' + req.params.user_id, req, function(data){
		res.end(data)
	})
}

module.exports = bios
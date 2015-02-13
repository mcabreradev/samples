var config = require('../../app/config/application.js'),
apiControl = require('../apiControl.js'),
awards = {}

awards.create = function(req,res){
	apiControl.post(config.base_path +'awards/awards/create/', req, function(data){
		res.end(data)
	})
}

awards.update = function(req,res){
  req.body = req.params
	apiControl.post(config.base_path +'awards/awards/update/'+ req.params.user_awards_id, req, function(data){
		res.end(data)
	})
}

awards.delete = function(req,res){
	apiControl.post(config.base_path +'awards/awards/delete/'+ req.params.user_awards_id, req, function(data){
		res.end(data)
	})
}

awards.read = function(req,res){
	apiControl.get(config.base_path +'awards/awards/read/'+ req.params.user_awards_id, req, function(data){
		res.end(data)
	})
}

awards.read_all = function(req,res){
	apiControl.get(config.base_path +'awards/awards/read_all/', req, function(data){
		res.end(data)
	})
}

awards.user = function(req,res){
	apiControl.get(config.base_path +'awards/awards/read_all/' + req.params.user_id, req, function(data){
		res.end(data)
	})
}

module.exports = awards
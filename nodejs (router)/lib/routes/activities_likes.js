var config = require('../../app/config/application.js'),
apiControl = require('../apiControl.js'),
activities_likes = {}

activities_likes.create = function(req,res){
	apiControl.post(config.base_path +'activities/activities_likes/create/', req, function(data){
		res.end(data)
	})
}

activities_likes.update = function(req,res){
  req.body = req.params
	apiControl.post(config.base_path +'activities/activities_likes/update/'+ req.params.activity_like_id, req, function(data){
		res.end(data)
	})
}

activities_likes.delete = function(req,res){
	apiControl.post(config.base_path +'activities/activities_likes/delete/'+ req.params.activity_like_id, req, function(data){
		res.end(data)
	})
}

activities_likes.read = function(req,res){
	apiControl.get(config.base_path +'activities/activities_likes/read/'+ req.params.activity_like_id, req, function(data){
		res.end(data)
	})
}

activities_likes.read_all = function(req,res){
	apiControl.get(config.base_path +'activities/activities_likes/read_all/'+ req.params.activity_id, req, function(data){
		res.end(data)
	})
}

module.exports = activities_likes;
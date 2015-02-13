var config = require('../../app/config/application.js'),
apiControl = require('../apiControl.js'),
activities_comments = {}

activities_comments.create = function(req,res){
	apiControl.post(config.base_path +'activities/activities_comments/create/', req, function(data){
		res.end(data)
	})
}

activities_comments.update = function(req,res){
  req.body = req.params
	apiControl.post(config.base_path +'activities/activities_comments/update/'+ req.params.activity_comment_id, req, function(data){
		res.end(data)
	})
}

activities_comments.delete = function(req,res){
	apiControl.post(config.base_path +'activities/activities_comments/delete/'+ req.params.activity_comment_id, req, function(data){
		res.end(data)
	})
}

activities_comments.report = function(req,res){
    apiControl.post(config.base_path +'activities/activities_comments/report/'+ req.params.activity_comment_id, req, function(data){
        res.end(data)
    })
}

activities_comments.read = function(req,res){
	apiControl.get(config.base_path +'activities/activities_comments/read/'+ req.params.activity_comment_id, req, function(data){
		res.end(data)
	})
}

activities_comments.read_all = function(req,res){
	apiControl.get(config.base_path +'activities/activities_comments/read_all/'+ req.params.activity_id, req, function(data){
		res.end(data)
	})
}

module.exports = activities_comments;
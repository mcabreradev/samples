var config = require('../../app/config/application.js'),
apiControl = require('../apiControl.js'),
activities_checkins = {}

activities_checkins.create = function(req,res){
	apiControl.post(config.base_path +'activities/activities_checkins/create/', req, function(data){
		res.end(data)
	})
}

activities_checkins.update = function(req,res){
  req.body = req.params
	apiControl.post(config.base_path +'activities/activities_checkins/update/'+ req.params.activity_checkin_id, req, function(data){
		res.end(data)
	})
}

activities_checkins.delete = function(req,res){
	apiControl.post(config.base_path +'activities/activities_checkins/delete/'+ req.params.activity_checkin_id, req, function(data){
		res.end(data)
	})
}

activities_checkins.read = function(req,res){
	apiControl.get(config.base_path +'activities/activities_checkins/read/'+ req.params.activity_checkin_id, req, function(data){
		res.end(data)
	})
}

activities_checkins.recent = function(req,res){
	apiControl.get(config.base_path +'activities/activities_checkins/recent/', req, function(data){
		res.end(data)
	})
}

module.exports = activities_checkins;
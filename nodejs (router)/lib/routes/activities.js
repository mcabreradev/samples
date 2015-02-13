var config = require('../../app/config/application.js'),
apiControl = require('../apiControl.js'),
activities = {}

activities.create = function(req,res){
	apiControl.post(config.base_path +'activities/activities/create/', req, function(data){
		res.end(data)
	})
}

activities.update = function(req,res){
  req.body = req.params
	apiControl.post(config.base_path +'activities/activities/update/'+ req.params.activity_id, req, function(data){
		res.end(data)
	})
}

activities.delete = function(req,res){
	apiControl.post(config.base_path +'activities/activities/delete/'+ req.params.activity_id, req, function(data){
		res.end(data)
	})
}

activities.read = function(req,res){
	apiControl.get(config.base_path +'activities/activities/read/'+ req.params.activity_id, req, function(data){
		res.end(data)
	})
}

activities.get_shared_count = function(req,res){
	apiControl.get(config.base_path +'activities/activities/get_shared_count/'+ req.params.activity_id, req, function(data){
		res.end(data)
	})
}

activities.read_all = function(req,res){
	apiControl.get(config.base_path +'activities/activities/read_all/', req, function(data){
		res.end(data)
	})
}

activities.read_by_type = function(req,res){
	apiControl.get(config.base_path +'activities/activities/read_all/'+ req.params.type+'/'+req.params.page, req, function(data){
		res.end(data)
	})
}

activities.read_by_user = function(req,res){
	apiControl.get(config.base_path +'activities/activities/read_by_user/'+ req.params.user_id+'/'+req.params.page, req, function(data){
		res.end(data)
	})
}

activities.read_for_radar = function(req,res){
	apiControl.post(config.base_path +'activities/activities/read_for_radar/'+ req.params.type+'/'+req.params.page, req, function(data){
		res.end(data)
	})
}

activities.search_by_type = function(req,res){
  apiControl.post(config.base_path +'activities/activities/read_all/'+ req.params.type+'/'+req.params.page, req, function(data){
		res.end(data)
	})
}

activities.share = function(req,res){
  apiControl.post(config.base_path +'activities/activities/share/', req, function(data){
		res.end(data)
	})
}

module.exports = activities;
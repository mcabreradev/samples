var config = require('../../app/config/application.js'),
apiControl = require('../apiControl.js'),
activities_images = {}

activities_images.create = function(req,res){
	apiControl.post(config.base_path +'activities/activities_images/create/', req, function(data){
		res.end(data)
	})
}

activities_images.read = function(req,res){
	apiControl.get(config.base_path +'activities/activities_images/read/'+ req.params.activity_id, req, function(data){
		res.end(data)
	})
}

module.exports = activities_images;
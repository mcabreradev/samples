var config = require('../../app/config/application.js'),
apiControl = require('../apiControl.js'),
activities_links = {}

activities_links.create = function(req,res){
	apiControl.post(config.base_path +'activities/activities_links/create/', req, function(data){
		res.end(data)
	})
}

activities_links.read = function(req,res){
	apiControl.get(config.base_path +'activities/activities_links/read/'+ req.params.activity_id, req, function(data){
		res.end(data)
	})
}

module.exports = activities_links;
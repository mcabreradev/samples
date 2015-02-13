var config = require('../../app/config/application.js'),
apiControl = require('../apiControl.js'),
    activities_missed_connections = {}

activities_missed_connections.recent = function(req,res){
	apiControl.get(config.base_path +'activities/activities_missed_connections/recent/', req, function(data){
		res.end(data)
	})
}

module.exports = activities_missed_connections;
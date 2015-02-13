var config = require('../../app/config/application.js'),
apiControl = require('../apiControl.js'),
degree_types = {}

// Degree Types
degree_types.read = function(req,res){
	apiControl.get(config.base_path +'educations/degree_types/read/'+ req.params.degree_type_id, req, function(data){
		res.end(data)
	})
}

degree_types.read_all = function(req,res){
	apiControl.get(config.base_path +'educations/degree_types/read_all/', req, function(data){
		res.end(data)
	})
}

module.exports = degree_types
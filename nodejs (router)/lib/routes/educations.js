var config = require('../../app/config/application.js'),
apiControl = require('../apiControl.js'),
educations = {}

// Educations
educations.create = function(req,res){
	apiControl.post(config.base_path +'educations/educations/create/', req, function(data){
		res.end(data)
	})
}

educations.update = function(req,res){
	apiControl.post(config.base_path +'educations/educations/update/'+ req.params.user_school_id, req, function(data){
		res.end(data)
	})
}

educations.delete = function(req,res){
	apiControl.post(config.base_path +'educations/educations/delete/'+ req.params.user_school_id, req, function(data){
		res.end(data)
	})
}

educations.read = function(req,res){
	apiControl.get(config.base_path +'educations/educations/read/'+ req.params.user_school_id, req, function(data){
		res.end(data)
	})
}

educations.current = function(req,res){
	apiControl.get(config.base_path +'educations/educations/current/'+ req.params.user_id, req, function(data){
		res.end(data)
	})
}

educations.year_names = function(req,res){
	apiControl.get(config.base_path +'educations/current_year_names/get_all/', req, function(data){
		res.end(data)
	})
}

educations.read_all = function(req,res){
	apiControl.get(config.base_path +'educations/educations/read_all/', req, function(data){
		res.end(data)
	})
}

educations.user = function(req,res){
	apiControl.get(config.base_path +'educations/educations/read_all/' + req.params.user_id, req, function(data){
		res.end(data)
	})
}

educations.campuses = function(req,res){
	apiControl.get(config.base_path +'educations/educations/campuses/', req, function(data){
		res.end(data)
	})
}

educations.majors = function(req,res){
	apiControl.get(config.base_path +'educations/educations/majors/', req, function(data){
		res.end(data)
	})
}

educations.schoolMajors = function(req,res){
	apiControl.get(config.base_path +'educations/educations/majors/'+ req.params.user_school_id, req, function(data){
		res.end(data)
	})
}

educations.schoolMinors = function(req,res){
	apiControl.get(config.base_path +'educations/educations/minors/'+ req.params.user_school_id, req, function(data){
		res.end(data)
	})
}

educations.deleteMajor = function(req,res){
	apiControl.post(config.base_path +'educations/educations/delete_major/'+ req.params.user_school_major_id, req, function(data){
		res.end(data)
	})
}
educations.deleteMinor = function(req,res){
	apiControl.post(config.base_path +'educations/educations/delete_minor/'+ req.params.user_school_minor_id, req, function(data){
		res.end(data)
	})
}

module.exports = educations
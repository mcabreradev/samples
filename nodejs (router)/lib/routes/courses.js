var config = require('../../app/config/application.js'),
apiControl = require('../apiControl.js'),
courses = {}

// Courses
courses.create = function(req,res){
	apiControl.post(config.base_path +'educations/courses/create/', req, function(data){
		res.end(data)
	})
}

courses.update = function(req,res){
  req.body = req.params
	apiControl.post(config.base_path +'educations/courses/update/'+ req.params.user_school_course_id, req, function(data){
		res.end(data)
	})
}

courses.delete = function(req,res){
	apiControl.post(config.base_path +'educations/courses/delete/'+ req.params.user_school_course_id, req, function(data){
		res.end(data)
	})
}

courses.read = function(req,res){
	apiControl.get(config.base_path +'educations/courses/read/'+ req.params.user_school_course_id, req, function(data){
		res.end(data)
	})
}

courses.read_all = function(req,res){
	apiControl.get(config.base_path +'educations/courses/read_all/', req, function(data){
		res.end(data)
	})
}

courses.read_all_by_university = function(req,res){
	apiControl.get(config.base_path +'educations/courses/read_all_by_university/'+ req.params.university_id+'/'+req.params.searchStr, req, function(data){
		res.end(data)
	})
}

courses.users = function(req,res){
	apiControl.get(config.base_path +'educations/courses/users/'+ req.params.course_id + '/'+ req.params.page, req, function(data){
		res.end(data)
	})
}

module.exports = courses
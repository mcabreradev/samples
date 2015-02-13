
var config = require('../../app/config/application.js'),
apiControl = require('../apiControl.js'),
connections = {}

connections.getFromUserId = function(req, res) {
	apiControl.get(config.base_path +'users/connections/get_from_user_id/'+ req.params.user_id, req, function(data) {
		res.end(data)
	})
}

connections.getGroups = function(req, res) {
	apiControl.get(config.base_path +'users/connections/groups/'+ req.params.student_id, req, function(data) {
		res.end(data)
	})
}

connections.getGroup = function(req, res) {
	apiControl.get(config.base_path +'users/connections/group/'+ req.params.group_id, req, function(data) {
		res.end(data)
	})
}

connections.getGroupByUserId = function(req, res) {
	apiControl.get(config.base_path +'users/connections/user_belongs_to_group/'+ req.params.user_id, req, function(data) {
		res.end(data)
	})
}

connections.updateGroup = function(req, res) {
	apiControl.post(config.base_path +'users/connections/save_group/'+ req.params.group_id, req, function(data) {
		res.end(data)
	})
}

connections.deleteGroup = function(req, res) {
	apiControl.post(config.base_path +'users/connections/delete_group/'+ req.params.group_id, req, function(data) {
		res.end(data)
	})
}

connections.connections = function(req, res) {
	apiControl.get(config.base_path +'users/connections/'+ req.params.filter + '/' + req.params.page + '/' + req.params.search, req, function(data) {
		res.end(data)
	})
}

connections.insertGroupUsers = function(req, res) {
	apiControl.post(config.base_path +'users/connections/insert_to_group/'+ req.params.group_id, req, function(data) {
		res.end(data)
	})
}

connections.deleteGroupUsers = function(req, res) {
	apiControl.get(config.base_path +'users/connections/delete_from_group/'+ req.params.group_id + '/' + req.params.users_ids, req, function(data) {
		res.end(data)
	})
}

connections.insertUser = function(req, res) {
	apiControl.post(config.base_path +'users/connections/connect/'+ req.params.user_id, req, function(data) {
		res.end(data)
	})
}

connections.deleteUser = function(req, res) {
	apiControl.post(config.base_path +'users/connections/remove/'+ req.params.user_id, req, function(data) {
		res.end(data)
	})
}

module.exports = connections

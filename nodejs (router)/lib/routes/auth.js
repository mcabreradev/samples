var config = require('../../app/config/application.js'),
apiControl = require('../apiControl.js'),
auth = {}

auth.login = function(req,res){
	apiControl.post(config.base_path +'users/auth/login', req, function(data){
		res.end(data)
	})
}

auth.logout = function(req,res){
	req.body = {'jwt_encoded':req.params.bearer}
	apiControl.post(config.base_path +'users/auth/logout/'+ req.params.bearer, req, function(data){
		res.end(data)
	})
}

auth.global_logout = function(req,res){
	apiControl.post(config.base_path +'users/auth/global_logout', req, function(data){
		res.end(data)
	})
}

auth.check_token = function(req,res,next){
	apiControl.post(config.base_path +'users/auth/check_token/'+ req.params.bearer, req, function(data){
		data_obj = JSON.parse(data)
		if(data_obj.result){
			req.headers.bearer = req.params.bearer
			return next()
		}
		res.writeHead(401)
		res.end()
	})
}

auth.generate_reset_token = function(req,res){
	apiControl.post(config.base_path +'users/auth/generate_reset_token', req, function(data){
		res.end(data)
	})
}

auth.reset_password = function(req,res){
	apiControl.post(config.base_path +'users/auth/reset_password', req, function(data){
		res.end(data)
	})
}

module.exports = auth

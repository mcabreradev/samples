var npm_package = require('../package.json')
var restify = require('restify')
var routes = require('./routes')
var throttle = require('micron-throttle')
var config = require('../app/config/application.js')

module.exports = {
	create: function(){
		var options = {
			name: npm_package.name,
			version: npm_package.version
		};

		var server = restify.createServer(options)
		server.pre(restify.pre.sanitizePath())
		server.pre(restify.pre.userAgentConnection())
		restify.CORS.ALLOW_HEADERS.push('bearer')
 		server.use(restify.CORS())
		server.use(restify.queryParser())
		server.use(restify.bodyParser())
		server.use(restify.gzipResponse())
		server.use(function add_default_headers(req, res, next) {
		    res.once('header', function () {
            if (req.route.path !== '/image/:imagePath') {
              res.setHeader('Content-Type', 'application/json');
            }
		    });
		    next();
		});

		server.use(
			function needAuthentication(req,res,next){
				if( config.isPrivateMethod(req) ){
					if (typeof req.headers.bearer == 'undefined') {
						res.writeHead(401)
						res.end()
					};
					req.params.bearer = req.headers.bearer
					var auth = require('./routes/auth')
					return auth.check_token(req,res,next)
				}
				return next()
			}
		);

		server.use(throttle({
			burst: 100, //Number	Steady state number of requests/second to allow
			rate: 50, // Number	If available, the amount of requests to burst to
			ip: true, // Boolean	Do throttling on a /32 (source IP)
			overrides: {
				'192.168.1.1': {
					rate: 0,  // Unlimited
					burst: 0 // Unlimited
				}
			}
		}));

		routes.apply(server)
		return server
	}
}
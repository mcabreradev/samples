var config = require('../app/config/application.js'),
request = require('request'),
util = require('util'),
routes = require('./routes')

apiControl = {}

apiControl.post = function(url,req,next,headers) {
  // @@TODO save originating transaction in transactions table
  var options = {
    'url': url,
    'form': req.body
  }
  if (headers) {
    console.log('=========== WARNING: Overriding headers in apiControl.post =============')
    options.headers = headers
  }
  // Only sets the bearer header if it has been received by the router.
  // Otherwise the user_id extractor process in MY_Controller constructor crash when tries to decode an nonexistent bearer.
  if (config.isPrivateMethod(req)) {
    options.headers = { 'bearer': req.headers.bearer }
  }
  request.post(
      options,
      function (error, response, body) {
        console.log('data sent to ',url);
        // communication error
        if (error || response.statusCode != 200) {
          // @@TODO save error in failed_transactions
          return next(error)
        }
        // @@TODO save transaction in transactions table
        // @@TODO add transaction_id to body
        next(body)
      }
  )
}

apiControl.get = function(url,req,next) {
  var params = {
    'url': url,
    'form': req.body
  }
  if (config.isPrivateMethod(req)) {
    params.headers = { 'bearer': req.headers.bearer }
  }
  // @@TODO save originating transaction in transactions table
  request.get(
      params,
      function (error, response, body) {
        console.log('data sent to ',url);
        // communication error
        if (error || response.statusCode != 200) {
          // @@TODO save error in failed_transactions
          return next(error)
        }
        // @@TODO save transaction in transactions table
        // @@TODO add transaction_id to body
        next(body)
      }
  )
}

module.exports = apiControl;
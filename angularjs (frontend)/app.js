'use strict';
var cehubClientApp = angular.module('cehubClientApp', [
  'ngSanitize',
  'ngRoute',
  'restangular',
  'ui.bootstrap',
  'ngTagsInput'
]);

cehubClientApp.config(function($httpProvider) {
  var logsOutUserOn401 = function($location, $q) {
    var success = function(response) {
      return response;
    };
    var error = function(response) {
      if (response.status === 401) {
        delete window.localStorage.auth_token;
        window.location = '/';
      }
      return $q.reject(response);
    };
    return function(promise) {
      return promise.then(success, error);
    };
  };
  $httpProvider.responseInterceptors.push(logsOutUserOn401);
});

cehubClientApp.config(function(RestangularProvider){
  console.log('CURRENT LOCATION',window.location.host);
  if (window.location.host === '127.0.0.1:9000' || window.location.host === 'local.host:9000' || window.location.host === '0.0.0.0:9000') {
    RestangularProvider.setBaseUrl('http://0.0.0.0:3000/');
  } else {
    RestangularProvider.setBaseUrl('http://67.207.156.199:3000/');
  }
  RestangularProvider.setDefaultHeaders({'Content-Type': 'application/json'});
});

cehubClientApp.config(function ($routeProvider) {
  $routeProvider
    .when('/', {
      templateUrl: 'views/home/home.html'
    , controller: 'MainCtrl'
    })
    .when('/forgot_password', {
      templateUrl: 'views/home/home.html'
    , controller: 'forgotPasswordCtrl'
    , modal: 'views/modals/forgot_password1.html'
    })
    .when('/reset_password/:reset_token', {
      templateUrl: 'views/home/home.html'
    , controller: 'resetPasswordCtrl'
    , modal: 'views/modals/reset_password1.html'
    })
    .when('/terms-conditions', {
      templateUrl: 'views/home/home.html'
    , controller: 'forgotPasswordCtrl'
    , modal: 'views/modals/forgot_password1.html'
    })
    .when('/registration/step2', {
      templateUrl: 'views/registration/step2.html'
    })
    .when('/registration/step3', {
      templateUrl: 'views/registration/step3.html'
    , controller: 'registrationStep3'
    })
    .when('/registration/step3/wizard', {
      templateUrl: 'views/registration/step3.html'
    , controller: 'registrationStep3'
    })
    .when('/access_token=:fb_token', {
      templateUrl: 'views/registration/step2.html'
    , controller: 'syncWithFacebook2Ctrl'
    })
    .when('/dashboard', {
      templateUrl: 'views/dashboard/dashboard.html'
    , controller: 'dashboardCtrl2'
    })
    .when('/account', {
      templateUrl: 'views/dashboard/account.html'
    , controller: 'accountSettings'
    })
    .when('/activity', {
      templateUrl: 'views/activity/activity.html'
    })
    .when('/students', {
      templateUrl: 'views/students/students.html'
    })
    .when('/students/recommended', {
      templateUrl: 'views/students/recommended.html'
    })
    .when('/students/search', {
      templateUrl: 'views/students/search.html'
    })
    .when('/students/search/class', {
      templateUrl: 'views/students/search-class.html'
    })
    .when('/students/group/:group', {
      templateUrl: 'views/students/group.html'
    })
    .when('/students/class/:course', {
      templateUrl: 'views/students/class.html'
    })
    .when('/profile/view/:user_id', {
      templateUrl: 'views/profile/about-me.html'
    })
    .when('/profile/edit', {
      templateUrl: 'views/profile/edit.html'
    , controller: 'editProfileController'
    })
    .when('/connections', {
      templateUrl: 'views/students/connections.html'
    })
    .when('/channels', {
      templateUrl: 'views/channels/channels.html'
    })
    .when('/channels/animation-nation-video-details', {
      templateUrl: 'views/channels/animation-nation-video-details.html'
    })
    .when('/channels/animation-nation', {
      templateUrl: 'views/channels/animation-nation.html'
    })
    .when('/channels/art-battles-u', {
      templateUrl: 'views/channels/art-battles-u.html'
    })
    .when('/channels/art-battles-u-video-details', {
      templateUrl: 'views/channels/art-battles-u-video-details.html'
    })
    .when('/channels/music', {
      templateUrl: 'views/channels/student-rant.html'
    })
    .when('/channels/featured', {
      templateUrl: 'views/channels/student-rant.html'
    })
    .when('/channels/3-minutes-of-funny', {
      templateUrl: 'views/channels/3-minutes-of-funny.html'
    })
    .when('/channels/3-minutes-of-funny-video-details', {
      templateUrl: 'views/channels/3-minutes-of-funny-video-details.html'
    })
    .when('/channels/student-news-network', {
      templateUrl: 'views/channels/student-news-network.html'
    })
    .when('/channels/student-news-network-video-details', {
      templateUrl: 'views/channels/student-news-network-video-details.html'
    })
    .when('/channels/student-films', {
      templateUrl: 'views/channels/student-films.html'
    })
    .when('/channels/student-films-video-details', {
      templateUrl: 'views/channels/student-films-video-details.html'
    })
    .when('/channels/student-rant', {
      templateUrl: 'views/channels/student-rant.html'
    })
    .when('/channels/student-rant-video-details', {
      templateUrl: 'views/channels/student-rant-video-details.html'
    })
    .when('/channels/music', {
      templateUrl: 'views/channels/music.html'
    })
    .when('/channels/music-video-details', {
      templateUrl: 'views/channels/music-video-details.html'
    })
    .when('/channels/featured', {
      templateUrl: 'views/channels/featured.html'
    })
    .when('/channels/featured-video-details', {
      templateUrl: 'views/channels/featured-video-details.html'
    })
    .when('/channels/commerical-spoofs', {
      templateUrl: 'views/channels/commerical-spoofs.html'
    })
    .when('/channels/commerical-spoofs-video-details', {
      templateUrl: 'views/channels/commerical-spoofs-video-details.html'
    })
    .when('/channels/tag-goes-here', {
      templateUrl: 'views/channels/tag-goes-here.html'
    })
    .when('/channels/tag', {
      templateUrl: 'views/channels/tag.html'
    })
    .when('/dailydose', {
      templateUrl: 'views/dailydose/dailydose.html'
    })
    .when('/dailydose/blog', {
      templateUrl: 'views/dailydose/dailydose-blog.html'
    })
    .when('/calendar', {
      templateUrl: 'views/calendar/calendar.html'
    })
    .when('/events', {
      templateUrl: 'views/events/rsvp.html'
    })
    .when('/events/my', {
      templateUrl: 'views/events/my-events.html'
    })
    .when('/events/pending', {
      templateUrl: 'views/events/pending-invites.html'
    })
    .when('/events/around', {
      templateUrl: 'views/events/around.html'
    })
    .when('/events/__example__', {
      templateUrl: 'views/events/__example__.html'
    })
    .when('/events/__example__/invited', {
      templateUrl: 'views/events/__example-invited__.html'
    })
    .when('/radar', {
      templateUrl: 'views/radar/radar.html'
    })
    .when('/radar/share', {
      templateUrl: 'views/radar/radar.html'
    , modal: 'views/modals/share.html'
    })
    .when('/stacks', {
      templateUrl: 'views/stacks/mystacks.html'
    })
    .when('/stacks/details', {
      templateUrl: 'views/stacks/details.html'
    })
    .when('/stacks/explore', {
      templateUrl: 'views/stacks/explore.html'
    })
    .when('/stacks/subscribed', {
      templateUrl: 'views/stacks/subscribed.html'
    })
    .when('/photos', {
      templateUrl: 'views/photos/all.html'
    })
    .when('/photos/photos-of-me', {
      templateUrl: 'views/photos/photos-of-me.html'
    })
    .when('/photos/videos-of-me', {
      templateUrl: 'views/photos/video-of-me.html'
    })
    .when('/photos/my-albums', {
      templateUrl: 'views/photos/my-albums.html'
    })
    .when('/photos/pending-upload', {
      templateUrl: 'views/photos/pending-upload.html'
    })
    .when('/messages', {
        templateUrl: 'views/messages/main.html',
        controller: 'messagesCtrl'
      })
    .when('/messages/create', {
        templateUrl: 'views/messages/main.html',
        controller: 'messagesCtrl'
      })
    .when('/messages/view/:messageId', {
        templateUrl: 'views/messages/main.html',
        controller: 'messagesCtrl'
      })
    .when('/about-us',{
        templateUrl: 'views/dashboard/about-us.html'
      })
    .when('/terms-conditions',{
        templateUrl: 'views/dashboard/terms-conditions.html'
      })
    .when('/logout',{
        templateUrl: 'views/home/home.html'
      , controller: 'logoutController'
      })
    .otherwise({
        redirectTo: '/'
      });
});
// runs when all the dependencies are met
cehubClientApp.run( function($rootScope, $location, $http, $window, Restangular) {
  // CORS setup
  $http.defaults.useXDomain = true;
  delete $http.defaults.headers.common['X-Requested-With'];
  // locationChange handler
  $rootScope.$watch(function() {
    return $location.path();
  },function(){
    if (typeof window.localStorage.auth_token !== 'undefined') {
      console.debug('Token found',window.localStorage.auth_token);
      $window.scrollTo(0,0);
      $http.defaults.headers.common.Bearer = window.localStorage.auth_token;
      if ($location.path() !== '/logout' && $location.path() !== '' && $location.path() !== '/') {
        Restangular.all('user/location').post({location:$location.path()}).then(function() {});
      }
    } else { console.debug('Token wasn\'t found!'); }
    angular.element(document.getElementsByClassName('removeme')).remove();
  });
});



// This MUST be moved to a proper service or factory
window.utils = {};
utils.api_url = 'http://0.0.0.0:3000/';

utils.parse_error = function(data) {
  // if not successful, show error message
  if (data.error.data) {
    var error_msg = '';
    for (var i in data.error.data) {
      error_msg += data.error.data[i]+'\n';
    }
    console.log('An error occurred while sending your data \n\n'+error_msg);
    return;
  }
  console.log('An error occurred: '+data.error.description);
};

utils.parseErrors = function(errors, ctrlModel) {
  if (errors.length === 0) { return; }
  console.warn(errors.length + ' validation errors have been found',errors);
  ctrlModel.$errors = {};
  for (var name in errors) {
    if (typeof(errors[name]) === 'object') {
      ctrlModel[name].$errors = {};
      for (var item in errors[name]) {
        ctrlModel[name].$errors[item] = errors[name][item];
      }
    }
    else {
      ctrlModel.$errors[name] = errors[name];
    }
  }
};

utils.redirect_if_not_logged_in = function($location,where) {

  if (typeof window.localStorage.auth_token === 'undefined') {
    var final_location = '/';
    if (where) { final_location = where; }
    $location.path(final_location);
    return;
  }
};
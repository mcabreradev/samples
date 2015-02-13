'use strict';
var cehubClientApp = angular.module('cehubClientApp');

cehubClientApp.controller('MainCtrl', function ($scope,$rootScope,$location,usersModel) {
  document.getElementsByTagName('body')[0].setAttribute('class','public-page land-page');
  if (typeof window.localStorage.auth_token !== 'undefined') {

    usersModel.get_me();
    usersModel.get_bio();
    $scope.$watch(function () { return usersModel.get_group('me'); },
      function (userData) {
        if (userData) {
          var location = userData.location;
          if (location) {
            $location.path( '/' + location );
          } else {
            $location.path( '/registration/step2' );
          }
        } else {
          $location.path( '/dashboard' );
        }
      }, true
    );

  }
});

cehubClientApp.controller('ShareCtrl', function (activitiesLinks,$rootScope,$scope,privacyModel,activitiesModel,locationsModel,venuesModel,activitiesCheckinsModel) {
  $scope.privacyPreclearName = 'activity.id';
  $scope.tempRandom = 'temp_' + Date.now() + '_' + Math.floor(Math.random() * 10000);
  $scope.venues = [];
  $scope.checkinVenue = false;
  $scope.shareContent = {};
  $scope.issetUserCurrentLocation = false;
  $scope.uploadedImages = [];
  $scope.shareModal = {
    currentTab: 'simplepost'
  };
  $scope.shareContent.text = '';

  $scope.checkin = false;

  $rootScope.$on('LocationCheckedIn', function(ev, checkin){
    $scope.checkin = checkin;
  });

  $scope.setCheckinVenue = function(index) {
    $scope.checkinVenue = $scope.venues[index];
  };

  var createActivity = function(activity, callback) {
    activitiesModel.save(activity, function(data){
      var activity_id = data.id;
      callback(activity_id);
    });
  };

  var createLocation = function(location, callback) {
    locationsModel.save(location, function(data){
      var location_id = data.id;
      callback(location_id);
    });
  };

  var createCheckin = function(checkin, callback) {
    activitiesCheckinsModel.create(checkin, function(data){
      var checkin_id = data.id;
      callback(checkin_id);
    });
  };

  var createVenue = function(venue, callback) {
    venuesModel.create(venue, function(data){
      var venue_id = data.id;
      callback(venue_id);
    });
  };

  var createImage = function(image) {
    activitiesModel.saveImage(image, function(){
    });
  };

  var updatePrivacyResources = function(activity_id, temp_id, privacyPreclearName) {
    privacyModel.updateResources(activity_id, temp_id, privacyPreclearName);
  };

  var saveParsedLink = function(activity_id) {
    if ($scope.parsedLink) {
      $scope.parsedLink.activity_id = activity_id;
      activitiesLinks.create($scope.parsedLink, function(){});
    }
  };

  $scope.createActivity = function(shareContent) {
    if ($scope.checkin !== false) {
      createLocation($scope.checkin.location, function(location_id){
        createActivity({content:shareContent,location_id:location_id}, function(activity_id){
          updatePrivacyResources(activity_id, $scope.tempRandom, $scope.privacyPreclearName);

          $scope.uploadedImages.forEach(function(item){
            createImage({filename: item.filename, activity_id: activity_id});
          });
          saveParsedLink(activity_id);

          createVenue($scope.checkin.venue, function(venue_id){
            createCheckin({activity_id:activity_id,venue_id:venue_id}, function(){
              $scope.$emit('activityCreated',{activity_id:activity_id,content:shareContent,location_id:location_id});
              $scope.$emit('closeAllDialogs');
            });
          });
        });
      });
    } else {
      createActivity({content:shareContent}, function(activity_id){
        updatePrivacyResources(activity_id, $scope.tempRandom, $scope.privacyPreclearName);

        $scope.uploadedImages.forEach(function(item){
          createImage({filename: item.filename, activity_id: activity_id});
        });
        saveParsedLink(activity_id);

        $scope.$emit('activityCreated',{activity_id:activity_id,content:shareContent});
        $scope.$emit('closeAllDialogs');
      });
    }
  };

  $scope.activityImageUpload = function(file) {
    $scope.uploadedImages.push(file);
		$scope.$apply();
  };

	$scope.removeImages = function() {
		if ($scope.uploadedImages.length) {
			$scope.uploadedImages = [];
			//angular.element('#ActivityImageUpload').uploadify('cancel', '*');
			angular.element('#ActivityImageUpload').uploadify('destroy');
		}
		vendor.fileUploadInit('ActivityImageUpload', $scope, {
			folder: 'activity',
			preview: 'sthumb',
			multi: true,
			filesLimit: 1,
			auto: true,
			width: '1280',
			thumb: '565_318',
			sthumb: '110_110',
			type: 'images'
		});
	};
  $scope.removeImages();

  var parsed_url = false;
  $scope.$watch('shareContent.text', function(){
//    console.log($scope.shareContent.text);

    var urlPattern = /\b((https?:\/\/|www.)[-A-Z0-9+&@#\/%?\'=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$])/i;
    var matches = urlPattern.exec($scope.shareContent.text);
		if (matches) {
			var currentUrl = matches[0];
			if (currentUrl.substr(0, 4) !== 'http') {
				currentUrl = 'http://' + currentUrl;
			}
      if (currentUrl !== parsed_url) {
        parsed_url = currentUrl;

        activitiesModel.get_content({url:parsed_url}, function(content){
          if (content && content.title) {
            $scope.parsedLink = content;
          } else {
            $scope.parsedLink = false;
          }
        });
      }
    }
  });

});

cehubClientApp.controller('homeSliderCtrl',function($scope) {
  $scope.slides = [0, 1, 2, 3];
  $scope.homeCurrentSlide = 0;

  $scope.switchSlide = function(slideNum) {
    $scope.homeCurrentSlide = slideNum;
  };

  $scope.prevSlide = function(){
    if ($scope.homeCurrentSlide === 0) { return; }
    $scope.switchSlide($scope.homeCurrentSlide-1);
  };

  $scope.nextSlide = function(){
    if (($scope.homeCurrentSlide+1) === $scope.slides.length) { return; }
    $scope.switchSlide($scope.homeCurrentSlide+1);
  };

});

cehubClientApp.controller('channelsSliderCtrl',function($scope) {
  $scope.slides = [0, 1, 2, 3,4];
  $scope.channelsCurrentSlide = 0;

  $scope.switchSlide = function(slideNum) {
    $scope.channelsCurrentSlide = slideNum;
  };

  $scope.prevSlide = function(){
    if ($scope.channelsCurrentSlide === 0) { return; }
    $scope.switchSlide($scope.channelsCurrentSlide-1);
  };

  $scope.nextSlide = function(){
    if (($scope.channelsCurrentSlide+1) === $scope.slides.length) { return; }
    $scope.switchSlide($scope.channelsCurrentSlide+1);
  };
});

cehubClientApp.controller('registrationForm',function($scope,$rootScope,$location,$http,Restangular,usersModel) {
  $scope.monthes = [
    {title: 'January',value: '01'}
    ,{title: 'February',value: '02'}
    ,{title: 'March',value: '03'}
    ,{title: 'April',value: '04'}
    ,{title: 'May',value: '05'}
    ,{title: 'June',value: '06'}
    ,{title: 'July',value: '07'}
    ,{title: 'August',value: '08'}
    ,{title: 'September',value: '09'}
    ,{title: 'October',value: '10'}
    ,{title: 'November',value: '11'}
    ,{title: 'December',value: '12'}
	];
  var $i;
  $scope.days = [];
  for ($i = 1; $i <= 32; $i++) {
    if ($i > 9) {
      $scope.days.push({title:$i,value:$i});
    } else {
      $scope.days.push({title:'0' + $i,value:'0' + $i});
    }
  }
  $scope.years = [];
  for ($i = 2014; $i >= 1935; $i--) {
    $scope.years.push({title:$i,value:$i});
  }


  $scope.user = {};
  $scope.universities = [];
  $scope.user.birthday = {};
  $scope.user.birthday.day = '';
  $scope.user.birthday.month = '';
  $scope.user.birthday.year = '';
  $scope.user.university_id = '';

  Restangular.one('universities').customGET().then(function(data){
    var university = null;
    for (var i in data.universities) {
      university = data.universities[i];
      $scope.universities.push({'id': university.university_id, 'name': university.name});
    }
  });

	$scope.submitHandler = function() {
    console.log($scope.user.university_id);
    console.log('REGISTER');
    var register = Restangular.all('users');
    register.post($scope.user).then(function(data){
      if (data.error) {
        $scope.user.$errors = {};
        return utils.parseErrors(data.error.data, $scope.user);
      }

      var login = Restangular.all('login');
      login.post({email:$scope.user.email, password:$scope.user.password}).then(function(data){
        if (data.error) { return utils.parse_error(data); }
        window.localStorage.auth_token = data.auth.token;
        console.debug('Got JWT: ',window.localStorage.auth_token);
        $http.defaults.headers.common.Bearer = window.localStorage.auth_token;

        $scope.$watch(function () { return usersModel.get_group('me'); },
          function (userData) {
            if (userData) {
              var location = userData.location;
              if (location) {
                $location.path( location );
              } else {
                $location.path('/registration/step2');
              }
            } else {
              $location.path('/registration/step2');
            }
          }, true
        );
      });

      console.log('You have registered successfully! Now please login');
    });
	}; // end $scope.submitHandler
});

cehubClientApp.controller('loginForm',function($scope,$rootScope,$location,$http,Restangular,usersModel) {
  usersModel.set_group('me', null);
  $scope.login = {};

  $scope.submitHandler = function() {
    $scope.login.email = $($('.login-form input')[0]).val();
    $scope.login.password = $($('.login-form input')[1]).val();
    var login = Restangular.all('login');
    login.post($scope.login).then(function(data){
      if (data.error) {
        $scope.login.$errors = {};
        return utils.parseErrors(data.error.data, $scope.login);
      }
      window.localStorage.auth_token = data.auth.token;
      console.debug('we obtained a token >>>>>>>>>>> ',window.localStorage.auth_token);
      $http.defaults.headers.common.Bearer = window.localStorage.auth_token;

      usersModel.get_me();
      $scope.$watch(function () { return usersModel.get_group('me'); },
        function (userData) {
          if (userData) {
            if (userData.location) {
              //$location.path( userData.location );
							$location.path('/dasboard');
            } else {
              $location.path('/registration/step2');
            }
          }
        }, true
      );
    });
	};
});

cehubClientApp.controller('logoutController',function($scope,$location,Restangular) {
  delete window.localStorage.auth_token;
  console.debug('we have no more token..... ', window.localStorage.auth_token);
  var logout = Restangular.all('logout');
  logout.post({}).then(function(data){
    if (data.error) { return utils.parse_error(data); }
  });
  $location.path('/');
});

cehubClientApp.controller('forgotPasswordCtrl',function($scope,$location,Restangular) {
  $scope.submitHandler = function() {
    var forgotPassword = Restangular.all('forgot_password');
    forgotPassword.post({'email':$scope.forgotPasswordEmail}).then(
      function(data){
        if (data.error) { return utils.parse_error(data); }
        else{
          var ModalCtrl = angular.element(document.getElementById('ModalCtrl')).scope();
          ModalCtrl.open('views/modals/forgot_password2.html', 'popup-forgot');
        }
      }
    );
  };
});

cehubClientApp.controller('resetPasswordCtrl',function($scope,$location,$routeParams,Restangular) {
  document.getElementsByTagName('body')[0].setAttribute('class','public-page land-page');
  $scope.resetPassword = {
    reset_token: $routeParams.reset_token,
    password: '',
    confirm_password: ''
  };
  $scope.submitHandler = function() {
    var resetPassword = Restangular.all('reset_password');
    resetPassword.post($scope.resetPassword).then(
      function(data){
        if (data.error) { return utils.parse_error(data); }
        else{
          var ModalCtrl = angular.element(document.getElementById('ModalCtrl')).scope();
          ModalCtrl.open('views/modals/reset_password2.html');
        }
      }
    );
  };
});

cehubClientApp.controller('dashboardCtrl',function($scope,$location,$rootScope,Restangular,dashboardModel) {
  if ( ! $rootScope.editPhotoVideoModal ) {
    $rootScope.editPhotoVideoModal = {};
  }
  $scope.changeTab = function(tab){
    console.log('changeTab executed: ',tab,$scope.editProfileModal.currentTab);
    if (tab !== $scope.editProfileModal.currentTab) {
      if ($scope.editProfileModal.currentTab === 'overview' || $scope.editProfileModal.currentTab === 'education') {
        $scope.$emit($scope.editProfileModal.currentTab+'SubmitHandler');
        console.log($scope.editProfileModal.currentTab+'SubmitHandler was executed by an $emit');
      }
      $scope.editProfileModal.currentTab = tab;
      dashboardModel.set(tab);
    }
  };
  $scope.$watch(function () { return dashboardModel.get(); },
    function (tab) {
      $scope.editProfileModal = { currentTab : tab };
    }, true
  );
});

var ModalInstanceCtrl = function ($scope, $modalInstance, $modalStack, data) {

	$scope.data = data;

  $scope.ok = function (data) {
    $scope.forgotPasswordEmail = data;
    $modalInstance.close();
  };
  $scope.cancel = function (needs_confirmation) {
    if (typeof needs_confirmation === 'undefined') { needs_confirmation = false; }
    var execute = true;
    if (needs_confirmation) {
      execute = confirm('You have unsaved changes. Are you sure you want to close?');
      $scope.$broadcast('closeDialogWithoutSaving',execute);
    }
    if(execute) { $modalInstance.close(); }
  };

  $scope.$on('closeAllDialogs',function(){
    console.log('closeDialog event listened!');
    $modalStack.dismissAll('normal');
  });
};

window.ModalCtrl = function ($rootScope, $scope, $modal, $route) {
  $scope.open = function (modalTemplateUrl) {
    // css class to customize popup styles
    var windowClass = typeof arguments[1] === 'undefined' ? 'popup-default' : arguments[1];
		// data for template
    var data = typeof arguments[2] === 'undefined' ? {} : arguments[2];
    // Broadcast closeOverlay events to shut down all the overlays in the site!
    $rootScope.$broadcast('closeOverlay');
    $modal.open({
      templateUrl: modalTemplateUrl,
      controller: ModalInstanceCtrl,
      windowClass: windowClass,
			resolve: {
				data: function () {
					return data;
				}
			}
    });
  };
  if ($route.current.modal) {
    $scope.open($route.current.modal);
  }
};
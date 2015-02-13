'use strict';
var cehubClientApp = angular.module('cehubClientApp');
cehubClientApp.directive('overlayWizard', ['usersModel', '$location', function (usersModel, $location) {
  return {
    controller: function ($rootScope, $scope) {
      $scope.getLocation = function () {
        return $location.path();
      };
      $scope.setLocation = function (path) {
        $location.path(path);
      };
    },
    restrict: 'E',
    link: function ($scope) {
      setTimeout(function () {

        if ($scope.getLocation() !== '/registration/step3/wizard') {
          return;
        }

        window.scrollTo(0, 0);
        var body = angular.element(document.getElementsByTagName('body'));
        var overlay = angular.element(document.getElementsByClassName('overlay-dashboard-wizard'));
        var edit_btn_set = angular.element(document.getElementsByClassName('edit-btn-set'));
        var edit_btn_setc = angular.element(document.getElementsByClassName('edit-btn-setc'));

        angular.forEach(edit_btn_set, function (that) {

          var btn, offset, css, newbtn = null;
          if (that.tagName !== 'DIV' && that.tagName !== 'SPAN') {
            btn = angular.element(that).parent().next().children().children();
          } else {
            btn = angular.element(that).children().next();
          }
          btn = btn[0];
          if (btn.tagName === 'SPAN' || btn.tagName === 'DIV') {
            btn = angular.element(btn).children().children()[0];
          }

          offset = btn.getBoundingClientRect();
          css = 'left: ' + offset.left + 'px; top: ' + (offset.top - 5) + 'px;';
          newbtn = angular.element(that).clone().attr('style', css).addClass('adeedtip removeme').bind('click', function () {
            $scope.closeOverlay();
          });
          body.append(newbtn);
        });

        angular.forEach(edit_btn_setc, function (that) {
          that = angular.element(that);
          var btn, offset, css, newbtn = null;
          if (that.children().hasClass('tip5') || that.children().hasClass('tip9')) {
            btn = that.next().children().children();
          } else {
            btn = that.children().next().children().children();
          }

          btn = btn[0];
          if (typeof(btn) !== 'undefined') {
            offset = btn.getBoundingClientRect();
            css = 'left: ' + offset.left + 'px; top: ' + (offset.top - 5) + 'px;';
            newbtn = angular.element(that).clone().attr('style', css).addClass('adeedtip removeme').bind('click', function () {
              $scope.closeOverlay();
            });
            body.append(newbtn);
          }
        });

        $scope.closeOverlay = function () {
          $scope.overlayClosed = true;
          overlay.hide();
          body.removeClass('active-tip');
          angular.element(document.getElementsByClassName('adeedtip')).hide();
        };

        $scope.showOverlay = function () {
          $scope.overlayClosed = false;
          overlay.show();
          body.addClass('active-tip');
          angular.element(document.getElementsByClassName('adeedtip')).show();
        };
        $scope.overlayClosed = false;
        overlay.show();
        body.addClass('active-tip');
      }, 1200);

    }
  };
}]);

cehubClientApp.directive('ngAutocompletePlaces', ['$rootScope', function ($rootScope) {
  return {
    scope: true,
    link: function (scope, element) {
      vendor.getCurrentCoordinates(function (err, coords) {
        if (!err) {
          var defaultBounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(coords.latitude - 0.001, coords.longitude - 0.001),
            new google.maps.LatLng(coords.latitude + 0.001, coords.longitude + 0.001)
          );

          var options = {
            bounds: defaultBounds,
            types: ['establishment']
          };

          scope.gPlace = new google.maps.places.Autocomplete(element[0], options);
          google.maps.event.addListener(scope.gPlace, 'place_changed', function () {
            var data = scope.gPlace.getPlace();
            var location = {};

            scope.$apply(function () {
              var components = data.address_components;
              if (typeof components === 'undefined') {
                return false;
              }
              components.reverse();
              for (var z in components) {
                if (typeof components[z].types === 'undefined') {
                  return false;
                }
                var types = components[z].types;
                for (var y in types) {
                  if (types[y] === 'country') {
                    location.country = components[z].long_name;
                    break;
                  }
                  if (types[y] === 'administrative_area_level_1') {
                    location.level_1 = components[z].long_name;
                    break;
                  }
                  if (types[y] === 'locality') {
                    location.level_2 = components[z].long_name;
                    break;
                  }
                }
              }

              location.remote_location_id = data.id;
              location.latlng = data.geometry.location.k + ',' + data.geometry.location.A;

              var checkin = {
                venue: {
                  remote_establishment_id: data.id,
                  name: data.name,
                  icon: data.icon,
                  vicinity: data.vicinity,
                  location: location.latlng,
                },
                location: location
              };

              $rootScope.$broadcast('LocationCheckedIn', checkin);
            });
          });
        }
      });
    }
  };
}]);

cehubClientApp.directive('groupSelector', ['privacyModel', '$rootScope', function (privacyModel, $rootScope) {
  var template =
    '<div class="groups-select" ng-mouseover="showAllGroups = true" ng-mouseleave="showAllGroups = false">' +
      '<span ng-class="{ current:true, active: showAllGroups }">{{currentGroupsName}}</span>' +
      '<div ng-class="{ list:true, active: showAllGroups }">' +
      '<ul>' +
      '<li ng-repeat="group in groups track by $index">' +
      '<label>' +
      '<input type="checkbox" ng-model="groups[$index].is_active" ng-true-value="1" ng-false-value="0" ng-change="updateState($index)">' +
      '<span class="ui-button-text">{{group.name}}</span>' +
      '</label>' +
      '</li>' +
      '</ul>' +
      '<span ng-controller="ModalCtrl">' +
      '<a class="btn-add btn-popup-w" ng-click="open(\'views/modals/create-group.html\',\'popup-share\')">Create New Group +</a>' +
      '</span>' +
      '</div>' +
      '</div>';
  return {
    restrict: 'E',
    transclude: true,
    scope: true,
    template: template,
    link: function ($scope, $element, $attrs) {
      $scope.currentGroupsName = '';
      $scope.groups = [];

      var setCurrentName = function (groups) {
        console.warn('configuring privacy names');
        var someStateSelected = 0;
        groups.forEach(function (item, key) {
          if (groups[key].is_active === '1') {
            $scope.currentGroupsName = groups[key].name;
            someStateSelected++;
          }
        });
        if (someStateSelected > 1) {
          $scope.currentGroupsName = 'Custom Groups';
        }
      };

      $scope.updateState = function ($index) {
        var setFixedState = function ($index) {
          $scope.groups.forEach(function (item, key) {
            if (key === $index) {
              $scope.groups[$index].is_active = '1';
              privacyModel.updateState($scope.groups[key]);
            } else {
              if ($scope.groups[key].is_active !== '0') {
                $scope.groups[key].is_active = '0';
                privacyModel.updateState($scope.groups[key]);
              }
            }
          });
        };

        /*
         * id 1 in db is All Students, $index is 0
         * id 2 in db is My Connections, $index is 1
         * id 3 in db is Only Me, $index is 2
         */
        if ($index === 0 || $index === 1 || $index === 2) {
          setFixedState($index);
          $scope.currentGroupsName = $scope.groups[$index].name;
        } else {
          for (var $i = 0; $i <= 2; $i++) {
            if ($scope.groups[$i].is_active !== '0') {
              $scope.groups[$i].is_active = '0';
              privacyModel.updateState($scope.groups[$i]);
            }
          }
          privacyModel.updateState($scope.groups[$index]);

          var someStateSelected = 0;
          $scope.groups.forEach(function (item, key) {
            if ($scope.groups[key].is_active === '1') {
              someStateSelected++;
            }
          });
          if (!someStateSelected) {
            $scope.groups[2].is_active = '1';
            privacyModel.updateState($scope.groups[2]);
            $scope.currentGroupsName = $scope.groups[2].name;
          }
        }

        setCurrentName($scope.groups);
        privacyModel.updateState($scope.groups[$index]);

        $rootScope.$broadcast('ViewCustomGroupsChanged.' + $attrs.name);
      };

      privacyModel.getOptions($attrs.name, function (data) {
        $scope.groups = data.privacy;
        setCurrentName(data.privacy);
      });

      $rootScope.$on('refreshPrivacyDropdowns', function () {
        privacyModel.getOptions($attrs.name, function (data) {
          $scope.groups = data.privacy;
          setCurrentName(data.privacy);
        });
      });
    }
  };
}]);

cehubClientApp.directive('groupSelectorProfile', ['privacyModel', '$rootScope', 'connectionsModel', function (privacyModel, $rootScope, connectionsModel) {
  return {
    restrict: 'E',
    transclude: true,
    scope: true,
    templateUrl: 'views/partials/group-selector-profile.html',
    link: function ($scope, $element, $attrs) {

      $attrs.$observe('userId', function (userId) {
        if (userId) {
          $scope.showAllGroups = false;
					$scope.groups = [];
          $scope.updateGroup = function ($index) {
            privacyModel.updateGroup($scope.groups[$index].group_id, userId, $scope.groups[$index].is_active);
						$scope.connected = true;
//            $rootScope.$broadcast('ViewCustomGroupsChanged.'+$attrs.name);
          };

          privacyModel.getGroups(userId, function (data) {
            $scope.groups = data.privacy.groups;
            $scope.connected = data.privacy.connected;
          });

          $rootScope.$on('refreshPrivacyDropdowns', function () {
            privacyModel.getGroups(userId, function (data) {
              $scope.groups = data.privacy.groups;
              $scope.connected = data.privacy.connected;
            });
          });

          $scope.createConnection = function () {
            if (!$scope.connected) {
              connectionsModel.createConnection(userId, function (data) {
                console.log(data);
                $scope.connected = true;
              });
            }
          };

					$scope.mouseover = function () {
            if ($scope.connected) {
              $scope.showAllGroups = true;
            }
          };

					$scope.mouseleave = function () {
            $scope.showAllGroups = false;
          };

					$scope.click = function () {
            if ( ! $scope.connected) {
              $scope.showAllGroups = true;
            }
          };
        }
      });
    }
  };
}]);

cehubClientApp.directive('dropDown', [/*'$rootScope',*/ function (/*$rootScope*/) {
  return {
    restrict: 'E',
    transclude: true,
    scope: {
      callback: '=',
      ngeModel: '=',
      addclass: '@',
      placeholder: '@',
      optionName: '@',
      optionVal: '@',
      options: '=',
      hidePlaceholder: '@'
    },
    templateUrl: 'views/partials/dropdown.html',
    link: function ($scope/*, $element, $attrs*/) { //man, please use jshint before push changes
      $scope.visible = false;
      $scope.openedAtLeastOnce = false;
      $scope.changeSelect = function(value, name) {
        if(typeof($scope.ngeModel) !== 'undefined' ){
          $scope.ngeModel = value;
        }
        $scope.selectedVal = value;
        $scope.selectedName = name;
        if ($scope.callback) {
          $scope.callback(value);
        }
        $scope.openedAtLeastOnce = true;
      };
    }
  };
}]);

cehubClientApp.directive('privacy', ['privacyModel', function (privacyModel) {
  var template = [
    '<div class="drop-wrapr students">'
    , '<span class="drop">'
    , '{{currentPrivacyStateName}}'
    , '</span>'
    , '<ul>'
    , '<li ng-repeat="privacyState in privacyStates track by $index">'
    , '<label><input type="checkbox" ng-model="privacyStates[$index].is_active" ng-true-value="1" ng-false-value="0" ng-change="updateState($index)">'
    , '{{privacyState.name}}</label>'
    , '</li>'
    , '</ul>'
    , '</div>'
  ].join('\n');
  return {
    restrict: 'E',
    transclude: true,
    scope: true,
    template: template,
    link: function ($scope, $element, $attrs) {
      $scope.currentPrivacyStateName = '';
      $scope.privacyStates = [];

      var setCurrentName = function (privacyStates) {
        console.warn('configuring privacy names');
        var someStateSelected = 0;
        privacyStates.forEach(function (item, key) {
          if (privacyStates[key].is_active === '1') {
            $scope.currentPrivacyStateName = privacyStates[key].name;
            someStateSelected++;
          }
        });
        if (someStateSelected > 1) {
          $scope.currentPrivacyStateName = 'Custom Groups';
        }
      };

      $scope.updateState = function ($index) {
        var setFixedState = function ($index) {
          $scope.privacyStates.forEach(function (item, key) {
            if (key === $index) {
              $scope.privacyStates[$index].is_active = '1';
              privacyModel.updateState($scope.privacyStates[key]);
            } else {
              if ($scope.privacyStates[key].is_active !== '0') {
                $scope.privacyStates[key].is_active = '0';
                privacyModel.updateState($scope.privacyStates[key]);
              }
            }
          });
        };

        /*
         * id 1 in db is All Students, $index is 0
         * id 2 in db is My Connections, $index is 1
         * id 3 in db is Only Me, $index is 2
         */
        if ($index === 0 || $index === 1 || $index === 2) {
          setFixedState($index);
          $scope.currentPrivacyStateName = $scope.privacyStates[$index].name;
        } else {
          for (var $i = 0; $i <= 2; $i++) {
            if ($scope.privacyStates[$i].is_active !== '0') {
              $scope.privacyStates[$i].is_active = '0';
              privacyModel.updateState($scope.privacyStates[$i]);
            }
          }
          privacyModel.updateState($scope.privacyStates[$index]);

          var someStateSelected = 0;
          $scope.privacyStates.forEach(function (item, key) {
            if ($scope.privacyStates[key].is_active === '1') {
              someStateSelected++;
            }
          });
          if (!someStateSelected) {
            $scope.privacyStates[2].is_active = '1';
            privacyModel.updateState($scope.privacyStates[2]);
            $scope.currentPrivacyStateName = $scope.privacyStates[2].name;
          }
        }

        setCurrentName($scope.privacyStates);
        privacyModel.updateState($scope.privacyStates[$index]);
      };

      privacyModel.getOptions($attrs.name, function (data) {
        $scope.privacyStates = data.privacy;
        setCurrentName(data.privacy);
      });
    }
  };
}]);

cehubClientApp.directive('focusMe', function ($timeout) {
  return {
    scope: { trigger: '@focusMe' },
    link: function (scope, element) {
      scope.$watch('trigger', function (value) {
        if (value === 'true') {
          $timeout(function () {
            element[0].focus();
          });
        }
      });
    }
  };
});

cehubClientApp.directive('calendar', function () {
  return {
    link: function () {
      var date = new Date();
      var d = date.getDate();
      var m = date.getMonth();
      var y = date.getFullYear();

      angular.element('#calendar').fullCalendar({
        header: {
          left: '',
          center: 'prev,title,next',
          right: 'agendaDay,agendaWeek,month'
        },
        editable: false,
        events: [
          {
            title: 'All Day Event',
            start: new Date(y, m, 1),
            backgroundColor: '#3c94ee',
            urlv: '/#/events/__example__',
            days: 'Wednesdays',
            description: 'Morgantown, WV',
            edit: ''
          },
          {
            title: 'Long Event',
            longItem: true,
            start: new Date(y, m, d - 5),
            end: new Date(y, m, d - 2),
            backgroundColor: '#14ce97',
            urlv: '/#/events/__example__',
            days: 'Wednesdays',
            description: 'Morgantown, WV',
            edit: ''
          },
          {
            id: 999,
            title: 'Repeating Event',
            start: new Date(y, m, d - 3, 16, 0),
            allDay: false,
            editlink: '#/events/__example__',
            editlinkT: 'Edit Courses',
            backgroundColor: '#94539a',
            urlv: '/#/events/__example__',
            days: 'Wednesdays',
            edit: ''
          },
          {
            id: 999,
            title: 'Repeating Event',
            start: new Date(y, m, d + 4, 16, 0),
            allDay: false,
            backgroundColor: '#94539a',
            urlv: '/#/events/__example__',
            days: 'Wednesdays',
            description: 'Morgantown, WV',
            edit: ''
          },
          {
            title: 'Meeting',
            start: new Date(y, m, d, 10, 30),
            end: new Date(y, m, d, 11, 0),
            editevent: '#/events/__example__',
            editeventT: 'View Event',
            allDay: false,
            backgroundColor: '#4fb323',
            urlv: '/#/events/__example__',
            days: 'Wednesdays',
            description: 'Morgantown, WV',
            edit: ''
          },
          {
            title: 'Lunch',
            start: new Date(y, m, d, 16, 0),
            end: new Date(y, m, d, 18, 0),
            allDay: false,
            editevent: '#/events/__example__',
            editeventT: 'View Event',
            backgroundColor: '#1aad82',
            urlv: '/#/events/__example__',
            days: 'Wednesdays',
            description: 'Morgantown, WV',
            edit: ''
          },
          {
            title: 'Birthday Party',
            start: new Date(y, m, d + 1, 19, 0),
            end: new Date(y, m, d + 1, 22, 30),
            allDay: false,
            backgroundColor: '#4fb323',
            urlv: '/#/events/__example__',
            days: 'Wednesdays',
            description: 'Morgantown, WV',
            edit: ''
          },
          {
            title: 'Click for Google',
            start: new Date(y, m, 28),
            end: new Date(y, m, 29),
            backgroundColor: '#4fb323',
            urlv: '/#/events/__example__',
            days: 'Wednesdays',
            description: 'Morgantown, WV',
            edit: ''
          }
        ]
      });
    }
  };
});

cehubClientApp.directive('mapbox', function () {
  return {
    link: function (scope, element) {
      scope.map = L.mapbox.map(element.attr('id'), 'cehubdev.i03i79k4', {minZoom: 6}).on('ready', function () {
        scope.$broadcast('mapReady');
        $('.activities-list').on('click', 'li', function () {
          var index = $(this).data('index');
          scope.openTooltip(index);
        });
      });
      new L.Control.Zoom({ position: 'bottomleft' }).addTo(scope.map);
      scope.myLayer = L.mapbox.featureLayer().addTo(scope.map);
      scope.myLayer.on('click', function (e) {
        var marker = e.layer;
        var markerData = marker.toGeoJSON();
        if (scope.currentMarker === markerData.properties.index) {
          return false;
        }
        scope.$apply(function () {
          scope.currentMarker = markerData.properties.index;
        });
        scope.map.panTo(marker.getLatLng());
      });
      // ADD CUSTOM ICON
      scope.myLayer.on('layeradd', function (e) {
        var marker = e.layer;
        var markerData = marker.toGeoJSON();
        marker.setIcon(L.icon(markerData.properties.icon));
      });
    }
  };
});

cehubClientApp.directive('resize', function ($window) {
  return function (scope) {
    var w = angular.element($window);
    scope.getWindowDimensions = function () {
      return { 'h': w.height(), 'w': w.width() };
    };
    scope.$watch(scope.getWindowDimensions, function (newValue) {
      scope.windowHeight = newValue.h;
      scope.windowWidth = newValue.w;
    }, true);

    w.bind('resize', function () {
      scope.$apply();
    });
  };
});

cehubClientApp.directive('publicPage', function () {
  return function () {
    angular.element('body').addClass('public-page');
  };
});

cehubClientApp.directive('stickyFooter', function () {
  return function () {
    angular.element('body').addClass('sticky-footer');
  };
});

cehubClientApp.directive('ngMoment', function ($interval) {
  return function ($scope, $element, $attrs) {
    if (typeof $attrs.ngMomentLive !== 'undefined') {
      // Validation and setting default value if needed
      $attrs.ngMomentLive = parseInt($attrs.ngMomentLive);
      if ($attrs.ngMomentLive < 1) {
        $attrs.ngMomentLive = 1000;
      }
      $interval(function () {
        $element.html(moment.utc($attrs.ngMoment).fromNow());
      }, $attrs.ngMomentLive);
    } else {
      $element.html(moment.utc($attrs.ngMoment).fromNow());
    }
  };
});

cehubClientApp.directive('scrollTrigger', function ($window) {
  return function ($scope, $element, $attrs) {
    var offset = parseInt($attrs.threshold) || 0;
    var $document = angular.element(document);
    $document.bind('scroll', function () {
      if ($document.scrollTop() + $window.innerHeight > $document.height() - offset) {
        $scope.$apply($attrs.scrollTrigger);
      }
    });
  };
});

cehubClientApp.directive('autocomplete', function () {
  var KEY_DW = 40,
    KEY_UP = 38,
    KEY_ES = 27,
    KEY_EN = 13,
    KEY_BS = 8,
    MIN_LENGTH = 3,
    PAUSE = 500;

  return {
    restrict: 'EA',
    replace: true,
    scope: {
      options: '=',
      model: '='
    },
    link: function ($scope, $element/*, $attrs*/) {
      $element.on('keyup', $scope.elementKeyPressed);
      var inputField = $element.find('input');
      inputField.on('keyup', $scope.inputKeyPressed);
    },
    templateUrl: 'views/partials/autocomplete.html',
    controller: function ($scope, $timeout) {

      $scope.init = function () {
        $scope.options.minlength = $scope.options.minlength || MIN_LENGTH;
        $scope.options.pause = (typeof $scope.options.pause === 'number' && $scope.options.pause >= 0) ? $scope.options.pause : PAUSE;
        $scope.searchTimer = null;
        $scope.hideTimer = null;
        $scope.currentIndex = null;

        if ($scope.options.mode) {
          if ($scope.options.mode !== 'local') { //Only 'local' or 'remote', so we should be sure that mode is correct
            $scope.options.mode = 'remote';
          }
        } else {
          if ($scope.options.localOptions) {
            $scope.options.mode = 'local';
          }
          if ($scope.options.getRemoteResultsFunc || $scope.options.remoteUrl) {
            $scope.options.mode = 'remote'; //remote mode has bigger priority
          }
        }
      };

      $scope.$watch(function () {
          return $scope.model;
        },
        function () {
          $scope.searchStr = $scope.getTitle($scope.model);
        });

      $scope.inputKeyPressed = function (event) {
        if (!(event.which === KEY_UP || event.which === KEY_DW || event.which === KEY_EN)) {
          if (!$scope.searchStr || $scope.searchStr === '') {
            $scope.showDropdown = false;
            $scope.lastSearchTerm = null;
          } else if ($scope.isNewSearchNeeded($scope.searchStr, $scope.lastSearchTerm)) {
            $scope.lastSearchTerm = $scope.searchStr;
            $scope.showDropdown = true;
            $scope.currentIndex = -1;
            $scope.results = [];

            if ($scope.searchTimer) {
              $timeout.cancel($scope.searchTimer);
            }

            $scope.searching = true;

            $scope.searchTimer = $timeout(function () {
              $scope.searchTimerComplete($scope.searchStr);
            }, $scope.options.pause);
          }
        } else {
          event.preventDefault();
        }
      };

      $scope.elementKeyPressed = function (event) {
        if (event.which === KEY_DW && $scope.results) {
          if (($scope.currentIndex + 1) < $scope.results.length) {
            $scope.$apply(function () {
              $scope.currentIndex++;
            });
            event.preventDefault();
          }

        } else if (event.which === KEY_UP) {
          if ($scope.currentIndex >= 1) {
            $scope.currentIndex--;
            $scope.$apply();
            event.preventDefault();
          }

        } else if (event.which === KEY_EN && $scope.results) {
          if ($scope.currentIndex >= 0 && $scope.currentIndex < $scope.results.length) {
            $scope.selectResult($scope.results[$scope.currentIndex]);
            $scope.$apply();
            event.preventDefault();
          } else {
            if ($scope.results.length > 0) {
              $scope.selectResult($scope.results[0]);
              event.preventDefault();
            }
            event.preventDefault();
            $scope.results = [];
            $scope.$apply();
          }
        } else if (event.which === KEY_ES) {
          $scope.results = [];
          $scope.showDropdown = false;
          $scope.$apply();
        } else if (event.which === KEY_BS) {
          $scope.selectedObject = null;
          $scope.$apply();
        }
      };

      $scope.isNewSearchNeeded = function (newTerm, oldTerm) {
        return newTerm.length >= $scope.options.minlength && newTerm !== oldTerm;
      };

      $scope.searchTimerComplete = function (str) {
        if ($scope.options.mode === 'local') {
          return $scope.searchLocal(str);
        }
        return $scope.searchRemote(str);
      };

      $scope.searchLocal = function (str) {
        var matches = [];
        if (str.length >= $scope.options.minlength && $scope.options.localOptions) {
          for (var i = 0; i < $scope.options.localOptions.length; i++) {
            if (($scope.options.localOptions[i][$scope.options.title].toLowerCase().indexOf(str.toLowerCase()) >= 0)) {
              matches[matches.length] = $scope.options.localOptions[i];
            }
          }
          $scope.searching = false;
          $scope.processResults(matches);
        }
      };

      $scope.searchRemote = function (str) {
        var callback = function (responseData) {
          $scope.searching = false;
          $scope.processResults(responseData);
        };
        if ($scope.options.getRemoteResultsFunc) {
          $scope.options.getRemoteResultsFunc(str, callback);
        }
      };

      $scope.processResults = function (responseData) {
        $scope.results = responseData;
      };

      $scope.hoverRow = function (index) {
        $scope.currentIndex = index;
      };

      $scope.hideResults = function () {
        $scope.hideTimer = $timeout(function () {
          $scope.showDropdown = false;
          $scope.updateTitle();
        }, PAUSE);
      };

      $scope.resetHideResults = function () {
        if ($scope.hideTimer) {
          $timeout.cancel($scope.hideTimer);
        }
      };

      $scope.getTitle = function (result) {
        if (!result) {
          return '';
        }
        return result[$scope.options.title];
      };

      $scope.updateTitle = function () {
        $scope.searchStr = $scope.getTitle($scope.model);
      };

      $scope.selectResult = function (result) {
        var completeSelectCallback = function () {
          $scope.updateTitle();
          $scope.showDropdown = false;
          $scope.results = [];
          $scope.lastSearchTerm = null;
        };
        var errorSelectCallback = function () {

        };
        var successSelectCallback = function (model) {
          $scope.model = model;
        };
        if ($scope.options.beforeSelect) {
          var beforeSelectArgs = {
            oldModel: $scope.model,
            newModel: result,
            successSelectCallback: successSelectCallback,
            errorSelectCallback: errorSelectCallback,
            completeSelectCallback: completeSelectCallback
          };
          return $scope.options.beforeSelect(beforeSelectArgs);
        }
        successSelectCallback(result);
        completeSelectCallback();
      };

      $scope.createNew = function () {
        if (!$scope.options.createNew.func) {
          console.log('Directive "autocomplete" error: missed option "createNew.func"');
        }
        var callback = function (model) {
          $scope.model = model;
          $scope.options.createNew.active = true;
        };
        var createNewArgs = {
          str: $scope.searchStr,
          callback: callback
        };
        $scope.options.createNew.func(createNewArgs);
      };

      $scope.cancelCreateNew = function () {
        $scope.model = null;
        $scope.options.createNew.active = false;
      };

      $scope.init();
    }
  };
});
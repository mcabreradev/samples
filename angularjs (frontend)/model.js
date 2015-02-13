/*globals utils:false*/
'use strict';
var cehubClientApp = angular.module('cehubClientApp');
cehubClientApp.service('educationModel', function (Restangular, $rootScope) {
  var exports = {};
  this.set = function (index, key, value) {
    exports.educations[index][key] = value;
    $rootScope.$broadcast('educationModelSetter');
  };
  this.set_group = function (index, data) {
    exports.educations[index] = data;
    $rootScope.$broadcast('educationModelGroupSetter');
  };
  this.get_all = function () {
    return exports.educations;
  };
  this.get = function (index, key) {
    return exports.educations[index][key];
  };
  this.get_group = function (index) {
    return exports.educations[index];
  };
  this.saveCourse = function (course) {
    if (course.user_school_course_id) {
      Restangular.one('educations/courses/' + course.user_school_course_id).customPUT(course).then(function (data) {
        if (data.error) {
          console.log('error while doing PUT educations/courses/' + course.user_school_course_id, data.error);
          course.$errors = {};
          return utils.parseErrors(data.error.data, course);
        }
      });
    } else {
      Restangular.all('educations/courses').post(course).then(function (data) {
        if (data.error) {
          console.log('error while doing POST educations/courses/', data.error);
          course.$errors = {};
          return utils.parseErrors(data.error.data, course);
        }
      });
    }
  };
  this.removeCourse = function (course) {
    if (course.user_school_course_id) {
      Restangular.one('educations/course/' + course.user_school_course_id).remove().then(function (data) {
        if (data.error) {
          return utils.parse_error(data);
        }
      });
    }
  };
  this.removeMajor = function (major) {
    if (major.user_school_major_id) {
      Restangular.one('educations/major/' + major.user_school_major_id).remove().then(function (data) {
        if (data.error) {
          return utils.parse_error(data);
        }
      });
    }
  };
  this.removeMinor = function (minor) {
    if (minor.user_school_minor_id) {
      Restangular.one('educations/minor/' + minor.user_school_minor_id).remove().then(function (data) {
        if (data.error) {
          return utils.parse_error(data);
        }
      });
    }
  };
  this.saveEducation = function (edu, callback) {
    if (edu.user_school_id) {
      Restangular.one('educations/' + edu.user_school_id).customPUT(edu).then(function (data) {
        edu.$errors = {};
        if (data.error) {
          utils.parseErrors(data.error.data, edu);
        }
        if (callback) {
          callback();
        }
      });
      return;
    }
    Restangular.all('educations').post(edu).then(function (data) {
      edu.$errors = {};
      if (data.error) {
        return utils.parseErrors(data.error.data, edu);
      }
      $rootScope.$broadcast('educationModelCreateNew', data.error);
      if (callback) {
        callback();
      }
    });
  };
  this.deleteEducation = function (edu) {
    Restangular.one('educations/' + edu.user_school_id).remove().then(function (data) {
      if (data.error) {
        return utils.parse_error(data);
      }
      $rootScope.$broadcast('educationModelDelete');
    });
  };
  this.get_data = function () {
    Restangular.one('educations').get().then(function (data) {
      if (data.error) {
        return utils.parse_error(data);
      }
      exports.educations = data.educations;
      $rootScope.$broadcast('educationModelGetData');
    });
  };
  this.getCurrentEducationByUserId = function (user_id, callback) {
    Restangular.one('educations/current/' + user_id).get().then(function (data) {
      if (data.error) {
        return utils.parse_error(data);
      }
      callback(data.education);
    });
  };
  this.getCurrentUniversityName = function () {
    Restangular.one('educations/current').get().then(function (data) {
      if (data.error) {
        return utils.parse_error(data);
      }
      exports.educations = data.educations;
      $rootScope.$broadcast('educationModelGetData');
    });
  };
  this.get_highschool = function () {
    Restangular.one('highschools').get().then(function (data) {
      if (data.error) {
        return utils.parse_error(data);
      }
      $rootScope.$broadcast('educationModelGetHighschool', data.highschools);
    });
  };
  this.saveHighschool = function (highschool) {
    if (highschool.user_highschool_id) {
      Restangular.one('highschools/' + highschool.user_highschool_id).customPUT(highschool).then(function (data) {
        highschool.$errors = {};
        $rootScope.disableUpdateUserDialogSubmit = false;
        if (data.error) {
          return utils.parseErrors(data.error.data, highschool);
        }
      });
    } else {
      Restangular.all('highschools').post(highschool).then(function (data) {
        highschool.$errors = {};
        $rootScope.disableUpdateUserDialogSubmit = false;
        if (data.error) {
          return utils.parseErrors(data.error.data, highschool);
        }
      });
    }
  };

  this.getCampuses = function (callback) {
    Restangular.one('educations/campuses').get().then(function (data) {
      if (data.error) {
        return utils.parse_error(data);
      }
      callback(data.educations);
    });
  };

  this.getCurrentYearNames = function (callback) {
    Restangular.one('educations/year_names').get().then(function (data) {
      if (!data.error) {
        callback(data.current_year_names);
      }
    });
  };

  this.getMajors = function (callback) {
    Restangular.one('educations/majors').get().then(function (data) {
      if (!data.error) {
        callback(data.educations);
      }
    });
  };

  this.getCurrentUserMajors = function (user_school_id, callback) {
    Restangular.one('educations/majors/' + user_school_id).get().then(function (data) {
      if (!data.error) {
        callback(data.educations);
      }
    });
  };

  this.getCurrentUserMinors = function (user_school_id, callback) {
    Restangular.one('educations/minors/' + user_school_id).get().then(function (data) {
      if (!data.error) {
        callback(data.educations);
      }
    });
  };

  this.getStudentData = function (user_id, callback) {
    Restangular.one('educations/user/' + user_id).get().then(function (data) {
      if (!data.error) {
        callback(data.educations);
      }
    });
  };

  this.getCoursesByUniversityId = function (university_id, searchStr, callback) {
    Restangular.one('educations/courses/university/' + university_id + '/' + searchStr).get().then(function (data) {
      if (!data.error) {
        callback(data.courses);
      }
    });
  };

  this.getCourseById = function (course_id, callback) {
    Restangular.one('/educations/course/' + course_id).get().then(function (data) {
      if (!data.error) {
        callback(data.course);
      }
    });
  };

  this.getUsersByCourseId = function (course_id, page, callback) {
    Restangular.one('/educations/course/' + course_id + '/users/' + page).get().then(function (data) {
      if (!data.error) {
        callback(data.courses);
      }
    });
  };

});
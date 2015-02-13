'use strict';
var cehubClientApp = angular.module('cehubClientApp');

cehubClientApp.filter('range', function() {
  return function(input, min, max) {
    min = parseInt(min); //Make string input int
    max = parseInt(max);
    for (var i=min; i<max; i++){
      input.push(i);
    }
    return input;
  };
});

cehubClientApp.filter('randomNumber',function(){
  return Math.floor((Math.random()*6)+1);
});

cehubClientApp.filter('capitalize', function() {
	return function(input) {
		if (input!==null){
			input = input.toLowerCase();
		}
		return input.substring(0,1).toUpperCase()+input.substring(1);
	};
});

cehubClientApp.filter('truncate', function() {
  return function(value, wordwise, max, tail) {
    if (!value){
      return '';
    }
    max = parseInt(max, 10);
    if (!max){
      return value;
    }
    if (value.length <= max){
      return value;
    }

    value = value.substr(0, max);
    if (wordwise) {
      var lastspace = value.lastIndexOf(' ');
      if (lastspace !== -1) {
        value = value.substr(0, lastspace);
      }
    }

    return value + (tail || ' …');
  };
});

cehubClientApp.filter('bindDate', function() {
  return function(input) {
    if (input!==null){
      input = input.split('-');
      input = new Date( parseInt(input[0], 10), parseInt(input[1], 10)-1, parseInt(input[2], 10));
    }
    return input;
  };
});

// cehubClientApp.filter('limitInterests', function() {
//   return function( input, limit, selected, popular, parent) {
//     var filtered = [];
//     for (var z in input) { // Itera los interests.
//       var match = true;
//       // Checks the parenthood.
//       if (match && typeof parent === 'undefined') { return;}
//       match = false;
//       if (typeof input[z].parents === 'undefined') { return;}
//       var parents = input[z].parents;
//       for (var y in parents) { // Itera los IDs de padres de un determinado interest.
//         if (parent.interest_id === parents[y]) {
//           match = true;
//         }
//       }

//       // Is popular?
//       if (match && typeof popular !== 'undefined') {
//         match = ( (popular && input[z].popular) || (!popular && !input[z].popular) ) ? true : false;
//       }
//       // Is selected?
//       if (match && typeof selected !== 'undefined') {
//         match = ( (selected && input[z].selected) || (!selected && !input[z].selected) ) ? true : false;
//       }
//       // Fills the array with the item.
//       if (match) {
//         filtered.push(input[z]);
//         if (typeof limit !== 'undefined' && filtered.length >= limit) {
//           break;
//         }
//       }
//     }
//     return filtered;
//   };
// });
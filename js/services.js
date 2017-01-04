angular.module('ezine.services', [])
	.service('APIs', ['$http', '$rootScope', function ($http, $rootScope) {
		'use strict';
		
		return {
			login: function (data) {
				return $http.post('http://wwwlogix.com/dev/ezine/services/app/ezine_login_check', data);
			},
			editcompany: function (data) {
				return $http.post('http://wwwlogix.com/dev/ezine/services/app/ezine_edit_companyname', data );
			},
			getarticles: function () {
				return $http.get('http://wwwlogix.com/dev/ezine/services/app/ezine_get_all_articles');
			},
			getarticleStatus: function(data) {
				return $http.post('http://wwwlogix.com/dev/ezine/services/app/ezine_get_user_article_recording', data);
			}, 
			getconfig: function() {
				return $http.get('http://wwwlogix.com/dev/ezine/services/app/ezine_get_config');
			}
		};
	}])

	.factory('ConnectivityMonitor', function($rootScope, $cordovaNetwork){
 
	  return {
	    isOnline: function(){
	      /*if(ionic.Platform.isWebView()){
	        return $cordovaNetwork.isOnline();    
	      } else {
	        return navigator.onLine;
	      }*/
	       if(navigator || navigator.connection.type != Connection.NONE) { 
	       	return navigator.onLine;
	       } else {
	       	return !navigator.onLine;
	       }
	    },
	    isOffline: function(){
	      /*if(ionic.Platform.isWebView()){
	        return !$cordovaNetwork.isOnline();    
	      } else {
	        return !navigator.onLine;
	      }*/
	    },
	    startWatching: function(){
	        if(ionic.Platform.isWebView()){
	 
	          $rootScope.$on('$cordovaNetwork:online', function(event, networkState){
	            console.log("went online");
	          });
	 
	          $rootScope.$on('$cordovaNetwork:offline', function(event, networkState){
	            console.log("went offline");
	          });
	 
	        }
	        else {
	 
	          window.addEventListener("online", function(e) {
	            console.log("went online");
	          }, false);    
	 
	          window.addEventListener("offline", function(e) {
	            console.log("went offline");
	          }, false);  
	        }       
	    }
	  }
	})

	;
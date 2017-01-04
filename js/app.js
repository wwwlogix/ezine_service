
angular.module('ezine', ['ionic', 'ezine.controllers', 'ezine.services', 'ngStorage', 'ngCordova', 'ngAudio'])

.run(function($ionicPlatform, $ionicLoading, $rootScope, $localStorage, ConnectivityMonitor, $ionicPopup, APIs) {
  $rootScope.$storage = $localStorage;
  $ionicPlatform.ready(function() {
    if(window.cordova && window.cordova.plugins.Keyboard) {
      // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
      // for form inputs)
      cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);

      // Don't remove this line unless you know what you are doing. It stops the viewport
      // from snapping when text inputs are focused. Ionic handles this internally for
      // a much nicer keyboard experience.
      cordova.plugins.Keyboard.disableScroll(true);
    }
    if(window.StatusBar) {
      StatusBar.styleDefault();
    }
    if (navigator.splashscreen){
      console.log("hiding splash");
      navigator.splashscreen.hide();
    }
    APIs.getconfig().success(function(datas){
      console.log("config fetch "+angular.toJson(datas));
      if (datas.status == "success") {
        $rootScope.$storage.config = datas.msg[0];
      } else {
        $rootScope.$storage.config = undefined;
      }
    }).error(function(err){
      alert(err);
    });
    if (ionic.Platform.isAndroid()) {
      $rootScope.$storage.platform = "android";
    } else {
      $rootScope.$storage.platform = "ios";
    }
  });

  //global spinner for http intercept
  $rootScope.$on('loading:show', function() {
    $ionicLoading.show({template: '<ion-spinner></ion-spinner>'})
  });

  $rootScope.$on('loading:hide', function() {
    $ionicLoading.hide();
  });

  $rootScope.showAlert = function(title, msg) {
    var alertPopup = $ionicPopup.alert({
      title: title,
      template: msg
    });
    return alertPopup/*.then(function(res) {
      console.log('Thank you ...');
    });*/
  };

})


.config(function($stateProvider, $urlRouterProvider) {
  $stateProvider

  .state('splash', {
    url: '/',
    templateUrl: 'views/splash.html',
    controller: 'SplashCtrl'
  })

  .state('login', {
    url: '/login',
    templateUrl: 'views/login.html',
    controller: 'LoginCtrl'
  })

  .state('start', {
    url: '/start',
    templateUrl: 'views/start.html',
    controller: 'StartCtrl'
  })
  

  .state('home', {
    url: '/home',
    templateUrl: 'views/home.html',
    controller: 'HomeCtrl'
  })

  .state('record', {
    url: '/record/:type',
    templateUrl: 'views/record.html',
    controller: 'RecordCtrl'
  })

  .state('session', {
    url: '/session/:type',
    templateUrl: 'views/session.html',
    controller: 'SessionCtrl'
  })

  .state('complete', {
    url: '/complete',
    templateUrl: 'views/complete.html',
  })

  ;
  // if none of the above states are matched, use this as the fallback
  $urlRouterProvider.otherwise('/');
})

.config(function($httpProvider) {
  $httpProvider.interceptors.push(function($rootScope) {
    return {
      request: function(config) {
        $rootScope.$broadcast('loading:show')
        return config  
      },
      response: function(response) {
        $rootScope.$broadcast('loading:hide');
        return response
      },
      requestError: function(rejection) {
        $rootScope.$broadcast('loading:hide');
        return rejection
      },
      responseError: function(rejection) {
        $rootScope.$broadcast('loading:hide');
        return rejection
      }
    }
  })
  //$httpProvider.defaults.headers.common['Access-Control-Allow-Origin'] = '*';  
})
.config(function($ionicConfigProvider) {
  $ionicConfigProvider.views.maxCache(3);
})
            

;
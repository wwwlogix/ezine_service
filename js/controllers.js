angular.module('ezine.controllers', [])

.controller('SplashCtrl', function($scope, $ionicModal, $timeout, $rootScope, $state, $ionicActionSheet, $window, $ionicHistory, APIs) {
  /*$timeout(function() {
    $ionicHistory.nextViewOptions({
      disableBack: true
    });
    $state.go('login');
  }, 3000);*/
  $state.go('login');
})

.controller('LoginCtrl', function(APIs, $scope, $ionicModal, $timeout, $rootScope, $state, $ionicActionSheet, $window, $ionicHistory) {
  $scope.loginData = {};

  $scope.doLogin = function() {
    console.log('Doing login', $scope.loginData);
    APIs.login($scope.loginData).success(function(data){
      console.log(data);
      if (data) {
        if (data.status == "success"){
          $rootScope.$storage.user = data.msg;
          if (!$rootScope.$storage.user.companyname) {
            $rootScope.$storage.user.companyname = "";
            $state.go('start');  
          } else {
            $state.go('home');
          }  
        } else {
          $scope.error = data.msg;
        }  
      }
      
    }).error(function(err){
      console.log(err);
    })
  }; 
  $scope.$on('$ionicView.beforeEnter', function(){
    if ($rootScope.$storage.user) {
      $state.go('home');
    } 
  });


})



.controller('StartCtrl', function($scope, $ionicModal, $timeout, $rootScope, $state, $ionicActionSheet, $window, $ionicHistory, APIs) {
  $scope.data = {};
  $scope.updateCompany = function(){
    console.log($rootScope.$storage.user.user_id);
    console.log($scope.data.companyname);

    APIs.editcompany({user_id:$rootScope.$storage.user.user_id, companyname: $scope.data.companyname}).success(function(data){
      if (data.status == "success") {
        $rootScope.$storage.user.companyname = $scope.data.companyname;
      } else {

      }
      $rootScope.showAlert(data.status, data.msg).then(function(){
        $ionicHistory.nextViewOptions({
          disableBack: true
        });
        $state.go('home');
      });
    }).error(function(err){
      alert(err);
    });
  }

})

.controller('HomeCtrl', function($scope, $ionicModal, $timeout, $rootScope, $state, $ionicActionSheet, $window, $ionicHistory, APIs, $ionicPlatform) {

  $scope.options = {
    direction: 'vertical',
    slidesPerView: '1',
    paginationClickable: true,
    showNavButtons: false
  };
 
  $scope.data = {};
 
  $scope.$watch('data.slider',function(slider){
      console.log('My slider object is ', slider);
      // Your custom logic here
  });

  $scope.goRecord = function(item){
    $rootScope.$storage.sitem = item;
    $ionicHistory.nextViewOptions({
      disableBack: true
    });
    $state.go('session', {type: 1});
  }
  $scope.logout = function(){
    $rootScope.$storage.user = undefined;
    $ionicHistory.nextViewOptions({
      disableBack: true
    });
    $state.go('login');
  };
  $scope.exitApp = function(){
    $scope.logout();
    ionic.Platform.exitApp(); // stops the app
  };
  $scope.play = function(){
    $ionicPlatform.ready(function() {
      TTS.speak('Hello world, this is test audio speaker.', function () {
        //alert('success');

        alert('success speaking text');
      }, function (reason) {
          alert(reason);
      });
    });
  }

  $scope.$on('$ionicView.beforeEnter', function(){
    $scope.articles = {};
    if (!$rootScope.$storage.user) {
      $ionicHistory.nextViewOptions({
        disableBack: true
      });
      $state.go('login');
    } else {
      if (!$rootScope.$storage.user.companyname) {
        $ionicHistory.nextViewOptions({
          disableBack: true
        });
        $state.go('start');
      } else {
        APIs.getarticles().success(function(data){
          console.log(angular.toJson(data));
          if (data) {
            if (data.status == "success") {
              $scope.articles = data.msg;
            } else {
              $rootScope.showAlert(data.status, data.msg);
            }  
          }
        });  
      }
    }  
  });
  

  
})

.controller('RecordCtrl', function($scope, $ionicModal, $stateParams, $timeout, $rootScope, $state, $ionicActionSheet, $window, $ionicHistory, $cordovaCapture, $interval, $cordovaFile, $ionicPlatform, $ionicLoading, ngAudio, APIs) {
  
  console.log('Type: '+$stateParams.type);

  $scope.data = { recording: undefined, ms: '' };
  $scope.myMedia;
  $scope.title = "";
  $scope.stitle = "";

  $rootScope.$storage.src = "";
  $rootScope.$storage.fileURL = "";
  $scope.spoken = false;
  $scope.sound = {};
  $scope.ctime=0;
  $scope.onSuccess = function(e) {
    console.log("Created Audio for Recording");
    console.log(angular.toJson(e));
  };
  $scope.onError = function(error) {
    console.log('onerror')
    alert('code: '    + error.code    + '\n' + 'message: ' + error.message + '\n');
  }
  $scope.onStatus = function(status) {
    /*Media.MEDIA_NONE = 0;
    Media.MEDIA_STARTING = 1;
    Media.MEDIA_RUNNING = 2;
    Media.MEDIA_PAUSED = 3;
    Media.MEDIA_STOPPED = 4;*/
    console.log('media status '+status);
    if (status == 2) {
      $scope.data.ms = "recording";
    } else if (status == 3) {
      $scope.data.ms = "paused";
    } else if (status == 4) {
      $scope.data.ms = "stopped";
    }
  }
  
  $scope.startRecord = function() {    
    if ( angular.isDefined($scope.data.recording) ) { 
      if (($scope.ctime >= $scope.minrec) && ($scope.ctime <= $scope.maxrec)) {
        $scope.stopAudio = true;
      } else {
        $scope.stopAudio = false;
        $scope.data.ms = "recording";     
      }
      return; 
    } else { 
      $scope.stopAudio = false;
      ngAudio.play('img/bleep.mp3'); 
    }

    var options = {};
    
    if ($stateParams.type == 1){
      $scope.started = 1;
    } else if ($stateParams.type == 2){
      $scope.started = 2;
    } else if ($stateParams.type == 3){
      $scope.started = 3;
    } else if ($stateParams.type == 4){
      $scope.started = 4;
    } else if ($stateParams.type == 5){
      $scope.started = 5;
    } else if ($stateParams.type == 6){
      $scope.started = 6;
    } else if ($stateParams.type == 7){
      $scope.started = 7;
    }
    $ionicPlatform.ready(function() {
      //window.requestFileSystem(LocalFileSystem.TEMPORARY, 0, gotFS, fail);
      //window.resolveLocalFileSystemURL($scope.sound.file, function(fe) {
      /*function gotFS(fileSystem) {
        console.log(fileSystem.root.fullPath);   
      }
      function fail() {
        alert('error file creation');
      }*/
      if (cordova.file.documentsDirectory) {
          $rootScope.$storage.fileURL = "documents://" + $rootScope.$storage.src; // for iOS
          console.log($rootScope.$storage.fileURL);
        } else {
          $rootScope.$storage.fileURL = cordova.file.externalRootDirectory + $rootScope.$storage.src; // for Android
          console.log($rootScope.$storage.fileURL);
        }
        console.log("Location: "+$rootScope.$storage.fileURL)
       

        if ($rootScope.$storage.config) {
          $scope.minrec = $rootScope.$storage.config.min_rec_time.slice(0, $rootScope.$storage.config.min_rec_time.length-3)*60;
          $scope.maxrec = $rootScope.$storage.config.max_rec_time.slice(0, $rootScope.$storage.config.max_rec_time.length-3)*60;
        }
        
        $scope.data.recording = $interval(function() {
          if (!$scope.stopAudio) {
            if ($scope.ctime > $scope.maxrec) {
              $scope.stopAudio = true;
            }
            if ($scope.data.ms == "recording") {
              $scope.ctime++;  
            }
          } else {
            $scope.stopRecord();
          }
        }, 1000);

        $scope.myMedia = new Media($rootScope.$storage.fileURL, $scope.onSuccess, $scope.onError, $scope.onStatus);
        $scope.myMedia.setVolume('1.0');
        $scope.myMedia.startRecord();
        $scope.data.ms = "recording";
     
    });
  };
 
  $scope.nextStep = function() {
    console.log('next step')
    var mtype = Number($stateParams.type)+1;
    $scope.started = mtype;    
    var win = function (r) {
      console.log("Code = " + r.responseCode);
      console.log("Response = " + r.response);
      console.log("Sent = " + r.bytesSent);
      //setTimeout(function() {
        $ionicHistory.nextViewOptions({
          disableBack: true
        });
        if (mtype < 8) {
          $state.go('record', {type: mtype}, {reload: true});  
        } else {
          APIs.getarticleStatus({"user_id":$rootScope.$storage.user.user_id, "article_id": $rootScope.$storage.sitem.article_id}).success(function(data) {
            console.log('article status '+angular.toJson(data));
            if (data.status == "success") {
              console.log("session status "+data.msg[0].session_Completed);
              if (data.msg[0].session_Completed == 1) {
                $state.go('complete');
              } else {
                $state.go('session', {type: 1});
              }
              console.log(angular.toJson(data));
            } else {
              console.log('article status failed');
              //$rootScope.showAlert(data.status, data.msg);
            }
          }).error(function(ee){
            $rootScope.showAlert("Warning", "Couldn't fetch status of the articles, please make sure you have internet connection!");
          })    
        }
      //}, 10);
      $ionicLoading.hide();
    }
    var fail = function (error) {
      if (error.code == 1) {
        alert("File Not Found Err");  
      } else if (error.code == 2) {
        alert("INVALID_URL_ERR");  
      } else if (error.code == 3) {
        alert("CONNECTION_ERR");
      } else if (error.code == 4) {
        alert("ABORT_ERR");
      } else if (error.code == 5) {
        alert("NOT_MODIFIED_ERR");
      }
      $ionicLoading.hide();
    }

    var options = new FileUploadOptions();
    options.fileKey = "file";
    options.chunkedMode = false;
    options.headers = {
      Connection: "close"
    };
    if ($stateParams.type == 1) {
      options.params = {
        article_id: $rootScope.$storage.sitem.article_id,
        user_id: $rootScope.$storage.user.user_id,
        owner_file_name: $rootScope.$storage.src
      };  
    } else if ($stateParams.type == 2) {
      options.params = {
        article_id: $rootScope.$storage.sitem.article_id,
        user_id: $rootScope.$storage.user.user_id,
        ne1_file_name: $rootScope.$storage.src
      };  
    } else if ($stateParams.type == 3) {
      options.params = {
        article_id: $rootScope.$storage.sitem.article_id,
        user_id: $rootScope.$storage.user.user_id,
        ne1_inst_file_name: $rootScope.$storage.src
      };  
    } else if ($stateParams.type == 4) {
      options.params = {
        article_id: $rootScope.$storage.sitem.article_id,
        user_id: $rootScope.$storage.user.user_id,
        ne2_file_name: $rootScope.$storage.src
      };  
    } else if ($stateParams.type == 5) {
      options.params = {
        article_id: $rootScope.$storage.sitem.article_id,
        user_id: $rootScope.$storage.user.user_id,
        ne2_inst_file_name: $rootScope.$storage.src
      };  
    } else if ($stateParams.type == 6) {
      options.params = {
        article_id: $rootScope.$storage.sitem.article_id,
        user_id: $rootScope.$storage.user.user_id,
        custom_article_file_name: $rootScope.$storage.src
      };  
    } else if ($stateParams.type == 7) {
      options.params = {
        article_id: $rootScope.$storage.sitem.article_id,
        user_id: $rootScope.$storage.user.user_id,
        custom_article_inst_file_name: $rootScope.$storage.src
      };  
    }
    options.fileName = $rootScope.$storage.src;
    options.mimeType = "audio/wav";

    var ft = new FileTransfer();
    ft.onprogress = function(progressEvent) {
      var perc = (progressEvent.loaded / progressEvent.total) * 100;
      $ionicLoading.show({
          template: 'Uploading ... ' + window.Math.round(perc) + '% <ion-spinner icon="circles"></ion-spinner>',
          showBackdrop: true
     }); 
    };
    if ($stateParams.type == 1) {
      if (cordova.file.documentsDirectory) {
        ft.upload(cordova.file.documentsDirectory + $rootScope.$storage.src, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_owner_recording"), win,fail, options);
      } else {
        ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_owner_recording"), win,fail, options);
      }      
    } else if ($stateParams.type == 2) {
      if (cordova.file.documentsDirectory) {
        ft.upload(cordova.file.documentsDirectory + $rootScope.$storage.src, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne1_recording"), win,fail, options);
      } else {
        ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne1_recording"), win,fail, options);
      }
    } else if ($stateParams.type == 3) {
      if (cordova.file.documentsDirectory) {
        ft.upload(cordova.file.documentsDirectory + $rootScope.$storage.src, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne1_inst_recording"), win,fail, options);
      } else {
        ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne1_inst_recording"), win,fail, options);
      }
      //ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne1_inst_recording"), win,fail, options);
    } else if ($stateParams.type == 4) {
      if (cordova.file.documentsDirectory) {
        ft.upload(cordova.file.documentsDirectory + $rootScope.$storage.src, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne2_recording"), win,fail, options);
      } else {
        ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne2_recording"), win,fail, options);
      }
      //ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne2_recording"), win,fail, options);
    } else if ($stateParams.type == 5) {
      if (cordova.file.documentsDirectory) {
        ft.upload(cordova.file.documentsDirectory + $rootScope.$storage.src, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne2_inst_recording"), win,fail, options);
      } else {
        ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne2_inst_recording"), win,fail, options);
      }
      //ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne2_inst_recording"), win,fail, options);
    } else if ($stateParams.type == 6) {
      if (cordova.file.documentsDirectory) {
        ft.upload(cordova.file.documentsDirectory + $rootScope.$storage.src, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_custom_article_recording"), win,fail, options);
      } else {
        ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_custom_article_recording"), win,fail, options);
      }
      //ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_custom_article_recording"), win,fail, options);
    } else if ($stateParams.type == 7) {
      if (cordova.file.documentsDirectory) {
        ft.upload(cordova.file.documentsDirectory + $rootScope.$storage.src, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_custom_article_inst_recording"), win,fail, options);
      } else {
        ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_custom_article_inst_recording"), win,fail, options);
      }
      //ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_custom_article_inst_recording"), win,fail, options);
    }
  };
  $scope.doPause = function() {
    console.log('puasing')
    //if (angular.isDefined($scope.data.recording)){
      console.log('pausing...'+$scope.data.ms)
      if ($scope.data.ms == "recording") {
        $scope.data.ms = "paused";
        console.log('paused'+angular.toJson($scope.myMedia));
        $scope.myMedia.pauseRecord();
        $interval.cancel($scope.data.recording);
        $scope.data.recording = undefined;
      } else {
        if ($scope.data.ms == "paused"){
          $scope.data.recording = $interval(function() {
            if (!$scope.stopAudio) {
              if ($scope.ctime > $scope.maxrec) {
                $scope.stopAudio = true;
              }
              if ($scope.data.ms == "recording") {
                $scope.ctime++;  
              }              
            } else {
              $scope.stopRecord();
            }
          }, 1000);
          $scope.data.ms = "recording";
          $scope.myMedia.resumeRecord();
          console.log('resume')
        }
      }
    //}
    if (!$scope.$$phase){
      $scope.$apply();
    }
  }; 
  $scope.stopRecord = function() {
    if (angular.isDefined($scope.data.recording)) {
      $interval.cancel($scope.data.recording);
      $scope.data.recording = undefined;
      $scope.ctime=0;
      $scope.data.ms = "stopped";
      $scope.started = $scope.started + "a";
      $scope.myMedia.stopRecord();
    }
  };

  $scope.$on('$destroy', function() {
    // Make sure that the interval is destroyed too
    console.log('destroy stopping record');
    $scope.stopRecord();
  });
  $scope.$on('$ionicView.leave', function() {
    // Make sure that the interval is destroyed too
    console.log('leave stopping record');
    $scope.stopRecord();
  });
  
  $scope.$on('$ionicView.beforeEnter', function(){
    if (!$rootScope.$storage.user) {
      $ionicHistory.nextViewOptions({
        disableBack: true
      });
      $state.go('login');
    }
    if ($stateParams.type == 1){
      $rootScope.$storage.src = $rootScope.$storage.sitem.article_month+$rootScope.$storage.sitem.article_year+"_ownersmessage.wav";//Feb16_ownersmessage
      $scope.title = $rootScope.$storage.sitem.article_month+"'s"+" Owner's message.";
      $scope.stitle = $rootScope.$storage.config["owners_message_note"] || "Points to make in your Letter from the Owner."
    } else if ($stateParams.type == 2) {
      $scope.title = $rootScope.$storage.sitem.article_month+"'s"+" News & Events.";
      $scope.stitle = $rootScope.$storage.config["n&e1_note"] || "1-2 Notices for your News & Events, First Notice."
      $rootScope.$storage.src = $rootScope.$storage.sitem.article_month+$rootScope.$storage.sitem.article_year+"_ne1.wav";
    } else if ($stateParams.type == 3) {
      $scope.title = $rootScope.$storage.sitem.article_month+"'s"+" News & Events.";
      $scope.stitle = $rootScope.$storage.config["n&e1_inst_note"] || "Instructions for gathering an image and further information for the first notice";
      $rootScope.$storage.src = $rootScope.$storage.sitem.article_month+$rootScope.$storage.sitem.article_year+"_ne1_inst.wav";
    } else if ($stateParams.type == 4) {
      $scope.title = $rootScope.$storage.sitem.article_month+"'s"+" News & Events.";
      $scope.stitle = $rootScope.$storage.config["n&e2_note"] || "1-2 Notices for your News & Events, Second Notice";
      $rootScope.$storage.src = $rootScope.$storage.sitem.article_month+$rootScope.$storage.sitem.article_year+"_ne2.wav";
    } else if ($stateParams.type == 5) {
      $scope.title = $rootScope.$storage.sitem.article_month+"'s"+" News & Events.";
      $scope.stitle = $rootScope.$storage.config["n&e2_inst_note"] || "Instructions for gathering an image and further information for the second notice";
      $rootScope.$storage.src = $rootScope.$storage.sitem.article_month+$rootScope.$storage.sitem.article_year+"_ne2_inst.wav";
    } else if ($stateParams.type == 6) {
      $scope.title = $rootScope.$storage.sitem.article_month+"'s"+" Customized Article.";
      $scope.stitle = $rootScope.$storage.config["custom_article_note"] || "What topic would you like covered in next month’s article?";
      $rootScope.$storage.src = $rootScope.$storage.sitem.article_month+$rootScope.$storage.sitem.article_year+"_customarticle.wav";
    } else if ($stateParams.type == 7) {
      $scope.title = "Customized Article.";
      $scope.stitle = $rootScope.$storage.config["custom_article_inst_note"] || "Instructions for gathering an image and or further information for your customized article?";
      $rootScope.$storage.src = $rootScope.$storage.sitem.article_month+$rootScope.$storage.sitem.article_year+"_customarticle_inst.wav";
    }

    if ($stateParams.type==1) {
      $scope.started = 11;  
    }
    if (!ionic.Platform.isAndroid()) {
      /*var ur = encodeURI('http://api.voicerss.org/?key=bce43da471ee43a4855e36096ac8137f&hl=en-us&src='+$scope.title+$scope.stitle)
      $scope.sound = ngAudio.load(ur);    
      console.log('loading voicerss 1');
      if ($scope.sound.error) {
        console.log('loading voicerss 2');
        $scope.sound = ngAudio.load(encodeURI('http://api.voicerss.org/?key=e6a221877f524b5b943346253b249550&hl=en-us&src='+$scope.title+$scope.stitle));
      }*/
      $scope.sound = {error:true};
    }
  });
  $scope.$on('$ionicView.afterEnter', function(){
    $ionicPlatform.ready(function() {
      if (ionic.Platform.isAndroid()) {
        TTS.speak({
          text: $scope.stitle,
          rate: 1.0
          }, function () { 
            console.log('text spoken');
            $scope.spoken = true;
            if (!$scope.$$phase){
      $scope.$apply();
    }
          }, function (reason) {
            alert(reason);
        });    
      } else {
        function ons(){
          console.log('success speech from api '+angular.toJson($scope.sound));
          if ($scope.sound && $scope.sound.error==undefined){
            $scope.sound.volume=1.0;
            setTimeout(function() {
              $scope.sound.play();                            
            }, 10);
          } else {
            TTS.speak({
              text: $scope.stitle,
              rate: 1.2
              }, function () { 
                console.log('text spoken');
                $scope.spoken = true;if (!$scope.$$phase){
      $scope.$apply();
    }
              }, function (reason) {
                alert(reason);
            });
          }
        }
        function one(){
          console.log('error speach from api');
          TTS.speak({
            text: $scope.stitle,
            rate: 1.4
            }, function () { 
              console.log('text spoken');
              $scope.spoken = true;if (!$scope.$$phase){
      $scope.$apply();
    }
            }, function (reason) {
              alert(reason);
          }); 
        }
        if ($scope.sound.error) {
          one();
        } else {
          ons();
        }
        //var m = new Media(ur, ons, one);
        //m.play();
      }
    });
  });

  $scope.exit = function(){
    $ionicHistory.nextViewOptions({
      disableBack: true
    });

    console.log('uploading and exiting')
    var mtype = Number($stateParams.type)+1;
    $scope.started = mtype;    
    var win = function (r) {
      console.log("Code = " + r.responseCode);
      console.log("Response = " + r.response);
      console.log("Sent = " + r.bytesSent);
      $ionicLoading.hide();
      $state.go('home');
    }
    var fail = function (error) {
      if (error.code == 1) {
        alert("File Not Found Err");  
      } else if (error.code == 2) {
        alert("INVALID_URL_ERR");  
      } else if (error.code == 3) {
        alert("CONNECTION_ERR");
      } else if (error.code == 4) {
        alert("ABORT_ERR");
      } else if (error.code == 5) {
        alert("NOT_MODIFIED_ERR");
      }
      $ionicLoading.hide();
      $state.go('home');
    }

    var options = new FileUploadOptions();
    options.fileKey = "file";
    options.chunkedMode = false;
    options.headers = {
      Connection: "close"
    };
    if ($stateParams.type == 1) {
      options.params = {
        article_id: $rootScope.$storage.sitem.article_id,
        user_id: $rootScope.$storage.user.user_id,
        owner_file_name: $rootScope.$storage.src
      };  
    } else if ($stateParams.type == 2) {
      options.params = {
        article_id: $rootScope.$storage.sitem.article_id,
        user_id: $rootScope.$storage.user.user_id,
        ne1_file_name: $rootScope.$storage.src
      };  
    } else if ($stateParams.type == 3) {
      options.params = {
        article_id: $rootScope.$storage.sitem.article_id,
        user_id: $rootScope.$storage.user.user_id,
        ne1_inst_file_name: $rootScope.$storage.src
      };  
    } else if ($stateParams.type == 4) {
      options.params = {
        article_id: $rootScope.$storage.sitem.article_id,
        user_id: $rootScope.$storage.user.user_id,
        ne2_file_name: $rootScope.$storage.src
      };  
    } else if ($stateParams.type == 5) {
      options.params = {
        article_id: $rootScope.$storage.sitem.article_id,
        user_id: $rootScope.$storage.user.user_id,
        ne2_inst_file_name: $rootScope.$storage.src
      };  
    } else if ($stateParams.type == 6) {
      options.params = {
        article_id: $rootScope.$storage.sitem.article_id,
        user_id: $rootScope.$storage.user.user_id,
        custom_article_file_name: $rootScope.$storage.src
      };  
    } else if ($stateParams.type == 7) {
      options.params = {
        article_id: $rootScope.$storage.sitem.article_id,
        user_id: $rootScope.$storage.user.user_id,
        custom_article_inst_file_name: $rootScope.$storage.src
      };  
    }
    options.fileName = $rootScope.$storage.src;
    options.mimeType = "audio/wav";

    var ft = new FileTransfer();
    ft.onprogress = function(progressEvent) {
      var perc = (progressEvent.loaded / progressEvent.total) * 100;
      $ionicLoading.show({
          template: 'Uploading ... ' + window.Math.round(perc) + '% <ion-spinner icon="circles"></ion-spinner>',
          showBackdrop: true
     }); 
    };
    if ($stateParams.type == 1) {
      if (cordova.file.documentsDirectory) {
        ft.upload(cordova.file.documentsDirectory + $rootScope.$storage.src, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_owner_recording"), win,fail, options);
      } else {
        ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_owner_recording"), win,fail, options);
      }      
    } else if ($stateParams.type == 2) {
      if (cordova.file.documentsDirectory) {
        ft.upload(cordova.file.documentsDirectory + $rootScope.$storage.src, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne1_recording"), win,fail, options);
      } else {
        ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne1_recording"), win,fail, options);
      }
    } else if ($stateParams.type == 3) {
      if (cordova.file.documentsDirectory) {
        ft.upload(cordova.file.documentsDirectory + $rootScope.$storage.src, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne1_inst_recording"), win,fail, options);
      } else {
        ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne1_inst_recording"), win,fail, options);
      }
    } else if ($stateParams.type == 4) {
      if (cordova.file.documentsDirectory) {
        ft.upload(cordova.file.documentsDirectory + $rootScope.$storage.src, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne2_recording"), win,fail, options);
      } else {
        ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne2_recording"), win,fail, options);
      }
    } else if ($stateParams.type == 5) {
      if (cordova.file.documentsDirectory) {
        ft.upload(cordova.file.documentsDirectory + $rootScope.$storage.src, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne2_inst_recording"), win,fail, options);
      } else {
        ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_ne2_inst_recording"), win,fail, options);
      }
    } else if ($stateParams.type == 6) {
      if (cordova.file.documentsDirectory) {
        ft.upload(cordova.file.documentsDirectory + $rootScope.$storage.src, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_custom_article_recording"), win,fail, options);
      } else {
        ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_custom_article_recording"), win,fail, options);
      }
    } else if ($stateParams.type == 7) {
      if (cordova.file.documentsDirectory) {
        ft.upload(cordova.file.documentsDirectory + $rootScope.$storage.src, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_custom_article_inst_recording"), win,fail, options);
      } else {
        ft.upload($rootScope.$storage.fileURL, encodeURI("http://wwwlogix.com/dev/ezine/services/app/ezine_set_custom_article_inst_recording"), win,fail, options);
      }
    }
  }
})

.controller('SessionCtrl', function($scope, $ionicModal, $stateParams, $timeout, $rootScope, $state, $ionicActionSheet, $window, $ionicHistory, $cordovaCapture, $interval, $cordovaFile, $ionicPlatform, $ionicLoading, APIs) {

  $scope.$on('$ionicView.beforeEnter', function(){
    $scope.data = {};
    if ($stateParams.type == 1) {
      $scope.title = "";
    } else if ($stateParams.type == 2) {
      $scope.title = "RE-Record One Part";
    }
    if (!$rootScope.$storage.user) {
      $ionicHistory.nextViewOptions({
        disableBack: true
      });
      $state.go('login');
    }//, article_id: $rootScope.$storage.sitem.article_id
    APIs.getarticleStatus({"user_id":$rootScope.$storage.user.user_id, "article_id": $rootScope.$storage.sitem.article_id}).success(function(data) {
      if (data.status == "success") {
        $scope.data.exist = data.msg[0];
        console.log(angular.toJson(data));
      } else {
        $scope.data.exist = undefined;
        //$rootScope.showAlert(data.status, data.msg);
      }
    }).error(function(ee){
      $rootScope.showAlert("Warning", "Couldn't fetch status of the articles, please make sure you have internet connection!");
    })
  });

  $scope.goRec = function(id) {
    $ionicHistory.nextViewOptions({
      disableBack: true
    });
    $state.go('record', {type: id});  
  };
  
  $scope.exit = function(){
    $ionicHistory.nextViewOptions({
      disableBack: true
    });
    $state.go('home');
  };

})


;

jQuery( document ).ready( function( $ ) {

    function setTokenSentToServer(val) {
       return window.localStorage.setItem('unique_user_id', val);
    }

    function isTokenSentToServer() {
       return window.localStorage.getItem('unique_user_id');
    }

    function saveToken(currentToken) {

        var unique_user_id = isTokenSentToServer();

        jQuery.ajax({
            type : "post",
            url : zealwpn_object.ajax_url,
            data : {action: "notification_token", token : currentToken, unique_id : unique_user_id },
            success: function(response) {
                setTokenSentToServer(response);
            }
        });
    }

    TokenElem = document.getElementById("token");

    // Initialize Firebase
    var config = {
        'apiKey': zealwpn_object.notification_apiKey,
        'projectId': zealwpn_object.notification_projectId,
        'messagingSenderId': zealwpn_object.notification_senderId,
        'appId': zealwpn_object.notification_appId,
    };
    firebase.initializeApp(config);
    firebase.analytics();

    const messaging = firebase.messaging();    

    navigator.serviceWorker.register(zealwpn_object.pluginsUrl + 'assets/js/firebase-messaging-sw.js')
    .then(function (registration) {
        /** Since we are using our own service worker ie firebase-messaging-sw.js file */
        messaging.useServiceWorker(registration);

        /** Lets request user whether we need to send the notifications or not */
        messaging.requestPermission()
            .then(function () {
                /** Standard function to get the token */
                messaging.getToken()
                .then(function(token) {
                    /** Here I am logging to my console. This token I will use for testing with PHP Notification */
                    if (token) {
                        var token = token;
                        saveToken(token); // Save token in db
                    } else {
                        console.log('No Instance ID token available. Request permission to generate one.');
                    }

                    /** SAVE TOKEN::From here you need to store the TOKEN by AJAX request to your server */
                })
                .catch(function(error) {
                    /** If some error happens while fetching the token then handle here */
                    
                    console.log('Error while fetching the token ' + error);
                });
            })
            .catch(function (error) {
                /** If user denies then handle something here */
                console.log('Permission denied ' + error);
            })
    })
    .catch(function () {
        console.log('Error in registering service worker');
    });



    /** What we need to do when the existing token refreshes for a user */
    messaging.onTokenRefresh(function() {
        messaging.getToken()
        .then(function(renewedToken) {
         
            if (renewedToken) {
                var token = renewedToken;
                saveToken(token); // Save token in db
            } else {
                console.log('No Instance ID token available. Request permission to generate one.');
            }


            /** UPDATE TOKEN::From here you need to store the TOKEN by AJAX request to your server */
        })
        .catch(function(error) {
            /** If some error happens while fetching the token then handle here */
            console.log('Error in fetching refreshed token ' + error);
        });
    });


    // Handle incoming messages
    messaging.onMessage(function(payload) {

        const timestamp = 42; 

        var notificationTitle = payload.data.title;
        const notificationOptions = {
            body: payload.data.body,
            icon: payload.data.icon,
            image: payload.data.image,
            click_action: payload.data.click_action, // To handle notification click when notification is moved to notification tray
            badge: payload.data.icon,
            //showTrigger: new firebase.firestore.Timestamp(timestamp,0),
            data: {
                click_action: payload.data.click_action
            }
        };

       var notification = new Notification(notificationTitle,notificationOptions);

    });

});
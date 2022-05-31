importScripts("https://www.gstatic.com/firebasejs/8.2.4/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/8.2.4/firebase-messaging.js");

// Initialize the Firebase app in the service worker by passing in the
// messagingSenderId.
var firebaseConfig = {
<<<<<<< Updated upstream
    apiKey: "AIzaSyBzyxX0Xw6yUM9nvChTeFbcyg99o1TJMGs", //REPLACE_WITH_YOUR_FIREBASE_MESSAGING_APP_ID
    authDomain: "push-notification-8eb13.firebaseapp.com",
    projectId: "push-notification-8eb13",
    storageBucket: "push-notification-8eb13.appspot.com",
    messagingSenderId: "123593987504",
    appId: "1:123593987504:web:62935156478b7848faf275",
    measurementId: "G-R3RKT51J1X"
=======
    apiKey: "AIzaSyBzyxX0Xw6yUM9nvChTeFbcyg99o1TJMGs",
    projectId: "push-notification-8eb13",
    messagingSenderId: "123593987504",
    appId: "1:123593987504:web:62935156478b7848faf275",
>>>>>>> Stashed changes
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);


// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    
    // Customize notification here
    const notificationTitle = payload.data.title;

    const notificationOptions = {
        body: payload.data.body,
        icon: payload.data.icon,
        image: payload.data.image,
        click_action: payload.data.click_action,
        //showTrigger: new firebase.firestore.Timestamp(42,0),
        data: {
            click_action: payload.data.click_action,
            image: payload.data.image,
        }   
    };  

    // Event when click on notification
    self.addEventListener('notificationclick', function(payload) {

        if (!payload.action) {
            // Was a normal notification click
            self.clients.openWindow(payload.notification.data.click_action, '_blank')
            payload.notification.close();
            return;
        }else{
            payload.notification.close();
        }
    });

    return self.registration.showNotification(
        notificationTitle,
        notificationOptions,
    );
});

messaging.setBackgroundMessageHandler(function(payload) {
    
    const notificationTitle = payload.data.title;
    const notificationOptions = {
        body: payload.data.body,
        icon: payload.data.icon,
        image: payload.data.image,
        click_action: payload.data.click_action,
        //showTrigger: new firebase.firestore.Timestamp(42,0),
        data: {
            click_action: payload.data.click_action,
            image: payload.data.image,
        }  
    };  

    // Event when click on notification
    self.addEventListener('notificationclick', function(payload) {

        if (!payload.action) {
            // Was a normal notification click
            self.clients.openWindow(payload.notification.data.click_action, '_blank')
            payload.notification.close();
            return;
        }else{
            payload.notification.close();
        }
    });

    return self.registration.showNotification(
        notificationTitle,
        notificationOptions,
    );
});
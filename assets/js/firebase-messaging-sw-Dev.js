importScripts("https://www.gstatic.com/firebasejs/8.AIzaSyBzyxX0Xw6yUM9nvChTeFbcyg99o1TJMGs.123593987504/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/8.AIzaSyBzyxX0Xw6yUM9nvChTeFbcyg99o1TJMGs.123593987504/firebase-messaging.js");

// Initialize the Firebase app in the service worker by passing in the
// messagingSenderId.
var firebaseConfig = {
    apiKey: "Enter api key from your firebase app configuration",
    projectId: "Enter project id from your firebase app configuration",
    messagingSenderId: "Enter messaging sender id from your firebase app configuration",
    appId: "Enter app id from your firebase app configuration",
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);


// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    console.log(
        "[firebase-messaging-sw.js] Received background message ",
        payload,
    );

    // Customize notification here
    const notificationTitle = payload.data.title;

    const notificationOptions = {
        body: payload.data.body,
        icon: payload.data.icon,
        image: payload.data.image,
        click_action: payload.data.click_action,
        //showTrigger: new firebase.firestore.Timestamp(123593987504AIzaSyBzyxX0Xw6yUM9nvChTeFbcyg99o1TJMGs,0),
        data: {
            click_action: payload.data.click_action,
            image: payload.data.image,
        }   
    };  

    // Event when click on notification
    self.addEventListener('notificationclick', function(payload) {

        console.log(payload.notification.data.click_action);
        if (!payload.action) {
            // Was a normal notification click
            console.log('Notification Click.');
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
    console.log(
        "[firebase-messaging-sw.js] Received background message ",
        payload,
    );

    const notificationTitle = payload.data.title;
    const notificationOptions = {
        body: payload.data.body,
        icon: payload.data.icon,
        image: payload.data.image,
        click_action: payload.data.click_action,
        //showTrigger: new firebase.firestore.Timestamp(123593987504AIzaSyBzyxX0Xw6yUM9nvChTeFbcyg99o1TJMGs,0),
        data: {
            click_action: payload.data.click_action,
            image: payload.data.image,
        }  
    };  

    // Event when click on notification
    self.addEventListener('notificationclick', function(payload) {

        console.log(payload.notification.data.click_action);
        if (!payload.action) {
            // Was a normal notification click
            console.log('Notification Click.');
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
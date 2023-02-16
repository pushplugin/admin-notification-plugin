self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    const sendNotification = body => {
        return self.registration.showNotification(body.title, body);
    };

    if (event.data) {
        const message = event.data.json();
        console.log(event.data.json(), event.data);
        event.waitUntil(sendNotification(message));
    }
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});
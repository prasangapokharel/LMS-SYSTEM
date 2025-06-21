// Service Worker for Push Notifications
self.addEventListener("push", (event) => {
  if (event.data) {
    const data = event.data.json()

    const options = {
      body: data.body,
      icon: data.icon || "/assets/images/notification-icon.png",
      badge: data.badge || "/assets/images/badge-icon.png",
      tag: data.tag || "lms-notification",
      requireInteraction: data.requireInteraction || false,
      actions: data.actions || [
        { action: "view", title: "View" },
        { action: "dismiss", title: "Dismiss" },
      ],
      data: data.data || {},
    }

    event.waitUntil(self.registration.showNotification(data.title, options))
  }
})

self.addEventListener("notificationclick", (event) => {
  event.notification.close()

  if (event.action === "view") {
    // Handle view action
    const urlToOpen = event.notification.data.url || "/students/"

    event.waitUntil(
      clients.matchAll().then((clientList) => {
        // Check if there's already a window/tab open
        for (let i = 0; i < clientList.length; i++) {
          const client = clientList[i]
          if (client.url.includes(urlToOpen) && "focus" in client) {
            return client.focus()
          }
        }

        // Open new window/tab
        if (clients.openWindow) {
          return clients.openWindow(urlToOpen)
        }
      }),
    )
  }
})

self.addEventListener("notificationclose", (event) => {
  // Handle notification close
  console.log("Notification closed:", event.notification.tag)
})

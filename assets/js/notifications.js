class NotificationManager {
  constructor() {
    this.eventSource = null
    this.notificationPermission = "default"
    this.init()
  }

  async init() {
    // Request notification permission
    if ("Notification" in window) {
      this.notificationPermission = await Notification.requestPermission()
    }

    // Initialize service worker for push notifications
    if ("serviceWorker" in navigator && "PushManager" in window) {
      await this.initServiceWorker()
    }

    // Start real-time notifications
    this.startRealTimeNotifications()

    // Initialize in-app notification UI
    this.initNotificationUI()
  }

  async initServiceWorker() {
    try {
      const registration = await navigator.serviceWorker.register("/sw.js")
      console.log("Service Worker registered:", registration)

      // Subscribe to push notifications
      const subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: this.urlBase64ToUint8Array(
          "BOTTp9eT7CXYpFjZ4WhJpkdENtK8ll9TJsMD5byw378y7ggK-I6b3l-jxyxwgFotTTViDPwyymh1X1r2uv8MBqY",
        ),
      })

      // Send subscription to server
      await fetch("/api/save-push-subscription.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(subscription),
      })
    } catch (error) {
      console.error("Service Worker registration failed:", error)
    }
  }

  startRealTimeNotifications() {
    this.eventSource = new EventSource("/api/notifications-stream.php")

    this.eventSource.onmessage = (event) => {
      const notification = JSON.parse(event.data)

      if (notification.type !== "heartbeat") {
        this.showNotification(notification)
      }
    }

    this.eventSource.onerror = (error) => {
      console.error("Notification stream error:", error)
      // Reconnect after 5 seconds
      setTimeout(() => this.startRealTimeNotifications(), 5000)
    }
  }

  showNotification(notification) {
    // Show browser notification
    if (this.notificationPermission === "granted") {
      const browserNotification = new Notification(notification.title, {
        body: notification.message,
        icon: "/assets/images/notification-icon.png",
        badge: "/assets/images/badge-icon.png",
        tag: notification.type,
        requireInteraction: notification.type === "exam_reminder",
      })

      browserNotification.onclick = () => {
        window.focus()
        this.handleNotificationClick(notification)
        browserNotification.close()
      }
    }

    // Show in-app notification
    this.showInAppNotification(notification)

    // Update notification badge
    this.updateNotificationBadge()
  }

  showInAppNotification(notification) {
    const container = document.getElementById("notification-container") || this.createNotificationContainer()

    const notificationEl = document.createElement("div")
    notificationEl.className = `notification notification-${notification.type}`
    notificationEl.innerHTML = `
            <div class="notification-content">
                <div class="notification-title">${notification.title}</div>
                <div class="notification-message">${notification.message}</div>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">×</button>
        `

    container.appendChild(notificationEl)

    // Auto-remove after 5 seconds
    setTimeout(() => {
      if (notificationEl.parentElement) {
        notificationEl.remove()
      }
    }, 5000)
  }

  createNotificationContainer() {
    const container = document.createElement("div")
    container.id = "notification-container"
    container.className = "notification-container"
    document.body.appendChild(container)
    return container
  }

  handleNotificationClick(notification) {
    switch (notification.type) {
      case "exam_reminder":
        window.location.href = "/students/schedule.php"
        break
      case "message":
        window.location.href = "/students/messages.php"
        break
      case "assignment":
        window.location.href = "/students/assignments.php"
        break
      default:
        // Default action
        break
    }
  }

  updateNotificationBadge() {
    fetch("/api/get-unread-count.php")
      .then((response) => response.json())
      .then((data) => {
        const badge = document.querySelector(".notification-badge")
        if (badge) {
          badge.textContent = data.count
          badge.style.display = data.count > 0 ? "block" : "none"
        }
      })
  }

  urlBase64ToUint8Array(base64String) {
    const padding = "=".repeat((4 - (base64String.length % 4)) % 4)
    const base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/")
    const rawData = window.atob(base64)
    const outputArray = new Uint8Array(rawData.length)
    for (let i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i)
    }
    return outputArray
  }
}

// Initialize notification manager when page loads
document.addEventListener("DOMContentLoaded", () => {
  window.notificationManager = new NotificationManager()
})

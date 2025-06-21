<?php
// Web Push Notification Service (requires web-push-php library)
class WebPushService {
    private $vapidKeys;
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        // Generate VAPID keys for your application
        $this->vapidKeys = [
            'subject' => 'mailto:prasangaramanpokharel@gmail.com',
            'publicKey' => 'BOTTp9eT7CXYpFjZ4WhJpkdENtK8ll9TJsMD5byw378y7ggK-I6b3l-jxyxwgFotTTViDPwyymh1X1r2uv8MBqY',
            'privateKey' => 'HEolDUcHxTNrVEAbEpqot_2OssHC05X7dlUtHGl5-ks'
        ];
    }
    
    // Save push subscription
    public function saveSubscription($user_id, $subscription) {
        try {
            $endpoint = $subscription['endpoint'];
            $p256dh = $subscription['keys']['p256dh'];
            $auth = $subscription['keys']['auth'];
            
            $stmt = $this->pdo->prepare("INSERT INTO push_subscriptions 
                                        (user_id, endpoint, p256dh_key, auth_key) 
                                        VALUES (?, ?, ?, ?) 
                                        ON DUPLICATE KEY UPDATE 
                                        p256dh_key = VALUES(p256dh_key), 
                                        auth_key = VALUES(auth_key)");
            return $stmt->execute([$user_id, $endpoint, $p256dh, $auth]);
        } catch (PDOException $e) {
            error_log("Save subscription error: " . $e->getMessage());
            return false;
        }
    }
    
    // Send push notification
    public function sendPushNotification($user_id, $title, $body, $data = []) {
        try {
            // Get user's push subscriptions
            $stmt = $this->pdo->prepare("SELECT * FROM push_subscriptions WHERE user_id = ? AND is_active = 1");
            $stmt->execute([$user_id]);
            $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $payload = json_encode([
                'title' => $title,
                'body' => $body,
                'icon' => '/assets/images/notification-icon.png',
                'badge' => '/assets/images/badge-icon.png',
                'data' => $data,
                'actions' => [
                    ['action' => 'view', 'title' => 'View'],
                    ['action' => 'dismiss', 'title' => 'Dismiss']
                ]
            ]);
            
            foreach ($subscriptions as $subscription) {
                // Use web-push library to send notification
                // This requires installing web-push-php via composer
                $this->sendToEndpoint($subscription, $payload);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Push notification error: " . $e->getMessage());
            return false;
        }
    }
    
    private function sendToEndpoint($subscription, $payload) {
        // Implementation would use web-push-php library
        // For now, this is a placeholder
        error_log("Would send push notification: " . $payload);
    }
}
?>

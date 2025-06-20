<?php
include '../include/loader.php';
include '../include/buffer.php';

requireRole('student');

$user = getCurrentUser($pdo);

// Debug: Let's check what user ID we're working with
error_log("Current user ID: " . $user['id']);
error_log("Current user: " . print_r($user, true));

// Get filter parameters
$message_type = $_GET['type'] ?? 'all';
$read_status = $_GET['status'] ?? 'all';
$priority_filter = $_GET['priority'] ?? 'all';

// Handle mark as read
if (isset($_GET['read']) && is_numeric($_GET['read'])) {
    $message_id = (int)$_GET['read'];
    try {
        $stmt = $pdo->prepare("UPDATE messages SET is_read = 1, read_at = NOW() WHERE id = ? AND recipient_id = ?");
        $stmt->execute([$message_id, $user['id']]);
        header('Location: messages.php');
        exit;
    } catch (PDOException $e) {
        error_log("Error marking message as read: " . $e->getMessage());
    }
}

// Build the messages query with debug
$query = "SELECT m.*, 
               sender.first_name as sender_first_name, 
               sender.last_name as sender_last_name,
               sender.role_id as sender_role
        FROM messages m
        JOIN users sender ON m.sender_id = sender.id
        WHERE m.recipient_id = ? AND m.is_deleted_by_recipient = 0";

$params = [$user['id']];

// Add type filter
if ($message_type !== 'all') {
    $query .= " AND m.message_type = ?";
    $params[] = $message_type;
}

// Add read status filter
if ($read_status === 'unread') {
    $query .= " AND m.is_read = 0";
} elseif ($read_status === 'read') {
    $query .= " AND m.is_read = 1";
}

// Add priority filter
if ($priority_filter !== 'all') {
    $query .= " AND m.priority = ?";
    $params[] = $priority_filter;
}

$query .= " ORDER BY m.sent_at DESC";

// Debug the query
error_log("Messages Query: " . $query);
error_log("Query Params: " . print_r($params, true));

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug results
    error_log("Found " . count($messages) . " messages for user ID: " . $user['id']);
    
} catch (PDOException $e) {
    error_log("Error fetching messages: " . $e->getMessage());
    $messages = [];
}

// Get message counts for stats
try {
    $stmt = $pdo->prepare("SELECT 
                          COUNT(*) as total_messages,
                          SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_count,
                          SUM(CASE WHEN message_type = 'announcement' THEN 1 ELSE 0 END) as announcements,
                          SUM(CASE WHEN priority = 'high' OR priority = 'urgent' THEN 1 ELSE 0 END) as important
                         FROM messages 
                         WHERE recipient_id = ? AND is_deleted_by_recipient = 0");
    $stmt->execute([$user['id']]);
    $message_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Debug stats
    error_log("Message stats: " . print_r($message_stats, true));
    
} catch (PDOException $e) {
    error_log("Error fetching message stats: " . $e->getMessage());
    $message_stats = [
        'total_messages' => 0,
        'unread_count' => 0,
        'announcements' => 0,
        'important' => 0
    ];
}

// Let's also check what's in the messages table for debugging
try {
    $debug_stmt = $pdo->prepare("SELECT id, sender_id, recipient_id, subject, message_type, priority, is_read, sent_at FROM messages WHERE is_deleted_by_recipient = 0 ORDER BY sent_at DESC LIMIT 10");
    $debug_stmt->execute();
    $all_messages = $debug_stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("All recent messages in database: " . print_r($all_messages, true));
} catch (PDOException $e) {
    error_log("Error in debug query: " . $e->getMessage());
}

// Function to get priority color
function getPriorityColor($priority) {
    switch ($priority) {
        case 'urgent': return '#ef4444';
        case 'high': return '#f59e0b';
        case 'normal': return '#3b82f6';
        case 'low': return '#6b7280';
        default: return '#3b82f6';
    }
}

// Function to get message type icon
function getMessageTypeIcon($type) {
    $icons = [
        'personal' => '💬',
        'announcement' => '📢',
        'system' => '⚙️',
        'assignment' => '📝'
    ];
    return $icons[$type] ?? '💬';
}

// Function to get sender role badge
function getSenderRoleBadge($role_id) {
    switch ($role_id) {
        case 1: return 'Principal';
        case 2: return 'Teacher';
        case 3: return 'Student';
        default: return 'User';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Student Portal</title>
    <link rel="stylesheet" href="../assets/css/ui.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            padding-bottom: 80px;
        }

        .mobile-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 60px 20px 30px;
            text-align: center;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 400px;
            margin: 0 auto;
        }

        .back-btn {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 8px;
        }

        .page-title {
            font-size: 20px;
            font-weight: 600;
            flex: 1;
            text-align: center;
        }

        .page-subtitle {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 4px;
        }

        .stats-container {
            background: white;
            margin: -15px 20px 20px;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            text-align: center;
        }

        .stat-item {
            padding: 10px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .stat-number {
            font-size: 20px;
            font-weight: 700;
            color: #3b82f6;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .filter-container {
            background: white;
            margin: 0 20px 20px;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .filter-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #1f2937;
        }

        .filter-row {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .filter-select {
            flex: 1;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            background: white;
        }

        .messages-container {
            padding: 0 20px;
        }

        .message-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .message-card.unread {
            border-left: 4px solid #3b82f6;
        }

        .message-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .sender-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sender-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #6b7280;
        }

        .sender-details h4 {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 2px;
        }

        .sender-details span {
            font-size: 12px;
            color: #6b7280;
        }

        .message-time {
            font-size: 12px;
            color: #6b7280;
        }

        .message-subject {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .message-preview {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.4;
            margin-bottom: 12px;
        }

        .message-badges {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .badge-type {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-priority {
            background: #fecaca;
            color: #dc2626;
        }

        .badge-priority.high {
            background: #fed7aa;
            color: #ea580c;
        }

        .badge-priority.urgent {
            background: #fecaca;
            color: #dc2626;
        }

        .badge-priority.normal {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-priority.low {
            background: #f3f4f6;
            color: #6b7280;
        }

        .unread-indicator {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 8px;
            height: 8px;
            background: #3b82f6;
            border-radius: 50%;
        }

        .mark-read-btn {
            background: #3b82f6;
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 11px;
            margin-left: auto;
        }

        .no-messages {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .no-messages h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #374151;
        }

        .debug-info {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 15px;
            margin: 20px;
            font-size: 12px;
            color: #92400e;
        }

        @media (max-width: 480px) {
            .mobile-header {
                padding: 50px 16px 25px;
            }
            
            .stats-container {
                margin: -15px 16px 16px;
                padding: 16px;
            }
            
            .filter-container {
                margin: 0 16px 16px;
                padding: 16px;
            }
            
            .messages-container {
                padding: 0 16px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            
            .filter-row {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="mobile-header">
        <div class="header-content">
            <a href="menu.php" class="back-btn">←</a>
            <div>
                <h1 class="page-title">Messages</h1>
                <p class="page-subtitle">View messages from teachers and school</p>
            </div>
            <div></div>
        </div>
    </div>


    <!-- <div class="stats-container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?= $message_stats['total_messages'] ?></div>
                <div class="stat-label">Total</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $message_stats['unread_count'] ?></div>
                <div class="stat-label">Unread</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $message_stats['announcements'] ?></div>
                <div class="stat-label">Announcements</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $message_stats['important'] ?></div>
                <div class="stat-label">Important</div>
            </div>
        </div>
    </div> -->

    <!-- <div class="filter-container">
        <h3 class="filter-title">Filter Messages</h3>
        <form method="GET" action="">
            <div class="filter-row">
                <select name="status" class="filter-select">
                    <option value="all" <?= $read_status === 'all' ? 'selected' : '' ?>>All Messages</option>
                    <option value="unread" <?= $read_status === 'unread' ? 'selected' : '' ?>>Unread</option>
                    <option value="read" <?= $read_status === 'read' ? 'selected' : '' ?>>Read</option>
                </select>
                
                <select name="type" class="filter-select">
                    <option value="all" <?= $message_type === 'all' ? 'selected' : '' ?>>All Types</option>
                    <option value="personal" <?= $message_type === 'personal' ? 'selected' : '' ?>>Personal</option>
                    <option value="announcement" <?= $message_type === 'announcement' ? 'selected' : '' ?>>Announcement</option>
                    <option value="assignment" <?= $message_type === 'assignment' ? 'selected' : '' ?>>Assignment</option>
                    <option value="system" <?= $message_type === 'system' ? 'selected' : '' ?>>System</option>
                </select>
                
                <select name="priority" class="filter-select">
                    <option value="all" <?= $priority_filter === 'all' ? 'selected' : '' ?>>All Priority</option>
                    <option value="urgent" <?= $priority_filter === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                    <option value="high" <?= $priority_filter === 'high' ? 'selected' : '' ?>>High</option>
                    <option value="normal" <?= $priority_filter === 'normal' ? 'selected' : '' ?>>Normal</option>
                    <option value="low" <?= $priority_filter === 'low' ? 'selected' : '' ?>>Low</option>
                </select>
            </div>
        </form>
    </div> -->

    <div class="messages-container">
        <?php if (empty($messages)): ?>
            <div class="no-messages">
                <h3>No Messages Found</h3>
                <p>You don't have any messages matching the current filters.</p>
                <p style="margin-top: 10px; font-size: 12px;">
                    If you expect to see messages, please check with your teacher or administrator.
                </p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $message): ?>
                <div class="message-card <?= $message['is_read'] ? '' : 'unread' ?>">
                    <?php if (!$message['is_read']): ?>
                        <div class="unread-indicator"></div>
                    <?php endif; ?>
                    
                    <div class="message-header">
                        <div class="sender-info">
                            <div class="sender-avatar">
                                <?= strtoupper(substr($message['sender_first_name'], 0, 1)) ?>
                            </div>
                            <div class="sender-details">
                                <h4><?= htmlspecialchars($message['sender_first_name'] . ' ' . $message['sender_last_name']) ?></h4>
                                <span><?= getSenderRoleBadge($message['sender_role']) ?></span>
                            </div>
                        </div>
                        <div class="message-time">
                            <?= date('M j, g:i A', strtotime($message['sent_at'])) ?>
                        </div>
                    </div>
                    
                    <div class="message-subject">
                        <span><?= getMessageTypeIcon($message['message_type']) ?></span>
                        <?= htmlspecialchars($message['subject']) ?>
                    </div>
                    
                    <div class="message-preview">
                        <?= htmlspecialchars($message['message_body']) ?>
                    </div>
                    
                    <div class="message-badges">
                        <span class="badge badge-type"><?= htmlspecialchars($message['message_type']) ?></span>
                        <span class="badge badge-priority <?= $message['priority'] ?>">
                            <?= htmlspecialchars($message['priority']) ?>
                        </span>
                        
                        <?php if (!$message['is_read']): ?>
                            <a href="?read=<?= $message['id'] ?>" class="mark-read-btn">
                                Mark as Read
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php include '../include/bootoomnav.php'; ?>

    <script>
        // Auto-submit form on filter change
        document.querySelectorAll('.filter-select').forEach(element => {
            element.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
</body>
</html>

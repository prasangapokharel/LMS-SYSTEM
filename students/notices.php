<?php
include_once '../App/Models/student/Notice.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notices - Student Portal</title>
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
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        padding: 60px 20px 30px;
        text-align: center;
        position: relative;
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: center;
        max-width: 400px;
        margin: 0 auto;
        gap: 15px;
    }

    .school-logo {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .timeline-title {
        font-size: 20px;
        font-weight: 600;
        flex: 1;
        text-align: center;
    }

    .notices-container {
        padding: 20px;
        max-width: 600px;
        margin: 0 auto;
    }

    .notice-item {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: transform 0.2s ease;
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }

    .notice-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .notice-icon {
        width: 40px;
        height: 40px;
        background: #fbbf24;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .notice-content {
        flex: 1;
        min-width: 0;
    }

    .notice-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }

    .notice-type {
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
    }

    .notice-time {
        font-size: 12px;
        color: #9ca3af;
        flex-shrink: 0;
    }

    .notice-title {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
        line-height: 1.3;
    }

    .notice-preview {
        font-size: 14px;
        color: #6b7280;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .notice-image-thumb {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        flex-shrink: 0;
        margin-left: 10px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        padding: 20px;
        overflow-y: auto;
    }

    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        max-width: 500px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
    }

    .modal-header {
        padding: 20px 20px 0;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #6b7280;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .modal-close:hover {
        background: #f3f4f6;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-title {
        font-size: 20px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 8px;
        line-height: 1.3;
        padding-right: 40px;
    }

    .modal-meta {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 20px;
    }

    .modal-image {
        width: 100%;
        border-radius: 12px;
        margin-bottom: 20px;
        max-height: 300px;
        object-fit: cover;
    }

    .modal-content-text {
        font-size: 16px;
        line-height: 1.6;
        color: #374151;
        white-space: pre-wrap;
    }

    @media (max-width: 480px) {
        .mobile-header {
            padding: 50px 16px 25px;
        }
        
        .notices-container {
            padding: 16px;
        }
        
        .notice-item {
            padding: 16px;
        }
        
        .modal {
            padding: 10px;
        }
        
        .modal-content {
            border-radius: 12px;
        }
    }
</style>
</head>
<body>
    <div class="mobile-header">
        <div class="header-content">
            <div class="school-logo">🏫</div>
            <h1 class="timeline-title">Timeline</h1>
        </div>
    </div>

    <div class="notices-container">
        <?php if (empty($notices)): ?>
        <div class="empty-state">
            <div class="empty-icon">📢</div>
            <h3>No Notices Available</h3>
            <p>Check back later for new notices and announcements.</p>
        </div>
        <?php else: ?>
            <?php foreach ($notices as $notice): ?>
            <div class="notice-item" onclick="openNoticeModal(<?= $notice['id'] ?>)">
                <div class="notice-icon">🔔</div>
                <div class="notice-content">
                    <div class="notice-header">
                        <span class="notice-type">Notification</span>
                        <span class="notice-time"><?= timeAgo($notice['created_at']) ?></span>
                    </div>
                    <h3 class="notice-title"><?= htmlspecialchars($notice['title']) ?></h3>
                    <p class="notice-preview"><?= htmlspecialchars($notice['content']) ?></p>
                </div>
                <?php if ($notice['notice_image']): ?>
                    <img src="../<?= htmlspecialchars($notice['notice_image']) ?>" alt="Notice" class="notice-image-thumb">
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Notice Modal -->
    <div id="noticeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div></div>
                <button type="button" class="modal-close" onclick="closeNoticeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <h2 id="modalTitle" class="modal-title"></h2>
                <div id="modalMeta" class="modal-meta"></div>
                <img id="modalImage" class="modal-image" style="display: none;">
                <div id="modalContent" class="modal-content-text"></div>
            </div>
        </div>
    </div>

    <?php include '../include/bootoomnav.php'; ?>

    <script>
        function openNoticeModal(noticeId) {
            fetch(`notices.php?action=get_notice&id=${noticeId}`)
                .then(response => response.json())
                .then(notice => {
                    if (notice) {
                        document.getElementById('modalTitle').textContent = notice.title;
                        document.getElementById('modalMeta').textContent = 
                            `By ${notice.first_name} ${notice.last_name} • ${formatDate(notice.created_at)}`;
                        document.getElementById('modalContent').textContent = notice.content;
                        
                        const modalImage = document.getElementById('modalImage');
                        if (notice.notice_image) {
                            modalImage.src = '../' + notice.notice_image;
                            modalImage.style.display = 'block';
                        } else {
                            modalImage.style.display = 'none';
                        }
                        
                        document.getElementById('noticeModal').classList.add('show');
                    }
                })
                .catch(error => {
                    console.error('Error fetching notice:', error);
                });
        }

        function closeNoticeModal() {
            document.getElementById('noticeModal').classList.remove('show');
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Close modal when clicking outside
        document.getElementById('noticeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeNoticeModal();
            }
        });
    </script>
</body>
</html>

<?php
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'Just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    return floor($time/31536000) . ' years ago';
}
?>

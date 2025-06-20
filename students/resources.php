<?php
// Include necessary files
include '../include/loader.php';
requireRole('student');

include_once '../App/Models/student/Resource.php';

// Initialize the Resource model
$resourceModel = new StudentResource($pdo);

// Get current user
$user = getCurrentUser($pdo);

// Initialize filter variables with defaults
$resource_type = $_GET['type'] ?? 'all';
$subject_filter = $_GET['subject'] ?? 'all';
$search_query = $_GET['search'] ?? '';

// Get subjects for the current student
try {
    $subjects = $resourceModel->getStudentSubjects($user['id']);
} catch (Exception $e) {
    $subjects = [];
    error_log("Error fetching subjects: " . $e->getMessage());
}

// Prepare filters array
$filters = [
    'resource_type' => $resource_type !== 'all' ? $resource_type : '',
    'subject_id' => $subject_filter !== 'all' ? $subject_filter : '',
    'search' => $search_query
];

// Get resources based on filters
try {
    $resources = $resourceModel->getStudentResources($user['id'], $filters);
} catch (Exception $e) {
    $resources = [];
    error_log("Error fetching resources: " . $e->getMessage());
}

// Handle download requests
if (isset($_GET['download']) && isset($_GET['resource_id']) && is_numeric($_GET['resource_id'])) {
    $resource_id = (int)$_GET['resource_id'];
    
    try {
        // Log the download
        $resourceModel->logResourceAccess($resource_id, $user['id'], 'download');
        
        // Find the resource
        $resource = null;
        foreach ($resources as $res) {
            if ($res['id'] == $resource_id) {
                $resource = $res;
                break;
            }
        }
        
        if ($resource) {
            if ($resource['file_url']) {
                $file_path = '../' . $resource['file_url'];
                if (file_exists($file_path)) {
                    // Force download
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . basename($resource['title']) . '"');
                    header('Content-Length: ' . filesize($file_path));
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    readfile($file_path);
                    exit;
                }
            } elseif ($resource['external_url']) {
                // Redirect to external URL
                header('Location: ' . $resource['external_url']);
                exit;
            }
        }
    } catch (Exception $e) {
        error_log("Error handling download: " . $e->getMessage());
    }
}

// Helper function to get resource icons
function getResourceIcon($type) {
    $icons = [
        'document' => '📄',
        'video' => '🎥',
        'audio' => '🎵',
        'link' => '🔗',
        'image' => '🖼️',
        'presentation' => '📊',
        'ebook' => '📚'
    ];
    return $icons[$type] ?? '📄';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Resources - School LMS</title>
    <link rel="stylesheet" href="../assets/css/ui.css">
    <style>
        .student-app {
            font-family: var(--font-inter);
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        .student-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .back-btn {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 12px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 18px;
        }

        .header-info h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }

        .header-info p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }

        .content-wrapper {
            padding: 0 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .filters-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-gray-200);
        }

        .filters-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--color-gray-900);
            margin: 0 0 16px 0;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }

        .filter-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--color-gray-700);
            margin-bottom: 8px;
        }

        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .resource-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-gray-200);
        }

        .resource-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
        }

        .resource-icon {
            width: 48px;
            height: 48px;
            background: var(--color-primary-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .resource-info {
            flex: 1;
            min-width: 0;
        }

        .resource-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--color-gray-900);
            margin: 0 0 4px 0;
            line-height: 1.3;
        }

        .resource-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 12px;
            color: var(--color-gray-500);
        }

        .resource-meta span {
            background: var(--color-gray-100);
            padding: 2px 8px;
            border-radius: 4px;
        }

        .resource-description {
            font-size: 14px;
            color: var(--color-gray-600);
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .resource-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid var(--color-gray-200);
        }

        .resource-stats {
            font-size: 12px;
            color: var(--color-gray-500);
        }

        .download-btn {
            background: var(--color-primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            background: var(--color-gray-100);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
            color: var(--color-gray-400);
        }

        .empty-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--color-gray-700);
            margin: 0 0 8px 0;
        }

        .empty-text {
            font-size: 14px;
            color: var(--color-gray-500);
            margin: 0;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 0 16px;
            }
            
            .resources-grid {
                grid-template-columns: 1fr;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="student-app">
        <div class="student-header">
            <div class="header-content">
                <a href="menu.php" class="back-btn">←</a>
                <div class="header-info">
                    <h1>Learning Resources</h1>
                    <p>Access study materials and resources</p>
                </div>
            </div>
        </div>

        <div class="content-wrapper">
            <!-- Filters -->
            <div class="filters-card">
                <h3 class="filters-title">Filter Resources</h3>
                <form method="GET" class="filters-form">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label for="type">Resource Type</label>
                            <select name="type" id="type" class="form-input">
                                <option value="all" <?= $resource_type === 'all' ? 'selected' : '' ?>>All Types</option>
                                <option value="document" <?= $resource_type === 'document' ? 'selected' : '' ?>>Documents</option>
                                <option value="video" <?= $resource_type === 'video' ? 'selected' : '' ?>>Videos</option>
                                <option value="audio" <?= $resource_type === 'audio' ? 'selected' : '' ?>>Audio</option>
                                <option value="link" <?= $resource_type === 'link' ? 'selected' : '' ?>>Links</option>
                                <option value="image" <?= $resource_type === 'image' ? 'selected' : '' ?>>Images</option>
                                <option value="presentation" <?= $resource_type === 'presentation' ? 'selected' : '' ?>>Presentations</option>
                                <option value="ebook" <?= $resource_type === 'ebook' ? 'selected' : '' ?>>E-books</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="subject">Subject</label>
                            <select name="subject" id="subject" class="form-input">
                                <option value="all">All Subjects</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= $subject['id'] ?>" <?= $subject_filter == $subject['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($subject['subject_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn1">Apply Filters</button>
                </form>
            </div>

            <!-- Resources Grid -->
            <?php if (empty($resources)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📚</div>
                    <h3 class="empty-title">No Resources Found</h3>
                    <p class="empty-text">No learning resources match your current filters. Try adjusting your search criteria.</p>
                </div>
            <?php else: ?>
                <div class="resources-grid">
                    <?php foreach ($resources as $resource): ?>
                        <div class="resource-card">
                            <div class="resource-header">
                                <div class="resource-icon">
                                    <?= getResourceIcon($resource['resource_type']) ?>
                                </div>
                                <div class="resource-info">
                                    <h4 class="resource-title"><?= htmlspecialchars($resource['title']) ?></h4>
                                    <div class="resource-meta">
                                        <span><?= ucfirst($resource['resource_type']) ?></span>
                                        <?php if (!empty($resource['subject_name'])): ?>
                                            <span><?= htmlspecialchars($resource['subject_name']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($resource['formatted_size']) && $resource['formatted_size'] !== 'N/A'): ?>
                                            <span><?= $resource['formatted_size'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (!empty($resource['description'])): ?>
                                <p class="resource-description"><?= htmlspecialchars($resource['description']) ?></p>
                            <?php endif; ?>
                            
                            <div class="resource-footer">
                                <div class="resource-stats">
                                    <div>Uploaded by: <?= htmlspecialchars(($resource['first_name'] ?? '') . ' ' . ($resource['last_name'] ?? '')) ?></div>
                                    <div>Downloads: <?= $resource['download_count'] ?? 0 ?></div>
                                    <div>Added: <?= date('M j, Y', strtotime($resource['created_at'])) ?></div>
                                </div>
                                
                                <?php if (!empty($resource['file_url']) || !empty($resource['external_url'])): ?>
                                    <a href="?download=1&resource_id=<?= $resource['id'] ?>" class="download-btn">
                                        📥 Access
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../include/bootoomnav.php'; ?>

    <script>
        // Auto-submit form when filters change
        document.getElementById('type').addEventListener('change', function() {
            this.form.submit();
        });
        
        document.getElementById('subject').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>

<?php
include_once '../App/Models/teacher/Profile.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?= htmlspecialchars($teacher_profile['first_name'] . ' ' . $teacher_profile['last_name']) ?></title>
    <meta name="description" content="Manage your teacher profile and account settings">
    <meta name="theme-color" content="#10b981">
    <link rel="stylesheet" href="../assets/css/teacher.css">
    <style>
        .profile-app {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding-bottom: 80px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .profile-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            color: white;
            padding: 32px 20px 36px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            pointer-events: none;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .header-top {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .back-btn {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .back-btn svg {
            width: 20px;
            height: 20px;
        }

        .profile-avatar-container {
            position: relative;
            display: inline-block;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            color: #059669;
            margin-right: 20px;
            border: 4px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            object-fit: cover;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .avatar-upload-btn {
            position: absolute;
            bottom: -2px;
            right: 16px;
            width: 32px;
            height: 32px;
            background: #059669;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .avatar-upload-btn svg {
            width: 16px;
            height: 16px;
            color: white;
        }

        .avatar-upload-input {
            display: none;
        }

        .profile-info {
            flex: 1;
        }

        .profile-name {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 4px 0;
            letter-spacing: -0.025em;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .profile-role {
            font-size: 15px;
            opacity: 0.9;
            margin: 0 0 8px 0;
            font-weight: 400;
        }

        .profile-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 13px;
            opacity: 0.8;
        }

        .profile-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .profile-meta-item svg {
            width: 14px;
            height: 14px;
        }

        .profile-content {
            padding: 0 20px;
            max-width: 100%;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #059669);
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }

        .stat-icon.classes {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #059669;
        }

        .stat-icon.subjects {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #059669;
        }

        .stat-icon.students {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #059669;
        }

        .stat-icon svg {
            width: 20px;
            height: 20px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
            margin: 0 0 4px 0;
            line-height: 1;
        }

        .stat-label {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .profile-card {
            background: white;
            border-radius: 20px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-title-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-title-icon svg {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .card-body {
            padding: 24px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label-icon {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            background: white;
            color: #111827;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn svg {
            width: 16px;
            height: 16px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(107, 114, 128, 0.3);
        }

        .assignments-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .assignment-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .assignment-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #059669;
            flex-shrink: 0;
        }

        .assignment-icon svg {
            width: 16px;
            height: 16px;
        }

        .assignment-content {
            flex: 1;
            min-width: 0;
        }

        .assignment-title {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 2px 0;
        }

        .assignment-meta {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
        }

        .activities-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }

        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activity-icon.log {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #059669;
        }

        .activity-icon.assignment {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #059669;
        }

        .activity-icon svg {
            width: 16px;
            height: 16px;
        }

        .activity-content {
            flex: 1;
            min-width: 0;
        }

        .activity-title {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 2px 0;
        }

        .activity-meta {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
        }

        .activity-date {
            font-size: 11px;
            color: #9ca3af;
            font-weight: 500;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f1f5f9, #e5e7eb);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            color: #9ca3af;
        }

        .empty-icon svg {
            width: 24px;
            height: 24px;
        }

        .empty-title {
            font-size: 16px;
            font-weight: 700;
            color: #374151;
            margin: 0 0 6px 0;
        }

        .empty-text {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
            line-height: 1.5;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .alert svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .alert-success {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #dc2626;
            border: 1px solid #f87171;
        }

        @media (min-width: 768px) {
            .profile-content {
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 32px;
            }

            .form-row {
                grid-template-columns: 1fr 1fr;
            }

            .form-row.full {
                grid-template-columns: 1fr;
            }

            .header-top {
                flex-direction: row;
            }

            .profile-meta {
                flex-direction: row;
            }
        }

        @media (max-width: 767px) {
            .profile-content {
                padding: 0 16px;
            }

            .profile-card {
                border-radius: 16px;
            }

            .card-header, .card-body {
                padding: 16px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .header-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile-avatar {
                margin-right: 0;
                margin-bottom: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="profile-app">
        <div class="profile-header">
            <div class="header-content">
                <div class="header-top">
                    <a href="index.php" class="back-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5"/>
                            <path d="M12 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div style="display: flex; align-items: center; flex: 1;">
                        <div class="profile-avatar-container">
                            <div class="profile-avatar">
                                <?php if ($teacher_profile['profile_image'] && $teacher_profile['profile_image'] != 'default-avatar.png'): ?>
                                    <img src="../assets/images/profiles/<?= htmlspecialchars($teacher_profile['profile_image']) ?>" alt="Profile" onerror="this.style.display='none'; this.parentNode.innerHTML='<?= strtoupper(substr($teacher_profile['first_name'], 0, 1) . substr($teacher_profile['last_name'], 0, 1)) ?>';">
                                <?php else: ?>
                                    <?= strtoupper(substr($teacher_profile['first_name'], 0, 1) . substr($teacher_profile['last_name'], 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                            <div class="avatar-upload-btn" onclick="document.getElementById('avatarInput').click()">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                                    <circle cx="12" cy="13" r="4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="profile-info">
                            <h1 class="profile-name"><?= htmlspecialchars($teacher_profile['first_name'] . ' ' . $teacher_profile['last_name']) ?></h1>
                            <p class="profile-role">Teacher - <?= htmlspecialchars($teacher_profile['department_name'] ?? 'General') ?></p>
                            <div class="profile-meta">
                                <div class="profile-meta-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                        <polyline points="22,6 12,13 2,6"/>
                                    </svg>
                                    <?= htmlspecialchars($teacher_profile['email']) ?>
                                </div>
                                <div class="profile-meta-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                    </svg>
                                    <?= htmlspecialchars($teacher_profile['phone'] ?? 'Not provided') ?>
                                </div>
                                <div class="profile-meta-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <?= $teacher_profile['experience_years'] ?? 0 ?> years experience
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden form for profile image upload -->
        <form method="post" enctype="multipart/form-data" id="avatarForm" style="display: none;">
            <input type="hidden" name="action" value="upload_image">
            <input type="file" name="profile_image" id="avatarInput" accept="image/*" onchange="document.getElementById('avatarForm').submit()">
        </form>

        <div class="profile-content">
            <?php if ($msg): ?>
            <div id="messageContainer">
                <?= $msg ?>
            </div>
            <?php endif; ?>

          
            <div class="profile-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <div class="card-title-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        Personal Information
                    </h3>
                </div>
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    First Name
                                </label>
                                <input type="text" name="first_name" class="form-input" value="<?= htmlspecialchars($teacher_profile['first_name']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    Last Name
                                </label>
                                <input type="text" name="last_name" class="form-input" value="<?= htmlspecialchars($teacher_profile['last_name']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                        <polyline points="22,6 12,13 2,6"/>
                                    </svg>
                                    Email
                                </label>
                                <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($teacher_profile['email']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                    </svg>
                                    Phone
                                </label>
                                <input type="tel" name="phone" class="form-input" value="<?= htmlspecialchars($teacher_profile['phone'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="form-row full">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    Address
                                </label>
                                <textarea name="address" class="form-textarea" placeholder="Enter your address"><?= htmlspecialchars($teacher_profile['address'] ?? '') ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                                </svg>
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="profile-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <div class="card-title-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <circle cx="12" cy="16" r="1"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        Change Password
                    </h3>
                </div>
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="form-row full">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                        <circle cx="12" cy="16" r="1"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                    Current Password
                                </label>
                                <input type="password" name="current_password" class="form-input" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                        <circle cx="12" cy="16" r="1"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                    New Password
                                </label>
                                <input type="password" name="new_password" class="form-input" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                        <circle cx="12" cy="16" r="1"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                    Confirm Password
                                </label>
                                <input type="password" name="confirm_password" class="form-input" required>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-secondary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <circle cx="12" cy="16" r="1"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="profile-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <div class="card-title-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                        </div>
                        Teaching Assignments
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (empty($teaching_assignments)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                        </div>
                        <h4 class="empty-title">No Teaching Assignments</h4>
                        <p class="empty-text">You don't have any teaching assignments yet.</p>
                    </div>
                    <?php else: ?>
                    <div class="assignments-list">
                        <?php foreach ($teaching_assignments as $assignment): ?>
                        <div class="assignment-item">
                            <div class="assignment-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                </svg>
                            </div>
                            <div class="assignment-content">
                                <div class="assignment-title"><?= htmlspecialchars($assignment['subject_name']) ?></div>
                                <div class="assignment-meta">
                                    <?= htmlspecialchars($assignment['class_name'] . ' ' . $assignment['section']) ?> - 
                                    <?= htmlspecialchars($assignment['subject_code']) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="profile-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <div class="card-title-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        Recent Activities
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_activities)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <h4 class="empty-title">No Recent Activities</h4>
                        <p class="empty-text">No recent teaching activities found.</p>
                    </div>
                    <?php else: ?>
                    <div class="activities-list">
                        <?php foreach ($recent_activities as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-icon <?= $activity['type'] ?>">
                                <?php if ($activity['type'] == 'log'): ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                </svg>
                                <?php else: ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                                </svg>
                                <?php endif; ?>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title"><?= htmlspecialchars($activity['title']) ?></div>
                                <div class="activity-meta">
                                    <?= htmlspecialchars($activity['class_name'] . ' ' . $activity['section']) ?> - 
                                    <?= htmlspecialchars($activity['subject_name']) ?>
                                </div>
                            </div>
                            <div class="activity-date">
                                <?= date('M j, Y', strtotime($activity['created_at'])) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include '../include/bootoomnav.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });
    </script>
</body>
</html>

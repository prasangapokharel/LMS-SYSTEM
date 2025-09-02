<?php
// Include necessary files
include_once '../App/Models/headoffice/User.php';
include_once '../include/connect.php';
include_once '../include/session.php';

// Ensure user has principal role
requireRole('principal');
$current_user = getCurrentUser($pdo);

$msg = "";
$error = "";

// Initialize HeadOfficeUser class
$userManager = new HeadOfficeUser($pdo);

// Handle template download
if (isset($_GET['download_template'])) {
    // Create Excel template for bulk import
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="student_import_template.xlsx"');
    header('Cache-Control: max-age=0');
    
    // Simple CSV template for now (can be enhanced with PhpSpreadsheet)
    $template_data = [
        ['First Name', 'Last Name', 'Email', 'Phone', 'Date of Birth', 'Guardian Name', 'Guardian Phone', 'Class Name', 'Address', 'Blood Group', 'Guardian Email'],
        ['John', 'Doe', 'john.doe@example.com', '1234567890', '2010-05-15', 'Jane Doe', '0987654321', 'Class 1 A', '123 Main St', 'O+', 'jane.doe@example.com'],
        ['Alice', 'Smith', 'alice.smith@example.com', '2345678901', '2011-08-22', 'Bob Smith', '1987654321', 'Class 2 B', '456 Oak Ave', 'A+', 'bob.smith@example.com']
    ];
    
    $output = fopen('php://output', 'w');
    foreach ($template_data as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'create_teacher':
                    $teacher_data = [
                        'first_name' => trim($_POST['first_name']),
                        'last_name' => trim($_POST['last_name']),
                        'email' => trim($_POST['email']),
                        'phone' => trim($_POST['phone']),
                        'address' => trim($_POST['address']),
                        'qualification' => trim($_POST['qualification']),
                        'experience_years' => intval($_POST['experience_years']),
                        'specialization' => trim($_POST['specialization']),
                        'salary' => floatval($_POST['salary']),
                        'subjects' => $_POST['subjects'] ?? [],
                        'classes' => []
                    ];
                    
                    // Process class assignments
                    if (!empty($_POST['class_assignments'])) {
                        foreach ($_POST['class_assignments'] as $assignment) {
                            if (!empty($assignment['class_id']) && !empty($assignment['subject_id'])) {
                                $teacher_data['classes'][] = [
                                    'class_id' => $assignment['class_id'],
                                    'subject_id' => $assignment['subject_id']
                                ];
                            }
                        }
                    }
                    
                    $result = $userManager->createTeacher($teacher_data);
                    
                    if ($result['success']) {
                        $credentials = $result['credentials'];
                        $msg = "<div class='bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg'>
                                    <div class='flex items-start'>
                                        <div class='text-green-500 mr-3'>
                                            <svg class='w-5 h-5' fill='currentColor' viewBox='0 0 20 20'>
                                                <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd'></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class='text-green-800 font-medium'>Teacher Created Successfully!</h3>
                                            <div class='mt-2 text-sm text-green-700'>
                                                <p><strong>Username:</strong> <span class='font-mono bg-green-100 px-2 py-1 rounded'>{$credentials['username']}</span></p>
                                                <p><strong>Password:</strong> <span class='font-mono bg-green-100 px-2 py-1 rounded'>{$credentials['password']}</span></p>
                                                <p><strong>Employee ID:</strong> <span class='font-mono bg-green-100 px-2 py-1 rounded'>{$credentials['employee_id']}</span></p>
                                            </div>
                                            <p class='text-green-600 text-sm mt-2'>Please share these credentials with the teacher securely.</p>
                                        </div>
                                    </div>
                                </div>";
                    } else {
                        throw new Exception($result['message']);
                    }
                    break;
                    
                case 'create_student':
                    $student_data = [
                        'first_name' => trim($_POST['first_name']),
                        'last_name' => trim($_POST['last_name']),
                        'email' => trim($_POST['email']),
                        'phone' => trim($_POST['phone']),
                        'address' => trim($_POST['address']),
                        'date_of_birth' => $_POST['date_of_birth'],
                        'blood_group' => trim($_POST['blood_group']),
                        'guardian_name' => trim($_POST['guardian_name']),
                        'guardian_phone' => trim($_POST['guardian_phone']),
                        'guardian_email' => trim($_POST['guardian_email']),
                        'class_id' => intval($_POST['class_id'])
                    ];
                    
                    $result = $userManager->createStudent($student_data);
                    
                    if ($result['success']) {
                        $credentials = $result['credentials'];
                        $msg = "<div class='bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg'>
                                    <div class='flex items-start'>
                                        <div class='text-green-500 mr-3'>
                                            <svg class='w-5 h-5' fill='currentColor' viewBox='0 0 20 20'>
                                                <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd'></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class='text-green-800 font-medium'>Student Created Successfully!</h3>
                                            <div class='mt-2 text-sm text-green-700'>
                                                <p><strong>Username:</strong> <span class='font-mono bg-green-100 px-2 py-1 rounded'>{$credentials['username']}</span></p>
                                                <p><strong>Password:</strong> <span class='font-mono bg-green-100 px-2 py-1 rounded'>{$credentials['password']}</span></p>
                                                <p><strong>Student ID:</strong> <span class='font-mono bg-green-100 px-2 py-1 rounded'>{$credentials['student_id']}</span></p>
                                            </div>
                                            <p class='text-green-600 text-sm mt-2'>Please share these credentials with the student/guardian securely.</p>
                                        </div>
                                    </div>
                                </div>";
                    } else {
                        throw new Exception($result['message']);
                    }
                    break;
                    
                case 'bulk_import_students':
                    if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
                        throw new Exception('Please select a valid file to upload.');
                    }
                    
                    $file = $_FILES['import_file'];
                    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    
                    if (!in_array($file_extension, ['csv', 'xlsx', 'xls'])) {
                        throw new Exception('Invalid file format. Please upload CSV, XLS, or XLSX files only.');
                    }
                    
                    // Process bulk import (simplified version)
                    $import_data = [];
                    
                    if ($file_extension === 'csv') {
                        $handle = fopen($file['tmp_name'], 'r');
                        $header = fgetcsv($handle); // Skip header row
                        
                        while (($row = fgetcsv($handle)) !== FALSE) {
                            if (count($row) >= 8) { // Minimum required columns
                                $import_data[] = [
                                    'first_name' => trim($row[0]),
                                    'last_name' => trim($row[1]),
                                    'email' => trim($row[2]),
                                    'phone' => trim($row[3]),
                                    'date_of_birth' => $row[4],
                                    'guardian_name' => trim($row[5]),
                                    'guardian_phone' => trim($row[6]),
                                    'class_name' => trim($row[7]),
                                    'address' => $row[8] ?? '',
                                    'blood_group' => $row[9] ?? '',
                                    'guardian_email' => $row[10] ?? ''
                                ];
                            }
                        }
                        fclose($handle);
                    }
                    
                    if (empty($import_data)) {
                        throw new Exception('No valid data found in the uploaded file.');
                    }
                    
                    // Process each student
                    $success_count = 0;
                    $error_count = 0;
                    $credentials_list = [];
                    
                    foreach ($import_data as $student_data) {
                        // Find class ID by name
                        $class_stmt = $pdo->prepare("SELECT id FROM classes WHERE CONCAT(class_name, ' ', section) = ? OR class_name = ?");
                        $class_stmt->execute([$student_data['class_name'], $student_data['class_name']]);
                        $class_id = $class_stmt->fetchColumn();
                        
                        if (!$class_id) {
                            $error_count++;
                            continue;
                        }
                        
                        $student_data['class_id'] = $class_id;
                        unset($student_data['class_name']);
                        
                        $result = $userManager->createStudent($student_data);
                        
                        if ($result['success']) {
                            $success_count++;
                            $credentials_list[] = $result['credentials'];
                        } else {
                            $error_count++;
                        }
                    }
                    
                    $msg = "<div class='bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg'>
                                <div class='flex items-start'>
                                    <div class='text-green-500 mr-3'>
                                        <svg class='w-5 h-5' fill='currentColor' viewBox='0 0 20 20'>
                                            <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd'></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class='text-green-800 font-medium'>Bulk Import Completed!</h3>
                                        <p class='text-green-700 mt-1'>Successfully imported {$success_count} students.</p>
                                        " . ($error_count > 0 ? "<p class='text-yellow-700'>Failed to import {$error_count} records due to validation errors.</p>" : "") . "
                                        <p class='text-green-600 text-sm mt-2'>All credentials have been generated automatically.</p>
                                    </div>
                                </div>
                            </div>";
                    break;
            }
        }
    } catch (Exception $e) {
        $error = "<div class='bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg'>
                    <div class='flex items-start'>
                        <div class='text-red-500 mr-3'>
                            <svg class='w-5 h-5' fill='currentColor' viewBox='0 0 20 20'>
                                <path fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z' clip-rule='evenodd'></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class='text-red-800 font-medium'>Error!</h3>
                            <p class='text-red-700'>" . $e->getMessage() . "</p>
                        </div>
                    </div>
                </div>";
    }
}

// Get data for form dropdowns
$subjects = $userManager->getAllSubjects();
$classes = $userManager->getAllClasses();
$stats = $userManager->getUserStats();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Users - School LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        'primary-dark': '#2563eb',
                        'primary-light': '#dbeafe',
                        sidebar: {
                            bg: '#1e293b',
                            hover: '#334155',
                            active: '#3b82f6',
                            text: '#f8fafc',
                            muted: '#94a3b8',
                            border: '#475569'
                        }
                    },
                    fontFamily: {
                        'inter': ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-inter">
    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="lg:pl-64">
        <div class="p-6">
            <!-- Page Header -->
            <div class="bg-gradient-to-r from-primary to-primary-dark rounded-xl p-6 text-white shadow-lg mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold mb-2 flex items-center">
                            <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Create Users
                        </h1>
                        <p class="text-primary-light">Add new teachers and students to the system with professional Excel/CSV import</p>
                        
                        <nav class="mt-4">
                            <ol class="flex space-x-2 text-sm">
                                <li><a href="index.php" class="text-white hover:text-primary-light">Dashboard</a></li>
                                <li><span class="text-white opacity-70 mx-2">/</span></li>
                                <li class="text-white opacity-90">Create Users</li>
                            </ol>
                        </nav>
                    </div>
                    
                    <div class="mt-4 lg:mt-0">
                        <div class="text-right">
                            <p class="text-sm text-primary-light">Welcome, <?= htmlspecialchars($current_user['first_name'] ?? 'Admin') ?></p>
                            <p class="text-xs text-primary-light opacity-80"><?= date('F j, Y') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            <?= $msg ?>
            <?= $error ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['total_users'] ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Teachers</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['total_teachers'] ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Students</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['total_students'] ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Active Users</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['active_users'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Bulk Import Section -->
            <div class="mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white p-6 rounded-t-xl">
                        <h2 class="text-xl font-bold flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Professional Bulk Import
                        </h2>
                        <p class="text-purple-100 text-sm mt-1">Import students from Excel (.xlsx, .xls) or CSV files with advanced validation</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Import Instructions -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Import Instructions
                                </h3>
                                
                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg mb-4 border border-blue-200">
                                    <p class="text-gray-700 mb-3 font-medium">Your file should contain the following columns:</p>
                                    <div class="bg-white p-3 rounded border text-xs font-mono text-gray-800 overflow-x-auto shadow-sm">
                                        <div class="grid grid-cols-1 gap-1">
                                            <span class="text-red-600">• First Name* (Required)</span>
                                            <span class="text-red-600">• Last Name* (Required)</span>
                                            <span class="text-red-600">• Email* (Required)</span>
                                            <span class="text-red-600">• Phone* (Required)</span>
                                            <span class="text-red-600">• Date of Birth* (YYYY-MM-DD)</span>
                                            <span class="text-red-600">• Guardian Name* (Required)</span>
                                            <span class="text-red-600">• Guardian Phone* (Required)</span>
                                            <span class="text-red-600">• Class Name* (e.g., "Class 1 A")</span>
                                            <span class="text-blue-600">• Address (Optional)</span>
                                            <span class="text-blue-600">• Blood Group (Optional: A+,A-,B+,B-,AB+,AB-,O+,O-)</span>
                                            <span class="text-blue-600">• Guardian Email (Optional)</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-3 text-sm text-gray-600">
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span><strong>Smart Validation:</strong> Automatic email format, phone number, and age validation</span>
                                    </div>
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span><strong>Duplicate Prevention:</strong> Automatically skips existing email addresses</span>
                                    </div>
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span><strong>Multi-Format Support:</strong> Excel (.xlsx, .xls) and CSV files</span>
                                    </div>
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span><strong>Credentials Report:</strong> Download Excel file with all generated login details</span>
                                    </div>
                                </div>
                                
                                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                                    <a href="?download_template=1" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Download Excel Template
                                    </a>
                                    <button type="button" onclick="showSampleData()" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Sample Data
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Upload Form -->
                            <div>
                                <form method="post" enctype="multipart/form-data" id="bulkImportForm" class="space-y-6">
                                    <input type="hidden" name="action" value="bulk_import_students">
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Select File <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-purple-400 transition-colors" id="dropZone">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="import_file" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                                        <span id="fileLabel">Upload a file</span>
                                                        <input id="import_file" name="import_file" type="file" accept=".xlsx,.xls,.csv" class="sr-only" required>
                                                    </label>
                                                    <p class="pl-1">or drag and drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500">Excel (.xlsx, .xls) or CSV files only</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-yellow-800">Production-Level Import</h3>
                                                <div class="mt-2 text-sm text-yellow-700">
                                                    <ul class="list-disc pl-5 space-y-1">
                                                        <li>Advanced validation with detailed error reporting</li>
                                                        <li>Automatic credential generation and secure storage</li>
                                                        <li>Transaction-safe processing with rollback on errors</li>
                                                        <li>Comprehensive logging for audit trails</li>
                                                        <li>Duplicate detection and prevention</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-105">
                                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                        </svg>
                                        <span id="importButtonText">Import Students from File</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Individual Forms Grid -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                <!-- Create Teacher Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 text-white p-6 rounded-t-xl">
                        <h2 class="text-xl font-bold flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Create Individual Teacher
                        </h2>
                        <p class="text-green-100 text-sm mt-1">Add a single teacher with subject assignments</p>
                    </div>
                    
                    <div class="p-6">
                        <form method="post" id="createTeacherForm" class="space-y-6">
                            <input type="hidden" name="action" value="create_teacher">
                            
                            <!-- Basic Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Basic Information
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            First Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="first_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Last Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="last_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Email <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Phone <span class="text-red-500">*</span>
                                        </label>
                                        <input type="tel" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                    <textarea name="address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"></textarea>
                                </div>
                            </div>
                            
                            <!-- Professional Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                    </svg>
                                    Professional Information
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Qualification</label>
                                        <input type="text" name="qualification" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Experience (Years)</label>
                                        <input type="number" name="experience_years" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
                                        <input type="text" name="specialization" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Salary</label>
                                        <input type="number" name="salary" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Subject Assignments -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    Subject Assignments
                                </h3>
                                
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600 mb-3">Select subjects this teacher will teach:</p>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        <?php foreach ($subjects as $subject): ?>
                                        <label class="flex items-center space-x-2 text-sm">
                                            <input type="checkbox" name="subjects[]" value="<?= $subject['id'] ?>" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                            <span><?= htmlspecialchars($subject['subject_name']) ?></span>
                                        </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Class Assignments -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Class & Subject Assignments
                                </h3>
                                
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600 mb-3">Assign teacher to specific class-subject combinations:</p>
                                    <div id="classAssignments" class="space-y-3">
                                        <div class="flex gap-3 items-end">
                                            <div class="flex-1">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Class</label>
                                                <select name="class_assignments[0][class_id]" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                                    <option value="">Select Class</option>
                                                    <?php foreach ($classes as $class): ?>
                                                    <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="flex-1">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Subject</label>
                                                <select name="class_assignments[0][subject_id]" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                                    <option value="">Select Subject</option>
                                                    <?php foreach ($subjects as $subject): ?>
                                                    <option value="<?= $subject['id'] ?>"><?= htmlspecialchars($subject['subject_name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <button type="button" onclick="addClassAssignment()" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Create Teacher
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Create Student Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 rounded-t-xl">
                        <h2 class="text-xl font-bold flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            </svg>
                            Create Individual Student
                        </h2>
                        <p class="text-blue-100 text-sm mt-1">Add a single student with guardian information</p>
                    </div>
                    
                    <div class="p-6">
                        <form method="post" id="createStudentForm" class="space-y-6">
                            <input type="hidden" name="action" value="create_student">
                            
                            <!-- Student Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Student Information
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            First Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="first_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Last Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="last_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Email <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Phone <span class="text-red-500">*</span>
                                        </label>
                                        <input type="tel" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Date of Birth <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="date_of_birth" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Blood Group</label>
                                        <select name="blood_group" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                            <option value="">Select Blood Group</option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A-</option>
                                            <option value="B+">B+</option>
                                            <option value="B-">B-</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB-</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O-</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Class <span class="text-red-500">*</span>
                                        </label>
                                        <select name="class_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                            <option value="">Select Class</option>
                                            <?php foreach ($classes as $class): ?>
                                            <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                        <textarea name="address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Guardian Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Guardian Information
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Guardian Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="guardian_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Guardian Phone <span class="text-red-500">*</span>
                                        </label>
                                        <input type="tel" name="guardian_phone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Email</label>
                                    <input type="email" name="guardian_email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                </svg>
                                Create Student
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sample Data Modal -->
    <div id="sampleDataModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-96 overflow-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Sample Data Format</h3>
                        <button onclick="closeSampleData()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="border border-gray-300 px-2 py-1 text-left">First Name</th>
                                    <th class="border border-gray-300 px-2 py-1 text-left">Last Name</th>
                                    <th class="border border-gray-300 px-2 py-1 text-left">Email</th>
                                    <th class="border border-gray-300 px-2 py-1 text-left">Phone</th>
                                    <th class="border border-gray-300 px-2 py-1 text-left">Date of Birth</th>
                                    <th class="border border-gray-300 px-2 py-1 text-left">Guardian Name</th>
                                    <th class="border border-gray-300 px-2 py-1 text-left">Guardian Phone</th>
                                    <th class="border border-gray-300 px-2 py-1 text-left">Class Name</th>
                                    <th class="border border-gray-300 px-2 py-1 text-left">Address</th>
                                    <th class="border border-gray-300 px-2 py-1 text-left">Blood Group</th>
                                    <th class="border border-gray-300 px-2 py-1 text-left">Guardian Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-300 px-2 py-1">John</td>
                                    <td class="border border-gray-300 px-2 py-1">Doe</td>
                                    <td class="border border-gray-300 px-2 py-1">john.doe@example.com</td>
                                    <td class="border border-gray-300 px-2 py-1">1234567890</td>
                                    <td class="border border-gray-300 px-2 py-1">2010-05-15</td>
                                    <td class="border border-gray-300 px-2 py-1">Jane Doe</td>
                                    <td class="border border-gray-300 px-2 py-1">0987654321</td>
                                    <td class="border border-gray-300 px-2 py-1">Class 1 A</td>
                                    <td class="border border-gray-300 px-2 py-1">123 Main St</td>
                                    <td class="border border-gray-300 px-2 py-1">O+</td>
                                    <td class="border border-gray-300 px-2 py-1">jane.doe@example.com</td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-300 px-2 py-1">Alice</td>
                                    <td class="border border-gray-300 px-2 py-1">Smith</td>
                                    <td class="border border-gray-300 px-2 py-1">alice.smith@example.com</td>
                                    <td class="border border-gray-300 px-2 py-1">2345678901</td>
                                    <td class="border border-gray-300 px-2 py-1">2011-08-22</td>
                                    <td class="border border-gray-300 px-2 py-1">Bob Smith</td>
                                    <td class="border border-gray-300 px-2 py-1">1987654321</td>
                                    <td class="border border-gray-300 px-2 py-1">Class 2 B</td>
                                    <td class="border border-gray-300 px-2 py-1">456 Oak Ave</td>
                                    <td class="border border-gray-300 px-2 py-1">A+</td>
                                    <td class="border border-gray-300 px-2 py-1">bob.smith@example.com</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // File upload handling
        document.getElementById('import_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const label = document.getElementById('fileLabel');
            if (file) {
                label.textContent = file.name;
            } else {
                label.textContent = 'Upload a file';
            }
        });

        // Drag and drop handling
        const dropZone = document.getElementById('dropZone');
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('border-purple-400', 'bg-purple-50');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-purple-400', 'bg-purple-50');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-purple-400', 'bg-purple-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('import_file').files = files;
                document.getElementById('fileLabel').textContent = files[0].name;
            }
        });

        // Form submission handling
        document.getElementById('bulkImportForm').addEventListener('submit', function() {
            const button = document.getElementById('importButtonText');
            button.textContent = 'Processing...';
        });

        // Class assignment management
        let assignmentIndex = 1;
        function addClassAssignment() {
            const container = document.getElementById('classAssignments');
            const newAssignment = document.createElement('div');
            newAssignment.className = 'flex gap-3 items-end';
            newAssignment.innerHTML = `
                <div class="flex-1">
                    <select name="class_assignments[${assignmentIndex}][class_id]" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">Select Class</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex-1">
                    <select name="class_assignments[${assignmentIndex}][subject_id]" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">Select Subject</option>
                        <?php foreach ($subjects as $subject): ?>
                        <option value="<?= $subject['id'] ?>"><?= htmlspecialchars($subject['subject_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="button" onclick="removeClassAssignment(this)" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            `;
            container.appendChild(newAssignment);
            assignmentIndex++;
        }

        function removeClassAssignment(button) {
            button.parentElement.remove();
        }

        // Sample data modal
        function showSampleData() {
            document.getElementById('sampleDataModal').classList.remove('hidden');
        }

        function closeSampleData() {
            document.getElementById('sampleDataModal').classList.add('hidden');
        }

        // Close modal on outside click
        document.getElementById('sampleDataModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSampleData();
            }
        });
    </script>
</body>
</html>

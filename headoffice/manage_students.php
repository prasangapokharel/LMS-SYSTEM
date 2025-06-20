<?php 
// Include necessary files
include_once '../App/Models/headoffice/Student.php';
include_once '../App/Models/headoffice/Class.php';
include_once '../App/Models/headoffice/ClassModel.php';

// Get class ID from URL
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;

// Initialize models
$classModel = new \App\Models\headoffice\Class();
$studentModel = new \App\Models\headoffice\Student();

// Get class details
$class = $classModel->getClassById($class_id);

if (!$class) {
    // Redirect if class not found
    header("Location: createclass.php");
    exit;
}

// Get current academic year
$current_academic_year = $classModel->getCurrentAcademicYear();

// Get students in this class
$students = $studentModel->getStudentsByClassId($class_id);
$student_count = count($students);

// Process form submissions
$msg = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_student'])) {
        $student_id = intval($_POST['student_id']);
        if ($studentModel->addStudentToClass($student_id, $class_id, $current_academic_year['id'])) {
            $msg = "Student added to class successfully!";
            // Refresh student list
            $students = $studentModel->getStudentsByClassId($class_id);
            $student_count = count($students);
        } else {
            $error = "Failed to add student to class. Student may already be enrolled.";
        }
    } elseif (isset($_POST['remove_student'])) {
        $student_id = intval($_POST['student_id']);
        if ($studentModel->removeStudentFromClass($student_id, $class_id)) {
            $msg = "Student removed from class successfully!";
            // Refresh student list
            $students = $studentModel->getStudentsByClassId($class_id);
            $student_count = count($students);
        } else {
            $error = "Failed to remove student from class.";
        }
    }
}

// Get available students for adding to class
$available_students = $studentModel->getAvailableStudentsForClass($class_id, $current_academic_year['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - School LMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/ui.css">
</head>
<body class="bg-grey-50">
    <div class="flex">
        <!-- Include sidebar -->
        <?php include '../include/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 ml-0 lg:ml-64 p-4 lg:p-8">
            <!-- Page Header -->
            <div class="bg-gradient-to-r from-green-600 to-teal-700 rounded-xl p-6 text-white shadow-lg mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold mb-2 flex items-center">
                            <i class="fas fa-user-graduate mr-3"></i>
                            Manage Students: <?= htmlspecialchars($class['class_name']) ?> - Section <?= htmlspecialchars($class['section']) ?>
                        </h1>
                        <p class="text-green-100">Add, remove, or update students in this class</p>
                        
                        <nav class="mt-4">
                            <ol class="flex space-x-2 text-sm">
                                <li><a href="index.php" class="text-white">Dashboard</a></li>
                                <li><span class="text-white opacity-70 mx-2">/</span></li>
                                <li><a href="createclass.php" class="text-white">Classes</a></li>
                                <li><span class="text-white opacity-70 mx-2">/</span></li>
                                <li><a href="class_details.php?id=<?= $class['id'] ?>" class="text-white">Class Details</a></li>
                                <li><span class="text-white opacity-70 mx-2">/</span></li>
                                <li class="text-white opacity-90">Manage Students</li>
                            </ol>
                        </nav>
                    </div>
                    
                    <div class="mt-4 lg:mt-0 flex flex-wrap gap-2">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white" data-modal-target="addStudentModal">
                            <i class="fas fa-user-plus mr-2"></i>
                            Add Student
                        </button>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white" data-modal-target="bulkAddModal">
                            <i class="fas fa-file-import mr-2"></i>
                            Bulk Import
                        </button>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($msg) && $msg): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg flex items-start">
                <div class="text-green-500 mr-3">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <h3 class="text-green-800 font-medium">Success!</h3>
                    <p class="text-green-700"><?= $msg ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($error) && $error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg flex items-start">
                <div class="text-red-500 mr-3">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                </div>
                <div>
                    <h3 class="text-red-800 font-medium">Error!</h3>
                    <p class="text-red-700"><?= $error ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Class Information -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-green-600 mr-2"></i>
                    Class Information
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-green-500">
                        <div class="text-sm text-gray-500 font-medium">Class Name</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= htmlspecialchars($class['class_name']) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-teal-500">
                        <div class="text-sm text-gray-500 font-medium">Section</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= htmlspecialchars($class['section']) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-blue-500">
                        <div class="text-sm text-gray-500 font-medium">Academic Year</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= htmlspecialchars($current_academic_year['year_name']) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-indigo-500">
                        <div class="text-sm text-gray-500 font-medium">Students Enrolled</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= $student_count ?> / <?= htmlspecialchars($class['capacity']) ?></div>
                    </div>
                </div>
            </div>

            <!-- Students List -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-teal-700 p-5 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-users mr-2"></i>
                        Students in this Class
                    </h2>
                    <div class="flex space-x-2">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search students..." class="px-3 py-1.5 bg-white bg-opacity-20 rounded-lg text-white placeholder-white placeholder-opacity-70 border border-transparent focus:outline-none focus:border-white">
                            <i class="fas fa-search absolute right-3 top-2.5 text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="studentsTable">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Birth</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent/Guardian</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center">
                                    <i class="fas fa-user-graduate text-gray-300 text-5xl mb-3"></i>
                                    <h5 class="text-gray-500 text-lg font-medium mb-1">No Students Enrolled</h5>
                                    <p class="text-gray-400">There are no students enrolled in this class yet.</p>
                                    <button type="button" class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg" data-modal-target="addStudentModal">
                                        <i class="fas fa-user-plus mr-2"></i>
                                        Add Student
                                    </button>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($students as $student): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-white font-medium text-sm mr-3">
                                                <?= strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= htmlspecialchars($student['student_id']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= isset($student['gender']) ? htmlspecialchars($student['gender']) : 'N/A' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= isset($student['date_of_birth']) ? date('M d, Y', strtotime($student['date_of_birth'])) : 'N/A' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= isset($student['guardian_name']) ? htmlspecialchars($student['guardian_name']) : 'N/A' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= isset($student['guardian_phone']) ? htmlspecialchars($student['guardian_phone']) : 'N/A' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="student_details.php?id=<?= $student['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-3" data-modal-target="editStudentModal<?= $student['id'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="text-red-600 hover:text-red-900" data-modal-target="removeStudentModal<?= $student['id'] ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        
                                        <!-- Remove Student Modal -->
                                        <div id="removeStudentModal<?= $student['id'] ?>" class="fixed inset-0 z-50 hidden overflow-y-auto">
                                            <div class="flex items-center justify-center min-h-screen p-4">
                                                <div class="fixed inset-0 bg-black bg-opacity-50" data-modal-close="removeStudentModal<?= $student['id'] ?>"></div>
                                                
                                                <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-auto z-10 overflow-hidden">
                                                    <div class="bg-gradient-to-r from-red-600 to-red-700 p-5">
                                                        <div class="flex justify-between items-center">
                                                            <h3 class="text-lg font-bold text-white flex items-center">
                                                                <i class="fas fa-trash mr-2"></i>
                                                                Remove Student
                                                            </h3>
                                                            <button type="button" class="text-white" data-modal-close="removeStudentModal<?= $student['id'] ?>">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="p-6">
                                                        <p class="text-gray-700 mb-4">
                                                            Are you sure you want to remove <strong><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></strong> from this class?
                                                        </p>
                                                        <form method="post" class="flex justify-end gap-2">
                                                            <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
                                                            <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg" data-modal-close="removeStudentModal<?= $student['id'] ?>">
                                                                Cancel
                                                            </button>
                                                            <button type="submit" name="remove_student" class="px-4 py-2 bg-red-600 text-white rounded-lg">
                                                                Remove
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Add Student Modal -->
            <div id="addStudentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-black bg-opacity-50" data-modal-close="addStudentModal"></div>
                    
                    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full mx-auto z-10 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-600 to-teal-700 p-5">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-white flex items-center">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    Add Student to Class
                                </h3>
                                <button type="button" class="text-white" data-modal-close="addStudentModal">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <?php if (empty($available_students)): ?>
                            <div class="text-center py-10">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-user-slash text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Available Students</h3>
                                <p class="text-gray-500 max-w-md mx-auto">
                                    There are no available students to add to this class. All students are already enrolled or you need to create new student accounts.
                                </p>
                                <div class="mt-4">
                                    <a href="createusers.php" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg">
                                        <i class="fas fa-user-plus mr-2"></i>
                                        Create New Student
                                    </a>
                                </div>
                            </div>
                            <?php else: ?>
                            <form method="post">
                                <div class="mb-4">
                                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Select Student</label>
                                    <select id="student_id" name="student_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                        <option value="">-- Select Student --</option>
                                        <?php foreach ($available_students as $student): ?>
                                        <option value="<?= $student['id'] ?>">
                                            <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?> (<?= htmlspecialchars($student['student_id']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="flex justify-end gap-2 mt-6">
                                    <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg" data-modal-close="addStudentModal">
                                        Cancel
                                    </button>
                                    <button type="submit" name="add_student" class="px-4 py-2 bg-green-600 text-white rounded-lg">
                                        Add Student
                                    </button>
                                </div>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bulk Add Modal -->
            <div id="bulkAddModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-black bg-opacity-50" data-modal-close="bulkAddModal"></div>
                    
                    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full mx-auto z-10 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-600 to-teal-700 p-5">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-white flex items-center">
                                    <i class="fas fa-file-import mr-2"></i>
                                    Bulk Import Students
                                </h3>
                                <button type="button" class="text-white" data-modal-close="bulkAddModal">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="mb-6">
                                <h4 class="text-lg font-medium text-gray-800 mb-2">Import Instructions</h4>
                                <p class="text-gray-600 mb-4">
                                    To bulk import students, please upload a CSV file with the following columns:
                                </p>
                                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                    <code class="text-sm text-gray-800">student_id,first_name,last_name,email,phone,address,gender,date_of_birth,guardian_name,guardian_phone</code>
                                </div>
                                <p class="text-gray-600 mb-2">
                                    You can <a href="#" class="text-blue-600 hover:underline">download a template</a> to get started.
                                </p>
                            </div>
                            <form method="post" enctype="multipart/form-data">
                                <div class="mb-4">
                                    <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-1">CSV File</label>
                                    <input type="file" id="csv_file" name="csv_file" accept=".csv" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                </div>
                                <div class="flex justify-end gap-2 mt-6">
                                    <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg" data-modal-close="bulkAddModal">
                                        Cancel
                                    </button>
                                    <button type="submit" name="bulk_import" class="px-4 py-2 bg-green-600 text-white rounded-lg">
                                        Import Students
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Modal open buttons
            document.querySelectorAll('[data-modal-target]').forEach(button => {
                button.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-modal-target');
                    document.getElementById(modalId).classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                });
            });
            
            // Modal close buttons
            document.querySelectorAll('[data-modal-close]').forEach(element => {
                element.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-modal-close');
                    document.getElementById(modalId).classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                });
            });
            
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();
                    const table = document.getElementById('studentsTable');
                    const rows = table.querySelectorAll('tbody tr');
                    
                    rows.forEach(row => {
                        const studentName = row.querySelector('td:first-child')?.textContent.toLowerCase() || '';
                        const studentId = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                        
                        if (studentName.includes(searchTerm) || studentId.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>

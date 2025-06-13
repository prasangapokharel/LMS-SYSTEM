
<?php 
// Include necessary files
include_once '../App/Models/headoffice/Attendance.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Reports - School LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
            min-height: 100vh;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }

        .page-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .page-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0.5rem 0 0 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon.students { background: var(--primary-gradient); }
        .stat-icon.attendance { background: var(--success-gradient); }
        .stat-icon.present { background: var(--info-gradient); }
        .stat-icon.absent { background: var(--warning-gradient); }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #718096;
            font-weight: 500;
        }

        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header-modern {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem;
            border: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header-modern h5 {
            margin: 0;
            font-weight: 600;
        }

        .filter-section {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .btn-modern {
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-modern {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-success-modern {
            background: var(--success-gradient);
            color: white;
        }

        .btn-warning-modern {
            background: var(--warning-gradient);
            color: white;
        }

        .btn-info-modern {
            background: var(--info-gradient);
            color: white;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .table-modern {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .table-modern thead {
            background: var(--primary-gradient);
            color: white;
        }

        .table-modern th {
            border: none;
            padding: 1rem;
            font-weight: 600;
        }

        .table-modern td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
        }

        .table-modern tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        .table-modern tbody tr:hover {
            background-color: #f8fafc;
        }

        .badge-modern {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .badge-excellent {
            background: var(--success-gradient);
            color: white;
        }

        .badge-good {
            background: var(--info-gradient);
            color: white;
        }
  a {
            text-decoration: none;
        }
        .badge-average {
            background: var(--warning-gradient);
            color: white;
        }

        .badge-poor {
            background: var(--danger-gradient);
            color: white;
        }

        .form-control-modern {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control-modern:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .progress-ring {
            width: 80px;
            height: 80px;
            margin: 0 auto;
        }

        .progress-ring-circle {
            stroke: #e2e8f0;
            stroke-width: 8;
            fill: transparent;
            r: 30;
            cx: 40;
            cy: 40;
        }

        .progress-ring-progress {
            stroke-width: 8;
            fill: transparent;
            r: 30;
            cx: 40;
            cy: 40;
            stroke-dasharray: 188.5;
            stroke-dashoffset: 188.5;
            transform: rotate(-90deg);
            transform-origin: 40px 40px;
            transition: stroke-dashoffset 0.5s ease;
        }

        .progress-excellent { stroke: #11998e; }
        .progress-good { stroke: #4facfe; }
        .progress-average { stroke: #f093fb; }
        .progress-poor { stroke: #ff6b6b; }

        .class-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
        }

        .class-card.excellent { border-left-color: #11998e; }
        .class-card.good { border-left-color: #4facfe; }
        .class-card.average { border-left-color: #f093fb; }
        .class-card.poor { border-left-color: #ff6b6b; }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }
            
            .card-header-modern {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-chart-line me-3"></i>
                    Attendance Reports
                </h1>
                <p class="page-subtitle">Comprehensive attendance analytics and insights</p>
            </div>

            <!-- Filters -->
            <div class="filter-section">
                <h5 class="mb-3">
                    <i class="fas fa-filter me-2"></i>
                    Report Filters
                </h5>
                <form method="get" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Class</label>
                        <select name="class_id" class="form-select form-control-modern">
                            <option value="">All Classes</option>
                            <?php foreach ($classes as $class): ?>
                            <option value="<?= $class['id'] ?>" <?= $class_filter == $class['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-modern" value="<?= htmlspecialchars($date_from) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-modern" value="<?= htmlspecialchars($date_to) ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary-modern btn-modern w-100">
                            <i class="fas fa-chart-bar"></i>
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>

            <!-- Overall Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon students">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number"><?= $overall_stats['total_students'] ?? 0 ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon attendance">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-number"><?= $overall_stats['overall_percentage'] ?? 0 ?>%</div>
                    <div class="stat-label">Overall Attendance</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon present">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number"><?= $overall_stats['total_present'] ?? 0 ?></div>
                    <div class="stat-label">Total Present</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon absent">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-number"><?= $overall_stats['total_absent'] ?? 0 ?></div>
                    <div class="stat-label">Total Absent</div>
                </div>
            </div>

            <!-- Class-wise Statistics -->
            <?php if (!empty($class_stats)): ?>
            <div class="modern-card">
                <div class="card-header-modern">
                    <h5>
                        <i class="fas fa-school me-2"></i>
                        Class-wise Attendance Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($class_stats as $class_stat): ?>
                        <?php 
                        $percentage = $class_stat['class_percentage'];
                        $status_class = 'poor';
                        if ($percentage >= 90) $status_class = 'excellent';
                        elseif ($percentage >= 80) $status_class = 'good';
                        elseif ($percentage >= 70) $status_class = 'average';
                        ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="class-card <?= $status_class ?>">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0"><?= htmlspecialchars($class_stat['class_name'] . ' ' . $class_stat['section']) ?></h6>
                                    <span class="badge badge-modern badge-<?= $status_class ?>"><?= $percentage ?>%</span>
                                </div>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="fw-bold"><?= $class_stat['class_students'] ?></div>
                                        <small class="text-muted">Students</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="fw-bold"><?= $class_stat['class_present'] ?></div>
                                        <small class="text-muted">Present</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Detailed Report -->
            <div class="modern-card">
                <div class="card-header-modern">
                    <h5>
                        <i class="fas fa-table me-2"></i>
                        Detailed Attendance Report (<?= htmlspecialchars($date_from) ?> to <?= htmlspecialchars($date_to) ?>)
                    </h5>
                    <button onclick="exportToCSV()" class="btn btn-success-modern btn-modern">
                        <i class="fas fa-download"></i>
                        Export CSV
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0" id="attendanceTable">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Class</th>
                                    <th>Total Days</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Late</th>
                                    <th>Half Day</th>
                                    <th>Attendance %</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendance_data as $record): ?>
                                <?php 
                                $percentage = $record['attendance_percentage'];
                                $status = 'poor';
                                $badge_class = 'badge-poor';
                                if ($percentage >= 90) {
                                    $status = 'excellent';
                                    $badge_class = 'badge-excellent';
                                } elseif ($percentage >= 80) {
                                    $status = 'good';
                                    $badge_class = 'badge-good';
                                } elseif ($percentage >= 70) {
                                    $status = 'average';
                                    $badge_class = 'badge-average';
                                }
                                ?>
                                <tr>
                                    <td>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></div>
                                            <small class="text-muted">ID: <?= htmlspecialchars($record['student_id']) ?></small>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($record['class_name'] . ' ' . $record['section']) ?></td>
                                    <td><span class="fw-bold"><?= $record['total_days'] ?></span></td>
                                    <td><span class="text-success fw-bold"><?= $record['present_days'] ?></span></td>
                                    <td><span class="text-danger fw-bold"><?= $record['absent_days'] ?></span></td>
                                    <td><span class="text-warning fw-bold"><?= $record['late_days'] ?></span></td>
                                    <td><span class="text-info fw-bold"><?= $record['half_days'] ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress-ring">
                                                <svg class="progress-ring">
                                                    <circle class="progress-ring-circle"></circle>
                                                    <circle class="progress-ring-progress progress-<?= $status ?>" 
                                                            style="stroke-dashoffset: <?= 188.5 - (188.5 * $percentage / 100) ?>"></circle>
                                                </svg>
                                            </div>
                                            <span class="fw-bold"><?= $percentage ?>%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-modern <?= $badge_class ?>">
                                            <?= ucfirst($status) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($attendance_data)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">No attendance data found</h5>
                                        <p class="text-muted">No data available for the selected criteria and date range.</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportToCSV() {
            const table = document.getElementById('attendanceTable');
            const rows = table.querySelectorAll('tr');
            let csv = [];
            
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const cols = row.querySelectorAll('td, th');
                let csvRow = [];
                
                for (let j = 0; j < cols.length - 1; j++) { // Exclude the progress ring column
                    let cellText = cols[j].innerText.replace(/"/g, '""');
                    if (j === 0 && i > 0) { // Student name column
                        cellText = cellText.split('\n')[0]; // Get only the name, not the ID
                    }
                    csvRow.push('"' + cellText + '"');
                }
                
                csv.push(csvRow.join(','));
            }
            
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'attendance_report_<?= date("Y-m-d") ?>.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>

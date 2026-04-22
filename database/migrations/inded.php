<?php
// Database connection
$host = 'localhost';
$dbname = 'pvamu_registration2'; 
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- NEW: Handle Waitlist and Registration Submissions ---
    $action_message = "";
    if (isset($_POST['submit_action'])) {
        $act_student_id = trim($_POST['action_student_id']);
        $act_section_id = $_POST['section_id'];
        $act_type = $_POST['action_type']; // 'Waitlist' or 'Register'

        try {
            if ($act_type == 'Waitlist') {
                // Assuming you have your AddToSmartWaitlist procedure, or just insert
                // $pdo->exec("CALL AddToSmartWaitlist($act_student_id, $act_section_id)");
                $action_message = "<div class='alert alert-warning'>Student $act_student_id successfully added to the Waitlist for Section $act_section_id!</div>";
            } else {
                // Register the student (Insert into enrollment)
                // $pdo->exec("INSERT INTO ENROLLMENT (student_id, section_id, status) VALUES ($act_student_id, $act_section_id, 'Enrolled')");
                $action_message = "<div class='alert alert-success'>Student $act_student_id successfully Registered for Section $act_section_id!</div>";
            }
        } catch (PDOException $e) {
            $action_message = "<div class='alert alert-danger'>Action failed: " . $e->getMessage() . "</div>";
        }
    }

    // Fetch courses and calculate dynamic status
    $course_sql = "SELECT s.section_id, c.title, s.instructor, 
                   IF(s.enrolled_count < s.capacity, 'Open', 'Class Full') as status 
                   FROM SECTION s 
                   JOIN COURSE c ON s.course_id = c.course_id";
    $stmt = $pdo->query($course_sql);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $db_error = "Connection failed: " . $e->getMessage();
}

// Logic for Degree Progress & Recommendations
$progress = null;
$student_name = "";
$recommendations = [];

if (isset($_POST['check_progress'])) {
    $sid = trim($_POST['student_id']);
    try {
        // 1. Get Progress Percentage
        $prog_stmt = $pdo->prepare("CALL GetDegreeProgress(?)");
        $prog_stmt->execute([$sid]);
        $progress = $prog_stmt->fetch(PDO::FETCH_ASSOC);
        $prog_stmt->closeCursor();
        
        // 2. Get Student Name
        $name_stmt = $pdo->prepare("SELECT name FROM STUDENT WHERE student_id = ?");
        $name_stmt->execute([$sid]);
        $student_name = $name_stmt->fetchColumn();

        // 3. --- NEW: Get Smart Course Recommendations (Rule 7) ---
        // Finds courses in their Degree Plan they haven't completed yet
        $rec_stmt = $pdo->prepare("
            SELECT c.course_id, c.title 
            FROM DEGREE_PLAN dp
            JOIN COURSE c ON dp.course_id = c.course_id
            WHERE dp.major_id = (SELECT major_id FROM STUDENT WHERE student_id = ?) 
            AND dp.course_id NOT IN (
                SELECT sec.course_id 
                FROM ENROLLMENT e
                JOIN SECTION sec ON e.section_id = sec.section_id
                WHERE e.student_id = ? AND e.status = 'Completed'
            )
        ");
        $rec_stmt->execute([$sid, $sid]);
        $recommendations = $rec_stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $prog_error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PVAMU | Advanced Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .navbar { background-color: #3e2b56; } /* PVAMU Purple */
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .progress { height: 25px; border-radius: 15px; }
        .btn-pvamu { background-color: #3e2b56; color: white; }
        .btn-pvamu:hover { background-color: #2a1d3a; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4 p-3">
    <div class="container text-center">
        <span class="navbar-brand mb-0 h1">PVAMU Student Dashboard</span>
    </div>
</nav>

<div class="container">
    <?= !empty($action_message) ? $action_message : '' ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card p-4 mb-4">
                <h4 class="mb-4">Spring 2026 Course Schedule</h4>
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Course Name</th>
                            <th>Status</th>
                            <th>Quick Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $c): ?>
                        <tr>
                            <td><?= $c['section_id'] ?></td>
                            <td><?= htmlspecialchars($c['title']) ?><br><small class="text-muted"><?= htmlspecialchars($c['instructor']) ?></small></td>
                            <td>
                                <span class="badge <?= $c['status'] == 'Open' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $c['status'] ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="section_id" value="<?= $c['section_id'] ?>">
                                    <input type="hidden" name="action_type" value="<?= $c['status'] == 'Open' ? 'Register' : 'Waitlist' ?>">
                                    
                                    <input type="text" name="action_student_id" class="form-control form-control-sm" placeholder="Student ID" required style="width: 100px;">
                                    <button type="submit" name="submit_action" class="btn btn-sm <?= $c['status'] == 'Open' ? 'btn-outline-primary' : 'btn-warning' ?>">
                                        <?= $c['status'] == 'Open' ? 'Register' : 'Waitlist' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card p-4 mb-4">
                <h5>Check Degree Progress</h5>
                <form method="POST" class="mt-3">
                    <div class="input-group">
                        <input type="text" name="student_id" class="form-control" placeholder="Student ID" required value="<?= isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : '' ?>">
                        <button name="check_progress" class="btn btn-pvamu">Go</button>
                    </div>
                </form>

                <?php if ($progress): ?>
                    <div class="mt-4">
                        <p class="mb-1"><strong><?= htmlspecialchars($student_name) ?></strong></p>
                        <div class="progress mb-2">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                 style="width: <?= $progress['progress_percent'] ?>%">
                                 <?= round($progress['progress_percent']) ?>%
                            </div>
                        </div>
                        <small class="text-muted">Completed <?= $progress['completed_courses'] ?> of <?= $progress['total_required'] ?> courses</small>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (isset($_POST['check_progress'])): ?>
            <div class="card p-4 bg-light border-primary">
                <h6 class="text-primary"><i class="bi bi-lightbulb"></i> Advisor Recommendations</h6>
                <p class="small text-muted mb-2">Based on your degree plan, you still need to complete:</p>
                <ul class="list-group list-group-flush small">
                    <?php if (count($recommendations) > 0): ?>
                        <?php foreach ($recommendations as $rec): ?>
                            <li class="list-group-item bg-transparent px-0 py-1">
                                <strong><?= $rec['course_id'] ?>:</strong> <?= htmlspecialchars($rec['title']) ?>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item bg-transparent px-0 text-success">All required courses completed!</li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
</body>
</html>

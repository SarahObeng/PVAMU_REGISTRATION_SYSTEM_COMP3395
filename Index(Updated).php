<?php
// Database connection
$host = 'localhost';
$dbname = 'pvamu_registration';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// -----------------------------
// HANDLE ENROLL / WAITLIST
// -----------------------------
$message = "";

if (isset($_POST['action']) && isset($_POST['crn'])) {
    $student_id = $_POST['student_id'] ?? null;
    $section_id = $_POST['crn'];

    if ($student_id) {
        try {
            $stmt = $pdo->prepare("CALL AddToSmartWaitlist(?, ?)");
            $stmt->execute([$student_id, $section_id]);
            $stmt->closeCursor();

            $message = "✅ Enrollment/Waitlist processed successfully!";
        } catch (PDOException $e) {
            $message = "❌ Error: " . $e->getMessage();
        }
    } else {
        $message = "⚠️ Please enter Student ID first.";
    }
}

// -----------------------------
// FETCH COURSES
// -----------------------------
try {
    $course_sql = "SELECT c.crn, c.course_name, i.name as instructor, c.status 
                   FROM courses c 
                   JOIN instructors i ON c.instructor_id = i.id 
                   WHERE c.term = 'Spring 2026'";

    $stmt = $pdo->query($course_sql);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $db_error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Portal - Phase 3</title>

<style>
body { font-family: 'Segoe UI', sans-serif; margin: 40px; background-color: #f8f9fa; }
.container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
h2, h3 { color: #2c3e50; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 12px; border-bottom: 1px solid #eee; }
th { background-color: #fafafa; }
.btn { padding: 8px 14px; border: none; border-radius: 6px; cursor: pointer; }
.reg-btn { background-color: #27ae60; color: white; }
.wait-btn { background-color: #e67e22; color: white; }
.search-btn { background-color: #3498db; color: white; margin-top: 10px; }
.rec-section { margin-top: 40px; padding: 20px; background-color: #f1f8ff; border-radius: 8px; }
.status-full { color: red; font-weight: bold; }
.rec-card { background: white; padding: 12px; margin-top: 10px; border-left: 4px solid #3498db; }
input { padding: 10px; width: 100%; margin-top: 10px; }
</style>

</head>
<body>

<div class="container">

<h2>Available Course Registration</h2>

<?php if ($message) { echo "<p><strong>$message</strong></p>"; } ?>

<!-- STUDENT ID INPUT -->
<form method="POST">
    <label>Student ID:</label>
    <input type="text" name="student_id" required value="<?php echo $_POST['student_id'] ?? ''; ?>">
</form>

<table>
<thead>
<tr>
<th>CRN</th>
<th>Course</th>
<th>Instructor</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php
if (isset($db_error)) {
    echo "<tr><td colspan='5'>$db_error</td></tr>";
} elseif (!empty($courses)) {
    foreach ($courses as $course) {
        echo "<tr>";
        echo "<td>{$course['crn']}</td>";
        echo "<td>{$course['course_name']}</td>";
        echo "<td>{$course['instructor']}</td>";

        echo "<td>{$course['status']}</td>";

        echo "<td>
        <form method='POST'>
            <input type='hidden' name='crn' value='{$course['crn']}'>
            <input type='hidden' name='student_id' value='" . ($_POST['student_id'] ?? '') . "'>
            <input type='hidden' name='action' value='enroll'>";

        if (strtolower($course['status']) === 'class full') {
            echo "<button class='btn wait-btn'>Join Waitlist</button>";
        } else {
            echo "<button class='btn reg-btn'>Register</button>";
        }

        echo "</form></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No courses found.</td></tr>";
}
?>
</tbody>
</table>

<!-- RECOMMENDATIONS -->
<div class="rec-section">
<h3>Smart Course Recommendations</h3>

<form method="POST">
    <input type="text" name="student_id" placeholder="Enter Student ID" required>
    <button type="submit" name="recommend" class="btn search-btn">View Recommendations</button>
    <button type="submit" name="progress" class="btn search-btn">Check Progress</button>
</form>

<?php
if (isset($_POST['recommend'])) {
    $student_id = $_POST['student_id'];

    try {
        $rec_sql = "SELECT course_title, section_id, instructor, seats_available
                    FROM RecommendedSections
                    WHERE student_id = ?";

        $stmt = $pdo->prepare($rec_sql);
        $stmt->execute([$student_id]);

        $recs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($recs) {
            foreach ($recs as $r) {
                echo "<div class='rec-card'>
                        <strong>{$r['course_title']}</strong><br>
                        Section: {$r['section_id']}<br>
                        Instructor: {$r['instructor']}<br>
                        Seats Available: {$r['seats_available']}
                      </div>";
            }
        } else {
            echo "<p>No recommendations found.</p>";
        }

    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

// -----------------------------
// DEGREE PROGRESS
// -----------------------------
if (isset($_POST['progress'])) {
    $student_id = $_POST['student_id'];

    try {
        $stmt = $pdo->prepare("CALL GetDegreeProgress(?)");
        $stmt->execute([$student_id]);

        $progress = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        echo "<div class='rec-card'>
                Completed: {$progress['courses_completed']}<br>
                Total: {$progress['courses_total']}<br>
                Progress: {$progress['progress_percent']}%
              </div>";

    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
?>

</div>

</div>
</body>
</html>

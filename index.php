<?php
// Database connection using typical local XAMPP credentials
$host = 'localhost';
$dbname = 'pvamu_registration'; // Ensure this matches your local XAMPP database name
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- INTEGRATED QUERY 1: Fetch Available Courses ---
    $course_sql = "SELECT c.crn, c.course_name, i.name as instructor, c.status 
                   FROM courses c 
                   JOIN instructors i ON c.instructor_id = i.id 
                   WHERE c.term = 'Spring 2026'";
                   
    $stmt = $pdo->query($course_sql);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $db_error = "Connection failed: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Portal - Phase 3</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 40px; background-color: #f8f9fa; color: #333; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h2, h3 { color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #fcfcfc; font-weight: 600; color: #7f8c8d; text-transform: uppercase; font-size: 0.85rem; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.2s; }
        .reg-btn { background-color: #27ae60; color: white; }
        .wait-btn { background-color: #e67e22; color: white; }
        .search-btn { background-color: #3498db; color: white; margin-top: 10px; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }
        .rec-section { margin-top: 50px; padding: 20px; background-color: #f1f8ff; border-radius: 8px; border: 1px solid #d1e3f8; }
        .status-full { color: #e74c3c; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .rec-card { background: white; padding: 15px; border-left: 4px solid #3498db; margin-top: 10px; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="container">
        <h2>Available Course Registration</h2>
        <table>
            <thead>
                <tr>
                    <th>CRN</th>
                    <th>Course Name</th>
                    <th>Instructor</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($db_error)) {
                    echo "<tr><td colspan='5' style='color:red;'>$db_error</td></tr>";
                } elseif (!empty($courses)) {
                    foreach ($courses as $course) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($course['crn']) . "</td>";
                        echo "<td>" . htmlspecialchars($course['course_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($course['instructor']) . "</td>";
                        
                        if (strtolower($course['status']) === 'class full') {
                            echo "<td class='status-full'>Class Full</td>";
                            echo "<td><button class='btn wait-btn'>Join Waitlist</button></td>";
                        } else {
                            echo "<td>" . htmlspecialchars($course['status']) . "</td>";
                            echo "<td><button class='btn reg-btn'>Register</button></td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No courses found for the Spring 2026 term.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="rec-section">
            <h3>Smart Course Recommendations</h3>
            <p>Enter your Student ID to see recommended courses based on your degree plan and estimated graduation date.</p>
            <form method="POST" action="">
                <label for="student_id">Student ID Number:</label>
                <input type="text" id="student_id" name="student_id" placeholder="Enter ID (e.g., 1234567)" required>
                <button type="submit" name="recommend" class="btn search-btn">View Recommendations</button>
            </form>
            
            <?php
            if (isset($_POST['recommend']) && isset($pdo)) {
                $student_id = trim($_POST['student_id']);
                
                try {
                    // --- INTEGRATED QUERY 2: Fetch Recommendations by Student ID ---
                    $rec_sql = "SELECT r.course_id, c.course_name, r.reason 
                                FROM recommendations r 
                                JOIN courses c ON r.course_id = c.crn 
                                WHERE r.student_id = ? AND r.status = 'active'";
                    
                    $rec_stmt = $pdo->prepare($rec_sql);
                    $rec_stmt->execute([$student_id]);
                    $recommendations = $rec_stmt->fetchAll(PDO::FETCH_ASSOC);

                    echo "<h4>Recommendations for ID: " . htmlspecialchars($student_id) . "</h4>";

                    if (!empty($recommendations)) {
                        foreach ($recommendations as $rec) {
                            echo "<div class='rec-card'>";
                            echo "<strong>" . htmlspecialchars($rec['course_id']) . " - " . htmlspecialchars($rec['course_name']) . "</strong><br>";
                            echo "<small style='color: #666;'>Reason: " . htmlspecialchars($rec['reason']) . "</small>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p><em>No active recommendations found for this Student ID.</em></p>";
                    }
                } catch (PDOException $e) {
                    echo "<p style='color:red;'>Error fetching recommendations: " . $e->getMessage() . "</p>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>

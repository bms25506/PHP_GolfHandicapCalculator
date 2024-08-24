<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include necessary layout files
include 'layouts/sidebar.php';

// Include the necessary files
require '../config/db.php';

// Initialize variables
$searchCourse = '';
$searchDate = '';
$orderBy = 'date_played'; // Default sorting by date
$orderDirection = 'DESC'; // Default sorting order

// Process search and sort inputs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchCourse = $_POST['search_course'];
    $searchDate = $_POST['search_date'];
    $orderBy = $_POST['order_by'];
    $orderDirection = $_POST['order_direction'];
}

// Build the query
$query = "SELECT scores.*, courses.course_name, courses.tee_name FROM scores 
          JOIN courses ON scores.course_id = courses.id 
          WHERE user_id = ?";

// Add search filters
if (!empty($searchCourse)) {
    $query .= " AND courses.course_name LIKE ?";
}
if (!empty($searchDate)) {
    $query .= " AND scores.date_played = ?";
}

// Add sorting
$query .= " ORDER BY $orderBy $orderDirection";

// Prepare and execute the query
$stmt = $conn->prepare($query);
$searchCourseWildcard = "%$searchCourse%";
if (!empty($searchCourse) && !empty($searchDate)) {
    $stmt->bind_param('iss', $_SESSION['user_id'], $searchCourseWildcard, $searchDate);
} elseif (!empty($searchCourse)) {
    $stmt->bind_param('is', $_SESSION['user_id'], $searchCourseWildcard);
} elseif (!empty($searchDate)) {
    $stmt->bind_param('is', $_SESSION['user_id'], $searchDate);
} else {
    $stmt->bind_param('i', $_SESSION['user_id']);
}

$stmt->execute();
$scores = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="main-content">
    <h1>Score History</h1>

    <!-- Search and Sort Form -->
    <form class="search-form" method="POST" action="">
        <div class="form-group">
            <label for="search_course">Search by Course:</label>
            <input type="text" name="search_course" id="search_course" placeholder="Course Name">
        </div>
        <div class="form-group">
            <label for="search_date">Search by Date:</label>
            <input type="date" name="search_date" id="search_date">
        </div>    
        <div class="form-group">
        <label for="order_by">Sort by:</label>
        <select name="order_by" id="order_by">
            <option value="date_played">Date</option>
            <option value="score">Score</option>
        </select>
        </div>
        <div class="form-group">
        <label for="order_direction">Order:</label>
        <select name="order_direction" id="order_direction">
            <option value="DESC">Descending</option>
            <option value="ASC">Ascending</option>
        </select>
        </div>

        <button type="submit">Search and Sort</button>
    </form>



    <!-- Score Table -->
    <?php if (!empty($scores)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Date Played</th>
                    <th>Course Name</th>
                    <th>Tee Name</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scores as $score) : ?>
                    <tr>
                        <td><?php echo date('Y-m-d H:i', strtotime($score['date_played'])); ?></td>
                        <td><?php echo htmlspecialchars($score['course_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($score['tee_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($score['score'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No scores found.</p>
    <?php endif; ?>
</div>


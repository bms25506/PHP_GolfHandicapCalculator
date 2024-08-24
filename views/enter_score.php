<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include the necessary files
require '../config/db.php';

/* // Initialize variables for form data
$course_id = '';
$scores = array_fill(0, 18, ''); // Initialize an array for 18 scores
$handicapIndex = null;
$handicap = null;
$holes = 18; // Default to 18 holes

// Fetch courses for the dropdown
$query = "SELECT id, tee_name, course_name, gender, back_nine_rating FROM courses ORDER BY course_name, tee_name, gender";
$result = $conn->query($query);
$courses = $result->fetch_all(MYSQLI_ASSOC);
$form_submitted = false;
$total_score = 0; // Initialize total_score

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];

    // Fetch the selected course data
    $query = "SELECT * FROM courses WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $course_id);
    $stmt->execute();
    $course = $stmt->get_result()->fetch_assoc();

    // Determine the number of holes
    $holes = (!empty($course['back_nine_rating']) && $course['back_nine_rating'] !== '') ? 18 : 9;

    // Get scores from the form
    for ($i = 0; $i < $holes; $i++) {
        $scores[$i] = $_POST['score_' . $i];
    }

    // Calculate the total score and Handicap Index
    $total_score = array_sum($scores);
    $handicapIndex = $total_score / count(array_filter($scores));
    $handicap = ($handicapIndex * ($course['slope_rating'] / 113)) + ($course['course_rating'] - $course['par']);

    // Set the date_played
    $date_played = date('Y-m-d H:i:s'); // Current date and time
    $form_submitted = true;

    // Redirect to avoid resubmission
    $_SESSION['handicap_index'] = $handicapIndex;
    $_SESSION['handicap'] = $handicap;
    $_SESSION['form_submitted'] = true;
    header('Location: /PHP_GolfHandicapCalculator/public/enter_score?success=1');
    exit();
}

// Check if the form was submitted and retrieve data from the session if necessary
if (isset($_SESSION['form_submitted']) && $_SESSION['form_submitted']) {
    $handicapIndex = $_SESSION['handicap_index'];
    $handicap = $_SESSION['handicap'];
    unset($_SESSION['form_submitted']);
} */

// Check if form submission was successful and display the results
if (isset($_GET['success']) && $_GET['success'] == 1 && isset($_SESSION['handicapIndex']) && isset($_SESSION['handicap'])) {
    $handicapIndex = $_SESSION['handicapIndex'];
    $handicapValue = $_SESSION['handicap'];
}
?>

<div class="main-content">
<?php if (isset($recentHandicap)): ?>
        <h2>Your Most Recent Handicap</h2>
        <p>Date Calculated: <?php echo $recentHandicap['date_calculated']; ?></p>
        <p>Handicap Index: <?php echo number_format($recentHandicap['handicap_index'], 2); ?></p>
        <p>Your Handicap: <?php echo number_format($recentHandicap['handicap'], 2); ?></p>
    <?php endif; ?>

    <h1>Enter Your Score</h1>
    <form method="POST" action="/PHP_GolfHandicapCalculator/public/enter_score">
        <!-- Course Dropdown -->
        <label for="course_id">Select Course</label>
        <select name="course_id" id="course_id" required>
            <option value="">Select Course</option>
            <?php foreach ($courses as $course) : ?>
                <option value="<?php echo $course['id']; ?>" 
                        data-holes="<?php echo (!empty($course['back_nine_rating']) && $course['back_nine_rating'] != '') ? 18 : 9; ?>" 
                        <?php echo ($course_id == $course['id']) ? 'selected' : ''; ?>>
                    <?php echo $course['course_name'] . ' - ' . $course['tee_name'] . ' (' . $course['gender'] . ')'; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Score Inputs -->
        <div class="score-inputs">
            <?php for ($i = 0; $i < $holes; $i++) : ?>
                <div class="score-column" id="hole_<?php echo $i; ?>">
                    <label for="score_<?php echo $i; ?>">Hole <?php echo $i + 1; ?> Score:</label>
                    <input type="number" name="score_<?php echo $i; ?>" id="score_<?php echo $i; ?>" value="<?php echo $scores[$i]; ?>" min="0" max="10" required>
                </div>
            <?php endfor; ?>
        </div>

        <!-- Submit Button -->
        <button type="submit">Calculate Handicap</button>
    </form>


</div>

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

?>

<div class="main-content">
    <h1>Your Handicaps</h1>

    <?php if (!empty($handicaps)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Date Calculated</th>
                    <th>Course Name</th>
                    <th>Tee Name</th>
                    <th>Handicap Index</th>
                    <th>Handicap</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($handicaps as $handicap) : ?>
                    <tr>
                        <td><?php echo date('Y-m-d H:i', strtotime($handicap['date_calculated'])); ?></td>
                        <td><?php echo htmlspecialchars($handicap['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($handicap['tee_name']); ?></td>
                        <td><?php echo number_format($handicap['handicap_index'], 2); ?></td>
                        <td><?php echo number_format($handicap['handicap'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No handicap records found.</p>
    <?php endif; ?>
</div>


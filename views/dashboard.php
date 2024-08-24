<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'layouts/sidebar.php';
include '../config/db.php';

// Fetch recent handicap, recent score, and performance summary data
$query = "SELECT handicap, handicap_index, date_calculated FROM handicap WHERE user_id = ? ORDER BY date_calculated DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$recentHandicap = $stmt->get_result()->fetch_assoc();

$query = "SELECT s.score, c.course_name, c.tee_name, s.date_played FROM scores s JOIN courses c ON s.course_id = c.id WHERE s.user_id = ? ORDER BY s.date_played DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$recentScore = $stmt->get_result()->fetch_assoc();

$query = "SELECT AVG(score) as average_score_per_game, SUM(holes) as total_holes_played FROM scores WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();

// Fetch scores for chart
$query = "SELECT date_played, score FROM scores WHERE user_id = ? ORDER BY date_played DESC LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$scores = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch the handicap progression data
$query = "SELECT handicap, date_played FROM scores WHERE user_id = ? ORDER BY date_played ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$handicapResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<div class="main-content">
    <h1>Dashboard</h1>
    <div class="dashboard-container">
        <div class="dashboard-item">
            <div class="card">
                <h2>Most Recent Handicap</h2>
                <p>Date Calculated: 
                <?php 
                    echo isset($recentHandicap['date_calculated']) ? $recentHandicap['date_calculated'] : 'N/A'; 
                ?>
                </p>
                <p>Handicap Index: 
                    <?php 
                    echo isset($recentHandicap['handicap_index']) ? number_format($recentHandicap['handicap_index'], 2) : '0.00'; 
                    ?>
                </p>
                <p>Handicap: 
                    <?php 
                    echo isset($recentHandicap['handicap']) ? number_format($recentHandicap['handicap'], 2) : '0.00'; 
                    ?>
                </p>            
            </div>
        </div>

        <div class="dashboard-item">
            <div class="card">
            <h3>Most Recent Score</h3>
                <p>Date Played: 
                    <?php 
                    echo isset($recentScore['date_played']) ? $recentScore['date_played'] : 'N/A'; 
                    ?>
                </p>
                <p>Course: 
                    <?php 
                    echo isset($recentScore['course_name']) ? $recentScore['course_name'] . ' - ' . $recentScore['tee_name'] : 'N/A'; 
                    ?>
                </p>
                <p>Score: 
                    <?php 
                    echo isset($recentScore['score']) ? $recentScore['score'] : 'N/A'; 
                    ?>
                </p>            
            </div>
        </div>

        <div class="dashboard-item">
            <div class="card">
                <h2>Performance Summary</h2>
                <p>Average Score Per Game: 
                <?php 
                    echo isset($summary['average_score_per_game']) ? number_format($summary['average_score_per_game'], 2) : '0.00'; 
                    ?>
                </p>
                <p>Total Holes Played: 
                    <?php 
                    echo isset($summary['total_holes_played']) ? $summary['total_holes_played'] : '0'; 
                    ?>
                </p>            
            </div>
        </div>

        <div class="chart-container">
            <canvas id="scoreChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="handicapChart"></canvas>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Score Chart
    const ctx = document.getElementById('scoreChart').getContext('2d');
    const scoreChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_map(function($date) {
                return date('Y-m-d', strtotime($date['date_played'])); // Format date without time            
            }, $scores)); ?>,
            datasets: [{
                label: 'Scores',
                data: <?php echo json_encode(array_column($scores, 'score')); ?>,
                borderColor: '#1e6120',
                backgroundColor: 'rgba(30, 97, 32, 0.2)',
                fill: true,
            }]
        },
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date Played'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Score'
                    },
                    beginAtZero: true
                }
            },        
            plugins: {
                legend: {
                    display: false,
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Scores Over Time',
                    font: {
                        size: 15,
                    },
                    padding: {
                        top: 5,
                        bottom: 5
                    }
                }
            }
        }
    });

    // Handicap Progression Chart
    const handicapCtx = document.getElementById('handicapChart').getContext('2d');
    const handicapChart = new Chart(handicapCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_map(function($date) {
                return date('Y-m-d', strtotime($date['date_played']));
            }, $handicapResults)); ?>,
            datasets: [{
                label: 'Handicap',
                data: <?php echo json_encode(array_column($handicapResults, 'handicap')); ?>,
                borderColor: 'rgba(0, 123, 255, 0.8)',
                backgroundColor: 'rgba(0, 123, 255, 0.4)',
                fill: true,
                tension: 0.1
            }]
        },
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date Calculated'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Handicap'
                    },
                    beginAtZero: false
                }
            },
            plugins: {
                legend: {
                    display: false,
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Handicap Progression Over Time',
                    font: {
                        size: 15,
                    },
                    padding: {
                        top: 5,
                        bottom: 5
                    }
                }
            }
        }
    });
</script>



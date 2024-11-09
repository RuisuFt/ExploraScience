<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
// Get current month and year (default is this month)
$current_month = isset($_GET['month']) ? $_GET['month'] : date('m');
$current_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Get the full month name (e.g., January, February, etc.)
$current_month_name = date('F', mktime(0, 0, 0, $current_month, 10)); // "F" gives the full month name

// Adjust month and year for previous/next month navigation
if ($current_month == 1) {
    $prev_month = 12;
    $prev_year = $current_year - 1;
} else {
    $prev_month = $current_month - 1;
    $prev_year = $current_year;
}

if ($current_month == 12) {
    $next_month = 1;
    $next_year = $current_year + 1;
} else {
    $next_month = $current_month + 1;
    $next_year = $current_year;
}


// Query to get login data for the selected month and year
$login_data_query = "
    SELECT 
        FLOOR((DAY(login_time) - 1) / 7) + 1 AS week_number, 
        COUNT(*) AS login_count
    FROM user_logins
    WHERE MONTH(login_time) = ?
    AND YEAR(login_time) = ?
    GROUP BY week_number
    ORDER BY week_number ASC
";

$stmt = $conn->prepare($login_data_query);
$stmt->bind_param("ii", $current_month, $current_year);
$stmt->execute();
$login_data_result = $stmt->get_result();

$weekly_logins = [];
while ($row = $login_data_result->fetch_assoc()) {
    $weekly_logins[$row['week_number']] = $row['login_count'];
}

// Fill in missing weeks with 0 logins
for ($week = 1; $week <= 5; $week++) {
    if (!isset($weekly_logins[$week])) {
        $weekly_logins[$week] = 0;
    }
}

$current_month_name = date('F', mktime(0, 0, 0, $current_month, 10));

// Query to get other stats (user count, question count, etc.)
$user_count_query = "SELECT COUNT(*) AS user_count FROM users";
$user_count_result = $conn->query($user_count_query);
$user_count = $user_count_result->fetch_assoc()['user_count'];

$question_count_query = "SELECT COUNT(*) AS question_count FROM questions WHERE archive = 0";
$question_count_result = $conn->query($question_count_query);
$question_count = $question_count_result->fetch_assoc()['question_count'];

$archived_question_count_query = "SELECT COUNT(*) AS archived_count FROM questions WHERE archive = 1";
$archived_question_count_result = $conn->query($archived_question_count_query);
$archived_question_count = $archived_question_count_result->fetch_assoc()['archived_count'];

$correct_answers_query = "SELECT COUNT(*) AS correct_count FROM user_answers WHERE is_correct = 1";
$correct_answers_result = $conn->query($correct_answers_query);
$correct_answers_count = $correct_answers_result->fetch_assoc()['correct_count'];

$wrong_answers_query = "SELECT COUNT(*) AS wrong_count FROM user_answers WHERE is_correct = 0";
$wrong_answers_result = $conn->query($wrong_answers_query);
$wrong_answers_count = $wrong_answers_result->fetch_assoc()['wrong_count'];

$wrong_and_right_answers_count = $correct_answers_count + $wrong_answers_count;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Dashboard</title>

    <!-- Load Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <header>
        <div class="top-bar">
            <div class="user-info">
                <img class="logo-img logo" src="images/agronomy_logo.png" alt="Logo">
            </div>
            <div class="username"><?php echo strtoupper($_SESSION['username']); ?></div>
            <div class="button-group" style="margin-left: auto;">
                <button class="btn" style="background-color: #888;" onclick="window.location.href='admin_dashboard.php'">Dashboard</button>
                <button class="btn" onclick="window.location.href='admin_course_outline_edit.php'">Course</button>
                <button class="btn" onclick="window.location.href='admin_accounts.php'">Accounts</button>
                <button class="btn" onclick="window.location.href='admin_question_pool.php'">Questions Pool</button>
                <button class="btn" onclick="window.location.href='admin_archive_questions.php'">Archive Questions</button>
                <button class="btn" onclick="window.location.href='admin_question_stat.php'">Questions Statistics</button>
                <button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>
            </div>
        </div>
    </header>

    <main>
        <div class="main-dashboard">
            <a href="admin_accounts.php" class="dashboard-box">
                <img src="images/users_image.png" alt="Users">
                <p>Users</p>
                <div class="count"><?php echo $user_count; ?> total</div>
            </a>

            <a href="admin_question_pool.php" class="dashboard-box">
                <img src="images/questions_image.png" alt="Questions">
                <p>Questions</p>
                <div class="count"><?php echo $question_count; ?> available</div>
            </a>

            <a href="admin_archive_questions.php" class="dashboard-box">
                <img src="images/archive_image.png" alt="Archived Questions">
                <p>Archived Questions</p>
                <div class="count"><?php echo $archived_question_count; ?> archived</div>
            </a>
        </div>
        <div class="statistics-graphs-wrapper">
            <a href="admin_archive_questions.php" class="dashboard-box">
                <div class="statistics-graphs">
                    <div class="statistics-item">
                        <canvas id="answerStatsChart" width="750" height="150"></canvas>
                    </div>
                </div>
            </a>

            <a href="admin_archive_questions.php" class="dashboard-box">
                <div class="statistics-item">
                    <canvas id="loginStatsChart" width="750" height="200"></canvas>

                </div>
            </a>
        </div>
        <br>
        <!-- Navigation buttons for previous/next month -->
        <div class="month-navigation">
            <a href="?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" class="btn">Previous Month</a>
            <a href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" class="btn">Next Month</a>
        </div>

    </main>

    <script>

        var ctx = document.getElementById('answerStatsChart').getContext('2d');
        var answerStatsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Users Made Correct Answers: ' + <?php echo $correct_answers_count; ?>, 'Users Made Wrong Answers: ' + <?php echo $wrong_answers_count; ?>, 'Overall Count: ' + <?php echo $wrong_and_right_answers_count; ?>],
                datasets: [{
                    label: 'Amount of User answered questions',
                    data: [<?php echo $correct_answers_count; ?>, <?php echo $wrong_answers_count; ?>, <?php echo $wrong_and_right_answers_count; ?>],
                    backgroundColor: ['#4CAF50', '#F44336', 'purple'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            // Set the box width to 0 to remove the color box
                            boxWidth: 0
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });





        var loginStatsCtx = document.getElementById('loginStatsChart').getContext('2d');
        var loginStatsChart = new Chart(loginStatsCtx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
                datasets: [{
                    label: 'User Logins',
                    data: [<?php echo implode(',', $weekly_logins); ?>],
                    borderColor: '#3e95cd', // Line color
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            // Set the box width to 0 to remove the color box
                            boxWidth: 0
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Logins of the Month ' + '<?php echo $current_month_name; ?>'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of Logins'
                        },
                        beginAtZero: true
                    }
                }
            }
        });

    </script>
</body>

</html>
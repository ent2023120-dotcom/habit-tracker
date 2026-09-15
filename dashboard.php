<?php
session_start();
include 'includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$message = "";

// Add new habit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_habit'])) {
    $habit_name = trim($_POST['habit_name']);
    $goal = intval($_POST['goal']);

    if (empty($habit_name)) {
        $message = "<div class='alert alert-danger'>Habit name cannot be empty!</div>";
    } elseif ($goal < 1 || $goal > 365) {
        $message = "<div class='alert alert-danger'>Goal must be between 1 and 365 days!</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO habits (user_id, habit_name, goal) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $user_id, $habit_name, $goal);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Habit added successfully! 🎉</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error adding habit.</div>";
        }
        $stmt->close();
    }
}

// Delete habit
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM habits WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit();
}

// Increment progress (AJAX)
if (isset($_POST['increment'])) {
    $habit_id = intval($_POST['habit_id']);
    $stmt = $conn->prepare("UPDATE habits SET progress = progress + 1 WHERE id = ? AND user_id = ? AND progress < goal");
    $stmt->bind_param("ii", $habit_id, $user_id);
    $stmt->execute();
    $stmt->close();

    // Get updated progress
    $stmt = $conn->prepare("SELECT progress, goal FROM habits WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $habit_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    echo json_encode($result);
    exit();
}

// Fetch all habits
$stmt = $conn->prepare("SELECT * FROM habits WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$habits = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Habit Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">🎯 Habit Tracker</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Welcome, <?= htmlspecialchars($username) ?>! 👋</h2>
        <p class="text-muted">Track your daily habits and stay consistent.</p>

        <?= $message ?>

        <!-- Add Habit Form -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <h4>Add New Habit</h4>
                <form method="POST" id="habitForm" onsubmit="return validateHabitForm()">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="habit_name" id="habit_name" class="form-control" placeholder="e.g., Drink 8 glasses of water" required>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="goal" id="goal" class="form-control" placeholder="Goal (days)" min="1" max="365" value="30" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="add_habit" class="btn btn-primary w-100">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Habit List -->
        <h4>My Habits</h4>
        <div class="row" id="habitList">
            <?php if ($habits->num_rows == 0): ?>
                <div class="col-12">
                    <div class="alert alert-info">No habits yet. Add your first habit above! 🌱</div>
                </div>
            <?php else: ?>
                <?php while ($habit = $habits->fetch_assoc()): 
                    $percent = $habit['goal'] > 0 ? round(($habit['progress'] / $habit['goal']) * 100) : 0;
                    if ($percent > 100) $percent = 100;
                ?>
                <div class="col-md-6 mb-3">
                    <div class="card habit-card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title"><?= htmlspecialchars($habit['habit_name']) ?></h5>
                                <a href="?delete=<?= $habit['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this habit?')">✕</a>
                            </div>
                            <p class="text-muted mb-2">
                                Progress: <span id="progress-<?= $habit['id'] ?>"><?= $habit['progress'] ?></span> / <?= $habit['goal'] ?> days
                            </p>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-success" id="bar-<?= $habit['id'] ?>" 
                                     role="progressbar" 
                                     style="width: <?= $percent ?>%;" 
                                     data-width="<?= $percent ?>">
                                    <?= $percent ?>%
                                </div>
                            </div>
                            <button class="btn btn-success btn-sm" onclick="incrementHabit(<?= $habit['id'] ?>)">+ Mark Today Done</button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
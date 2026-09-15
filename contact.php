<?php
session_start();
include 'includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $msg = trim($_POST['message']);

    // Server-side validation
    if (empty($name) || empty($email) || empty($msg)) {
        $message = "<div class='alert alert-danger'>All fields are required!</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger'>Invalid email format!</div>";
    } elseif (strlen($msg) < 10) {
        $message = "<div class='alert alert-danger'>Message must be at least 10 characters!</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $msg);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Thank you! Your message has been sent. ✅</div>";
        } else {
            $message = "<div class='alert alert-danger'>Something went wrong. Please try again.</div>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Habit Tracker</title>
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="auth/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="auth/login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="auth/register.php">Register</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5" style="max-width: 700px;">
        <div class="card shadow">
            <div class="card-body p-4">
                <h2 class="text-center mb-3">Contact Us 📬</h2>
                <p class="text-center text-muted">Have a question or feedback? Send us a message!</p>

                <?= $message ?>

                <form method="POST" id="contactForm" onsubmit="return validateContactForm()">
                    <div class="mb-3">
                        <label class="form-label">Your Name</label>
                        <input type="text" name="name" id="contact_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" id="contact_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" id="contact_message" class="form-control" rows="5" required></textarea>
                        <small class="text-muted">Minimum 10 characters</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Send Message</button>
                </form>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="row mt-4 text-center">
            <div class="col-md-4 mb-3">
                <div class="card p-3 shadow-sm">
                    <h5>📍 Address</h5>
                    <p class="text-muted mb-0">Rattota Rd,Matale</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card p-3 shadow-sm">
                    <h5>📧 Email</h5>
                    <p class="text-muted mb-0">sujani@habittracker.com</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card p-3 shadow-sm">
                    <h5>📞 Phone</h5>
                    <p class="text-muted mb-0">+94 11 234 5678</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
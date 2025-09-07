<?php
$userName = $_GET['name'] ?? 'Guest';

// If name is not in the URL, redirect to the main menu.
// This ensures the dashboard is accessed with a username.
if (empty($userName)) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AI Study Buddy - Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  </head>
  <body class="bg-light">
    <?php require_once 'navbar.php'; ?>
    <div class="container my-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card text-center">
                    <div class="card-header">
                        <h1>Dashboard</h1>
                    </div>
                    <div class="card-body">
                        <p class="lead">What would you like to do today?</p>
                        <div class="d-grid gap-3">
                            <a href="explanation.php?name=<?php echo urlencode($userName); ?>" class="btn btn-primary btn-lg">Get an Explanation</a>
                            <a href="quiz.php?name=<?php echo urlencode($userName); ?>" class="btn btn-success btn-lg">Take a Quiz</a>
                            <a href="code.php?name=<?php echo urlencode($userName); ?>" class="btn btn-info btn-lg">Generate Code</a>
                            
                            <button id="logoutBtn" class="btn btn-danger btn-lg mt-4">Logout</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutBtn = document.getElementById('logoutBtn');

            if (logoutBtn) {
                logoutBtn.addEventListener('click', function() {
                    localStorage.removeItem('userName');
                    localStorage.removeItem('userAge');
                    window.location.href = '../index.php'; // Redirect to main menu
                });
            }
        });
    </script>
  </body>
</html>
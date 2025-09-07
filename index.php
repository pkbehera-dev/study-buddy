<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AI Study Buddy - Main Menu</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  </head>
  <body class="bg-light">
    <div class="container my-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card text-center">
                    <div class="card-header">
                        <h1>Welcome to AI Study Buddy!</h1>
                    </div>
                    <form method="POST" action="index.php" class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Enter your name:</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Your Name" autocomplete="off" required />
                        </div>
                        <div class="mb-3">
                            <label for="age" class="form-label">Enter your age:</label>
                            <input type="number" id="age" name="age" class="form-control" placeholder="Your Age" autocomplete="off" required />
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Start Learning</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            const ageInput = document.getElementById('age');
            const form = document.querySelector('form');

            // Check localStorage on page load
            const storedName = localStorage.getItem('userName');
            const storedAge = localStorage.getItem('userAge');

            if (storedName && storedAge) {
                // If user details exist, redirect to dashboard
                window.location.href = `pages/dashboard.php?name=${encodeURIComponent(storedName)}`;
            }

            if (form) {
                form.addEventListener('submit', function(event) {
                    // Prevent default form submission for now, handle with JS
                    event.preventDefault();

                    const name = nameInput.value.trim();
                    const age = ageInput.value.trim();

                    if (name && age) {
                        localStorage.setItem('userName', name);
                        localStorage.setItem('userAge', age);
                        // Redirect to dashboard after saving
                        window.location.href = `pages/dashboard.php?name=${encodeURIComponent(name)}`;
                    } else {
                        alert('Please enter your name and age.');
                    }
                });
            }
        });
    </script>
  </body>
</html>
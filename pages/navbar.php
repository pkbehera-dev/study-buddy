<?php
// Ensure the $userName variable is available, otherwise default to 'Guest'
$userName = $userName ?? 'Guest';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Hello <?php echo htmlspecialchars($userName); ?>!</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php?name=<?php echo urlencode($userName); ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="documentation.php?name=<?php echo urlencode($userName); ?>">Documentation</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
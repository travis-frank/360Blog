<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="styles/register.css" />
  <script src="js/loginValidation.js"></script>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
      <div class="col-12 col-md-6 col-lg-5">
        <div class="card">
          <div class="text-center header-content">
            <img src="../../Images/logo.png" alt="Logo" width="75px" height="75px" />
            <h2>Login</h2>
          </div>

          <!-- Display error messages if any -->
          <?php session_start(); if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger text-center">
              <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
          <?php endif; ?>

          <form id="loginForm" method="POST" action="php/validate.php">
            <!-- Email -->
            <div class="form-group">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="example@email.com" required />
            </div>

            <!-- Password -->
            <div class="form-group">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required />
            </div>

            <!-- Login Button -->
            <button type="submit" class="btn btn-primary w-100">Login</button>

            <div class="text-center">
              <p class="sign-in-text">
                Don't have an account? <a href="register.html">Sign up</a>
              </p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

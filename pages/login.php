<?php
// Start session at the VERY TOP
session_start();

// Get error from session if it exists
$error = $_SESSION['login_error'] ?? '';

// Clear the error so it doesn't persist
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <form action="../actions/login.php" method="POST" class="bg-white p-8 rounded-xl shadow-md w-full max-w-md space-y-4">
    <h2 class="text-2xl font-bold text-center text-gray-700">Welcome Back</h2>
    
    <?php if (!empty($error)): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <input name="email" type="email" placeholder="Email" required 
           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
           class="w-full px-4 py-2 border rounded-lg">
    
    <input name="password" type="password" placeholder="Password" required 
           class="w-full px-4 py-2 border rounded-lg">

    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">Login</button>

    <p class="text-sm text-center mt-4">
      Don't have an account? <a href="register.php" class="text-blue-500">Sign Up</a>
    </p>
  </form>
</body>
</html>
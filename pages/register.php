<!-- signup.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Signup</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <form action="../actions/register.php" method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-xl shadow-md w-full max-w-md space-y-4">
    <h2 class="text-2xl font-bold text-center text-gray-700">Create an Account</h2>

    <?php
      session_start();
      if (!empty($_SESSION['register_error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <?= htmlspecialchars($_SESSION['register_error']); unset($_SESSION['register_error']); ?>
        </div>
    <?php endif; ?>

    <input name="username" type="text" placeholder="Username" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    <input name="email" type="email" placeholder="Email" required class="w-full px-4 py-2 border rounded-lg">
    <input name="password" type="password" placeholder="Password" required class="w-full px-4 py-2 border rounded-lg">
    
    <input type="file" name="avatar" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
    
    <textarea name="bio" placeholder="Bio (optional)" class="w-full px-4 py-2 border rounded-lg"></textarea>

    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">Sign Up</button>

    <p class="text-sm text-center mt-4">Already have an account? <a href="login.php" class="text-blue-500">Login</a></p>
  </form>
</body>
</html>

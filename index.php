<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Postify</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-500 text-white min-h-screen flex flex-col">
  <?php
   include "./includes/navbar.php"; 
   ?>

   <div class="flex  items-center flex-col min-h-screen">
    <h1 class="text-2xl font-bold mt-4">Welcome to Postify</h1>
    <p class=" mt-2">Your one-stop platform for sharing and discovering posts.</p>

    
   </div>

  <!-- <main class="flex-1 p-6">
    <?php // include "./pages/home.php"; ?>
  </main> -->

  <footer class="bg-gray-800 text-center py-4 relative z-50">
    <?php include "./includes/footer.php"; ?>
  </footer>
</body>
</html>

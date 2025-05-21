<?php
$current_year = date("Y");

$social_links = [
    'facebook' => 'https://facebook.com/yourpage',
    'twitter' => 'https://twitter.com/yourhandle',
    'instagram' => 'https://instagram.com/yourhandle',
];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<footer class="bg-white text-gray-900 py-6 dark:bg-gray-900 dark:text-white transition duration-300">
    <div class="container mx-auto px-6 text-center">
        <div class="mb-4">
            <a href="/about" class="mx-2 hover:text-gray-700 dark:hover:text-gray-300">About</a>
            <a href="/help" class="mx-2 hover:text-gray-700 dark:hover:text-gray-300">Help</a>
            <a href="/privacy-policy" class="mx-2 hover:text-gray-700 dark:hover:text-gray-300">Privacy</a>
            <a href="/terms-of-service" class="mx-2 hover:text-gray-700 dark:hover:text-gray-300">Terms</a>
        </div>

        <div class="mb-4">
            <?php foreach ($social_links as $platform => $link): ?>
                <a href="<?= $link ?>" target="_blank" class="mx-2 text-gray-800 hover:text-blue-600 dark:text-white dark:hover:text-blue-400">
                    <i class="fab fa-<?= $platform ?>"></i>
                </a>
            <?php endforeach; ?>
        </div>

        <p class="text-sm text-gray-700 dark:text-gray-300">&copy; <?= $current_year ?> Postify. All rights reserved.</p>
    </div>
</footer>
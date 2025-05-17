<?php
$current_year = date("Y");

$social_links = [
    'facebook' => 'https://facebook.com/yourpage',
    'twitter' => 'https://twitter.com/yourhandle',
    'instagram' => 'https://instagram.com/yourhandle',
];
?>

<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<footer class="bg-gray-800 text-white py-6">
    <div class="container mx-auto px-6 text-center">
        <!-- Quick Links -->
        <div class="mb-4">
            <a href="/about" class="mx-2 hover:text-gray-400">About</a>
            <a href="/help" class="mx-2 hover:text-gray-400">Help</a>
            <a href="/privacy-policy" class="mx-2 hover:text-gray-400">Privacy</a>
            <a href="/terms-of-service" class="mx-2 hover:text-gray-400">Terms</a>
        </div>

        <!-- Social Icons -->
        <div class="mb-4">
            <?php foreach ($social_links as $platform => $link): ?>
                <a href="<?= $link ?>" target="_blank" class="mx-2 hover:text-gray-400">
                    <i class="fab fa-<?= $platform ?>"></i>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Copyright -->
        <p class="text-sm">&copy; <?= $current_year ?> Postify. All rights reserved.</p>
    </div>
</footer>
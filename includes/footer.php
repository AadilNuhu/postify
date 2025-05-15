<?php
// This will automatically display the current year.
$current_year = date("Y");

// Example of dynamically fetching contact email and social media links (replace these with actual data from your database or config file).
$contact_email = "contact@yourdomain.com";
$social_links = [
    'facebook' => 'https://facebook.com/yourpage',
    'twitter' => 'https://twitter.com/yourhandle',
    'instagram' => 'https://instagram.com/yourhandle',
];
?>

<footer class="bg-gray-800 text-white py-6">
    <div class="container mx-auto px-6 md:px-12">
        <div class="flex flex-wrap justify-between">
            <!-- Left Side: Quick Links -->
            <div class="w-full md:w-1/3 mb-6 md:mb-0">
                <h3 class="text-xl font-semibold">Quick Links</h3>
                <ul class="mt-4 space-y-2">
                    <li><a href="/about" class="hover:text-gray-400">About Us</a></li>
                    <li><a href="/help" class="hover:text-gray-400">Help Center</a></li>
                    <li><a href="/privacy-policy" class="hover:text-gray-400">Privacy Policy</a></li>
                    <li><a href="/terms-of-service" class="hover:text-gray-400">Terms of Service</a></li>
                </ul>
            </div>

            <!-- Center: Contact Information -->
            <div class="w-full md:w-1/3 mb-6 md:mb-0 text-center">
                <h3 class="text-xl font-semibold">Contact Us</h3>
                <p class="mt-4">Feel free to reach out to us at:</p>
                <a href="mailto:<?php echo $contact_email; ?>" class="hover:text-gray-400"><?php echo $contact_email; ?></a>
            </div>

            <!-- Right Side: Social Media Links -->
            <div class="w-full md:w-1/3 text-center">
                <h3 class="text-xl font-semibold">Follow Us</h3>
                <div class="mt-4 space-x-4">
                    <?php foreach ($social_links as $platform => $link): ?>
                        <a href="<?php echo $link; ?>" target="_blank" class="text-white hover:text-gray-400">
                            <i class="fab fa-<?php echo $platform; ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Copyright -->
    <div class="text-center mt-6 text-sm">
        <p>&copy; <?php echo $current_year; ?> Your Platform Name. All rights reserved.</p>
    </div>
</footer>

<!-- Font Awesome CDN for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<?php
/**
 * Internet Connection Check Helper
 * Redirects to offline screen if internet socket cannot be reached
 */
function check_internet($redirectPath = "no_connection.php") {
    // Attempt socket connection with short timeout to prevent slow loading
    $sock = @fsockopen('8.8.8.8', 53, $errno, $errstr, 2);
    if (!$sock) {
        header("Location: " . $redirectPath);
        exit();
    }
    fclose($sock);
}

// Backward compatibility alias for legacy code
if (!function_exists('check_internat')) {
    function check_internat($redirectPath = "no_connection.php") {
        check_internet($redirectPath);
    }
}
?>

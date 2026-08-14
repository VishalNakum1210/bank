<?php
/**
 * Global Utility Functions & Helpers
 * NNP Online Banking System
 */

/**
 * Display modern toast / banner notification
 *
 * @param string $message The alert message text
 * @param string $color "red" / "#e11d48" for errors, "green" / "#16a34a" for success
 */
if (!function_exists('error_display')) {
    function error_display($message, $color = "red") {
        $is_success = ($color === "green" || $color === "#16a34a" || $color === "#10b981");
        $bg_color = $is_success ? "#16a34a" : (($color === "red" || $color === "#e11d48") ? "#e11d48" : $color);
        $icon = $is_success ? "bx-check-circle" : "bx-error";
        
        echo '<div style="background-color: ' . htmlspecialchars($bg_color) . ';" class="error_noti">
            <i class="bx ' . $icon . '"></i>
            <span>' . htmlspecialchars($message) . '</span>
            <i class="bx bx-x icon2" onclick="this.parentElement.remove();"></i>
        </div>';
    }
}

/**
 * Sanitize user input to prevent XSS
 */
if (!function_exists('clean_input')) {
    function clean_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
}

/**
 * Format currency to Indian Rupee (INR) standard format
 */
if (!function_exists('format_inr')) {
    function format_inr($amount) {
        return number_format((float)$amount, 2);
    }
}
?>

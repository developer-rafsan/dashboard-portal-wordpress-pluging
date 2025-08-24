<?php

defined('ABSPATH') or die('No direct access.');


// Shortcode for displaying the client dashboard on the front-end
function codex_client_dashboard_shortcode() {
    // Only output if this is not an AJAX request
    if (wp_doing_ajax()) {
        return '';
    }
    
    ob_start();
    ?>

<div style='position: fixed; top: 40px; right: 30px; background-color: #F5F5F5; padding: 20px 10px; z-index: 999999; border-radius: 10px; border: 1px solid #E0E0E0; box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);'
    id="toast-simple"
    class="notification-container flex items-center w-full max-w-xs p-4 space-x-4 rtl:space-x-reverse text-gray-500 bg-white divide-x rtl:divide-x-reverse divide-gray-200 rounded-lg shadow-sm dark:text-gray-400 dark:divide-gray-700 dark:bg-gray-800"
    role="alert">
    <svg class="w-5 h-5 text-blue-600 dark:text-blue-500 rotate-45" aria-hidden="true"
        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="m9 17 8 2L9 1 1 19l8-2Zm0 0V9" />
    </svg>
    <div id="notification-title" class="ps-4 text-sm font-normal">Message sent successfully.</div>
</div>

<?php
    include(plugin_dir_path(__FILE__) . '../templates/client-dashbord.php');
    return ob_get_clean();
}

add_shortcode('codex_client_dashboard', 'codex_client_dashboard_shortcode');
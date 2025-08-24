<?php
/*
Plugin Name: Dashboard Portal
Plugin URI: 
Description: A HIPAA-compliant client intake, portal, document delivery, and secure messaging system. This plugin provides a secure platform for managing sensitive client data, supporting features like encrypted messaging, secure document sharing, and client intake forms.
Version: 1.0.2
Author: Codex
Author URI: https://portfolio-client-y9gw.onrender.com
Text Domain: codex
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 6.2.2
Tested up to: 6.2.2
Requires PHP: 8.0
Tags: dashboard, client portal, document delivery, encryption, secure client intake, codex
*/

defined('ABSPATH') || exit;

// Define plugin constants
define('CODEX_PLUGIN_DIR', plugin_dir_path(__FILE__)); 
define('CODEX_PLUGIN_URL', plugin_dir_url(__FILE__));


/**
 * Enqueue scripts for Admin Dashboard
 */
function codex_enqueue_for_admin() {
    $screen = get_current_screen();

    if ($screen && $screen->id === 'toplevel_page_codex-dashboard') {
        // Tailwind CSS
        wp_enqueue_style(
            'codex-tailwind',
            'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css',
            [],
            '2.2.19'
        );

        // Custom admin CSS
        wp_enqueue_style(
            'codex-admin-css',
            CODEX_PLUGIN_URL . 'assets/css/admin.css',
            ['codex-tailwind'],
            '1.0.0'
        );

        // Custom admin JS
        wp_enqueue_script(
            'codex-admin-js',
            CODEX_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            '1.0.0',
            true
        );

        // Localize admin script
        wp_localize_script('codex-admin-js', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('nexuspros_case_intake_nonce')
        ]);
    }
}
add_action('admin_enqueue_scripts', 'codex_enqueue_for_admin');




/**
 * Enqueue scripts for frontend pages
 */
function codex_enqueue_for_page() {
    // Common styles
    wp_enqueue_style(
        'codex-tailwind',
        'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css',
        [],
        '2.2.19'
    );

    // Enqueue Stripe JS globally for any page that might use the client dashboard
    wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', [], null, true);

    // Client portal JS (Stripe payment handling) - enqueue globally
    wp_enqueue_script(
        'codex-client-js',
        CODEX_PLUGIN_URL . 'assets/js/client.js',
        ['jquery', 'stripe-js'],
        '1.0.0',
        true
    );

    // Localize for AJAX + Stripe - make available globally
    wp_localize_script('codex-client-js', 'ajax_object', [
        'ajax_url'        => admin_url('admin-ajax.php'),
        'nonce'           => wp_create_nonce('nexuspros_case_intake_nonce'),
        'publishable_key' => 'pk_test_51RmpJyIh3wZtrEkrdbnzmUU3Tr3agN5CX7moE62pAjyjPrfPBvou5d4wHdx8s5tK7TwOsRveSKVd9ibFisFIY6ke00SOrWTluA'
    ]);

    // Check client portal page specifically for additional styles
    if (is_page('client-portal')) {
        wp_enqueue_style(
            'codex-client-page-css',
            CODEX_PLUGIN_URL . 'assets/css/client.css',
            ['codex-tailwind'],
            '1.0.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'codex_enqueue_for_page');



/**
 * Plugin activation (create DB tables)
 */
register_activation_hook(__FILE__, 'codex_nexuspros_activate');
function codex_nexuspros_activate() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'nexuspros_cases_intake_forms';
    $charset_collate = $wpdb->get_charset_collate();

    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name) {
        set_transient('codex_intake_table_exists_notice', 'Intake forms database table already exists.', 5);
        return;
    }

    // SQL query for creating the table
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        
        -- Case Information
        case_id varchar(100) NOT NULL UNIQUE,
        case_status varchar(100) DEFAULT 'pending',
        rating INT DEFAULT NULL,
        rating_date datetime DEFAULT NULL,
        
        -- Package Information
        case_package_type varchar(100) NOT NULL,
        case_package_price varchar(100) NOT NULL,
        case_priority varchar(100) NOT NULL,
        
        -- Payment Information
        payment_status varchar(100) DEFAULT 'pending',
        payment_date date DEFAULT NULL,
        payment_amount varchar(100) DEFAULT NULL,
        payment_method varchar(100) DEFAULT NULL,

        -- user information
        user_id varchar(100) NOT NULL,
        user_name varchar(100) NOT NULL,
        user_email varchar(150) NOT NULL,
        
        -- Client Information
        first_name varchar(100) NOT NULL,
        last_name varchar(100) NOT NULL,
        email varchar(150) NOT NULL,
        phone varchar(30) NOT NULL,
        birth_date date NOT NULL,
        social_security_number varchar(100) NOT NULL,
        address varchar(255) NOT NULL,
        city varchar(100) NOT NULL,
        state varchar(50) NOT NULL,
        zip varchar(20) NOT NULL,
        
        -- Military Information
        branch_of_service varchar(100),
        final_rank varchar(50),
        start_date date,
        end_date date,
        va_file_number varchar(50),
        
        -- Medical Information
        current_medical_conditions text,
        service_connected_conditions text,
        medical_history text,
        current_treatment text,
        
        -- Document Information
        client_document_paths text,
        admin_document_paths text,
        consent_data_collection tinyint(1) DEFAULT 0,
        consent_privacy_policy tinyint(1) DEFAULT 0,
        consent_communication tinyint(1) DEFAULT 0,
        
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $result = dbDelta($sql);

    if ($result === false) {
        set_transient('codex_intake_table_creation_failed', 'Error creating the intake forms table.', 5);
    } else {
        set_transient('codex_intake_table_created_notice', 'Intake forms database table created successfully.', 5);
    }
    
    // Create notifications table
    create_notifications_table();
}




/**
 * Admin notices after activation
 */
add_action('admin_notices', 'codex_display_activation_notice');
function codex_display_activation_notice() {
    if ($message = get_transient('codex_intake_table_exists_notice')) {
        echo '<div class="notice notice-info is-dismissible"><p>' . esc_html($message) . '</p></div>';
        delete_transient('codex_intake_table_exists_notice');
    }
    if ($message = get_transient('codex_intake_table_created_notice')) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($message) . '</p></div>';
        delete_transient('codex_intake_table_created_notice');
    }
}



// Handle case status form update
add_action('wp_ajax_update_request_case_status', 'update_request_case_status');
add_action('wp_ajax_nopriv_update_request_case_status', 'update_request_case_status');

function update_request_case_status() {
    // Check nonce for security
    if ( ! isset($_POST['_ajax_nonce']) || ! wp_verify_nonce($_POST['_ajax_nonce'], 'nexuspros_case_intake_nonce') ) {
        wp_send_json_error(['message' => 'Invalid nonce']);
        wp_die();
    }

    global $wpdb;

    $table = $wpdb->prefix . 'nexuspros_cases_intake_forms';

    // Validate and sanitize input
    $case_id = isset($_POST['case_id']) ? sanitize_text_field($_POST['case_id']) : '';
    $status  = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

    if (empty($case_id) || empty($status)) {
        wp_send_json_error(['message' => 'Missing case_id or status']);
        wp_die();
    }

    $case_exists = $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE case_id = %s", $case_id)
    );
    
    $updated = $wpdb->update(
        $table,
        [ 'case_status' => $status ],
        [ 'case_id' => $case_id ],
        [ '%s' ],   
        [ '%s' ]  
    );

    if ( false !== $updated ) {
        // Create notification for status change
        create_notification(
            'status_change',
            'Case Status Changed',
            'Case #' . $case_id . ' status changed to: ' . ucfirst($status),
            $case_id
        );
        
        wp_send_json_success(['message' => 'Status updated successfully']);
    } else {
        wp_send_json_error(['message' => 'No rows updated or query failed']);
    }
}



// Handle case intake form submission
add_action('wp_ajax_handle_nexuspros_case_intake', 'handle_nexuspros_case_intake');
add_action('wp_ajax_nopriv_handle_nexuspros_case_intake', 'handle_nexuspros_case_intake');

/**
 * Send intake form submission email
 */
function send_intake_form_email($data, $uploaded_files) {
    $admin_email = get_option('admin_email');
    $client_email = 'developer.rafsanx@gmail.com';
    $site_name = get_bloginfo('name');
    
    // Format documents
    $documents_list = '';
    if (!empty($uploaded_files)) {
        foreach ($uploaded_files as $doc) {
            $filename = basename($doc);
            $documents_list .= "• " . $filename . "\n";
        }
    } else {
        $documents_list = "No documents uploaded\n";
    }
    
    // Email subject
    $subject = "New Case Submitted - Case #" . $data['case_id'] . " - " . $site_name;
    
    // Email body
    // Modern HTML email with cool color design and template style
    $message = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>New Case Submitted</title>
</head>
<body style="margin:0;padding:0;background:#f4f8fb;font-family:Segoe UI,Arial,sans-serif;">
  <table width="100%" bgcolor="#f4f8fb" cellpadding="0" cellspacing="0" style="padding:40px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,0.07);overflow:hidden;">
          <tr>
            <td style="background:linear-gradient(90deg,#2563eb 0%,#06b6d4 100%);padding:32px 0;text-align:center;">
              <h1 style="color:#fff;font-size:2rem;margin:0;letter-spacing:1px;">🚀 New Case Submitted</h1>
              <p style="color:#e0f2fe;font-size:1.1rem;margin:8px 0 0 0;">A new intake form has been received</p>
            </td>
          </tr>
          <tr>
            <td style="padding:32px;">
              <h2 style="color:#2563eb;font-size:1.2rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Case Information</h2>
              <table width="100%" style="font-size:1rem;color:#222;margin-bottom:18px;">
                <tr><td><strong>Case ID:</strong></td><td>' . esc_html($data['case_id']) . '</td></tr>
                <tr><td><strong>Status:</strong></td><td>' . esc_html(ucfirst($data['case_status'])) . '</td></tr>
                <tr><td><strong>Submission Date:</strong></td><td>' . esc_html(current_time('F j, Y g:i A')) . '</td></tr>
              </table>

              <h2 style="color:#06b6d4;font-size:1.1rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Client Information</h2>
              <table width="100%" style="font-size:1rem;color:#222;margin-bottom:18px;">
                <tr><td><strong>Name:</strong></td><td>' . esc_html($data['first_name'] . ' ' . $data['last_name']) . '</td></tr>
                <tr><td><strong>Email:</strong></td><td>' . esc_html($data['email']) . '</td></tr>
                <tr><td><strong>Phone:</strong></td><td>' . esc_html($data['phone']) . '</td></tr>
                <tr><td><strong>Date of Birth:</strong></td><td>' . ($data['birth_date'] ? esc_html(date('F j, Y', strtotime($data['birth_date']))) : 'Not provided') . '</td></tr>
                <tr><td><strong>SSN:</strong></td><td>' . esc_html($data['social_security_number']) . '</td></tr>
                <tr><td><strong>Address:</strong></td><td>' . esc_html($data['address'] . ', ' . $data['city'] . ', ' . $data['state'] . ' ' . $data['zip']) . '</td></tr>
              </table>

              <h2 style="color:#2563eb;font-size:1.1rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Package Details</h2>
              <table width="100%" style="font-size:1rem;color:#222;margin-bottom:18px;">
                <tr><td><strong>Type:</strong></td><td>' . esc_html($data['case_package_type'] ?: 'Not selected') . '</td></tr>
                <tr><td><strong>Price:</strong></td><td>$' . number_format(floatval($data['case_package_price'] ?: 0), 2) . '</td></tr>
                <tr><td><strong>Priority:</strong></td><td>' . esc_html(ucfirst($data['case_priority'] ?: 'Standard')) . '</td></tr>
              </table>

              <h2 style="color:#06b6d4;font-size:1.1rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Payment Information</h2>
              <table width="100%" style="font-size:1rem;color:#222;margin-bottom:18px;">
                <tr><td><strong>Status:</strong></td><td>' . esc_html(ucfirst($data['payment_status'])) . '</td></tr>
                ' . ($data['payment_date'] ? '<tr><td><strong>Payment Date:</strong></td><td>' . esc_html(date('F j, Y', strtotime($data['payment_date']))) . '</td></tr>' : '') . '
                ' . ($data['payment_amount'] ? '<tr><td><strong>Amount:</strong></td><td>$' . number_format(floatval($data['payment_amount']), 2) . '</td></tr>' : '') . '
                ' . ($data['payment_method'] ? '<tr><td><strong>Method:</strong></td><td>' . esc_html(ucfirst($data['payment_method'])) . '</td></tr>' : '') . '
              </table>

              <h2 style="color:#2563eb;font-size:1.1rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Military Information</h2>
              <table width="100%" style="font-size:1rem;color:#222;margin-bottom:18px;">
                <tr><td><strong>Branch:</strong></td><td>' . esc_html($data['branch_of_service'] ?: 'Not specified') . '</td></tr>
                <tr><td><strong>Final Rank:</strong></td><td>' . esc_html($data['final_rank'] ?: 'Not specified') . '</td></tr>
                <tr><td><strong>Service Period:</strong></td><td>' . ($data['start_date'] ? esc_html(date('F j, Y', strtotime($data['start_date']))) : 'Not specified') . ' - ' . ($data['end_date'] ? esc_html(date('F j, Y', strtotime($data['end_date']))) : 'Not specified') . '</td></tr>
                <tr><td><strong>VA File #:</strong></td><td>' . esc_html($data['va_file_number'] ?: 'Not specified') . '</td></tr>
              </table>

              <h2 style="color:#06b6d4;font-size:1.1rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Medical Information</h2>
              <div style="font-size:1rem;color:#222;margin-bottom:18px;">
                <strong>Current Medical Conditions:</strong><br>
                <span style="color:#374151;">' . nl2br(esc_html($data['current_medical_conditions'] ?: 'Not specified')) . '</span><br><br>
                <strong>Service Connected Conditions:</strong><br>
                <span style="color:#374151;">' . nl2br(esc_html($data['service_connected_conditions'] ?: 'Not specified')) . '</span><br><br>
                <strong>Medical History:</strong><br>
                <span style="color:#374151;">' . nl2br(esc_html($data['medical_history'] ?: 'Not specified')) . '</span><br><br>
                <strong>Current Treatment:</strong><br>
                <span style="color:#374151;">' . nl2br(esc_html($data['current_treatment'] ?: 'Not specified')) . '</span>
              </div>

              <h2 style="color:#2563eb;font-size:1.1rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Documents Uploaded</h2>
              <div style="font-size:1rem;color:#222;margin-bottom:18px;">
                <pre style="background:#f0f9ff;color:#0369a1;padding:12px 16px;border-radius:8px;font-size:1rem;line-height:1.5;white-space:pre-wrap;">' . esc_html($documents_list) . '</pre>
              </div>

              <h2 style="color:#06b6d4;font-size:1.1rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Consent Information</h2>
              <table width="100%" style="font-size:1rem;color:#222;margin-bottom:18px;">
                <tr><td><strong>Data Collection Consent:</strong></td><td>' . ($data['consent_data_collection'] ? 'Yes' : 'No') . '</td></tr>
                <tr><td><strong>Privacy Policy Consent:</strong></td><td>' . ($data['consent_privacy_policy'] ? 'Yes' : 'No') . '</td></tr>
                <tr><td><strong>Communication Consent:</strong></td><td>' . ($data['consent_communication'] ? 'Yes' : 'No') . '</td></tr>
              </table>

              <div style="margin:32px 0 0 0;padding:18px 24px;background:linear-gradient(90deg,#f0fdfa 0%,#e0e7ef 100%);border-radius:8px;">
                <p style="color:#2563eb;font-size:1.1rem;margin:0 0 8px 0;"><strong>Please review this case and take appropriate action.</strong></p>
                <p style="color:#64748b;font-size:1rem;margin:0;">Best regards,<br>' . esc_html($site_name) . ' System</p>
              </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
';

    // Headers
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $site_name . ' <' . $admin_email . '>',
        'Reply-To: ' . $admin_email
    ];
    
    // Send email to admin
    wp_mail($admin_email, $subject, $message, $headers);
    
    // Send confirmation email to client
    $client_subject = "Case Submission Confirmed - Case #" . $data['case_id'];
    $client_message = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Case Submission Confirmed</title>
</head>
<body style="margin:0;padding:0;background:#f4f8fb;font-family:Segoe UI,Arial,sans-serif;">
  <table width="100%" bgcolor="#f4f8fb" cellpadding="0" cellspacing="0" style="padding:40px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,0.07);overflow:hidden;">
          <tr>
            <td style="background:linear-gradient(90deg,#06b6d4 0%,#2563eb 100%);padding:32px 0;text-align:center;">
              <h1 style="color:#fff;font-size:2rem;margin:0;letter-spacing:1px;">✅ Case Submission Confirmed</h1>
              <p style="color:#e0f2fe;font-size:1.1rem;margin:8px 0 0 0;">Thank you for submitting your case</p>
            </td>
          </tr>
          <tr>
            <td style="padding:32px;">
              <p style="font-size:1.1rem;color:#222;">Dear <strong>' . esc_html($data['first_name']) . '</strong>,</p>
              <p style="font-size:1rem;color:#374151;margin-bottom:24px;">
                Thank you for submitting your case through our intake form. We have received your information and will review it shortly.
              </p>
              <h2 style="color:#2563eb;font-size:1.1rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Case Details</h2>
              <table width="100%" style="font-size:1rem;color:#222;margin-bottom:18px;">
                <tr><td><strong>Case ID:</strong></td><td>' . esc_html($data['case_id']) . '</td></tr>
                <tr><td><strong>Package:</strong></td><td>' . esc_html($data['case_package_type'] ?: 'Not selected') . '</td></tr>
                <tr><td><strong>Submission Date:</strong></td><td>' . esc_html(current_time('F j, Y g:i A')) . '</td></tr>
              </table>
              <div style="background:#f0fdfa;border-radius:8px;padding:18px 24px;margin-bottom:24px;">
                <h3 style="color:#06b6d4;font-size:1rem;margin:0 0 8px 0;">What\'s Next?</h3>
                <ul style="color:#2563eb;font-size:1rem;margin:0 0 0 18px;padding:0;">
                  <li>Our team will review your case within 1-2 business days</li>
                  <li>You will receive an email update once your case status changes</li>
                  <li>You can check your case status anytime through your client dashboard</li>
                </ul>
              </div>
              <p style="font-size:1rem;color:#374151;">
                If you have any questions or need to provide additional information, please don\'t hesitate to contact us.
              </p>
              <p style="color:#64748b;font-size:1rem;margin:32px 0 0 0;">Best regards,<br>' . esc_html($site_name) . ' Team</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
';
    
    wp_mail($client_email, $client_subject, $client_message, $headers);
}

// Handle case intake form submission
function handle_nexuspros_case_intake() {
    // Check nonce for security
    check_ajax_referer('nexuspros_case_intake_nonce', '_ajax_nonce');

    $uploaded_files = [];
    $current_user = wp_get_current_user();
    $user_id      = $current_user->ID;
    $user_email   = $current_user->user_email;
    $user_name    = $current_user->display_name;

    
    // Handle file upload if any file is provided
    if (!empty($_FILES['documents']['name'][0])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        foreach ($_FILES['documents']['name'] as $key => $filename) {
            $file = [
                'name'     => $_FILES['documents']['name'][$key],
                'type'     => $_FILES['documents']['type'][$key],
                'tmp_name' => $_FILES['documents']['tmp_name'][$key],
                'error'    => $_FILES['documents']['error'][$key],
                'size'     => $_FILES['documents']['size'][$key]
            ];

            // Upload to WordPress media library
            $upload = wp_handle_upload($file, ['test_form' => false]);

            if (!isset($upload['error']) && isset($upload['url'])) {
                $uploaded_files[] = $upload['url'];
            }
        }
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'nexuspros_cases_intake_forms';

    // Combine uploaded file URLs as JSON
    $document_paths = json_encode($uploaded_files);


    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
        wp_send_json_error('Database table does not exist. Please deactivate and reactivate the plugin.');
    }

    // Sanitize form data before inserting into the database
    $data = [
        'case_id' => sanitize_text_field($_POST['case_id'] ?? uniqid()), 
        'case_status' => sanitize_text_field($_POST['case_status'] ?? 'pending'),
        
        // Package Information
        'case_package_type' => sanitize_text_field($_POST['package'] ?? ''),
        'case_package_price' => sanitize_text_field($_POST['package_price'] ?? ''),
        'case_priority' => sanitize_text_field($_POST['case_priority'] ?? ''),
        
        // Payment Information
        'payment_status' => sanitize_text_field($_POST['payment_status'] ?? 'pending'),
        'payment_date' => sanitize_text_field($_POST['payment_date'] ?? ''),
        'payment_amount' => sanitize_text_field($_POST['payment_amount'] ?? ''),
        'payment_method' => sanitize_text_field($_POST['payment_method'] ?? ''),

        // user information
        'user_id'    => $user_id,
        'user_email' => $user_email,
        'user_name'  => $user_name,

        
        // Client Information
        'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
        'last_name' => sanitize_text_field($_POST['last_name'] ?? ''),
        'email' => sanitize_email($_POST['email'] ?? ''),
        'phone' => sanitize_text_field($_POST['phone'] ?? ''),
        'birth_date' => sanitize_text_field($_POST['dob'] ?? ''), 
        'social_security_number' => sanitize_text_field($_POST['ssn'] ?? ''),
        'address' => sanitize_text_field($_POST['address'] ?? ''),
        'city' => sanitize_text_field($_POST['city'] ?? ''),
        'state' => sanitize_text_field($_POST['state'] ?? ''),
        'zip' => sanitize_text_field($_POST['zip_code'] ?? ''), 
        
        // Military Information
        'branch_of_service' => sanitize_text_field($_POST['branch'] ?? ''), 
        'final_rank' => sanitize_text_field($_POST['rank'] ?? ''),
        'start_date' => sanitize_text_field($_POST['service_start'] ?? ''), 
        'end_date' => sanitize_text_field($_POST['service_end'] ?? ''), 
        'va_file_number' => sanitize_text_field($_POST['va_file_number'] ?? ''),
        
        // Medical Information
        'current_medical_conditions' => sanitize_textarea_field($_POST['current_conditions'] ?? ''),
        'service_connected_conditions' => sanitize_textarea_field($_POST['service_connected'] ?? ''),
        'medical_history' => sanitize_textarea_field($_POST['medical_history'] ?? ''),
        'current_treatment' => sanitize_textarea_field($_POST['current_treatment'] ?? ''),
        
        // Document Information
        'client_document_paths' => $document_paths,
        'admin_document_paths' => null,
        'consent_data_collection' => isset($_POST['data_consent']) ? 1 : 0,
        'consent_privacy_policy' => isset($_POST['privacy_consent']) ? 1 : 0,
        'consent_communication' => isset($_POST['communication_consent']) ? 1 : 0
    ];


    // Insert data into the database
    $inserted = $wpdb->insert($table_name, $data);

    // Check if insert was successful
    if ($inserted === false) {
        wp_send_json_error('Database insert failed: ' . $wpdb->last_error);
        wp_die();
    }

    // Send email notification
    send_intake_form_email($data, $uploaded_files);
    
    // Create notification
    create_notification(
        'intake_form',
        'New Case Submitted',
        'A new case #' . $data['case_id'] . ' has been submitted by ' . $data['first_name'] . ' ' . $data['last_name'],
        $data['case_id'],
        $data['user_id']
    );

    // Return success response with case_id
    wp_send_json_success([
        'message' => 'Case submitted successfully.',
        'case_id' => $data['case_id']
    ]);
    wp_die();
}





// -------------------- HANDLE PAYMENT --------------------
add_action('wp_ajax_handle_stripe_payment', 'handle_stripe_payment');
add_action('wp_ajax_nopriv_handle_stripe_payment', 'handle_stripe_payment');

// -------------------- HANDLE STRIPE PAYMENT --------------------
function handle_stripe_payment() {
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'nexuspros_case_intake_nonce')) {
        wp_send_json_error('Invalid security token');
    }

    $secret_key = 'sk_test_51RmpJyIh3wZtrEkrvOren5jnJ6RLAnp2kvFU8PmDcTo6My2fjqusBdkI1zqaIEuMDIPgFQpFO0LM3lAAiVqghhQf008C9QAWVz';
    if (empty($secret_key)) {
        wp_send_json_error(['message' => 'Stripe secret key is not set.']);
    }

    require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey($secret_key);

    $amount   = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $caseid_id = isset($_POST['caseid_id']) ? sanitize_text_field($_POST['caseid_id']) : '';
    $type     = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
    $email    = isset($_POST['customer_email']) ? sanitize_email($_POST['customer_email']) : '';

    // Validations
    if ($amount <= 0) {
        wp_send_json_error(['message' => 'Invalid amount']);
    }
    if (empty($email)) {
        wp_send_json_error(['message' => 'Email address is required']);
    }
    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Invalid email address']);
    }

    $amount_cents = intval($amount * 100);

    try {
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount'   => $amount_cents,
            'currency' => 'nzd',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => [
                'caseid_id'       => $caseid_id,
                'type'           => $type,
                'customer_email' => $email,
            ],
            'receipt_email' => $email,
        ]);

        wp_send_json_success([
            'client_secret'   => $paymentIntent->client_secret,
            'payment_intent'  => $paymentIntent->id
        ]);
    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }
}



/**
 * Send payment confirmation email
 */
function send_payment_confirmation_email($case, $payment_amount) {
    $admin_email = get_option('admin_email');
    $client_email = $case->email;
    $site_name = get_bloginfo('name');
    
    // Format payment amount
    $formatted_amount = '$' . number_format(floatval($payment_amount), 2);
    
    // Format documents
    $client_documents = !empty($case->client_document_paths) ? json_decode($case->client_document_paths, true) : [];
    $admin_documents = !empty($case->admin_document_paths) ? json_decode($case->admin_document_paths, true) : [];
    
    $client_docs_list = '';
    if (!empty($client_documents)) {
        foreach ($client_documents as $doc) {
            $filename = basename($doc);
            $client_docs_list .= "• " . $filename . "\n";
        }
    } else {
        $client_docs_list = "No client documents uploaded\n";
    }
    
    $admin_docs_list = '';
    if (!empty($admin_documents)) {
        foreach ($admin_documents as $doc) {
            $filename = basename($doc);
            $admin_docs_list .= "• " . $filename . "\n";
        }
    } else {
        $admin_docs_list = "No admin documents available\n";
    }
    
    // Admin email
    $admin_subject = "Payment Received - Case #" . $case->case_id . " - " . $formatted_amount . " - " . $site_name;
    
    // Admin email (HTML template)
    $admin_message = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Payment Received - Case #' . $case->case_id . '</title>
</head>
<body style="margin:0;padding:0;background:#f4f8fb;font-family:Segoe UI,Arial,sans-serif;">
  <table width="100%" bgcolor="#f4f8fb" cellpadding="0" cellspacing="0" style="padding:40px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,0.07);overflow:hidden;">
          <tr>
            <td style="background:linear-gradient(90deg,#2563eb 0%,#06b6d4 100%);padding:32px 0;text-align:center;">
              <h1 style="color:#fff;font-size:2rem;margin:0;letter-spacing:1px;">💳 Payment Received</h1>
              <p style="color:#e0f2fe;font-size:1.1rem;margin:8px 0 0 0;">A payment has been received for Case #' . esc_html($case->case_id) . '</p>
            </td>
          </tr>
          <tr>
            <td style="padding:32px;">
              <h2 style="color:#2563eb;font-size:1.2rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Payment Information</h2>
              <table width="100%" style="font-size:1rem;color:#222;margin-bottom:18px;">
                <tr><td><strong>Case ID:</strong></td><td>' . esc_html($case->case_id) . '</td></tr>
                <tr><td><strong>Payment Amount:</strong></td><td>' . esc_html($formatted_amount) . '</td></tr>
                <tr><td><strong>Payment Date:</strong></td><td>' . esc_html(date('F j, Y g:i A', strtotime($case->payment_date))) . '</td></tr>
                <tr><td><strong>Payment Status:</strong></td><td>' . esc_html(ucfirst($case->payment_status)) . '</td></tr>
                <tr><td><strong>Case Status:</strong></td><td>' . esc_html(ucfirst($case->case_status)) . '</td></tr>
              </table>

              <h2 style="color:#06b6d4;font-size:1.1rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Client Information</h2>
              <table width="100%" style="font-size:1rem;color:#222;margin-bottom:18px;">
                <tr><td><strong>Name:</strong></td><td>' . esc_html($case->first_name . ' ' . $case->last_name) . '</td></tr>
                <tr><td><strong>Email:</strong></td><td>' . esc_html($case->email) . '</td></tr>
                <tr><td><strong>Phone:</strong></td><td>' . esc_html($case->phone) . '</td></tr>
                <tr><td><strong>Date of Birth:</strong></td><td>' . ($case->birth_date ? esc_html(date('F j, Y', strtotime($case->birth_date))) : 'Not provided') . '</td></tr>
                <tr><td><strong>Address:</strong></td><td>' . esc_html($case->address . ', ' . $case->city . ', ' . $case->state . ' ' . $case->zip) . '</td></tr>
              </table>

              <h2 style="color:#2563eb;font-size:1.1rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Case Details</h2>
              <table width="100%" style="font-size:1rem;color:#222;margin-bottom:18px;">
                <tr><td><strong>Package Type:</strong></td><td>' . esc_html($case->case_package_type ?: 'Not specified') . '</td></tr>
                <tr><td><strong>Package Price:</strong></td><td>$' . number_format(floatval($case->case_package_price ?: 0), 2) . '</td></tr>
                <tr><td><strong>Priority:</strong></td><td>' . esc_html(ucfirst($case->case_priority ?: 'Standard')) . '</td></tr>
                <tr><td><strong>Created Date:</strong></td><td>' . esc_html(date('F j, Y g:i A', strtotime($case->created_at))) . '</td></tr>
              </table>

              <h2 style="color:#06b6d4;font-size:1.1rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Military Information</h2>
              <table width="100%" style="font-size:1rem;color:#222;margin-bottom:18px;">
                <tr><td><strong>Branch of Service:</strong></td><td>' . esc_html($case->branch_of_service ?: 'Not specified') . '</td></tr>
                <tr><td><strong>Final Rank:</strong></td><td>' . esc_html($case->final_rank ?: 'Not specified') . '</td></tr>
                <tr><td><strong>Service Period:</strong></td><td>' . ($case->start_date ? esc_html(date('F j, Y', strtotime($case->start_date))) : 'Not specified') . ' - ' . ($case->end_date ? esc_html(date('F j, Y', strtotime($case->end_date))) : 'Not specified') . '</td></tr>
                <tr><td><strong>VA File Number:</strong></td><td>' . esc_html($case->va_file_number ?: 'Not specified') . '</td></tr>
              </table>

              <h2 style="color:#2563eb;font-size:1.1rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Medical Information</h2>
              <table width="100%" style="font-size:1rem;color:#222;margin-bottom:18px;">
                <tr><td><strong>Current Medical Conditions:</strong></td><td>' . nl2br(esc_html($case->current_medical_conditions ?: 'Not specified')) . '</td></tr>
                <tr><td><strong>Service Connected Conditions:</strong></td><td>' . nl2br(esc_html($case->service_connected_conditions ?: 'Not specified')) . '</td></tr>
                <tr><td><strong>Medical History:</strong></td><td>' . nl2br(esc_html($case->medical_history ?: 'Not specified')) . '</td></tr>
                <tr><td><strong>Current Treatment:</strong></td><td>' . nl2br(esc_html($case->current_treatment ?: 'Not specified')) . '</td></tr>
              </table>

              <h2 style="color:#06b6d4;font-size:1.1rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Documents</h2>
              <div style="margin-bottom:18px;">
                <strong>Client Documents:</strong>
                <pre style="background:#f8fafc;border-radius:6px;padding:10px 14px;color:#2563eb;font-size:1rem;white-space:pre-wrap;">' . esc_html($client_docs_list) . '</pre>
                <strong>Admin Documents:</strong>
                <pre style="background:#f8fafc;border-radius:6px;padding:10px 14px;color:#06b6d4;font-size:1rem;white-space:pre-wrap;">' . esc_html($admin_docs_list) . '</pre>
              </div>

              <div style="background:#f0fdfa;border-radius:8px;padding:18px 24px;margin-bottom:24px;">
                <h3 style="color:#06b6d4;font-size:1rem;margin:0 0 8px 0;">Next Steps</h3>
                <ul style="color:#2563eb;font-size:1rem;margin:0 0 0 18px;padding:0;">
                  <li>The case is now in <strong>processing</strong> status</li>
                  <li>Please begin work on this case</li>
                </ul>
              </div>
              <p style="color:#64748b;font-size:1rem;margin:32px 0 0 0;">Best regards,<br>' . esc_html($site_name) . ' System</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
';

    // Client email (HTML template)
    $client_subject = "Payment Confirmation - Case #" . $case->case_id . " - " . $formatted_amount;
    
    $client_message = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Payment Confirmation - Case #' . $case->case_id . '</title>
</head>
<body style="margin:0;padding:0;background:#f4f8fb;font-family:Segoe UI,Arial,sans-serif;">
  <table width="100%" bgcolor="#f4f8fb" cellpadding="0" cellspacing="0" style="padding:40px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,0.07);overflow:hidden;">
          <tr>
            <td style="background:linear-gradient(90deg,#06b6d4 0%,#2563eb 100%);padding:32px 0;text-align:center;">
              <h1 style="color:#fff;font-size:2rem;margin:0;letter-spacing:1px;">✅ Payment Confirmed</h1>
              <p style="color:#e0f2fe;font-size:1.1rem;margin:8px 0 0 0;">Thank you for your payment, ' . esc_html($case->first_name) . '!</p>
            </td>
          </tr>
          <tr>
            <td style="padding:32px;">
              <h2 style="color:#2563eb;font-size:1.1rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Payment Confirmation</h2>
              <table width="100%" style="font-size:1rem;color:#222;margin-bottom:18px;">
                <tr><td><strong>Case ID:</strong></td><td>' . esc_html($case->case_id) . '</td></tr>
                <tr><td><strong>Payment Amount:</strong></td><td>' . esc_html($formatted_amount) . '</td></tr>
                <tr><td><strong>Payment Date:</strong></td><td>' . esc_html(date('F j, Y g:i A', strtotime($case->payment_date))) . '</td></tr>
                <tr><td><strong>Package:</strong></td><td>' . esc_html($case->case_package_type ?: 'Not specified') . '</td></tr>
              </table>

              <div style="background:#f0fdfa;border-radius:8px;padding:18px 24px;margin-bottom:24px;">
                <h3 style="color:#06b6d4;font-size:1rem;margin:0 0 8px 0;">What\'s Next?</h3>
                <ul style="color:#2563eb;font-size:1rem;margin:0 0 0 18px;padding:0;">
                  <li>Your payment has been confirmed</li>
                  <li>Your case is now in <strong>processing</strong> status</li>
                  <li>Our team will begin working on your case immediately</li>
                  <li>You will receive updates as your case progresses</li>
                  <li>You can check your case status anytime in your client dashboard</li>
                </ul>
                <p style="color:#2563eb;font-size:1rem;margin:12px 0 0 0;"><strong>Estimated Processing Time:</strong> 3-5 business days</p>
              </div>

              <h2 style="color:#2563eb;font-size:1.1rem;margin-bottom:8px;border-bottom:2px solid #e0e7ef;padding-bottom:4px;">Need Help?</h2>
              <ul style="color:#374151;font-size:1rem;margin:0 0 0 18px;padding:0;">
                <li>Log into your client dashboard to view case status</li>
                <li>Contact our support team for assistance</li>
                <li>Keep this email as your payment receipt</li>
              </ul>
              <p style="color:#64748b;font-size:1rem;margin:32px 0 0 0;">Thank you for choosing ' . esc_html($site_name) . '!<br>Best regards,<br>' . esc_html($site_name) . ' Team</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
';

    // Headers for HTML email
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $site_name . ' <' . $admin_email . '>',
        'Reply-To: ' . $admin_email
    ];
    
    // Send emails
    wp_mail($admin_email, $admin_subject, $admin_message, $headers);
    wp_mail($client_email, $client_subject, $client_message, $headers);
}

add_action('wp_ajax_update_case_payment_status', 'update_case_payment_status');

function update_case_payment_status() {
    // Verify nonce for security
    check_ajax_referer('nexuspros_case_intake_nonce', 'nonce');

    global $wpdb;

    // Sanitize and validate inputs
    $case_id        = isset($_POST['case_id']) ? sanitize_text_field($_POST['case_id']) : '';
    $payment_amount = isset($_POST['payment_amount']) ? floatval($_POST['payment_amount']) : 0;
    $payment_date   = current_time('mysql'); // WordPress timezone

    // Debug logging
    error_log('Payment Status Update - Received data: ' . json_encode([
        'case_id' => $case_id,
        'payment_amount' => $payment_amount,
        'raw_post' => $_POST
    ]));

    if (empty($case_id) || $payment_amount <= 0) {
        wp_send_json_error(['message' => 'Invalid payment data: case_id=' . $case_id . ', amount=' . $payment_amount]);
    }

    $table = $wpdb->prefix . 'nexuspros_cases_intake_forms';

    // Check if case exists
    $case_exists = $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE case_id = %s", $case_id)
    );

    if (!$case_exists) {
        wp_send_json_error(['message' => 'Case not found with ID: ' . $case_id]);
    }

    // Update case payment info
    $updated = $wpdb->update(
        $table,
        [
            'case_status'    => 'processing',
            'payment_status' => 'paid',
            'payment_date'   => $payment_date,
            'payment_amount' => $payment_amount
        ],
        ['case_id' => $case_id],
        // Formats for the values being updated
        ['%s', '%s', '%s', '%f'],
        // Format for the WHERE condition
        ['%s']
    );

    if ($updated !== false) {
        // Get updated case details for email
        $case = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table WHERE case_id = %s", $case_id)
        );
        
        if ($case) {
            // Send payment confirmation emails
            send_payment_confirmation_email($case, $payment_amount);
            
            // Create notification
            create_notification(
                'payment',
                'Payment Received',
                'Payment of $' . number_format($payment_amount, 2) . ' received for case #' . $case_id,
                $case_id
            );
        }
        
        wp_send_json_success(['message' => 'Payment info updated successfully', 'rows_affected' => $updated]);
    } else {
        wp_send_json_error(['message' => 'Failed to update payment info: ' . $wpdb->last_error]);
    }
}


add_action('wp_ajax_get_case_details', 'get_case_details');
add_action('wp_ajax_nopriv_get_case_details', 'get_case_details');


// Handle case details retrieval
function get_case_details() {
    // Verify nonce for security
    check_ajax_referer('nexuspros_case_intake_nonce', '_ajax_nonce');

    global $wpdb;

    // Sanitize and validate inputs
    $case_id = isset($_POST['case_id']) ? sanitize_text_field($_POST['case_id']) : '';

    if (empty($case_id)) {
        wp_send_json_error(['message' => 'Case ID is required']);
    }

    $table = $wpdb->prefix . 'nexuspros_cases_intake_forms';

    // Get case details
    $case = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table WHERE case_id = %s", $case_id)
    );

    if (!$case) {
        wp_send_json_error(['message' => 'Case not found']);
    }

    // Parse document paths
    $client_documents = !empty($case->client_document_paths) ? json_decode($case->client_document_paths, true) : [];
    $admin_documents = !empty($case->admin_document_paths) ? json_decode($case->admin_document_paths, true) : [];

    // Prepare response data
    $response_data = [
        'case_id' => $case->case_id,
        'created_at' => $case->created_at,
        'case_status' => $case->case_status,
        'payment_status' => $case->payment_status,
        'payment_date' => $case->payment_date,
        'payment_amount' => $case->payment_amount,
        'case_package_type' => $case->case_package_type,
        'case_package_price' => $case->case_package_price,
        
        // Personal Information
        'first_name' => $case->first_name,
        'last_name' => $case->last_name,
        'email' => $case->email,
        'phone' => $case->phone,
        'birth_date' => $case->birth_date,
        'address' => $case->address,
        'city' => $case->city,
        'state' => $case->state,
        'zip' => $case->zip,
        
        // Military Information
        'branch_of_service' => $case->branch_of_service,
        'final_rank' => $case->final_rank,
        'start_date' => $case->start_date,
        'end_date' => $case->end_date,
        'va_file_number' => $case->va_file_number,
        
        // Medical Information
        'current_medical_conditions' => $case->current_medical_conditions,
        'service_connected_conditions' => $case->service_connected_conditions,
        'medical_history' => $case->medical_history,
        'current_treatment' => $case->current_treatment,
        
        // Documents
        'client_documents' => $client_documents,
        'admin_documents' => $admin_documents,
        
        // Consent
        'consent_data_collection' => $case->consent_data_collection,
        'consent_privacy_policy' => $case->consent_privacy_policy,
        'consent_communication' => $case->consent_communication
    ];

    wp_send_json_success($response_data);
}


add_action('wp_ajax_update_case_details', 'update_case_details');
// Handle case details update
function update_case_details() {
    // Verify nonce for security
    check_ajax_referer('nexuspros_case_intake_nonce', '_ajax_nonce');

    global $wpdb;

    // Sanitize and validate inputs
    $case_id = isset($_POST['case_id']) ? sanitize_text_field($_POST['case_id']) : '';

    if (empty($case_id)) {
        wp_send_json_error(['message' => 'Case ID is required']);
    }

    $table = $wpdb->prefix . 'nexuspros_cases_intake_forms';

    // Check if case exists
    $case_exists = $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE case_id = %s", $case_id)
    );

    if (!$case_exists) {
        wp_send_json_error(['message' => 'Case not found']);
    }

    // Prepare update data
    $update_data = [];
    $format = [];

    // Case and Payment Status
    if (isset($_POST['case_status'])) {
        $update_data['case_status'] = sanitize_text_field($_POST['case_status']);
        $format[] = '%s';
    }

    if (isset($_POST['payment_status'])) {
        $update_data['payment_status'] = sanitize_text_field($_POST['payment_status']);
        $format[] = '%s';
    }

    // Package Information
    if (isset($_POST['case_package_type'])) {
        $update_data['case_package_type'] = sanitize_text_field($_POST['case_package_type']);
        $format[] = '%s';
    }

    if (isset($_POST['case_package_price'])) {
        $update_data['case_package_price'] = sanitize_text_field($_POST['case_package_price']);
        $format[] = '%s';
    }

    if (isset($_POST['payment_amount'])) {
        $update_data['payment_amount'] = sanitize_text_field($_POST['payment_amount']);
        $format[] = '%s';
    }

    if (isset($_POST['payment_date'])) {
        $update_data['payment_date'] = sanitize_text_field($_POST['payment_date']);
        $format[] = '%s';
    }

    // Personal Information
    if (isset($_POST['first_name'])) {
        $update_data['first_name'] = sanitize_text_field($_POST['first_name']);
        $format[] = '%s';
    }

    if (isset($_POST['last_name'])) {
        $update_data['last_name'] = sanitize_text_field($_POST['last_name']);
        $format[] = '%s';
    }

    if (isset($_POST['email'])) {
        $update_data['email'] = sanitize_email($_POST['email']);
        $format[] = '%s';
    }

    if (isset($_POST['phone'])) {
        $update_data['phone'] = sanitize_text_field($_POST['phone']);
        $format[] = '%s';
    }

    if (isset($_POST['birth_date'])) {
        $update_data['birth_date'] = sanitize_text_field($_POST['birth_date']);
        $format[] = '%s';
    }

    if (isset($_POST['address'])) {
        $update_data['address'] = sanitize_text_field($_POST['address']);
        $format[] = '%s';
    }

    if (isset($_POST['city'])) {
        $update_data['city'] = sanitize_text_field($_POST['city']);
        $format[] = '%s';
    }

    if (isset($_POST['state'])) {
        $update_data['state'] = sanitize_text_field($_POST['state']);
        $format[] = '%s';
    }

    if (isset($_POST['zip'])) {
        $update_data['zip'] = sanitize_text_field($_POST['zip']);
        $format[] = '%s';
    }

    // Military Information
    if (isset($_POST['branch_of_service'])) {
        $update_data['branch_of_service'] = sanitize_text_field($_POST['branch_of_service']);
        $format[] = '%s';
    }

    if (isset($_POST['final_rank'])) {
        $update_data['final_rank'] = sanitize_text_field($_POST['final_rank']);
        $format[] = '%s';
    }

    if (isset($_POST['start_date'])) {
        $update_data['start_date'] = sanitize_text_field($_POST['start_date']);
        $format[] = '%s';
    }

    if (isset($_POST['end_date'])) {
        $update_data['end_date'] = sanitize_text_field($_POST['end_date']);
        $format[] = '%s';
    }

    if (isset($_POST['va_file_number'])) {
        $update_data['va_file_number'] = sanitize_text_field($_POST['va_file_number']);
        $format[] = '%s';
    }

    // Medical Information
    if (isset($_POST['current_medical_conditions'])) {
        $update_data['current_medical_conditions'] = sanitize_textarea_field($_POST['current_medical_conditions']);
        $format[] = '%s';
    }

    if (isset($_POST['service_connected_conditions'])) {
        $update_data['service_connected_conditions'] = sanitize_textarea_field($_POST['service_connected_conditions']);
        $format[] = '%s';
    }

    if (isset($_POST['medical_history'])) {
        $update_data['medical_history'] = sanitize_textarea_field($_POST['medical_history']);
        $format[] = '%s';
    }

    if (isset($_POST['current_treatment'])) {
        $update_data['current_treatment'] = sanitize_textarea_field($_POST['current_treatment']);
        $format[] = '%s';
    }

    // Update the case
    if (!empty($update_data)) {
        $updated = $wpdb->update(
            $table,
            $update_data,
            ['case_id' => $case_id],
            $format,
            ['%s']
        );

        if ($updated !== false) {
            // Create notification for case update
            create_notification(
                'case_update',
                'Case Updated',
                'Case #' . $case_id . ' details have been updated by admin',
                $case_id
            );
            
            wp_send_json_success(['message' => 'Case updated successfully', 'rows_affected' => $updated]);
        } else {
            wp_send_json_error(['message' => 'Failed to update case: ' . $wpdb->last_error]);
        }
    } else {
        wp_send_json_error(['message' => 'No data provided for update']);
    }
}



// Add AJAX handlers for rating
add_action('wp_ajax_submit_case_rating', 'submit_case_rating');
add_action('wp_ajax_nopriv_submit_case_rating', 'submit_case_rating');

// Add AJAX handlers for database test
add_action('wp_ajax_test_database', 'test_database_connection');
add_action('wp_ajax_nopriv_test_database', 'test_database_connection');

// Add AJAX handlers for notifications
add_action('wp_ajax_get_notifications', 'get_notifications');
add_action('wp_ajax_add_notification', 'add_notification');
add_action('wp_ajax_mark_notifications_read', 'mark_notifications_read');
add_action('wp_ajax_clear_notifications', 'clear_notifications');

/**
 * Test database connection and table
 */
function test_database_connection() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['_ajax_nonce'], 'nexuspros_case_intake_nonce')) {
        wp_send_json_error(['message' => 'Security check failed']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'nexuspros_cases_intake_forms';
    
    try {
        // Test 1: Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name;
        
        if (!$table_exists) {
            // Table doesn't exist, try to create it
            $created = create_nexuspros_database_table();
            
            if ($created) {
                wp_send_json_success([
                    'message' => '✅ Database table was missing but has been created successfully!',
                    'action' => 'table_created'
                ]);
            } else {
                wp_send_json_error([
                    'message' => '❌ Database table is missing and could not be created. Please check permissions.',
                    'action' => 'creation_failed'
                ]);
            }
            return;
        }
        
        // Test 2: Check table structure
        $columns = $wpdb->get_results("DESCRIBE {$table_name}");
        if (empty($columns)) {
            wp_send_json_error([
                'message' => '❌ Database table exists but has structural issues.',
                'action' => 'structure_error'
            ]);
            return;
        }
        
        // Test 3: Try to perform a simple query
        $test_query = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
        if ($test_query === null && $wpdb->last_error) {
            wp_send_json_error([
                'message' => '❌ Database table exists but queries are failing: ' . $wpdb->last_error,
                'action' => 'query_error'
            ]);
            return;
        }
        
        // Test 4: Check for required columns (especially rating columns)
        $required_columns = ['case_id', 'email', 'first_name', 'last_name', 'rating', 'rating_comment', 'rating_date'];
        $existing_columns = array_column($columns, 'Field');
        $missing_columns = array_diff($required_columns, $existing_columns);
        
        if (!empty($missing_columns)) {
            // Try to add missing columns
            $added_columns = add_missing_database_columns($missing_columns);
            
            if ($added_columns) {
                wp_send_json_success([
                    'message' => '✅ Database OK! Missing columns were added: ' . implode(', ', $missing_columns),
                    'action' => 'columns_added',
                    'total_cases' => intval($test_query)
                ]);
            } else {
                wp_send_json_error([
                    'message' => '⚠️ Database table exists but is missing columns: ' . implode(', ', $missing_columns),
                    'action' => 'missing_columns'
                ]);
            }
            return;
        }
        
        // All tests passed
        wp_send_json_success([
            'message' => '✅ Database OK! Table exists and is working properly. Total cases: ' . intval($test_query),
            'action' => 'all_good',
            'total_cases' => intval($test_query),
            'total_columns' => count($columns)
        ]);
        
    } catch (Exception $e) {
        wp_send_json_error([
            'message' => '❌ Database error: ' . $e->getMessage(),
            'action' => 'exception'
        ]);
    }
}

/**
 * Create the database table
 */
function create_nexuspros_database_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'nexuspros_cases_intake_forms';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        
        -- Case Information
        case_id varchar(100) NOT NULL UNIQUE,
        case_status varchar(100) DEFAULT 'pending',
        rating INT DEFAULT NULL,
        rating_comment text,
        rating_date datetime DEFAULT NULL,
        
        -- Package Information
        case_package_type varchar(100) NOT NULL,
        case_package_price varchar(100) NOT NULL,
        case_priority varchar(100) NOT NULL,
        
        -- Payment Information
        payment_status varchar(100) DEFAULT 'pending',
        payment_date date DEFAULT NULL,
        payment_amount varchar(100) DEFAULT NULL,
        payment_method varchar(100) DEFAULT NULL,

        -- user information
        user_id varchar(100) NOT NULL,
        user_name varchar(100) NOT NULL,
        user_email varchar(150) NOT NULL,
        
        -- Client Information
        first_name varchar(100) NOT NULL,
        last_name varchar(100) NOT NULL,
        email varchar(150) NOT NULL,
        phone varchar(30) NOT NULL,
        birth_date date NOT NULL,
        social_security_number varchar(100) NOT NULL,
        address varchar(255) NOT NULL,
        city varchar(100) NOT NULL,
        state varchar(50) NOT NULL,
        zip varchar(20) NOT NULL,
        
        -- Military Information
        branch_of_service varchar(100),
        final_rank varchar(50),
        start_date date,
        end_date date,
        va_file_number varchar(50),
        
        -- Medical Information
        current_medical_conditions text,
        service_connected_conditions text,
        medical_history text,
        current_treatment text,
        
        -- Document Information
        client_document_paths text,
        admin_document_paths text,
        consent_data_collection tinyint(1) DEFAULT 0,
        consent_privacy_policy tinyint(1) DEFAULT 0,
        consent_communication tinyint(1) DEFAULT 0,
        
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $result = dbDelta($sql);
    
    // Check if table was created successfully
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name;
    return $table_exists;
}

/**
 * Add missing database columns
 */
function add_missing_database_columns($missing_columns) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'nexuspros_cases_intake_forms';
    
    $success = true;
    
    foreach ($missing_columns as $column) {
        switch ($column) {
            case 'rating':
                $result = $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN rating INT DEFAULT NULL");
                break;
            case 'rating_comment':
                $result = $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN rating_comment TEXT");
                break;
            case 'rating_date':
                $result = $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN rating_date DATETIME DEFAULT NULL");
                break;
            default:
                // Skip unknown columns
                continue 2;
        }
        
        if ($result === false) {
            $success = false;
        }
    }
    
    return $success;
}

/**
 * Create notifications table
 */
function create_notifications_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'nexuspros_notifications';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        type varchar(50) NOT NULL,
        title varchar(255) NOT NULL,
        message text NOT NULL,
        is_read tinyint(1) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        user_id bigint(20) DEFAULT NULL,
        case_id varchar(100) DEFAULT NULL,
        
        PRIMARY KEY (id),
        KEY type (type),
        KEY is_read (is_read),
        KEY created_at (created_at)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Get notifications
 */
function get_notifications() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['_ajax_nonce'], 'nexuspros_case_intake_nonce')) {
        wp_send_json_error(['message' => 'Security check failed']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'nexuspros_notifications';
    
    // Get latest 20 notifications
    $notifications = $wpdb->get_results(
        "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 20",
        ARRAY_A
    );
    
    wp_send_json_success(['notifications' => $notifications]);
}

/**
 * Add notification
 */
function add_notification() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['_ajax_nonce'], 'nexuspros_case_intake_nonce')) {
        wp_send_json_error(['message' => 'Security check failed']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'nexuspros_notifications';
    
    $type = sanitize_text_field($_POST['type']);
    $title = sanitize_text_field($_POST['title']);
    $message = sanitize_textarea_field($_POST['message']);
    $case_id = isset($_POST['case_id']) ? sanitize_text_field($_POST['case_id']) : null;
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
    
    $result = $wpdb->insert(
        $table_name,
        [
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'case_id' => $case_id,
            'user_id' => $user_id,
            'is_read' => 0
        ],
        ['%s', '%s', '%s', '%s', '%d', '%d']
    );
    
    if ($result !== false) {
        wp_send_json_success(['message' => 'Notification added']);
    } else {
        wp_send_json_error(['message' => 'Failed to add notification']);
    }
}

/**
 * Mark notifications as read
 */
function mark_notifications_read() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['_ajax_nonce'], 'nexuspros_case_intake_nonce')) {
        wp_send_json_error(['message' => 'Security check failed']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'nexuspros_notifications';
    
    $result = $wpdb->update(
        $table_name,
        ['is_read' => 1],
        ['is_read' => 0],
        ['%d'],
        ['%d']
    );
    
    wp_send_json_success(['message' => 'Notifications marked as read']);
}

/**
 * Clear all notifications
 */
function clear_notifications() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['_ajax_nonce'], 'nexuspros_case_intake_nonce')) {
        wp_send_json_error(['message' => 'Security check failed']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'nexuspros_notifications';
    
    $result = $wpdb->query("DELETE FROM $table_name");
    
    wp_send_json_success(['message' => 'All notifications cleared']);
}

/**
 * Helper function to create notification
 */
function create_notification($type, $title, $message, $case_id = null, $user_id = null) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'nexuspros_notifications';
    
    $wpdb->insert(
        $table_name,
        [
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'case_id' => $case_id,
            'user_id' => $user_id,
            'is_read' => 0
        ],
        ['%s', '%s', '%s', '%s', '%d']
    );
}

/**
 * Handle case rating submission
 */
function submit_case_rating() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['_ajax_nonce'], 'nexuspros_case_intake_nonce')) {
        wp_send_json_error(['message' => 'Security check failed']);
    }

    global $wpdb;
    $table = $wpdb->prefix . 'nexuspros_cases_intake_forms';

    // Get and sanitize data
    $case_id = sanitize_text_field($_POST['case_id']);
    $rating = intval($_POST['rating']);

    // Validate rating
    if ($rating < 1 || $rating > 5) {
        wp_send_json_error(['message' => 'Invalid rating value']);
    }

    // Check if case exists and belongs to current user
    $current_user = wp_get_current_user();
    $case = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE case_id = %s AND (user_id = %s OR user_email = %s)",
        $case_id,
        $current_user->ID,
        $current_user->user_email
    ));

    if (!$case) {
        wp_send_json_error(['message' => 'Case not found or access denied']);
    }

    // Check if case is completed
    if ($case->case_status !== 'completed') {
        wp_send_json_error(['message' => 'Only completed cases can be rated']);
    }

    // Check if already rated
    if (!empty($case->rating)) {
        wp_send_json_error(['message' => 'This case has already been rated']);
    }

    // Update the case with rating
    $updated = $wpdb->update(
        $table,
        [
            'rating' => $rating,
            'rating_date' => current_time('mysql')
        ],
        ['case_id' => $case_id],
        ['%d', '%s'],
        ['%s']
    );

    if ($updated !== false) {
        // Create notification for rating submission
        create_notification(
            'rating',
            'New Rating Received',
            'Case #' . $case_id . ' received a ' . $rating . '-star rating from ' . $case->first_name . ' ' . $case->last_name,
            $case_id,
            $current_user->ID
        );
        
        wp_send_json_success(['message' => 'Rating submitted successfully']);
    } else {
        wp_send_json_error(['message' => 'Failed to submit rating: ' . $wpdb->last_error]);
    }
}



/**
 * Update database to add rating fields
 */
function update_database_for_ratings() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'nexuspros_cases_intake_forms';
    
    // Check if rating column exists
    $rating_exists = $wpdb->get_results("SHOW COLUMNS FROM {$table_name} LIKE 'rating'");
    if (empty($rating_exists)) {
        $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN rating INT DEFAULT NULL");
    }

    
    // Check if rating_date column exists
    $date_exists = $wpdb->get_results("SHOW COLUMNS FROM {$table_name} LIKE 'rating_date'");
    if (empty($date_exists)) {
        $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN rating_date DATETIME DEFAULT NULL");
    }
}

// Run database update on plugin load
add_action('init', 'update_database_for_ratings');

// Include dashboards
include CODEX_PLUGIN_DIR . 'includes/client-dashboard.php';
include CODEX_PLUGIN_DIR . 'includes/admin-dashboard.php';
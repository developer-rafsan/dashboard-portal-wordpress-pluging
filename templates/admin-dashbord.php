<?php
    defined('ABSPATH') or die('No direct access.');

    global $wpdb;
    $table_name = $wpdb->prefix . 'nexuspros_cases_intake_forms';
    
    $results = $wpdb->get_results( "SELECT * FROM {$table_name}" );

    $total_cases = $active_cases = $completed_cases = $unpaid = $requested_cases = [];

    // Calculate monthly revenue
    $current_month = date('Y-m');
    $monthly_revenue = 0;
    $total_revenue = 0;
    $ratings = [];
    $average_rating = 0;

    if ($results) {
        foreach ($results as $row) {
            $total_cases[] = $row;
            if ($row->case_status === 'processing' || $row-> case_status === 'paid') $active_cases[] = $row;
            if ($row->case_status === 'completed' && $row->payment_status === 'paid') $completed_cases[] = $row;
            if ($row->case_status === 'pending' || $row->case_status === 'approved') $requested_cases[] = $row;
            if ($row->payment_status === 'pending') $unpaid[] = $row;
            
            // Calculate revenue
            if ($row->payment_status === 'paid' || $row->payment_status === 'completed') {
                $payment_amount = floatval($row->payment_amount ?: $row->case_package_price ?: 0);
                $total_revenue += $payment_amount;
                
                // Check if payment was made this month
                $payment_date = $row->payment_date ?: $row->created_at;
                if (date('Y-m', strtotime($payment_date)) === $current_month) {
                    $monthly_revenue += $payment_amount;
                }
            }
            
            // Collect ratings (assuming rating field exists)
            if (!empty($row->rating) && is_numeric($row->rating)) {
                $ratings[] = floatval($row->rating);
            }
        }
    }
    
    // Calculate average rating
    if (!empty($ratings)) {
        $average_rating = round(array_sum($ratings) / count($ratings), 1);
    }
    
    // Format revenue for display
    $monthly_revenue_formatted = '$' . number_format($monthly_revenue, 2);
    $total_revenue_formatted = '$' . number_format($total_revenue, 2);
?>


<!-- Page template for the admin dashboard -->
<div id="admin-dashboard">
    <div class="m-4 shadow-md p-8 rounded-lg bg-white">

        <!-- dashboard header -->
        <div class="flex justify-between items-center">
            <div>
                <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 10px; text-transform: capitalize;"> Nexus pro
                    Admin Dashboard</h2>
                <p class="text-gray-600 mb-6 ">Comprehensive system administration - Manage cases, clients, team, and
                    analytics
                </p>
            </div>
            <div class="flex justify-center items-center gap-4">
                <!-- Notification Bell -->
                <div class="relative">
                    <div id="notification-btn" class="relative cursor-pointer p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fa-solid fa-bell text-xl text-gray-600"></i>
                        <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                    </div>
                    
                    <!-- Notification Dropdown -->
                    <div id="notification-dropdown" class="absolute right-0 top-12 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 hidden">
                        <!-- Header -->
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="font-semibold text-gray-900">Notifications</h3>
                            <button id="mark-all-read" class="text-sm text-blue-600 hover:text-blue-800">Mark all read</button>
                        </div>
                        
                        <!-- Notifications List -->
                        <div id="notifications-list" class="max-h-64 overflow-y-auto">
                            <div class="p-4 text-center text-gray-500">
                                <i class="fa-solid fa-bell-slash text-2xl mb-2"></i>
                                <p>No new notifications</p>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="p-3 border-t border-gray-200 text-center">
                            <button id="clear-notifications" class="text-sm text-red-600 hover:text-red-800">Clear all notifications</button>
                        </div>
                    </div>
                </div>

                <button id="test-db-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-database"></i>
                    Test DB
                </button>
            </div>
        </div>
        
        <!-- Database Test Notification -->
        <div id="db-notification" class="hidden mb-4 p-4 rounded-lg"></div>


        <hr class="my-4 border-gray-200" />

        <!-- dashboard cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 w-full md:w-6/12 lg:w-9/12 rounded-lg my-4">
            <!-- Total Cases Card -->
            <div class="p-4 rounded-lg bg-gradient-to-r from-blue-50 to-blue-100 shadow-md flex flex-col justify-center gap-4 border border-blue-200">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-500 rounded-lg">
                        <i class="fa-solid fa-file-lines text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm text-gray-600 font-medium">Total Cases</h3>
                        <p class="text-3xl font-bold text-blue-700">
                            <?php echo empty($total_cases) ? '0' : str_pad(count($total_cases), 2, '0', STR_PAD_LEFT); ?>
                        </p>
                        <p class="text-xs text-gray-500">All time cases</p>
                    </div>
                </div>
            </div>

            <!-- Active Cases Card -->
            <div class="p-4 rounded-lg bg-gradient-to-r from-green-50 to-green-100 shadow-md flex flex-col justify-center gap-4 border border-green-200">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-green-500 rounded-lg">
                        <i class="fa-solid fa-users text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm text-gray-600 font-medium">Active Cases</h3>
                        <p class="text-3xl font-bold text-green-700">
                            <?php echo empty($active_cases) ? '0' : str_pad(count($active_cases), 2, '0', STR_PAD_LEFT); ?>
                        </p>
                        <p class="text-xs text-gray-500">Currently processing</p>
                    </div>
                </div>
            </div>

            <!-- Unpaid Cases Card -->
            <div class="p-4 rounded-lg bg-gradient-to-r from-red-50 to-red-100 shadow-md flex flex-col justify-center gap-4 border border-red-200">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-red-500 rounded-lg">
                        <i class="fa-solid fa-money-bill text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm text-gray-600 font-medium">Unpaid Cases</h3>
                        <p class="text-3xl font-bold text-red-700">
                            <?php echo empty($unpaid) ? '0' : str_pad(count($unpaid), 2, '0', STR_PAD_LEFT); ?>
                        </p>
                        <p class="text-xs text-gray-500">Pending payment</p>
                    </div>
                </div>
            </div>

            <!-- Requested Cases Card -->
            <div class="p-4 rounded-lg bg-gradient-to-r from-green-50 to-green-100 shadow-md flex flex-col justify-center gap-4 border border-green-200">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-green-500 rounded-lg">
                        <i class="fa-solid fa-clock text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm text-gray-600 font-medium">Requested Cases</h3>
                        <p class="text-3xl font-bold text-orange-700">
                            <?php echo empty($requested_cases) ? '0' : str_pad(count($requested_cases), 2, '0', STR_PAD_LEFT); ?>
                        </p>
                        <p class="text-xs text-gray-500">Awaiting approval</p>
                    </div>
                </div>
            </div>
            
            <!-- Monthly Revenue Card -->
            <div style="height: 100px; grid-column-start: 1; grid-column-end: 3;" class="p-4 rounded-lg bg-gradient-to-r from-green-50 to-green-100 shadow-md flex flex-col justify-center gap-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-green-500 rounded-lg">
                            <i class="fa-solid fa-dollar-sign text-2xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-600 font-medium">Monthly Revenue</h3>
                            <p class="text-3xl font-bold text-green-700"><?php echo $monthly_revenue_formatted; ?></p>
                            <p class="text-xs text-gray-500"><?php echo date('F Y'); ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">Total Revenue</div>
                        <div class="text-lg font-semibold text-green-600"><?php echo $total_revenue_formatted; ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Rating Card -->
            <div style="height: 100px;" class="p-4 rounded-lg bg-gradient-to-r from-yellow-50 to-yellow-100 shadow-md flex flex-col justify-center gap-4 border border-yellow-200">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-yellow-500 rounded-lg">
                        <i class="fa-solid fa-star text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm text-gray-600 font-medium">Average Rating</h3>
                        <div class="flex items-center gap-2">
                            <p class="text-3xl font-bold text-yellow-700"><?php echo $average_rating > 0 ? $average_rating : 'N/A'; ?></p>
                            <?php if ($average_rating > 0): ?>
                                <div class="flex items-center">
                                    <i class="fa-solid fa-star text-yellow-500"></i>
                                    <span class="text-sm text-gray-600 ml-1">/5</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-gray-500"><?php echo count($ratings); ?> reviews</p>
                    </div>
                </div>
            </div>
            
            <!-- Completed Cases Card -->
            <div style="height: 100px;" class="p-4 rounded-lg bg-gradient-to-r from-purple-50 to-purple-100 shadow-md flex flex-col justify-center gap-4 border border-purple-200">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-purple-500 rounded-lg">
                        <i class="fa-solid fa-check-circle text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm text-gray-600 font-medium">Completed Cases</h3>
                        <p class="text-3xl font-bold text-purple-700">
                            <?php echo empty($completed_cases) ? '0' : str_pad(count($completed_cases), 2, '0', STR_PAD_LEFT); ?>
                        </p>
                        <p class="text-xs text-gray-500">Successfully closed</p>
                    </div>
                </div>
            </div>
        </div>



        <!-- dashboard Case filter button -->
        <div class="w-full rounded-lg mt-8 border border-gray-100 bg-gray-100 shadow-md">
            <div class="flex justify-between items-center border-b border-gray-200 p-4 mb-4">
                <div class="flex gap-4">
                    <!-- Case Active Button -->
                    <button id="active-cases-btn-admin"
                        class="flex items-center gap-2 text-gray-600 p-2 rounded-lg bg-gray-200">
                        <i class="fa-solid fa-users"></i>
                        <p>Active Cases</p>
                    </button>

                    <!-- Clients Requested Button -->
                    <button id="requested-cases-btn-admin"
                        class="flex items-center gap-2 text-gray-600 p-2 rounded-lg bg-gray-200">
                        <i class="fa-solid fa-file-lines"></i>
                        <p>Requested Cases</p>
                    </button>
                    
                    <!-- Clients Unpaid Button -->
                    <button id="unpaid-cases-btn-admin"
                        class="flex items-center gap-2 text-gray-600 p-2 rounded-lg bg-gray-200">
                        <i class="fa-solid fa-money-bill"></i>
                        <p>Unpaid Cases</p>
                    </button>
                    
                    <!-- Clients complited Button -->
                    <button id="complited-cases-btn-admin"
                        class="flex items-center gap-2 text-gray-600 p-2 rounded-lg bg-gray-200">
                        <i class="fa-solid fa-money-bill"></i>
                        <p>Complited Cases</p>
                    </button>
                </div>

            </div>

            <!-- Content Area -->
            <div id="dynamic-content" class="p-4">
                <?php include(plugin_dir_path(__FILE__) . 'admin-content/active-cases.php'); ?>
                <?php include(plugin_dir_path(__FILE__) . 'admin-content/completed-cases.php'); ?>
                <?php include(plugin_dir_path(__FILE__) . 'admin-content/requested-cases.php'); ?>
                <?php include(plugin_dir_path(__FILE__) . 'admin-content/unpaid-cases.php'); ?>
            </div>
        </div>
    </div>
    
    <!-- Include Case Details Modal -->
    <?php include(plugin_dir_path(__FILE__) . 'admin-content/case-details-modal.php'); ?>
</div>

<script>
// Notification System
let notifications = [];
let notificationCount = 0;

// Test DB functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize notification system
    initNotificationSystem();
    
    // Load existing notifications
    loadNotifications();
    const testDbBtn = document.getElementById('test-db-btn');
    const notification = document.getElementById('db-notification');
    
    if (testDbBtn) {
        testDbBtn.addEventListener('click', function() {
            // Show loading state
            testDbBtn.disabled = true;
            testDbBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Testing...';
            
            // Hide previous notification
            notification.classList.add('hidden');
            
            // Make AJAX request
            const formData = new FormData();
            formData.append('action', 'test_database');
            formData.append('_ajax_nonce', ajax_object.nonce);
            
            fetch(ajax_object.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success notification
                    showNotification(data.data.message, 'success');
                } else {
                    // Show error notification
                    showNotification(data.data.message || 'Database test failed', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error testing database connection', 'error');
            })
            .finally(() => {
                // Reset button
                testDbBtn.disabled = false;
                testDbBtn.innerHTML = '<i class="fa-solid fa-database"></i> Test DB';
            });
        });
    }
    
    function showNotification(message, type) {
        const notification = document.getElementById('db-notification');
        
        // Clear previous classes
        notification.className = 'mb-4 p-4 rounded-lg';
        
        // Add type-specific classes
        if (type === 'success') {
            notification.classList.add('bg-green-100', 'border', 'border-green-300', 'text-green-800');
            notification.innerHTML = '<i class="fa-solid fa-check-circle mr-2"></i>' + message;
        } else {
            notification.classList.add('bg-red-100', 'border', 'border-red-300', 'text-red-800');
            notification.innerHTML = '<i class="fa-solid fa-exclamation-triangle mr-2"></i>' + message;
        }
        
        // Show notification
        notification.classList.remove('hidden');
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            notification.classList.add('hidden');
        }, 5000);
    }
    
    // Notification System Functions
    function initNotificationSystem() {
        const notificationBtn = document.getElementById('notification-btn');
        const dropdown = document.getElementById('notification-dropdown');
        const markAllRead = document.getElementById('mark-all-read');
        const clearNotifications = document.getElementById('clear-notifications');
        
        // Toggle dropdown
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && !notificationBtn.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
        
        // Mark all read
        markAllRead.addEventListener('click', function() {
            markAllNotificationsRead();
        });
        
        // Clear all notifications
        clearNotifications.addEventListener('click', function() {
            clearAllNotifications();
        });
        
        // Poll for new notifications every 10 seconds
        setInterval(loadNotifications, 10000);
    }
    
    function loadNotifications() {
        const formData = new FormData();
        formData.append('action', 'get_notifications');
        formData.append('_ajax_nonce', ajax_object.nonce);
        
        fetch(ajax_object.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                notifications = data.data.notifications || [];
                updateNotificationDisplay();
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
        });
    }
    
    function updateNotificationDisplay() {
        const badge = document.getElementById('notification-badge');
        const list = document.getElementById('notifications-list');
        
        // Update badge
        const unreadCount = notifications.filter(n => n.is_read === '0').length;
        notificationCount = unreadCount;
        
        if (unreadCount > 0) {
            badge.textContent = unreadCount;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
        
        // Update notifications list
        if (notifications.length === 0) {
            list.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    <i class="fa-solid fa-bell-slash text-2xl mb-2"></i>
                    <p>No new notifications</p>
                </div>
            `;
        } else {
            list.innerHTML = notifications.map(notification => `
                <div class="p-3 border-b border-gray-100 hover:bg-gray-50 ${notification.is_read === "1" ? 'opacity-60' : ''}" data-id="${notification.id}">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-1">
                            ${getNotificationIcon(notification.type)}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                            <p class="text-sm text-gray-600 mt-1">${notification.message}</p>
                            <p class="text-xs text-gray-400 mt-1">${formatDate(notification.created_at)}</p>
                        </div>
                        ${notification.is_read === "0" ? '<div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>' : ''}
                    </div>
                </div>
            `).join('');
        }
    }
    
    function getNotificationIcon(type) {
        const icons = {
            'intake_form': '<i class="fa-solid fa-file-plus text-green-500"></i>',
            'case_update': '<i class="fa-solid fa-edit text-blue-500"></i>',
            'payment': '<i class="fa-solid fa-credit-card text-yellow-500"></i>',
            'status_change': '<i class="fa-solid fa-exchange-alt text-purple-500"></i>',
            'rating': '<i class="fa-solid fa-star text-yellow-400"></i>',
            'system': '<i class="fa-solid fa-cog text-gray-500"></i>'
        };
        return icons[type] || '<i class="fa-solid fa-bell text-gray-500"></i>';
    }
    
    function formatDate(dateString) {
        const now = new Date();
        const date = new Date(dateString);
        const diffInMinutes = Math.floor((now - date) / (1000 * 60));
        
        if (diffInMinutes < 1) return 'Just now';
        if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
        if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
        return `${Math.floor(diffInMinutes / 1440)}d ago`;
    }
    
    function markAllNotificationsRead() {
        const formData = new FormData();
        formData.append('action', 'mark_notifications_read');
        formData.append('_ajax_nonce', ajax_object.nonce);
        
        fetch(ajax_object.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                notifications = notifications.map(n => ({...n, is_read: true}));
                updateNotificationDisplay();
            }
        })
        .catch(error => {
            console.error('Error marking notifications as read:', error);
        });
    }
    
    function clearAllNotifications() {
        const formData = new FormData();
        formData.append('action', 'clear_notifications');
        formData.append('_ajax_nonce', ajax_object.nonce);
        
        fetch(ajax_object.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                notifications = [];
                updateNotificationDisplay();
            }
        })
        .catch(error => {
            console.error('Error clearing notifications:', error);
        });
    }
    
    // Function to add new notification (called from other parts of the system)
    function addNotification(type, title, message) {
        const formData = new FormData();
        formData.append('action', 'add_notification');
        formData.append('_ajax_nonce', ajax_object.nonce);
        formData.append('type', type);
        formData.append('title', title);
        formData.append('message', message);
        
        fetch(ajax_object.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications(); // Refresh notifications
            }
        })
        .catch(error => {
            console.error('Error adding notification:', error);
        });
    }
    
    // Make addNotification globally available
    window.addNotification = addNotification;
});
</script>
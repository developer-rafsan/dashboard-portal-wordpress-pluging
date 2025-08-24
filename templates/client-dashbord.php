<?php
    defined('ABSPATH') or die('No direct access.');

    global $wpdb;

    // Table name
    $table_name = $wpdb->prefix . 'nexuspros_cases_intake_forms';

    // Current logged-in user
    $current_user = wp_get_current_user();
    $user_id      = $current_user->ID;
    $user_email   = $current_user->user_email;

    // Query data for this user
    $results_client = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %s OR user_email = %s", $user_id, $user_email)
    );

    $total_cases = $active_cases = $completed_cases = $rejected_cases = $requested_cases = [];

    if ($results_client) {
        foreach ($results_client as $row) {
            $total_cases[] = $row;
            if ($row->case_status === 'processing' || $row-> case_status === 'paid') $active_cases[] = $row;
            if ($row->case_status === 'completed' && $row->payment_status === 'paid') $completed_cases[] = $row;
            if ($row->case_status === 'rejected' && $row->payment_status !== 'paid') $rejected_cases[] = $row;
            if ($row->case_status === 'pending' || $row->case_status === 'approved') $requested_cases[] = $row;
        }
    }
?>

<!-- Page template for the admin dashboard -->
<div id="client-dashboard">
    <div class="wrap shadow-md p-8 rounded-lg bg-white">

        <!-- --------------------------------------------------------------------------------------------------- client dashboard header -->
        <div class="mb-4">
            <h2
                style="font-size: 24px; font-weight: 600; margin-bottom: 10px; text-transform: capitalize; color: #000000;">
                Nexus pro Client Dashboard</h2>
            <p style="color: #00000070; font-size: 16px; font-weight: 400; text-transform: capitalize;"
                class="text-gray-600 mb-6">
                Welcome back, <span
                    class="text-blue-500"><?php echo esc_html(wp_get_current_user()->display_name); ?></span>
            </p>
        </div>


        <!-- --------------------------------------------------------------------------------------------------- line -->
        <hr class="my-4 border-gray-200" />
        

        <!-- --------------------------------------------------------------------------------------------------- client dashboard cards -->
        <div
            class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 w-full rounded-lg my-4">
            <div class="p-4 rounded-lg bg-gray-100 shadow-md flex flex-col justify-center gap-4">
                <div class="flex items-center gap-4">
                    <i class="fa-solid fa-file-lines text-3xl text-blue-500"></i>
                    <div>
                        <h3 style="font-size: 14px; font-weight: 600; color: #00000099; margin-bottom: 4px;">Total Cases
                        </h3>
                        <p style="font-size: 24px; font-weight: 600; color: #000000;">
                            <?php echo empty($total_cases) ? 'N/A' : str_pad(count($total_cases), 2, '0', STR_PAD_LEFT); ?>
                        </p>
                    </div>
                </div>
                <p class="text-gray-600">This is your total cases</p>
            </div>

            <div class="p-4 rounded-lg bg-gray-100 shadow-md flex flex-col justify-center gap-4">
                <div class="flex items-center gap-4">
                    <i class="fa-solid fa-clock text-3xl text-green-500"></i>
                    <div>
                        <h3 style="font-size: 14px; font-weight: 600; color: #00000099; margin-bottom: 4px;">Active
                            Cases</h3>
                        <p style="font-size: 24px; font-weight: 600; color: #000000;">
                            <?php echo empty($active_cases) ? 'N/A' : str_pad(count($active_cases), 2, '0', STR_PAD_LEFT); ?>
                        </p>
                    </div>
                </div>
                <p class="text-gray-600">
                    <?php echo empty($completed_cases) ? 'N/A' : str_pad(count($completed_cases), 2, '0', STR_PAD_LEFT); ?>Completed Cases
                </p>
            </div>


            <div class="p-4 rounded-lg bg-gray-100 shadow-md flex flex-col justify-center gap-4">
                <div class="flex items-center gap-4">
                    <i class="fa-regular fa-circle-xmark text-3xl text-red-500"></i>
                    <div>
                        <h3 style="font-size: 14px; font-weight: 600; color: #00000099; margin-bottom: 4px;">Rejected
                            Cases
                        </h3>
                        <p style="font-size: 24px; font-weight: 600; color: #000000;">
                            <?php echo empty($rejected_cases) ? 'N/A' : str_pad(count($rejected_cases), 2, '0', STR_PAD_LEFT); ?>
                        </p>
                    </div>
                </div>
                <?php if (!empty($rejected_cases)) : ?>
                <p class="text-gray-600">Those cases are rejected</p>
                <?php endif; ?>
            </div>


            <div class="p-4 rounded-lg bg-gray-100 shadow-md flex flex-col justify-center gap-4">
                <div class="flex items-center gap-4">
                    <i class="fa-solid fa-plus text-3xl text-green-500"></i>
                    <div>
                        <h3 style="font-size: 14px; font-weight: 600; color: #00000099; margin-bottom: 4px;">Requested Cases
                        </h3>
                        <p style="font-size: 24px; font-weight: 600; color: #000000;">
                            <?php echo empty($requested_cases) ? 'N/A' : str_pad(count($requested_cases), 2, '0', STR_PAD_LEFT); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full rounded-lg mt-8 border border-gray-100 bg-gray-100 shadow-md">
            <!-- --------------------------------------------------------------------------------------------------- dashboard filter buttons -->
            <div class="flex gap-4 border-b border-gray-200 p-4 mb-4">
                <!-- Overview Button -->
                <div id="active-cases-btn"
                    class="flex items-center gap-2 text-gray-600 p-2 rounded-lg bg-gray-200 cursor-pointer">
                    <i class="fa-solid fa-file-lines text-sm"></i>
                    <p style="font-size: 14px; text-transform: capitalize;">Active Cases</p>
                </div>

                <!-- new cases -->
                <div id="requested-cases-btn"
                    class="flex items-center gap-2 text-gray-600 p-2 rounded-lg bg-gray-200 cursor-pointer">
                    <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                    <p style="font-size: 14px; text-transform: capitalize;">Requested Cases</p>
                </div>

                <!-- completed cases -->
                <div id="completed-cases-btn"
                    class="flex items-center gap-2 text-gray-600 p-2 rounded-lg bg-gray-200 cursor-pointer">
                    <i class="fa-solid fa-check text-sm"></i>
                    <p style="font-size: 14px; text-transform: capitalize;">Completed Cases</p>
                </div>

                <!-- rejected cases -->
                <div id="rejected-cases-btn"
                    class="flex items-center gap-2 text-gray-600 p-2 rounded-lg bg-gray-200 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                    <p style="font-size: 14px; text-transform: capitalize;">Rejected Cases</p>
                </div>

                <!-- Profile Button -->
                <div id="clients-profile-btn"
                    class="flex items-center gap-2 text-gray-600 p-2 rounded-lg bg-gray-200 cursor-pointer">
                    <i class="fa-solid fa-user text-sm"></i>
                    <p style="font-size: 14px; text-transform: capitalize;">Profile</p>
                </div>
            </div>

            <!-- --------------------------------------------------------------------------------------------------- dashboard Content Area -->
            <div id="dynamic-content" class="p-4">
                <?php include(plugin_dir_path(__FILE__) . 'client-content/active-cases.php'); ?>
                <?php include(plugin_dir_path(__FILE__) . 'client-content/requested-cases.php'); ?>
                <?php include(plugin_dir_path(__FILE__) . 'client-content/completed-cases.php'); ?>
                <?php include(plugin_dir_path(__FILE__) . 'client-content/rejected-cases.php'); ?>
                <?php include(plugin_dir_path(__FILE__) . 'client-content/profile.php'); ?>
            </div>
        </div>

    </div>


    <!-- Intake Form -->
    <div id="intake-form"
        class="fixed top-0 left-0 w-full h-screen bg-gray-500 bg-opacity-80 z-50 overflow-hidden flex justify-center items-center hidden">
        <?php include(plugin_dir_path(__FILE__) . 'intake-form/intake-form.php'); ?>
    </div>
    
    <!-- Intake Form payment -->
    <?php include(plugin_dir_path(__FILE__) . 'intake-form/payment-form.php'); ?>

    <!-- Case Details Modal -->
    <div id="case-details-modal" class="fixed top-2 left-0 w-full h-screen bg-gray-500 bg-opacity-80 z-50 overflow-hidden flex justify-center items-center hidden">
        <div class="w-full max-w-4xl bg-white rounded-lg shadow-xl p-6 max-h-screen overflow-y-auto">
            <!-- Modal Header -->
            <div class="border-b pb-4 mb-4 flex justify-between items-center">
                <div class="text-xl font-bold text-blue-500">Case Details</div>
                <div id="close-case-modal" class="text-gray-500 hover:text-gray-700 text-2xl cursor-pointer">
                    <i class="fa-solid fa-times"></i>
                </div>
            </div>

            <!-- Case Details Content -->
            <div id="case-details-content">
                <!-- Content will be populated via JavaScript -->
                <div class="flex justify-center items-center py-8">
                    <div class="spinner border-4 border-gray-200 border-t-blue-500 rounded-full w-12 h-12 animate-spin"></div>
                    <span class="ml-3 text-gray-600">Loading case details...</span>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .hidden {
        display: none;
    }
</style>

<script>
// Case Details Modal Functionality
document.addEventListener('DOMContentLoaded', function() {  
  // Handle view button clicks
  document.addEventListener('click', function(e) {
      if (e.target.closest('.view-btn')) {
          e.preventDefault();            
          const caseId = e.target.closest('.view-btn').dataset.caseId;
          if (caseId) {
              openCaseDetailsModal(caseId);
          }
      }
  });

  // Close modal functionality
  const modal = document.getElementById('case-details-modal');
  const closeBtn = document.getElementById('close-case-modal');
  
  if (closeBtn) {
      closeBtn.addEventListener('click', function() {
          closeCaseDetailsModal();
      });
  }

  // Close modal when clicking outside
  if (modal) {
      modal.addEventListener('click', function(e) {
          if (e.target === modal) {
              closeCaseDetailsModal();
          }
      });
  }
});

function openCaseDetailsModal(caseId) {
  const modal = document.getElementById('case-details-modal');
  const content = document.getElementById('case-details-content');
  
  if (!modal || !content) return;

  // Show modal with loading state
  modal.classList.remove('hidden');
  content.innerHTML = `
      <div class="flex justify-center items-center py-8">
          <div class="spinner border-4 border-gray-200 border-t-blue-500 rounded-full w-12 h-12 animate-spin"></div>
          <span class="ml-3 text-gray-600">Loading case details...</span>
      </div>
  `;

  // Fetch case details
  const formData = new FormData();
  formData.append('action', 'get_case_details');
  formData.append('_ajax_nonce', ajax_object.nonce);
  formData.append('case_id', caseId);

  fetch(ajax_object.ajax_url, {
      method: 'POST',
      body: formData
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
          displayCaseDetails(data.data);
      } else {
          content.innerHTML = `
              <div class="text-center py-8">
                  <div class="text-red-500 text-lg">Error: ${data.data.message || 'Failed to load case details'}</div>
              </div>
          `;
      }
  })
  .catch(error => {
      console.error('Error fetching case details:', error);
      content.innerHTML = `
          <div class="text-center py-8">
              <div class="text-red-500 text-lg">Error loading case details. Please try again.</div>
          </div>
      `;
  });
}

function closeCaseDetailsModal() {
  const modal = document.getElementById('case-details-modal');
  if (modal) {
      modal.classList.add('hidden');
  }
}

function displayCaseDetails(caseData) {
  const content = document.getElementById('case-details-content');
  if (!content) return;

  const formatDate = (dateStr) => {
      if (!dateStr) return 'N/A';
      return new Date(dateStr).toLocaleDateString();
  };

  const formatCurrency = (amount) => {
      if (!amount) return 'N/A';
      return `$${parseFloat(amount).toFixed(2)}`;
  };

  const getStatusBadge = (status) => {
      const statusClasses = {
          'pending': 'bg-yellow-100 text-yellow-800',
          'approved': 'bg-blue-100 text-blue-800',
          'processing': 'bg-blue-100 text-blue-800',
          'completed': 'bg-green-100 text-green-800',
          'rejected': 'bg-red-100 text-red-800'
      };
      const className = statusClasses[status] || 'bg-gray-100 text-gray-800';
      return `<span class="px-2 py-1 rounded-full text-sm font-medium ${className}">${status}</span>`;
  };

  const getPaymentStatusBadge = (status) => {
      const statusClasses = {
          'pending': 'bg-yellow-100 text-yellow-800',
          'completed': 'bg-green-100 text-green-800',
          'paid': 'bg-green-100 text-green-800',
          'failed': 'bg-red-100 text-red-800'
      };
      const className = statusClasses[status] || 'bg-gray-100 text-gray-800';
      return `<span class="px-2 py-1 rounded-full text-sm font-medium ${className}">${status}</span>`;
  };

  content.innerHTML = `
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <!-- Case Overview -->
          <div class="bg-gray-50 p-4 rounded-lg">
              <div class="text-lg font-semibold mb-4 text-gray-800">Case Overview</div>
              <div class="space-y-1">
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Case ID:</span> ${caseData.case_id}</div>
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Created:</span> ${formatDate(caseData.created_at)}</div>
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Status:</span> ${getStatusBadge(caseData.case_status)}</div>
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Payment Status:</span> ${getPaymentStatusBadge(caseData.payment_status)}</div>
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Package:</span> ${caseData.case_package_type || 'N/A'}</div>
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Amount:</span> ${formatCurrency(caseData.case_package_price)}</div>
                  ${caseData.payment_date ? `<div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Payment Date:</span> ${formatDate(caseData.payment_date)}</div>` : ''}
              </div>
          </div>

          <!-- Personal Information -->
          <div class="bg-gray-50 p-4 rounded-lg">
              <div class="text-lg font-semibold mb-4 text-gray-800">Personal Information</div>
              <div class="space-y-1">
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Name:</span> ${caseData.first_name} ${caseData.last_name}</div>
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Email:</span> ${caseData.email}</div>
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Phone:</span> ${caseData.phone}</div>
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Date of Birth:</span> ${formatDate(caseData.birth_date)}</div>
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Address:</span> ${caseData.address}, ${caseData.city}, ${caseData.state} ${caseData.zip}</div>
              </div>
          </div>

          <!-- Military Information -->
          <div class="bg-gray-50 p-4 rounded-lg">
              <div class="text-lg font-semibold mb-4 text-gray-800">Military Service</div>
              <div class="space-y-1">
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Branch:</span> ${caseData.branch_of_service || 'N/A'}</div>
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Final Rank:</span> ${caseData.final_rank || 'N/A'}</div>
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Service Period:</span> ${formatDate(caseData.start_date)} - ${formatDate(caseData.end_date)}</div>
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">VA File Number:</span> ${caseData.va_file_number || 'N/A'}</div>
              </div>
          </div>

          <!-- Medical Information -->
          <div class="bg-gray-50 p-4 rounded-lg">
              <div class="text-lg font-semibold mb-4 text-gray-800">Medical Information</div>
              <div class="space-y-1">
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Current Conditions:</span><br>
                      <div class="text-sm text-gray-600 mt-1">${caseData.current_medical_conditions || 'N/A'}</div>
                  </div>
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Service Connected:</span><br>
                      <div class="text-sm text-gray-600 mt-1">${caseData.service_connected_conditions || 'N/A'}</div>
                  </div>
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Medical History:</span><br>
                      <div class="text-sm text-gray-600 mt-1">${caseData.medical_history || 'N/A'}</div>
                  </div>
                  <div class="text-gray-800 font-bold"><span class="font-medium text-gray-500">Current Treatment:</span><br>
                      <div class="text-sm text-gray-600 mt-1">${caseData.current_treatment || 'N/A'}</div>
                  </div>
              </div>
          </div>
      </div>

      ${(caseData.client_documents.length > 0 || caseData.admin_documents.length > 0) ? `
      <div class="mt-6">
          <div class="text-lg font-semibold mb-4 text-gray-800">Documents</div>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
              ${caseData.client_documents.length > 0 ? `
              <div class="bg-blue-50 p-4 rounded-lg">
                  <div class="font-medium text-blue-800">Client Documents</div>
                  <div class="space-y-1">
                      ${caseData.client_documents.map(doc => `
                          <a href="${doc}" target="_blank" class="block text-sm text-blue-600 hover:text-blue-800 underline">
                              ${doc.split('/').pop()}
                          </a>
                      `).join('')}
                  </div>
              </div>
              ` : ''}
              
              ${caseData.admin_documents.length > 0 ? `
              <div class="bg-green-50 p-4 rounded-lg">
                  <div class="font-medium text-green-800">Completed Documents</div>
                  <div class="space-y-1">
                      ${caseData.admin_documents.map(doc => `
                          <a href="${doc}" target="_blank" class="block text-sm text-green-600 hover:text-green-800 underline">
                              ${doc.split('/').pop()}
                          </a>
                      `).join('')}
                  </div>
              </div>
              ` : ''}
          </div>
      </div>
      ` : ''}
  `;
}

</script>

<!-- Rating Modal -->
<div id="rating-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex justify-center items-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <div class="text-xl font-semibold text-gray-900">Rate Your Experience</div>
                <div id="close-rating-modal" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </div>
            </div>
            
            <!-- Modal Content -->
            <div class="p-6">
                <form id="rating-form">
                    <input type="hidden" id="rating-case-id" name="case_id">
                    
                    <!-- Star Rating -->
                    <div class="mb-6">
                        <label class="block text-center text-sm font-medium text-gray-700 mb-3">How would you rate your experience?</label>
                        <div class="flex justify-center space-x-2" id="star-rating">
                            <i class="fa-regular fa-star text-3xl text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors" data-rating="1"></i>
                            <i class="fa-regular fa-star text-3xl text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors" data-rating="2"></i>
                            <i class="fa-regular fa-star text-3xl text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors" data-rating="3"></i>
                            <i class="fa-regular fa-star text-3xl text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors" data-rating="4"></i>
                            <i class="fa-regular fa-star text-3xl text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors" data-rating="5"></i>
                        </div>
                        <div class="text-center mt-2">
                            <span id="rating-text" class="text-sm text-gray-600">Click on a star to rate</span>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex justify-end gap-3">
                        <button type="submit" id="submit-rating"
                                class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-spinner fa-spin hidden"></i>
                            <span>Submit Rating</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Rating Modal Functionality
let selectedRating = 0;

// Make function globally available
window.showRatingModal = function(caseId) {    
    const ratingModal = document.getElementById('rating-modal');
    const ratingCaseId = document.getElementById('rating-case-id');
    
    if (!ratingModal) {
        return;
    }
    
    if (!ratingCaseId) {
        return;
    }
    
    ratingCaseId.value = caseId;
    ratingModal.classList.remove('hidden');
    resetRatingForm();
}

window.resetRatingForm = function() {
    selectedRating = 0;
    const stars = document.querySelectorAll('#star-rating i');
    stars.forEach(star => {
        star.className = 'fa-regular fa-star text-3xl text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors';
    });
    document.getElementById('rating-text').textContent = 'Click on a star to rate';
    document.getElementById('submit-rating').disabled = true;
}

// Star rating functionality
document.addEventListener('DOMContentLoaded', function() {    
    const starContainer = document.getElementById('star-rating');
    const ratingText = document.getElementById('rating-text');
    const submitBtn = document.getElementById('submit-rating');
    const closeBtn = document.getElementById('close-rating-modal');
    const ratingModal = document.getElementById('rating-modal');
    const ratingForm = document.getElementById('rating-form');
    
    // Check if all elements exist
    if (!starContainer) {
        return;
    }
    if (!ratingText) {
        return;
    }
    if (!submitBtn) {
        return;
    }
    if (!closeBtn) {
        return;
    }
    if (!ratingModal) {
        return;
    }
    if (!ratingForm) {
        return;
    }
    
    // Star click events
    starContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('fa-star')) {
            const rating = parseInt(e.target.dataset.rating);
            selectedRating = rating;
            updateStars(rating);
            updateRatingText(rating);
            submitBtn.disabled = false;
        }
    });
    
    // Star hover events
    starContainer.addEventListener('mouseover', function(e) {
        if (e.target.classList.contains('fa-star')) {
            const rating = parseInt(e.target.dataset.rating);
            updateStars(rating);
        }
    });
    
    starContainer.addEventListener('mouseout', function() {
        updateStars(selectedRating);
    });
    
    // Close modal events
    closeBtn.addEventListener('click', function() {
        ratingModal.classList.add('hidden');
    });
    
    // Cancel button event
    const cancelBtn = document.getElementById('cancel-rating');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            ratingModal.classList.add('hidden');
        });
    }
    
    // Close modal when clicking outside
    ratingModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
    
    // Form submission
    ratingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log("ok");
        
        submitRating();
    });
});

window.updateStars = function(rating) {
    const stars = document.querySelectorAll('#star-rating i');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.className = 'fa-solid fa-star text-3xl text-yellow-400 cursor-pointer';
        } else {
            star.className = 'fa-regular fa-star text-3xl text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors';
        }
    });
}

window.updateRatingText = function(rating) {
    const ratingTexts = {
        1: 'Poor - Very dissatisfied',
        2: 'Fair - Somewhat dissatisfied',
        3: 'Good - Satisfied',
        4: 'Very Good - Very satisfied',
        5: 'Excellent - Extremely satisfied'
    };
    document.getElementById('rating-text').textContent = ratingTexts[rating] || 'Click on a star to rate';
}

window.submitRating = function() {
    console.log('submitRating function called');
    
    const caseId = document.getElementById('rating-case-id').value;
    const submitBtn = document.getElementById('submit-rating');
    const spinner = submitBtn.querySelector('.fa-spinner');
    const submitText = submitBtn.querySelector('span');
    
    if (selectedRating === 0) {
        return;
    }
    
    if (!ajax_object) {
        return;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    if (spinner) {
        spinner.classList.remove('hidden');
    }
    if (submitText) {
        submitText.textContent = 'Submitting...';
    }
    
    // Prepare form data
    const formData = new FormData();
    formData.append('action', 'submit_case_rating');
    formData.append('_ajax_nonce', ajax_object.nonce);
    formData.append('case_id', caseId);
    formData.append('rating', selectedRating);
    
    // Submit rating
    fetch(ajax_object.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            
            // Close modal
            document.getElementById('rating-modal').classList.add('hidden');
            
            // Reload the page to show updated rating
            location.reload();
        } else {
            throw new Error(data.data?.message || 'Failed to submit rating');
        }
    })
    .catch(error => {
        alert('Error submitting rating: ' + error.message);
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        if (spinner) {
            spinner.classList.add('hidden');
        }
        if (submitText) {
            submitText.textContent = 'Submit Rating';
        }
    });
}
</script>
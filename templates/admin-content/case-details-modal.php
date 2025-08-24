<!-- Case Details Modal -->
<div id="case-details-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex justify-center p-4">
        <div style="height:90vh" class="bg-white rounded-lg shadow-xl max-w-4xl w-full overflow-y-scroll">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Case Details</h3>
                <button id="close-case-modal" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div id="case-details-content" class="p-6">
                <!-- Content will be loaded dynamically -->
                <div class="flex justify-center items-center h-32">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Case Details Modal Functionality
function openCaseDetailsModal(caseId) {
    const modal = document.getElementById('case-details-modal');
    const content = document.getElementById('case-details-content');
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Load case details via AJAX
    const formData = new FormData();
    formData.append('action', 'get_case_details');
    formData.append('_ajax_nonce', ajax_object.nonce);
    formData.append('case_id', caseId);
    
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
        if (data.success) {
            content.innerHTML = generateCaseDetailsHTML(data.data);
            // Setup edit functionality after content is loaded
            setupEditFunctionality();
        } else {
            content.innerHTML = `
                <div class="text-center text-red-500 p-8">
                    <i class="fa-solid fa-exclamation-triangle text-4xl mb-4"></i>
                    <p class="text-lg font-semibold">Error loading case details</p>
                    <p class="text-sm text-gray-600">${data.data?.message || 'Unknown error occurred'}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        content.innerHTML = `
            <div class="text-center text-red-500 p-8">
                <i class="fa-solid fa-exclamation-triangle text-4xl mb-4"></i>
                <p class="text-lg font-semibold">Error loading case details</p>
                <p class="text-sm text-gray-600">Please try again later</p>
            </div>
        `;
    });
}

// Function to get appropriate icon for file type
function getFileIcon(fileExtension) {
    const iconMap = {
        'pdf': 'fa-solid fa-file-pdf',
        'doc': 'fa-solid fa-file-word',
        'docx': 'fa-solid fa-file-word',
        'xls': 'fa-solid fa-file-excel',
        'xlsx': 'fa-solid fa-file-excel',
        'ppt': 'fa-solid fa-file-powerpoint',
        'pptx': 'fa-solid fa-file-powerpoint',
        'txt': 'fa-solid fa-file-lines',
        'jpg': 'fa-solid fa-file-image',
        'jpeg': 'fa-solid fa-file-image',
        'png': 'fa-solid fa-file-image',
        'gif': 'fa-solid fa-file-image',
        'zip': 'fa-solid fa-file-zipper',
        'rar': 'fa-solid fa-file-zipper',
        'mp4': 'fa-solid fa-file-video',
        'avi': 'fa-solid fa-file-video',
        'mov': 'fa-solid fa-file-video'
    };
    return iconMap[fileExtension] || 'fa-solid fa-file';
}

// Function to handle file download
function downloadFile(url, fileName) {
    const link = document.createElement('a');
    link.href = url;
    link.download = fileName;
    link.target = '_blank';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Generate HTML for case details
function generateCaseDetailsHTML(caseData) {
    const statusColors = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'approved': 'bg-blue-100 text-blue-800',
        'processing': 'bg-green-100 text-green-800',
        'completed': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800'
    };
    
    const paymentColors = {
        'pending': 'bg-red-100 text-red-800',
        'completed': 'bg-green-100 text-green-800',
        'paid': 'bg-green-100 text-green-800'
    };
    
    return `
        <div class="space-y-6">
            <!-- Case Header with Edit Toggle -->
            <div class="border-b border-gray-200 pb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Case: ${caseData.case_id}</h2>
                        <p class="text-gray-600">Created: ${new Date(caseData.created_at).toLocaleDateString()}</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 rounded-full text-sm font-medium ${statusColors[caseData.case_status] || 'bg-gray-100 text-gray-800'}">
                            ${caseData.case_status}
                        </span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium ${paymentColors[caseData.payment_status] || 'bg-gray-100 text-gray-800'}">
                            ${caseData.payment_status}
                        </span>
                        <button id="edit-toggle-btn" class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 transition-colors">
                            <i class="fa-solid fa-edit"></i> Edit
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Edit Form (Hidden by default) -->
            <form id="case-edit-form" class="hidden space-y-6">
                <!-- Case Status and Payment Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3">Case Status</h3>
                        <select name="case_status" class="w-full p-2 border border-gray-300 rounded-md">
                            <option value="pending" ${caseData.case_status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="approved" ${caseData.case_status === 'approved' ? 'selected' : ''}>Approved</option>
                            <option value="processing" ${caseData.case_status === 'processing' ? 'selected' : ''}>Processing</option>
                            <option value="completed" ${caseData.case_status === 'completed' ? 'selected' : ''}>Completed</option>
                            <option value="rejected" ${caseData.case_status === 'rejected' ? 'selected' : ''}>Rejected</option>
                        </select>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3">Payment Status</h3>
                        <select name="payment_status" class="w-full p-2 border border-gray-300 rounded-md">
                            <option value="pending" ${caseData.payment_status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="paid" ${caseData.payment_status === 'paid' ? 'selected' : ''}>Paid</option>
                            <option value="completed" ${caseData.payment_status === 'completed' ? 'selected' : ''}>Completed</option>
                        </select>
                    </div>
                </div>
                
                <!-- Package Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3">Package Information</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Package Type</label>
                                <input type="text" name="case_package_type" value="${caseData.case_package_type || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Package Price</label>
                                <input type="text" name="case_package_price" value="${caseData.case_package_price || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Payment Amount</label>
                                <input type="text" name="payment_amount" value="${caseData.payment_amount || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Payment Date</label>
                                <input type="date" name="payment_date" value="${caseData.payment_date || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Personal Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3">Personal Information</h3>
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">First Name</label>
                                    <input type="text" name="first_name" value="${caseData.first_name || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Last Name</label>
                                    <input type="text" name="last_name" value="${caseData.last_name || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" value="${caseData.email || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <input type="text" name="phone" value="${caseData.phone || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Birth Date</label>
                                <input type="date" name="birth_date" value="${caseData.birth_date || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Address</label>
                                <input type="text" name="address" value="${caseData.address || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">City</label>
                                    <input type="text" name="city" value="${caseData.city || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">State</label>
                                    <input type="text" name="state" value="${caseData.state || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">ZIP</label>
                                    <input type="text" name="zip" value="${caseData.zip || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Military Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Military Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Branch of Service</label>
                                <input type="text" name="branch_of_service" value="${caseData.branch_of_service || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Final Rank</label>
                                <input type="text" name="final_rank" value="${caseData.final_rank || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">VA File Number</label>
                                <input type="text" name="va_file_number" value="${caseData.va_file_number || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Service Start Date</label>
                                <input type="date" name="start_date" value="${caseData.start_date || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Service End Date</label>
                                <input type="date" name="end_date" value="${caseData.end_date || ''}" class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Medical Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Medical Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Current Medical Conditions</label>
                            <textarea name="current_medical_conditions" rows="3" class="w-full p-2 border border-gray-300 rounded-md">${caseData.current_medical_conditions || ''}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Service Connected Conditions</label>
                            <textarea name="service_connected_conditions" rows="3" class="w-full p-2 border border-gray-300 rounded-md">${caseData.service_connected_conditions || ''}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Medical History</label>
                            <textarea name="medical_history" rows="3" class="w-full p-2 border border-gray-300 rounded-md">${caseData.medical_history || ''}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Current Treatment</label>
                            <textarea name="current_treatment" rows="3" class="w-full p-2 border border-gray-300 rounded-md">${caseData.current_treatment || ''}</textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" id="cancel-edit-btn" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors">
                        <i class="fa-solid fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
            
            <!-- View Mode Content -->
            <div id="view-mode-content" class="space-y-6">
                <!-- Package Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3">Package Information</h3>
                        <div class="space-y-2">
                            <p><strong>Type:</strong> ${caseData.case_package_type || 'N/A'}</p>
                            <p><strong>Price:</strong> $${caseData.case_package_price || 'N/A'}</p>
                            ${caseData.payment_amount ? `<p><strong>Amount Paid:</strong> $${caseData.payment_amount}</p>` : ''}
                            ${caseData.payment_date ? `<p><strong>Payment Date:</strong> ${new Date(caseData.payment_date).toLocaleDateString()}</p>` : ''}
                        </div>
                    </div>
                    
                    <!-- Personal Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3">Personal Information</h3>
                        <div class="space-y-2">
                            <p><strong>Name:</strong> ${caseData.first_name} ${caseData.last_name}</p>
                            <p><strong>Email:</strong> ${caseData.email}</p>
                            <p><strong>Phone:</strong> ${caseData.phone}</p>
                            <p><strong>Birth Date:</strong> ${caseData.birth_date}</p>
                            <p><strong>Address:</strong> ${caseData.address}, ${caseData.city}, ${caseData.state} ${caseData.zip}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Military Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Military Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <p><strong>Branch:</strong> ${caseData.branch_of_service || 'N/A'}</p>
                            <p><strong>Final Rank:</strong> ${caseData.final_rank || 'N/A'}</p>
                            <p><strong>VA File Number:</strong> ${caseData.va_file_number || 'N/A'}</p>
                        </div>
                        <div class="space-y-2">
                            <p><strong>Service Start:</strong> ${caseData.start_date || 'N/A'}</p>
                            <p><strong>Service End:</strong> ${caseData.end_date || 'N/A'}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Medical Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Medical Information</h3>
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-700">Current Medical Conditions</h4>
                            <p class="text-gray-600 mt-1">${caseData.current_medical_conditions || 'No information provided'}</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700">Service Connected Conditions</h4>
                            <p class="text-gray-600 mt-1">${caseData.service_connected_conditions || 'No information provided'}</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700">Medical History</h4>
                            <p class="text-gray-600 mt-1">${caseData.medical_history || 'No information provided'}</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700">Current Treatment</h4>
                            <p class="text-gray-600 mt-1">${caseData.current_treatment || 'No information provided'}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Documents -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Documents</h3>
                    <div class="space-y-6">
                        <!-- Client Documents -->
                        <div>
                            <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                                <i class="fa-solid fa-user text-blue-600 mr-2"></i>
                                Client Documents
                            </h4>
                            ${caseData.client_documents && caseData.client_documents.length > 0 ? 
                                `<div class="space-y-2">
                                    ${caseData.client_documents.map((doc, index) => {
                                        const fileName = doc.split('/').pop();
                                        const fileExtension = fileName.split('.').pop().toLowerCase();
                                        const iconClass = getFileIcon(fileExtension);
                                        return `
                                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                                                <div class="flex items-center space-x-3">
                                                    <i class="${iconClass} text-2xl text-gray-500"></i>
                                                    <div>
                                                        <p class="font-medium text-gray-900">${fileName}</p>
                                                        <p class="text-sm text-gray-500">Client Document</p>
                                                    </div>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <a href="${doc}" target="_blank" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors">
                                                        <i class="fa-solid fa-eye mr-1"></i>
                                                        View
                                                    </a>
                                                    <a href="${doc}" download="${fileName}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-green-600 bg-green-50 border border-green-200 rounded-md hover:bg-green-100 transition-colors">
                                                        <i class="fa-solid fa-download mr-1"></i>
                                                        Download
                                                    </a>
                                                </div>
                                            </div>
                                        `;
                                    }).join('')}
                                </div>` : 
                                '<div class="text-center py-6"><i class="fa-solid fa-folder-open text-4xl text-gray-300 mb-2"></i><p class="text-gray-600">No client documents uploaded</p></div>'
                            }
                        </div>
                        
                        <!-- Admin Documents -->
                        <div>
                            <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                                <i class="fa-solid fa-user-shield text-purple-600 mr-2"></i>
                                Admin Documents
                            </h4>
                            ${caseData.admin_documents && caseData.admin_documents.length > 0 ? 
                                `<div class="space-y-2">
                                    ${caseData.admin_documents.map((doc, index) => {
                                        const fileName = doc.split('/').pop();
                                        const fileExtension = fileName.split('.').pop().toLowerCase();
                                        const iconClass = getFileIcon(fileExtension);
                                        return `
                                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                                                <div class="flex items-center space-x-3">
                                                    <i class="${iconClass} text-2xl text-gray-500"></i>
                                                    <div>
                                                        <p class="font-medium text-gray-900">${fileName}</p>
                                                        <p class="text-sm text-gray-500">Admin Document</p>
                                                    </div>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <a href="${doc}" target="_blank" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-purple-600 bg-purple-50 border border-purple-200 rounded-md hover:bg-purple-100 transition-colors">
                                                        <i class="fa-solid fa-eye mr-1"></i>
                                                        View
                                                    </a>
                                                    <a href="${doc}" download="${fileName}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-green-600 bg-green-50 border border-green-200 rounded-md hover:bg-green-100 transition-colors">
                                                        <i class="fa-solid fa-download mr-1"></i>
                                                        Download
                                                    </a>
                                                </div>
                                            </div>
                                        `;
                                    }).join('')}
                                </div>` : 
                                '<div class="text-center py-6"><i class="fa-solid fa-folder-open text-4xl text-gray-300 mb-2"></i><p class="text-gray-600">No admin documents uploaded</p></div>'
                            }
                        </div>
                    </div>
                </div>
                
                <!-- Consent Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Consent Information</h3>
                    <div class="space-y-2">
                        <p><strong>Data Collection:</strong> ${caseData.consent_data_collection ? '✓ Agreed' : '✗ Not agreed'}</p>
                        <p><strong>Privacy Policy:</strong> ${caseData.consent_privacy_policy ? '✓ Agreed' : '✗ Not agreed'}</p>
                        <p><strong>Communication:</strong> ${caseData.consent_communication ? '✓ Agreed' : '✗ Not agreed'}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Close modal functionality
document.getElementById('close-case-modal').addEventListener('click', function() {
    document.getElementById('case-details-modal').classList.add('hidden');
});

// Close modal when clicking outside
document.getElementById('case-details-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

// Edit functionality
let currentCaseId = null;

// Function to handle edit toggle
function setupEditFunctionality() {
    const editToggleBtn = document.getElementById('edit-toggle-btn');
    const editForm = document.getElementById('case-edit-form');
    const viewModeContent = document.getElementById('view-mode-content');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');

    if (editToggleBtn) {
        editToggleBtn.addEventListener('click', function() {
            
            editForm.classList.remove('hidden');
            viewModeContent.classList.add('hidden');
            editToggleBtn.innerHTML = '<i class="fa-solid fa-eye"></i> View';
            editToggleBtn.classList.remove('bg-blue-500');
            editToggleBtn.classList.add('bg-gray-500');
        });
    }
    
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', function() {
            editForm.classList.add('hidden');
            viewModeContent.classList.remove('hidden');
            editToggleBtn.innerHTML = '<i class="fa-solid fa-edit"></i> Edit';
            editToggleBtn.classList.remove('bg-gray-500');
            editToggleBtn.classList.add('bg-blue-500');
        });
    }
    
    // Handle form submission
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(editForm);
            formData.append('action', 'update_case_details');
            formData.append('_ajax_nonce', ajax_object.nonce);
            formData.append('case_id', currentCaseId);
            
            // Show loading state
            const submitBtn = editForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
            submitBtn.disabled = true;
            
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
                if (data.success) {
                    // Show success notification
                    const notificationContainer = document.querySelector('.notification-container');
                    if (notificationContainer) {
                        notificationContainer.querySelector('#notification-title_admin').textContent = 'Case updated successfully';
                        notificationContainer.classList.add('active');
                        setTimeout(() => {
                            notificationContainer.classList.remove('active');
                        }, 3000);
                    }
                    
                    // Switch back to view mode
                    editForm.classList.add('hidden');
                    viewModeContent.classList.remove('hidden');
                    editToggleBtn.innerHTML = '<i class="fa-solid fa-edit"></i> Edit';
                    editToggleBtn.classList.remove('bg-gray-500');
                    editToggleBtn.classList.add('bg-blue-500');
                    
                    // Reload the case details to show updated data
                    openCaseDetailsModal(currentCaseId);
                } else {
                    throw new Error(data.data?.message || 'Update failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Show error notification
                const notificationContainer = document.querySelector('.notification-container');
                if (notificationContainer) {
                    notificationContainer.querySelector('#notification-title_admin').textContent = 'Update failed: ' + error.message;
                    notificationContainer.classList.add('active');
                    setTimeout(() => {
                        notificationContainer.classList.remove('active');
                    }, 3000);
                }
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
}

// Update the openCaseDetailsModal function to store case ID
const originalOpenCaseDetailsModal = openCaseDetailsModal;
openCaseDetailsModal = function(caseId) {
    currentCaseId = caseId;
    originalOpenCaseDetailsModal(caseId);
};
</script>

<?php
    defined('ABSPATH') or die('No direct access.');
?>

<!-- Tailwind Multi-Step Case Intake Form -->
<div id="intake-form-container" class="w-6/12 md:w-10/12 lg:w-8/12 xl:w-6/12 2xl:w-4/12 p-4">
    <!-- Header -->
    <div class="bg-blue-600 rounded-t-lg p-4 sm:p-6 text-white">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h1 style="font-size: 2rem; margin-bottom: 10px; color: #FFFFFF;" class="font-bold">New Client Intake
                    Form</h1>
                <p style="color:rgba(255, 255, 255, 0.56);" class="text-sm sm:text-base">Secure HIPAA-Compliant
                    Submission</p>
            </div>
            <div class="text-white text-3xl sm:text-4xl">🛡️</div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="progress-bar-container">
        <div class="progress-bar">
            <div class="step-circle active" data-step="1">
                <span class="step-number">1</span>
                <span class="step-label">Personal</span>
            </div>
            <div class="step-line active"></div>

            <div class="step-circle" data-step="2">
                <span class="step-number">2</span>
                <span class="step-label">Military</span>
            </div>
            <div class="step-line"></div>

            <div class="step-circle" data-step="3">
                <span class="step-number">3</span>
                <span class="step-label">Medical</span>
            </div>
            <div class="step-line"></div>

            <div class="step-circle" data-step="4">
                <span class="step-number">4</span>
                <span class="step-label">Documents</span>
            </div>
            <div class="step-line"></div>

            <div class="step-circle" data-step="5">
                <span class="step-number">5</span>
                <span class="step-label">Consent</span>
            </div>
            <div class="step-line"></div>

            <div class="step-circle" data-step="6">
                <span class="step-number">6</span>
                <span class="step-label">Package</span>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-b-lg p-4 sm:p-4">
        <form id="intake-form-form" method="post" enctype="multipart/form-data">
            <!-- Step 1: Personal Information -->
            <div class="step-content" id="step-1">
                <h2 style="font-size: 1.2rem; margin-bottom: 10px;" class="font-bold text-gray-900">Personal Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date of Birth <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="dob" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Social Security Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="ssn" required placeholder="XXX-XX-XXXX"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Encrypted and HIPAA protected</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="address" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            City <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="city" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            State <span class="text-red-500">*</span>
                        </label>
                        <select name="state" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select State</option>
                            <option value="AL">Alabama</option>
                            <option value="AK">Alaska</option>
                            <option value="AZ">Arizona</option>
                            <option value="AR">Arkansas</option>
                            <option value="CA">California</option>
                            <option value="CO">Colorado</option>
                            <option value="CT">Connecticut</option>
                            <option value="DE">Delaware</option>
                            <option value="FL">Florida</option>
                            <option value="GA">Georgia</option>
                            <option value="HI">Hawaii</option>
                            <option value="ID">Idaho</option>
                            <option value="IL">Illinois</option>
                            <option value="IN">Indiana</option>
                            <option value="IA">Iowa</option>
                            <option value="KS">Kansas</option>
                            <option value="KY">Kentucky</option>
                            <option value="LA">Louisiana</option>
                            <option value="ME">Maine</option>
                            <option value="MD">Maryland</option>
                            <option value="MA">Massachusetts</option>
                            <option value="MI">Michigan</option>
                            <option value="MN">Minnesota</option>
                            <option value="MS">Mississippi</option>
                            <option value="MO">Missouri</option>
                            <option value="MT">Montana</option>
                            <option value="NE">Nebraska</option>
                            <option value="NV">Nevada</option>
                            <option value="NH">New Hampshire</option>
                            <option value="NJ">New Jersey</option>
                            <option value="NM">New Mexico</option>
                            <option value="NY">New York</option>
                            <option value="NC">North Carolina</option>
                            <option value="ND">North Dakota</option>
                            <option value="OH">Ohio</option>
                            <option value="OK">Oklahoma</option>
                            <option value="OR">Oregon</option>
                            <option value="PA">Pennsylvania</option>
                            <option value="RI">Rhode Island</option>
                            <option value="SC">South Carolina</option>
                            <option value="SD">South Dakota</option>
                            <option value="TN">Tennessee</option>
                            <option value="TX">Texas</option>
                            <option value="UT">Utah</option>
                            <option value="VT">Vermont</option>
                            <option value="VA">Virginia</option>
                            <option value="WA">Washington</option>
                            <option value="WV">West Virginia</option>
                            <option value="WI">Wisconsin</option>
                            <option value="WY">Wyoming</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            ZIP Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="zip_code" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Step 2: Military Service -->
            <div class="step-content hidden" id="step-2">
                <h2 style="font-size: 1.2rem; margin-bottom: 10px;" class="font-bold text-gray-900">Military Service
                    Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Branch of Service <span class="text-red-500">*</span>
                        </label>
                        <select name="branch" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Branch</option>
                            <option value="army">Army</option>
                            <option value="air_force">Air Force</option>
                            <option value="navy">Navy</option>
                            <option value="marine_corps">Marine Corps</option>
                            <option value="coast_guard">Coast Guard</option>
                            <option value="space_force">Space Force</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Final Rank <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="rank" required placeholder="e.g., E-5, O-3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Service Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="service_start" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Service End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="service_end" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            MOS/AOC/AOS/Rate
                        </label>
                        <input type="number" name="va_file_number" placeholder="C-file number or SSN if no C-file"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Step 3: Medical Information -->
            <div class="step-content hidden" id="step-3">
                <h2 style="font-size: 1.2rem; margin-bottom: 10px;" class="font-bold text-gray-900">Medical Information
                </h2>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Current Medical Conditions <span class="text-red-500">*</span>
                        </label>
                        <textarea name="current_conditions" required rows="4"
                            placeholder="Describe your current medical conditions..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Service-Connected Conditions
                        </label>
                        <textarea name="service_connected" rows="4"
                            placeholder="List any conditions you believe are connected to your military service..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Medical History & Treatment
                        </label>
                        <textarea name="medical_history" rows="4"
                            placeholder="Include relevant medical history, hospitalizations, surgeries..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Current Treatment & Medications
                        </label>
                        <textarea name="current_treatment" rows="4"
                            placeholder="List current medications, treatments, and healthcare providers..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>
            </div>

            <!-- Step 4: Document Upload -->
            <div class="step-content hidden" id="step-4">
                <h2 style="font-size: 1.2rem; margin-bottom: 10px;" class="font-bold text-gray-900">Document Upload</h2>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <div class="text-4xl text-gray-400 mb-4">📤</div>
                    <h3 style="font-size: 1.2rem; margin-bottom: 10px;" class="font-bold text-gray-900">Upload
                        Supporting Documents</h3>
                    <p style="font-size: 1rem; margin-bottom: 10px;" class="text-gray-500 mb-6">
                        Upload medical records, service records, and other supporting documentation
                    </p>

                    <input class="hidden" type="file" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" id="file-upload">
                    <label for="file-upload"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 cursor-pointer">
                        Choose Files
                    </label>

                    <p style="font-size: 1rem; margin-top: 20px; color: #6b7280; font-weight: 400;" class="text-sm">
                        Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max 10MB per file)
                    </p>

                    <!-- File Preview Section -->
                    <div id="file-list" class="mt-4 text-left"></div>
                </div>
            </div>

            <!-- Step 5: Consent -->
            <div class="step-content hidden" id="step-5">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-2">
                    <!-- Tier 1 -->
                    <div class="p-4 shadow-lg border rounded-md package-selection-card" data-price="400"
                        data-value="Basic">
                        <div class="flex justify-between">
                            <div>
                                <h2 style="font-size: 1.2rem;" class="font-bold text-gray-900">
                                    Basic</h2>
                                <p style="font-size: 1rem;; color: #6b7280;" class="text-gray-500">
                                    Single, Straightforward Condition
                                </p>
                            </div>
                            <h1 style="font-size: 1.5rem;" class="font-bold text-gray-900">$400
                            </h1>
                        </div>
                        <hr style="margin-top: 10px; margin-bottom: 10px;" />
                        <ul style="font-size: 1rem; color: #6b7280;">
                            <li style="font-size: 1rem; color: #6b7280;" class="text-gray-500"><i
                                    class="fa-solid fa-check"></i> Covers one medical condition with a clear,
                                direct connection.</li>
                            <li style="font-size: 1rem;color: #6b7280;" class="text-gray-500"><i
                                    class="fa-solid fa-check"></i> Minimal complexity, commonly recognized
                                conditions.</li>
                            <li style="font-size: 1rem; color: #6b7280;" class="text-gray-500"><i
                                    class="fa-solid fa-check"></i> Limited records review and simple rationale.
                            </li>
                        </ul>
                    </div>


                    <!-- Tier 2 -->
                    <div class="p-4 shadow-lg border rounded-md package-selection-card" data-price="450"
                        data-value="Intermediate">
                        <div class="flex justify-between">
                            <div>
                                <h2 style="font-size: 1.2rem;" class="font-bold text-gray-900">
                                    Intermediate</h2>
                                <p style="font-size: 1rem;color: #6b7280;" class="text-gray-500">
                                    Multiple or Moderately Complex Conditions
                                </p>
                            </div>
                            <h1 style="font-size: 1.5rem;" class="font-bold text-gray-900">$450
                            </h1>
                        </div>
                        <hr style="margin-top: 10px; margin-bottom: 10px;" />
                        <ul style="font-size: 1rem;color: #6b7280;">
                            <li style="font-size: 1rem;color: #6b7280;" class="text-gray-500"><i
                                    class="fa-solid fa-check"></i> Two or more related conditions, or
                                complicating factors.</li>
                            <li style="font-size: 1rem;color: #6b7280;" class="text-gray-500"><i
                                    class="fa-solid fa-check"></i> Requires deeper record review and nuanced
                                opinion.</li>
                            <li style="font-size: 1rem;color: #6b7280;" class="text-gray-500"><i
                                    class="fa-solid fa-check"></i> References medical literature if needed.</li>
                        </ul>
                    </div>


                    <!-- Tier 3 -->
                    <div style="grid-column: span 2;" class="p-4 shadow-lg border rounded-md package-selection-card"
                        data-price="500" data-value="Advanced">
                        <div class="flex justify-between">
                            <div>
                                <h2 style="font-size: 1.2rem; margin-bottom: 10px;" class="font-bold text-gray-900">
                                    Advanced</h2>
                                <p style="font-size: 1rem;color: #6b7280;" class="text-gray-500">
                                    Multiple, Complex, or Rare Conditions
                                </p>
                            </div>
                            <h1 style="font-size: 1.5rem; margin-bottom: 10px;" class="font-bold text-gray-900">$500
                            </h1>
                        </div>
                        <hr style="margin-top: 10px; margin-bottom: 10px;" />
                        <ul style="font-size: 1rem;color: #6b7280;">
                            <li style="font-size: 1rem;color: #6b7280;" class="text-gray-500"><i
                                    class="fa-solid fa-check"></i> Three or more or rare/complex conditions.
                            </li>
                            <li style="font-size: 1rem;color: #6b7280;" class="text-gray-500"><i
                                    class="fa-solid fa-check"></i> Comprehensive record review & advanced
                                studies.
                            </li>
                            <li style="font-size: 1rem;color: #6b7280;" class="text-gray-500"><i
                                    class="fa-solid fa-check"></i> May involve multiple specialists.
                            </li>
                        </ul>
                    </div>
                    <input require type="hidden" name="package" id="selectedPackage">
                </div>
            </div>


            <!-- Step 6: Consent -->
            <div class="step-content hidden" id="step-6">
                <h2 style="font-size: 1.2rem; margin-bottom: 10px;" class="font-bold text-gray-900">Consent & Agreement
                </h2>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <input type="checkbox" name="data_consent" required
                            class="text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-3 text-sm text-gray-700">
                            I consent to the collection, processing, and storage of my personal health information
                            (PHI) and personally identifiable information (PII) for the purpose of preparing my
                            Nexus Letter. I understand this information will be handled in accordance with HIPAA
                            regulations.
                        </label>
                    </div>

                    <div class="flex items-start">
                        <input type="checkbox" name="privacy_consent" required
                            class="text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-3 text-sm text-gray-700">
                            I have read and agree to the <a href="#" class="text-blue-600 underline">Privacy
                                Policy</a> and <a href="#" class="text-blue-600 underline">Terms of Service</a>.
                        </label>
                    </div>

                    <div class="flex items-start">
                        <input type="checkbox" name="communication_consent" required
                            class="text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-3 text-sm text-gray-700">
                            I consent to receive communications regarding my case via email and SMS to the contact
                            information provided. I understand that no PHI/PII will be included in these
                            communications.
                        </label>
                    </div>
                </div>

                <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="text-green-600 mr-3">✅</div>
                        <div>
                            <h4 class="font-semibold text-green-900">Secure Submission</h4>
                            <p class="text-green-700 text-sm">Upon submission, you will receive a secure portal
                                login to track your case and communicate with our team. Your information is
                                encrypted and protected.</p>
                        </div>
                    </div>
                </div>
            </div>






            <!-- Navigation Buttons -->
            <div id="navigation-buttons" class="flex justify-between pt-6">
                <button type="button" id="prev-btn"
                    class="px-6 py-2 text-gray-600 hover:text-gray-800 disabled:opacity-50 disabled:cursor-not-allowed active">
                    Back
                </button>

                <button type="button" id="next-btn"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 active">
                    Next
                </button>

                <button type="submit" id="submit-btn" name="submit_intake"
                    class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Submit Application
                </button>
            </div>
        </form>
    </div>
</div>


<!-- --------------------------------------------------------------------------------------------------------------------------------------script -->
<script>
const cards = document.querySelectorAll('.package-selection-card');
const hiddenInput = document.getElementById('selectedPackage');

cards.forEach(card => {
    card.addEventListener('click', () => {
        cards.forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        hiddenInput.value = card.dataset.value;
        hiddenInput.dataset.price = card.dataset.price;
    });
});


// Ensure correct form selector
const form = document.getElementById('intake-form-form');


if (form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submit-btn');
        submitBtn.textContent = 'Submitting...';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';

        const formData = new FormData(form);
        formData.append('action', 'handle_nexuspros_case_intake');
        formData.append('_ajax_nonce', ajax_object.nonce);
        formData.append('package_price', hiddenInput.dataset.price);

        fetch(ajax_object.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Check if response is OK first
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Check content type
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Response is not JSON. Received: ' + contentType);
                }
                
                return response.json();
            })
            .then(data => {
                console.log("Form submission response:", data);
                
                const notificationContainer = document.querySelector('.notification-container');
                notificationContainer.classList.add('active');

                if (data.success) {
                    notificationContainer.querySelector('#notification-title').textContent = 'Submitted Successfully';
                } else {
                    notificationContainer.querySelector('#notification-title').textContent = 'Submission Failed';
                }
            })
            .catch(error => {
                console.error('Form submission error:', error);
                
                const notificationContainer = document.querySelector('.notification-container');
                notificationContainer.classList.add('active');

                notificationContainer.querySelector('#notification-title').textContent = 'Submission Failed';
            })
            .finally(() => {
                submitBtn.textContent = 'Submit Application';
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                const notificationContainer = document.querySelector('.notification-container');
                setTimeout(() => {
                    notificationContainer.classList.remove('active');
                    window.location.reload();
                }, 1000);
            });
    });
}




document.getElementById("file-upload").addEventListener("change", function(event) {
    const fileList = Array.from(event.target.files); // Convert FileList to Array
    const fileListContainer = document.getElementById("file-list");

    // Clear previous previews
    fileListContainer.innerHTML = '';

    fileList.forEach((file, index) => {
        const fileReader = new FileReader();

        fileReader.onload = function(e) {
            const fileType = file.type.split('/')[0];
            const fileName = file.name;

            const preview = document.createElement('div');
            preview.classList.add('file-preview', 'mb-4', 'flex', 'items-center', 'gap-4', 'p-2',
                'border', 'rounded-md', 'border-gray-300');

            let fileTypeText = 'File';
            if (fileType === 'image') fileTypeText = 'Image';
            else if (file.type === 'application/pdf') fileTypeText = 'PDF';
            else if (file.type === 'application/msword' || file.type ===
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
                fileTypeText = 'Word';

            preview.innerHTML = `
                <div class="flex-1 flex justify-between items-center">
                    <div>
                        <div class="text-sm font-semibold text-gray-700">${fileName}</div>
                        <div class="text-xs text-gray-500">${fileTypeText}</div>
                    </div>
                    <div>
                        <button type="button" class="remove-btn text-red-500 hover:text-red-700 active" data-index="${index}">X</button>
                    </div>
                </div>
            `;

            fileListContainer.appendChild(preview);

            // Close button event
            preview.querySelector('.remove-btn').addEventListener('click', function() {
                preview.remove(); // remove preview from DOM

                // remove file from FileList (you cannot directly modify FileList, so create new DataTransfer)
                const dt = new DataTransfer();
                fileList.splice(index, 1); // remove from array
                fileList.forEach(f => dt.items.add(f));
                event.target.files = dt.files; // update input files
            });
        };
        fileReader.readAsDataURL(file);
    });
});
</script>

<!-- --------------------------------------------------------------------------------------------------------------------------------------style -->
<style>
#intake-form button {
    display: none;
}

#intake-form button.active {
    display: block;
    background-color: #2563eb;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

#intake-form button.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

#intake-form-container select {
    padding: 0px 10px;
    border-radius: 10px;
    background-color: #f5f5f5;
    border: 1px solid #e5e7eb;
    font-size: 1rem;
    font-weight: medium;
    color: #6b7280;
    font-family: 'Inter', sans-serif;
    outline: none;
    height: 38px;
}


#intake-form-container select option {
    font-size: 1rem;
    font-weight: 400;
    color: #6b7280;
    font-family: 'Inter', sans-serif;
    background-color: #f5f5f5;
    border: 1px solid #e5e7eb;
    outline: none;
}

#intake-form-container input,
#intake-form-container textarea {
    padding: 10px;
    border-radius: 10px;
    height: 35px;
    background-color: #f5f5f5;
    border: 1px solid #e5e7eb;
    font-size: 1rem;
    font-weight: medium;
    color: #6b7280;
    font-family: 'Inter', sans-serif;
    outline: none;
}

#intake-form-container textarea {
    height: 50px;
}

#intake-form-container input:focus,
#intake-form-container select:focus,
#intake-form-container textarea:focus {
    border: 1px solid rgb(177, 178, 180);
    outline: none;
}


#intake-form-container input.border-red-500,
#intake-form-container select.border-red-500,
#intake-form-container textarea.border-red-500 {
    border: 1px solid red;
}

#intake-form-container input.border-gray-300,
#intake-form-container select.border-gray-300,
#intake-form-container textarea.border-gray-300 {
    border: 1px solid #e5e7eb;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e5e7eb;
    /* Tailwind gray-200 */
    border-top: 4px solid #3b82f6;
    /* Tailwind blue-500 */
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: auto;
    /* center horizontally */
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}


/* Progress Bar Container */
.progress-bar-container {
    background-color: white;
    padding: 30px 30px;
    border-bottom: 1px solid #e5e7eb;
}

/* Flex container for step alignment */
.progress-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Step Circle Styles */
.step-circle {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    background-color: #e5e7eb;
    color: #6b7280;
    font-weight: bold;
    font-size: 1rem;
    transition: all 0.3s ease;
}

/* Active Circle Styles */
.step-circle.active {
    background-color: #2563eb;
    color: white;
}

/* Label below the circle */
.step-label {
    position: absolute;
    bottom: -20px;
    font-size: 0.75rem;
    color: #6b7280;
    text-align: center;
}

/* Line between circles */
.step-line {
    width: 4rem;
    height: 2px;
    background-color: #e5e7eb;
    transition: background-color 0.3s ease;
}

/* Active Line Styles */
.step-line.active {
    background-color: #2563eb;
}

/* Completed Step Styles */
.step-circle.completed {
    background-color: #34d399;
    color: white;
}

.step-line.completed {
    background-color: #34d399;
}

/* Styling the step number */
.step-number {
    font-size: 1rem;
    font-weight: medium;
}

/* Responsive Styling: Stack steps on smaller screens */
@media (max-width: 640px) {
    .progress-bar {
        flex-direction: column;
        align-items: flex-start;
    }

    .step-circle {
        margin-bottom: 1rem;
    }

    .step-line {
        width: 3rem;
    }
}

.package-selection-card.selected {
    border: 2px solid #2563eb;
}
</style>
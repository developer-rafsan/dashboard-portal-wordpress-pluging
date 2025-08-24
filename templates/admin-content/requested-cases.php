<div id="requested-case-content-admin" class="content-section hidden">
    <div id="cash-cases-header" class="flex justify-between items-center">
        <div id="cash-cases-header-left">
            <h3 style="font-size: 24px; font-weight: 600; text-transform: capitalize; margin-bottom: 5px;">Requested
                Cases
            </h3>
            <p style="font-size: 16px; font-weight: 400; color: #00000070; text-transform: capitalize;">Manage and track
                Requested cases here.</p>
        </div>
    </div>



    <!-- Case List -->
    <div id="case-list-container" class="mt-4 space-y-4">
        <?php
        if(!empty($requested_cases)){
            foreach($requested_cases as $case){
                ?>
        <div class="flex justify-between items-center bg-white py-4 px-6 rounded-md border border-gray-200 case-card">
            <div>
                <h3 style="font-size: 22px; font-weight: 500; text-transform: capitalize;"> Nexus Letter Application -
                    <?php echo $case->first_name; ?> <?php echo $case->last_name; ?>
                </h3>
                <div class="flex items-center gap-4 mt-1">
                    <p style="font-size: 14px; font-weight: 400; text-transform: capitalize; color: #00000070;">Case ID:
                        <span style="font-weight: 500; color: #000000;"><?php echo $case->case_id; ?></span>
                    </p>
                    <p style="font-size: 14px; font-weight: 400; text-transform: capitalize; color: #00000070;">User:
                        <span style="font-weight: 500; color: #000000;"><?php echo $case->user_name; ?></span>
                    </p>
                </div>
                <ul class="flex items-center gap-2 mt-4">
                    <li>
                        <?php if($case->payment_status == 'completed'){
                                    ?>
                        <span class="bg-green-200 text-green-700 text-sm px-2 py-1 rounded-full cursor-pointer">
                            <i class="fa-solid fa-circle-check"></i> Paid
                        </span>
                        <?php
                                 } else {
                                    ?>
                        <span
                            class="bg-red-500 text-white text-opacity-80 text-sm px-2 py-1 rounded-full cursor-pointer">
                            <i class="fa-solid fa-circle-xmark"></i> Unpaid
                        </span>
                        <?php
                                 } ?>
                    </li>
                    <li>
                        <span
                            class="bg-blue-100 text-blue-700 text-opacity-80 text-sm px-2 py-1 rounded-full cursor-pointer capitalize">
                            <?php echo $case->case_status; ?>
                        </span>
                    </li>
                    <li>
                        <span
                            class="bg-blue-100 text-blue-700 text-opacity-80 text-sm px-2 py-1 rounded-full cursor-pointer capitalize">
                            <?php echo empty($case->case_package_type) ? 'No Selection' : $case->case_package_type; ?>
                        </span>
                    </li>
                </ul>
            </div>

            <div class="flex items-center justify-end gap-2">

                <!-- View Details Button -->
                <button 
                    onclick="openCaseDetailsModal('<?php echo $case->case_id; ?>')"
                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors">
                    <i class="fa-solid fa-eye"></i> View Details
                </button>

                <?php if ( $case->case_status === 'approved' ) : ?>
                <p class="text-sm text-gray-600">Payment is pending. The amount will be credited once received.</p>
                <?php else : ?>
                <button 
                data-case-id="<?php echo $case->case_id; ?>" 
                data-action="approved"
                class="bg-yellow-500 text-white px-4 py-2 rounded-md new-case-action hover:bg-yellow-600 transition-colors">
                    <i class="fa-solid fa-check"></i> Approve
                </button>

                <button 
                data-case-id="<?php echo $case->case_id; ?>" 
                data-action="rejected"
                class="bg-red-500 text-white px-4 py-2 rounded-md new-case-action hover:bg-red-600 transition-colors">
                    <i class="fa-solid fa-times"></i> Reject
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php
            }   
        } else {
            ?>
        <div class="flex justify-center items-center h-full">
            <div class="text-center text-gray-500">
                <i class="fa-solid fa-inbox text-4xl mb-4"></i>
                <p>No active cases found</p>
            </div>
        </div>
        <?php
        }
        ?>
    </div>
</div>


<script>
const newCaseAction = document.querySelectorAll(".new-case-action");
newCaseAction.forEach(action => {
    action.addEventListener("click", () => {        
        const data = {
            case_id: action.dataset.caseId,
            action: action.dataset.action
        };        

        const formData = new FormData();
        formData.append('action', 'update_request_case_status');
        formData.append('_ajax_nonce', ajax_object.nonce);
        formData.append('case_id', data.case_id);
        formData.append('status', data.action);

        fetch(ajax_object.ajax_url, {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const notificationContainer = document.querySelector('.notification-container');

                if (data.success) {
                    notificationContainer.querySelector('#notification-title').textContent = 'Updated Successfully';
                } else {
                    notificationContainer.querySelector('#notification-title').textContent = 'Update Failed';
                }
            })
            .catch(error => {
                const notificationContainer = document.querySelector('.notification-container');
                notificationContainer.querySelector('#notification-title').textContent = 'Update Failed';
            })
            .finally(() => {
                const notificationContainer = document.querySelector('.notification-container');
                notificationContainer.classList.add('active');
                setTimeout(() => {
                    notificationContainer.classList.remove('active');
                    window.location.reload();
                }, 1000);
            });
    });
});
</script>
<?php
$current_user = wp_get_current_user();
?>

<!-- New Cases Content -->
<div id="rejected-cases-content" class="hidden">
    <div id="cash-cases-header" class="flex justify-between items-center">
        <div id="cash-cases-header-left">
            <h3 style="font-size: 24px; font-weight: 600; text-transform: capitalize; margin-bottom: 5px;">Rejected
                Cases
            </h3>
            <p style="font-size: 16px; font-weight: 400; color: #00000070; text-transform: capitalize;">Manage and track
                Rejected cases here.</p>
        </div>
    </div>



    <!-- Case List -->
    <div id="case-list-container" class="mt-4 space-y-4">
        <?php
        if(!empty($rejected_cases)){
            foreach($rejected_cases as $case){
                ?>
        <div class="flex justify-between items-center bg-white py-4 px-6 rounded-md border border-red-500">
            <div>
                <h3 style="font-size: 22px; font-weight: 500; text-transform: capitalize;"> Nexus Letter Application -
                    <?php echo $case->first_name; ?> <?php echo $case->last_name; ?>
                </h3>
                <div class="flex items-center gap-4 mt-1">
                    <p style="font-size: 14px; font-weight: 400; text-transform: capitalize; color: #00000070;">Case ID:
                        <span style="font-weight: 500; color: #000000;"><?php echo $case->case_id; ?></span>
                    </p>
                    <p style="font-size: 14px; font-weight: 400; text-transform: capitalize; color: #00000070;">User:
                        <span
                            style="font-weight: 500; color: #000000;"><?php echo $current_user->display_name; ?></span>
                    </p>
                </div>
                <ul class="flex items-center gap-2 mt-4">
                    <li>
                        <?php if($case->payment_status == 'paid'){
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
                <div class="bg-blue-500 text-white px-4 py-2 rounded-md cursor-pointer view-btn" 
                     data-case-id="<?php echo $case->case_id; ?>">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <p class="text-sm text-blue-900">Case rejected by doctor. Please contact support.</p>
                <i id="case-info-icon" class="fa-solid fa-circle-info text-blue-900"></i>
            </div>
        </div>
        <?php
            }   
        } else {
            ?>
        <div class="flex justify-center items-center h-full">
            <!-- Add New Appointment Button -->
            <div id="add-appointment-btn"
                class="cursor-pointer bg-blue-500 text-white px-4 py-2 rounded-md add-appointment-btn">
                <div class="flex items-center gap-2"></div>
                <i class="fa-solid fa-plus mr-2"></i> Add new Appointment
            </div>
        </div>
        <?php
        }
        ?>
    </div>
</div>
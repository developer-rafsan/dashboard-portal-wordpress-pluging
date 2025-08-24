<!-- Unpaid Cases Content -->
<div id="unpaid-case-content-admin" class="content-section">
    <div id="unpaid-cases-header" class="flex justify-between items-center">
        <div id="unpaid-cases-header-left">
            <h3 style="font-size: 24px; font-weight: 600; text-transform: capitalize; margin-bottom: 5px;">Unpaid Cases</h3>
            <p style="font-size: 16px; font-weight: 400; color: #00000070; text-transform: capitalize;">Manage unpaid cases here.</p>
        </div>
    </div>

    <!-- Case List -->
    <div id="unpaid-case-list-container" class="mt-4 space-y-4">
        <?php
        if(!empty($unpaid)){
            foreach($unpaid as $case){
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
                        <span
                            class="bg-red-500 text-white text-opacity-80 text-sm px-2 py-1 rounded-full cursor-pointer">
                            <i class="fa-solid fa-circle-xmark"></i> Unpaid
                        </span>
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
            </div>
        </div>
        <?php
            }   
        } else {
            ?>
        <div class="flex justify-center items-center h-full">
            <div class="text-center text-gray-500">
                <i class="fa-solid fa-money-bill text-4xl mb-4"></i>
                <p>No unpaid cases found</p>
            </div>
        </div>
        <?php
        }
        ?>
    </div>
</div>
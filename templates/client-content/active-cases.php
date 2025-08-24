<?php
defined('ABSPATH') or die('No direct access.');
?>

<!-- --------------------------------------------------------------------------------------------------- case-overview.php -->
<div id="active-cases-content">
    <div id="cash-overview-header" class="flex justify-between items-center">
        <div id="cash-overview-header-left">
            <h3 style="font-size: 24px; font-weight: 600; text-transform: capitalize; margin-bottom: 5px;">Active Cases
            </h3>
            <p style="font-size: 16px; font-weight: 400; color: #00000070; text-transform: capitalize;">Manage and track
                Active cases here.</p>
        </div>

        <?php 
        if(!empty($active_cases)) {
            ?>
        <!-- Add New Appointment Button -->
        <div id="add-appointment-btn"
            class="cursor-pointer bg-blue-500 text-white px-4 py-2 rounded-md add-appointment-btn">
            <i class="fa-solid fa-plus mr-2"></i> Add new Appointment
        </div>
        <?php
        }
        ?>


    </div>

    <!-- Case List -->
    <div id="case-list-container" class="mt-4 space-y-4">
        <?php
        if(!empty($active_cases)){
            foreach($active_cases as $case){
                ?>
        <div class="flex justify-between items-center bg-white py-4 px-6 rounded-md border border-gray-200">
            <div>
                <h3 style="font-size: 22px; font-weight: 500; text-transform: capitalize;"> Nexus Letter Application -
                    <?php echo $case->first_name; ?> <?php echo $case->last_name; ?>
                </h3>
                <div class="flex items-center gap-4 mt-1">
                    <p style="font-size: 14px; font-weight: 600; text-transform: capitalize; color: #00000070;">Case ID:
                        <span style="font-weight: 600; color: #000000;"><?php echo $case->case_id; ?></span>
                    </p>
                    <p style="font-size: 14px; font-weight: 600; text-transform: capitalize; color: #00000070;">User:
                        <span style="font-weight: 600; color: #000000;"><?php echo $case->user_name; ?></span>
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
                <?php
        $document_paths = !empty($case->admin_document_paths) ? json_decode($case->admin_document_paths, true) : [];

        if (!empty($document_paths)) : ?>
                <div onclick="downloadDocuments()" class="bg-red-500 text-white px-4 py-2 rounded-md cursor-pointer">
                    <i class="fa-solid fa-file-download"></i>
                </div>

                <script>
                function downloadDocuments() {
                    let files = <?php echo json_encode($document_paths); ?>;
                    files.forEach(function(file) {
                        let link = document.createElement('a');
                        link.href = file;
                        link.download = file.split('/').pop(); // Gets filename
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    });
                }
                </script>
                <?php endif; ?>
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
                <i class="fa-solid fa-plus mr-2"></i> Add new Appointment
            </div>
        </div>
        <?php
        }
        ?>
    </div>
</div>
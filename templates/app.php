<?php
/**
 * Main application template for BrightsideAI
 */
?>
<div 
    x-data="brightsideApp" 
    class="brightsideai-app min-h-screen bg-gray-50"
    x-cloak
>
    <?php require_once BRIGHTSIDEAI_PATH . 'templates/partials/loading-spinner.php'; ?>

    <!-- Dashboard View -->
    <div x-show="view === 'dashboard'" class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <?php require_once BRIGHTSIDEAI_PATH . 'templates/partials/header.php'; ?>

            <!-- Include Projects Grid -->
            <?php require_once BRIGHTSIDEAI_PATH . 'templates/partials/projects-grid.php'; ?>
        </div>
    </div>

    <!-- Project View -->
    <div x-show="view === 'project'" class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Project Header -->
            <?php require_once BRIGHTSIDEAI_PATH . 'templates/partials/views/project/header.php'; ?>

            <!-- Project Progress -->
            <?php require_once BRIGHTSIDEAI_PATH . 'templates/partials/views/project/progress.php'; ?>
        
            <!-- Project Actions -->
            <?php require_once BRIGHTSIDEAI_PATH . 'templates/partials/views/project/actions.php'; ?>
            
        </div>
    </div>

    <!-- Script Builder View -->
    <div x-show="view === 'builder'" class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Builder Header -->
            <?php require_once BRIGHTSIDEAI_PATH . 'templates/partials/views/script-builder/header.php'; ?>


            <!-- Script Builder Content -->
            <?php require_once BRIGHTSIDEAI_PATH . 'templates/partials/views/script-builder/content.php'; ?>

        </div>
    </div>

    <!-- Marketing Suite View -->
    <div x-show="view === 'marketing'" class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Marketing Header -->
            <?php require_once BRIGHTSIDEAI_PATH . 'templates/partials/views/marketing/header.php'; ?>

            <!-- Marketing Suite Content -->
            <?php require_once BRIGHTSIDEAI_PATH . 'templates/partials/views/marketing/content.php'; ?>

        </div>
    </div>

    <!-- Modals -->
   
    <!-- Include Modals -->
    <?php 
    require_once BRIGHTSIDEAI_PATH . 'templates/modals/buy-credits.php';
    require_once BRIGHTSIDEAI_PATH . 'templates/modals/new-project.php';
    require_once BRIGHTSIDEAI_PATH . 'templates/modals/edit-project.php';
    ?>
    <!-- Toast Notifications -->
    <div
        x-show="toastVisible"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-x-full"
        x-transition:enter-end="opacity-100 transform translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform translate-x-full"
        class="fixed right-0 top-0 mt-4 mr-4"
    >
        <!-- Toast content will be dynamically inserted here -->
    </div>
</div>
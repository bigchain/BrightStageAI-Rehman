<div class="flex items-center justify-between mb-8">
                <!-- Logo on the left -->
                <img 
                    src="<?php echo BRIGHTSIDEAI_URL; ?>assets/images/placeholder-logo.svg" 
                    alt="BrightStage AI"
                    class="h-8 w-auto"
                />
                
                <!-- Right-side elements in a single line -->
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 mr-2">Credits: <span class="font-medium text-gray-900" x-text="credits"></span></span>
                    
                    <button 
                        data-modal-target="buy-credits-modal" 
                        data-modal-toggle="buy-credits-modal" 
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium mr-4"
                    >
                        Buy Credits
                    </button>

                    <button 
                        data-modal-target="new-project-modal" 
                        data-modal-toggle="new-project-modal" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300"
                    >
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Project
                    </button>
                </div>
            </div>
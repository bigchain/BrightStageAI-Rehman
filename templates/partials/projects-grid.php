<?php
/**
 * Projects Grid Partial
 * 
 * @package BrightsideAI
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<!-- Projects Grid -->
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    <template x-for="project in projects" :key="project.id">
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-all duration-200">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <h3 
                        class="text-lg font-semibold text-gray-900 hover:text-indigo-600 cursor-pointer transition-colors"
                        x-text="project.name"
                        @click="setCurrentProject(project)"
                    ></h3>

                    
                    <!-- Project Actions Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button 
                            @click="open = !open"
                            class="text-gray-400 hover:text-gray-500"
                            type="button"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div 
                            x-show="open" 
                            @click.away="open = false"
                            class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                        >
                            <button 
                                @click="editProject(project); open = false"
                                class="relative inline-flex items-center w-full px-4 py-2 text-sm font-medium border-b border-gray-200 rounded-t-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700"
                            >
                                <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5.2l-7.4 7.4L4 14l1.4-3.6L13.8 2l1.2.4M15 5.2l2.8-2.8L15 0M2 20h16"/>
                                </svg>
                                Edit Project
                            </button>
                            <button 
                                @click="deleteProject(project); open = false"
                                class="relative inline-flex items-center w-full px-4 py-2 text-sm font-medium rounded-b-lg hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:ring-2 focus:ring-red-700 focus:text-red-700"
                            >
                                <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete Project
                            </button>
                        </div>
                    </div>
                </div>

                <p class="mt-2 text-sm text-gray-500" x-text="project.shortDescription"></p>
                
                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="relative pt-1">
                        <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                            <div
                                class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500 transition-all duration-300"
                                :style="'width: ' + project.progress + '%'"
                            ></div>
                        </div>
                        <div class="mt-2 flex justify-between items-center">
                            <p class="text-xs text-gray-500" x-text="project.progress + '% Complete'"></p>
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <span class="text-xs text-gray-500" x-text="(project.creditsUsed || 0) + ' Credits Used'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Footer -->
                <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm text-gray-500" x-text="formatDate(project.createdAt)"></span>
                    </div>
                    <button 
                        @click="setCurrentProject(project)"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-50 hover:bg-indigo-100"
                    >
                        Open Project
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
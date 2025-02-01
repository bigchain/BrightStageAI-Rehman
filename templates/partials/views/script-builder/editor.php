<?php
/**
 * Script Editor Partial
 * 
 * @package BrightsideAI
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="bg-white shadow rounded-lg p-6">
    <div class="space-y-6">
        <!-- Description Section -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Webinar Description
            </label>
            
            <!-- Tips Section -->
            <div class="mb-4">
                <details class="group" open>
                    <summary class="cursor-pointer text-sm text-gray-500 hover:underline">
                        Tips for a great description
                    </summary>
                    <div class="mt-2 space-y-1 text-sm text-gray-500 pl-4">
                        <p>• Clearly define your target audience</p>
                        <p>• List 3-5 key learning objectives</p>
                        <p>• Mention specific takeaways</p>
                        <p>• Keep it between 50-1000 characters</p>
                    </div>
                </details>
            </div>

            <div class="mt-1">
                <textarea
                    x-model="webinarDescription"
                    rows="6"
                    class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md"
                    placeholder="Describe your webinar, including topic, objectives, key points, and audience..."
                ></textarea>
            </div>
        </div>

        <!-- Duration Section -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Duration (minutes)
            </label>
            <input 
                type="range" 
                x-model="webinarDuration"
                min="15"
                max="45"
                step="5"
                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer mt-2"
            >
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>15m</span>
                <span>30m</span>
                <span>45m</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-4">
            <button 
                @click="enhanceDescription"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
                :disabled="isEnhancing || !webinarDescription"
            >
                <template x-if="!isEnhancing">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </template>
                <template x-if="isEnhancing">
                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                </template>
                <span x-text="isEnhancing ? 'Enhancing...' : 'Enhance with AI'"></span>
            </button>

            <button 
                @click="generateScript"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700"
                :disabled="isGenerating || !enhancedDescription"
            >
                <template x-if="!isGenerating">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
                <template x-if="isGenerating">
                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                </template>
                <span x-text="isGenerating ? 'Generating...' : 'Generate Script'"></span>
            </button>
        </div>

        <!-- Enhanced Description Alert -->
        <template x-if="enhancedDescription && !slideContent">
            <div class="rounded-md bg-blue-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Description Enhanced</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Your description has been enhanced. Click 'Generate Content' to create your slides and narration.</p>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Generated Content Section -->
        <template x-if="slideContent">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Slide Content (Markdown Format)</label>
                    <textarea
                        x-model="slideContent"
                        rows="12"
                        class="mt-2 shadow-sm block w-full sm:text-sm border-gray-300 rounded-md font-mono"
                    ></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Narration Text</label>
                    <textarea
                        x-model="narrationText"
                        rows="8"
                        class="mt-2 shadow-sm block w-full sm:text-sm border-gray-300 rounded-md"
                    ></textarea>
                </div>
            </div>
        </template>

        <!-- Save and Navigate Buttons -->
        <template x-if="slideContent && narrationText">
            <div class="flex justify-between pt-4">
                <button 
                    @click="saveChanges"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
                    :disabled="isSaving"
                >
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span x-text="isSaving ? 'Saving...' : 'Save Changes'"></span>
                </button>

                <!-- Add Navigation Button -->
                <button 
                    @click="navigateToSlides"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50"
                    :disabled="!currentProject?.script"
                >
                    <span>Generate Slides</span>
                    <svg class="h-5 w-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </template>
    </div>
</div>
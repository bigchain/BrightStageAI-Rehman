<?php
/**
 * Slides Builder Template
 * 
 * @package BrightsideAI
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="max-w-7xl mx-auto">
    <!-- Main Grid Layout -->
    <div class="grid md:grid-cols-2 gap-4">
        <!-- Configuration Panel -->
        <div class="space-y-6">
            <!-- Voice Selection -->
            <div>
                <h3 class="text-sm font-medium text-gray-900 mb-2">Voice Selection</h3>
                <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown" class="w-full justify-between text-gray-900 bg-white border border-gray-300 hover:bg-gray-50 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
                    Emma
                    <svg class="w-4 h-4 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700">
                    <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownDefaultButton">
                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">Emma</a></li>
                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">John</a></li>
                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">Sarah</a></li>
                    </ul>
                </div>
                
                <div class="grid md:grid-cols-2 items-center gap-4">
                    <button onclick="playPreviewVoice()" class="mt-2 text-gray-900 bg-white border border-gray-300 hover:bg-gray-50 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Preview Voice
                    </button>
                    <div class="mt-2 text-sm text-gray-600">Friendly and professional voice</div>
                </div>
                <audio id="previewAudio" src="https://audio-previews.elements.envatousercontent.com/files/266360320/preview.mp3"></audio>
            </div>

            <!-- Template Selection -->
            <div>
                <h3 class="text-sm font-medium text-gray-900 mb-4">Presentation Template</h3>
                <div class="space-y-4">
                    <!-- Modern Minimal -->
                    <div class="flex items-center p-4 border rounded-lg max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                        <input checked id="template-modern" type="radio" value="modern" name="template" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <div class="ms-4 flex-1">
                            <label for="template-modern" class="text-sm font-medium text-gray-900">Modern Minimal</label>
                            <p class="text-sm text-gray-500">Clean and contemporary design</p>
                        </div>
                    </div>
                    <!-- Corporate Professional -->
                    <div class="flex items-center p-4 border rounded-lg max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                        <input id="template-corporate" type="radio" value="corporate" name="template" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <div class="ms-4 flex-1">
                            <label for="template-corporate" class="text-sm font-medium text-gray-900">Corporate Professional</label>
                            <p class="text-sm text-gray-500">Traditional business style</p>
                        </div>
                    </div>
                    <!-- Creative Impact -->
                    <div class="flex items-center p-4 border rounded-lg max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                        <input id="template-creative" type="radio" value="creative" name="template" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <div class="ms-4 flex-1">
                            <label for="template-creative" class="text-sm font-medium text-gray-900">Creative Impact</label>
                            <p class="text-sm text-gray-500">Bold and dynamic design</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Headshot -->
            <div>
                <h3 class="text-sm font-medium text-gray-900 mb-2">Presenter Headshot</h3>
                <label class="flex flex-col items-center justify-center h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="mb-2 text-sm text-gray-500">Click to upload</p>
                        <p class="text-xs text-gray-500">PNG, JPG (MAX. 800x800px)</p>
                    </div>
                    <input type="file" class="hidden" accept="image/*" />
                </label>
            </div>
        </div>

        <!-- Preview Grid -->
        <div>
            <div>
                <button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700 w-full">Generate Slides</button>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <!-- Introduction -->
                <div class="rounded-lg max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <img class="w-10 h-10 rounded-sm" src="<?php echo BRIGHTSIDEAI_URL; ?>assets/images/placeholderimage.png" alt="Default avatar">
                    <p class="text-lg font-semibold text-gray-900 hover:text-indigo-600 cursor-pointer transition-colors flex flex-col items-center">Introduction</p>
                </div>
                <!-- Key Points -->
                <div class="rounded-lg max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                     <img class="w-10 h-10 rounded-sm" src="<?php echo BRIGHTSIDEAI_URL; ?>assets/images/placeholderimage.png" alt="Default avatar">
                    <p class="text-lg font-semibold text-gray-900 hover:text-indigo-600 cursor-pointer transition-colors flex flex-col items-center">Key Points</p>
                </div>
                <!-- Main Content 1 -->
                <div class="rounded-lg max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                     <img class="w-10 h-10 rounded-sm" src="<?php echo BRIGHTSIDEAI_URL; ?>assets/images/placeholderimage.png" alt="Default avatar">
                    <p class="text-lg font-semibold text-gray-900 hover:text-indigo-600 cursor-pointer transition-colors flex flex-col items-center">Main Content 1</p>
                </div>
                <!-- Main Content 2 -->
                <div class="rounded-lg max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                     <img class="w-10 h-10 rounded-sm" src="<?php echo BRIGHTSIDEAI_URL; ?>assets/images/placeholderimage.png" alt="Default avatar">
                    <p class="text-lg font-semibold text-gray-900 hover:text-indigo-600 cursor-pointer transition-colors flex flex-col items-center">Main Content 2</p>
                </div>
                <!-- Call to Action -->
                <div class="rounded-lg max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                     <img class="w-10 h-10 rounded-sm" src="<?php echo BRIGHTSIDEAI_URL; ?>assets/images/placeholderimage.png" alt="Default avatar">
                    <p class="text-lg font-semibold text-gray-900 hover:text-indigo-600 cursor-pointer transition-colors flex flex-col items-center">Call to Action</p>
                </div>
                <!-- Q&A -->
                <div class="rounded-lg max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                     <img class="w-10 h-10 rounded-sm" src="<?php echo BRIGHTSIDEAI_URL; ?>assets/images/placeholderimage.png" alt="Default avatar">
                    <p class="text-lg font-semibold text-gray-900 hover:text-indigo-600 cursor-pointer transition-colors flex flex-col items-center">Q&A</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Actions -->
    <div class="flex gap-4 mt-8 justify-between">
        <div class="flex gap-4">
            <button class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download PPTX
            </button>
            <button class="inline-flex items-center px-5 py-2.5 text-sm font-medium py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Generate Video
            </button>
        </div>
        <button class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-blue-300">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Upload Enhanced Version
        </button>
    </div>
</div>

<script>
function playPreviewVoice() {
    const audio = document.getElementById('previewAudio');
    audio.play();
}
</script>
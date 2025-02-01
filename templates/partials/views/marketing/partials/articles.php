<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-xl font-medium text-gray-900">Articles</h2>
        <p class="mt-1 text-sm text-gray-500">Create engaging articles about your webinar</p>
    </div>

    <!-- Article Context Input -->
    <div class="space-y-2">
        <label for="article-context" class="block text-sm font-medium text-gray-700">Article Context</label>
        <textarea
            id="article-context"
            rows="4"
            class="shadow-sm block w-full sm:text-sm border border-gray-300 rounded-md"
            placeholder="Add any specific points, angles, or context you'd like to include in the article. This will be combined with your webinar content."
        ></textarea>
    </div>

    <!-- Generated Article Area -->
    <div class="space-y-2">
        <label for="generated-article" class="block text-sm font-medium text-gray-700">Generated Article</label>
        <textarea
            id="generated-article"
            rows="12"
            class="shadow-sm block w-full sm:text-sm border border-gray-300 rounded-md font-mono"
            placeholder="Generate an article..."
            readonly
        ></textarea>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between">
        <button 
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
        >
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Generate Article
        </button>
        <button class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download
        </button>
    </div>
</div>
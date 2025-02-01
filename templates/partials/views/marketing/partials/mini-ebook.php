<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-xl font-medium text-gray-900">Mini eBook</h2>
        <p class="mt-1 text-sm text-gray-500">Create a comprehensive guide based on your webinar content. This eBook will serve as a valuable lead magnet, expanding on the topics covered in your webinar and providing additional insights and resources.</p>
    </div>

    <!-- Book Title Input -->
    <div class="space-y-2">
        <label for="book-title" class="block text-sm font-medium text-gray-700">Book Title</label>
        <input
            type="text"
            id="book-title"
            class="shadow-sm block w-full sm:text-sm border border-gray-300 rounded-md"
            placeholder="Add a Title, AI will decide the final for you"
        />
    </div>

    <!-- Additional Context -->
    <div class="space-y-2">
        <label for="additional-context" class="block text-sm font-medium text-gray-700">Additional Context</label>
        <textarea
            id="additional-context"
            rows="4"
            class="shadow-sm block w-full sm:text-sm border border-gray-300 rounded-md"
            placeholder="This book will take context from the webinar. Please add any additional guides (no need to add full context as we will create one from the webinar, except you say otherwise)"
        ></textarea>
    </div>

    <!-- Key Concept -->
    <div class="space-y-2">
        <label for="key-concept" class="block text-sm font-medium text-gray-700">Key Concept</label>
        <input
            type="text"
            id="key-concept"
            class="shadow-sm block w-full sm:text-sm border border-gray-300 rounded-md"
            placeholder="What is the key concept you want to emphasize?"
        />
    </div>

    <!-- Generate Button -->
    <div>
        <button 
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
        >
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Generate eBook Structure
        </button>
    </div>
</div>
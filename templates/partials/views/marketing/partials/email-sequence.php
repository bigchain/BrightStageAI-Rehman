<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-xl font-medium text-gray-900">Email Sequence</h2>
        <p class="mt-1 text-sm text-gray-500">Create a series of promotional emails</p>
    </div>

    <!-- Email Sequence Input -->
    <div class="space-y-2">
        <label for="email-guidance" class="block text-sm font-medium text-gray-700">Email Sequence Guidance</label>
        <textarea
            id="email-guidance"
            rows="8"
            class="shadow-sm block w-full sm:text-sm border border-gray-300 rounded-md font-mono"
            placeholder="Provide guidance for email sequence generation:
- Target audience characteristics
- Key pain points to address
- Special offers or bonuses
- Unique value propositions
- Call-to-action preferences
- Preferred tone (formal/casual)
- Any specific dates/times for the webinar

AI will generate a strategic 7-email sequence based on your input and webinar content."
        ></textarea>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between">
        <button 
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
        >
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Generate Sequence
        </button>
        <button class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download
        </button>
    </div>
</div>
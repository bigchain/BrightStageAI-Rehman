<div class="mt-8 grid gap-6 sm:grid-cols-2">
                <!-- Script Builder Card -->
                <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900">Script Builder</h3>
                        <p class="mt-1 text-sm text-gray-500">Create your webinar script and content.</p>
                        <div class="mt-6">
                            <button 
                                @click="view = 'builder'"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
                            >
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Open Builder
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Marketing Suite Card -->
                <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900">Marketing Suite</h3>
                        <p class="mt-1 text-sm text-gray-500">Generate promotional content for your webinar.</p>
                        <div class="mt-6">
                            <button 
                                @click="view = 'marketing'"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
                            >
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                                Open Marketing Suite
                            </button>
                        </div>
                    </div>
                </div>
            </div>
<div class="mt-8 bg-white overflow-hidden shadow rounded-lg p-6">
    <h2 class="text-lg font-medium text-gray-900 mb-4">Project Progress</h2>
    <div class="relative pt-1">
        <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
            <div
                class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-300"
                :style="'width: ' + currentProject?.progress + '%'"
            ></div>
        </div>
        <p class="mt-2 text-sm text-gray-600" x-text="currentProject?.progress + '% Complete'"></p>
    </div>
</div>
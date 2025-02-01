<div class="bg-white shadow rounded-lg p-6">
    <!-- Tab Navigation -->
    <div class="border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500" id="script-builder-tabs" data-tabs-toggle="#script-builder-content" data-tabs-active-classes="text-blue-600 border-b-2 border-blue-600" data-tabs-inactive-classes="border-b-2 border-transparent text-gray-500 hover:text-gray-600 hover:border-gray-300" role="tablist">
            <li class="me-2" role="presentation">
                <button id="editor-tab" data-tabs-target="#editor" type="button" role="tab" aria-controls="editor" aria-selected="true" class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg">
                    <svg class="w-4 h-4 me-2 text-gray-400 group-hover:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.96 2.96 0 0 0 .13 5H5Z"/>
                        <path d="M6.737 11.061a2.961 2.961 0 0 1 .81-1.515l6.117-6.116A4.839 4.839 0 0 1 16 2.141V2a1.97 1.97 0 0 0-1.933-2H7v5a2 2 0 0 1-2 2H0v11a1.969 1.969 0 0 0 1.933 2h12.134A1.97 1.97 0 0 0 16 18v-3.093l-1.546 1.546c-.413.413-.94.695-1.513.81l-3.4.679a2.947 2.947 0 0 1-1.85-.227 2.96 2.96 0 0 1-1.635-3.257l.681-3.397Z"/>
                    </svg>
                    Script Editor
                </button>
            </li>
            <li class="me-2" role="presentation">
                <button id="slides-tab" data-tabs-target="#slides" type="button" role="tab" aria-controls="slides" aria-selected="false" class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg">
                    <svg class="w-4 h-4 me-2 text-gray-400 group-hover:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 18">
                        <path d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10Zm10 0h-4.286A1.857 1.857 0 0 0 10 11.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 18 16.143v-4.286A1.857 1.857 0 0 0 16.143 10Z"/>
                    </svg>
                    Slides
                </button>
            </li>
            <li class="me-2" role="presentation">
                <button id="export-tab" data-tabs-target="#export" type="button" role="tab" aria-controls="export" aria-selected="false" class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg">
                    <svg class="w-4 h-4 me-2 text-gray-400 group-hover:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
                        <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
                    </svg>
                    Export
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Contents -->
    <div id="script-builder-content">
        <div class="hidden p-4 rounded-lg" id="editor" role="tabpanel" aria-labelledby="editor-tab">
            <?php require_once BRIGHTSIDEAI_PATH . 'templates/partials/views/script-builder/editor.php'; ?>
        </div>
        <div class="hidden p-4 rounded-lg" id="slides" role="tabpanel" aria-labelledby="slides-tab">
            <?php require_once BRIGHTSIDEAI_PATH . 'templates/partials/views/script-builder/slides.php'; ?>
        </div>
        <div class="hidden p-4 rounded-lg" id="export" role="tabpanel" aria-labelledby="export-tab">
            <?php require_once BRIGHTSIDEAI_PATH . 'templates/partials/views/script-builder/export.php'; ?>
        </div>
    </div>
</div>
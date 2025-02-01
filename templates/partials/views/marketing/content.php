<div class="bg-white shadow rounded-lg">
    <!-- Tabs -->
    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="marketing-tabs" data-tabs-toggle="#marketing-content" role="tablist">
            <li class="me-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="articles-tab" data-tabs-target="#articles" type="button" role="tab" aria-controls="articles" aria-selected="false">Articles</button>
            </li>
            <li class="me-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="press-release-tab" data-tabs-target="#press-release" type="button" role="tab" aria-controls="press-release" aria-selected="false">Press Release</button>
            </li>
            <li class="me-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="social-posts-tab" data-tabs-target="#social-posts" type="button" role="tab" aria-controls="social-posts" aria-selected="false">Social Posts</button>
            </li>
            <li class="me-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="email-sequence-tab" data-tabs-target="#email-sequence" type="button" role="tab" aria-controls="email-sequence" aria-selected="false">Email Sequence</button>
            </li>
            <li role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="mini-ebook-tab" data-tabs-target="#mini-ebook" type="button" role="tab" aria-controls="mini-ebook" aria-selected="false">Mini eBook</button>
            </li>
        </ul>
    </div>

    <!-- Tab Content -->
    <div id="marketing-content">
        <div class="hidden p-6" id="articles" role="tabpanel" aria-labelledby="articles-tab">
            <?php include plugin_dir_path(__FILE__) . 'partials/articles.php'; ?>
        </div>

        <div class="hidden p-6" id="press-release" role="tabpanel" aria-labelledby="press-release-tab">
            <?php include plugin_dir_path(__FILE__) . 'partials/press-release.php'; ?>
        </div>

        <div class="hidden p-6" id="social-posts" role="tabpanel" aria-labelledby="social-posts-tab">
            <?php include plugin_dir_path(__FILE__) . 'partials/social-posts.php'; ?>
        </div>

        <div class="hidden p-6" id="email-sequence" role="tabpanel" aria-labelledby="email-sequence-tab">
            <?php include plugin_dir_path(__FILE__) . 'partials/email-sequence.php'; ?>
        </div>

        <div class="hidden p-6" id="mini-ebook" role="tabpanel" aria-labelledby="mini-ebook-tab">
            <?php include plugin_dir_path(__FILE__) . 'partials/mini-ebook.php'; ?>
        </div>
    </div>
</div>
<section class="mb-4">
    <a class="small text-decoration-none" href="/press-releases">
        <i class="bi bi-arrow-left me-1"></i>Back to Press Releases</a>
        <h1 class="lh-page-greeting mt-3 mb-1">Press Releases</h1>
        <p class="text-secondary mb-0">Publish or update a press release or new article.</p>
</section>
<section class="card lh-card">
    <div class="card-body p-4 p-lg-5">
        <form method="post" 
              action="/press-release-edit" 
              enctype="multipart/form-data" 
              class="row g-4">

            <div class="col-xl-6 col-lg-6 col-sm-12">
                <input type="hidden" name="_token" value="<?= e($_SESSION['csrf']) ?>">
                <input type="hidden" name="id" value="<?= e($pressRelease['pr_id']) ?>">
                <div class="col-12 mb-3">
                    <label class="form-label" for="title">Title</label>
                    <input class="form-control" id="title" name="title" maxlength="150" value="<?= e($pressRelease['title']) ?>" required>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="description">Event Date</label>
                    <input class="form-control" type="date" name="event_date" value="<?= e($pressRelease['event_date']) ?>" required="">
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label" for="file">Cover Photo</label>
                    <input class="form-control" id="cover_photo" name="cover_photo" type="file" accept=".jpg,.jpeg,.png" >
                    <div class="form-text">JPGE, JPG, PNG. Maximum file size: 3 MB.</div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-sm-12">
                <div class="col-12 mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="5" maxlength="5000"><?= e($pressRelease['description']) ?></textarea>
                </div>
                <?php if (!empty($pressRelease['cover_photo'])): ?>
                <div class="col-12 mb-3">
                    <div class="form-text mb-2">Current cover photo:</div>
                    <img
                        src="<?= e(storage_asset($pressRelease['cover_photo'])) ?>"
                        alt="Current cover photo"
                        class="img-fluid rounded"
                        style="max-height: 200px;"
                    >
                </div>
                <?php endif; ?>

            </div>


            <div class="col-12">

                <div id="press-release-links">

                    <?php
                    if (!empty($pressRelease['links'][0])):
                        $ppr = $pressRelease['links'][0];
                    ?>

                    <!-- Required Primary Link -->
                    <div class="link-section primary-link border border-light-subtle p-3 mb-3 rounded">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Primary Link</h6>
                            <span class="badge text-bg-primary">Primary</span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">News Source</label>
                            <select
                                class="form-select"
                                name="links[0][news_source]"
                                required
                            >
                                <option value="Online News Portals" <?= ($ppr['news_source'] ?? '') === 'Online News Portals' ? 'selected' : '' ?>>Online News Portals</option>
                                <option value="Newspaper (Print)" <?= ($ppr['news_source'] ?? '') === 'Newspaper (Print)' ? 'selected' : '' ?>>Newspaper (Print)</option>
                                <option value="Magazine" <?= ($ppr['news_source'] ?? '') === 'Magazine' ? 'selected' : '' ?>>Magazine</option>
                                <option value="Official Website News" <?= ($ppr['news_source'] ?? '') === 'Official Website News' ? 'selected' : '' ?>>Official Website News</option>
                                <option value="Blogs" <?= ($ppr['news_source'] ?? '') === 'Blogs' ? 'selected' : '' ?>>Blogs</option>
                                <option value="Social Media (SNS News)" <?= ($ppr['news_source'] ?? '') === 'Social Media (SNS News)' ? 'selected' : '' ?>>Social Media (SNS News)</option>
                                <option value="Influencer/Content Creator Updates" <?= ($ppr['news_source'] ?? '') === 'Influencer/Content Creator Updates' ? 'selected' : '' ?>>Influencer/Content Creator Updates</option>
                                <option value="Television News" <?= ($ppr['news_source'] ?? '') === 'Television News' ? 'selected' : '' ?>>Television News</option>
                                <option value="Radio News" <?= ($ppr['news_source'] ?? '') === 'Radio News' ? 'selected' : '' ?>>Radio News</option>
                                <option value="Newsletters / Email Updates" <?= ($ppr['news_source'] ?? '') === 'Newsletters / Email Updates' ? 'selected' : '' ?>>Newsletters / Email Updates</option>
                                <option value="News Aggregators (e.g., Google News)" <?= ($ppr['news_source'] ?? '') === 'News Aggregators (e.g., Google News)' ? 'selected' : '' ?>>News Aggregators (e.g., Google News)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">News Content Type</label>
                            <select
                                class="form-select"
                                name="links[0][news_content_type]"
                                required
                            >
                                <option value="Press Release" <?= ($ppr['news_content_type'] ?? '') === 'Press Release' ? 'selected' : '' ?>>Press Release</option>
                                <option value="News Article" <?= ($ppr['news_content_type'] ?? '') === 'News Article' ? 'selected' : '' ?>>News Article</option>
                                <option value="Feature Story" <?= ($ppr['news_content_type'] ?? '') === 'Feature Story' ? 'selected' : '' ?>>Feature Story</option>
                                <option value="Editorial / Opinion Piece" <?= ($ppr['news_content_type'] ?? '') === 'Editorial / Opinion Piece' ? 'selected' : '' ?>>Editorial / Opinion Piece</option>
                                <option value="Video Feature" <?= ($ppr['news_content_type'] ?? '') === 'Video Feature' ? 'selected' : '' ?>>Video Feature</option>
                                <option value="News Report (TV/Radio)" <?= ($ppr['news_content_type'] ?? '') === 'News Report (TV/Radio)' ? 'selected' : '' ?>>News Report (TV/Radio)</option>
                                <option value="Interview Segment" <?= ($ppr['news_content_type'] ?? '') === 'Interview Segment' ? 'selected' : '' ?>>Interview Segment</option>
                                <option value="Photojournalism / Image Story" <?= ($ppr['news_content_type'] ?? '') === 'Photojournalism / Image Story' ? 'selected' : '' ?>>Photojournalism / Image Story</option>
                                <option value="Podcast / Audio News" <?= ($ppr['news_content_type'] ?? '') === 'Podcast / Audio News' ? 'selected' : '' ?>>Podcast / Audio News</option>
                                <option value="Live Coverage / Breaking News" <?= ($ppr['news_content_type'] ?? '') === 'Live Coverage / Breaking News' ? 'selected' : '' ?>>Live Coverage / Breaking News</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date Released</label>
                            <input
                                class="form-control"
                                type="date"
                                name="links[0][date_released]"
                                value="<?= e($ppr['date_released']) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Media Outlet</label>
                            <input
                                type="text"
                                name="links[0][media_outlet]"
                                class="form-control"
                                value="<?= e($ppr['media_outlet']) ?>"
                                required
                            >
                            <div class="form-text">
                                e.g., ABS-CBN, Manila Standard, GMA Network
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Article URL</label>
                            <input
                                type="url"
                                name="links[0][link]"
                                class="form-control"
                                value="<?= e($ppr['link']) ?>"
                                required
                            >
                        </div>

                        <input
                            type="hidden"
                            name="links[0][is_primary]"
                            value="1"
                        >

                    </div>

                    <?php 
                    endif;
                    ?>

                    <?php 
                    if (!empty($pressRelease['links']) && count($pressRelease['links']) > 1):
                        foreach ($pressRelease['links'] as $k => $pr): 
                        
                    ?>

                    <div class="link-section additional-link border border-light-subtle p-3 mb-3 rounded">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Media Link</h6>

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-danger remove-link"
                            >
                                <i class="bi bi-trash me-1"></i>
                                Remove
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">News Source</label>

                            <select
                                class="form-select news-source"
                                name="links[<?=$k;?>][news_source]"
                                required
                            >
                                <option value="Online News Portals">Online News Portals</option>
                                <option value="Newspaper (Print)">Newspaper (Print)</option>
                                <option value="Magazine">Magazine</option>
                                <option value="Official Website News">Official Website News</option>
                                <option value="Blogs">Blogs</option>
                                <option value="Social Media (SNS News)">Social Media (SNS News)</option>
                                <option value="Influencer/Content Creator Updates">Influencer/Content Creator Updates</option>
                                <option value="Television News">Television News</option>
                                <option value="Radio News">Radio News</option>
                                <option value="Newsletters / Email Updates">Newsletters / Email Updates</option>
                                <option value="News Aggregators (e.g., Google News)">News Aggregators (e.g., Google News)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">News Content Type</label>

                            <select
                                class="form-select news-content-type"
                                name="links[<?=$k;?>][news_content_type]"
                                required
                            >
                                <option value="Press Release">Press Release</option>
                                <option value="News Article">News Article</option>
                                <option value="Feature Story">Feature Story</option>
                                <option value="Editorial / Opinion Piece">Editorial / Opinion Piece</option>
                                <option value="Video Feature">Video Feature</option>
                                <option value="News Report (TV/Radio)">News Report (TV/Radio)</option>
                                <option value="Interview Segment">Interview Segment</option>
                                <option value="Photojournalism / Image Story">Photojournalism / Image Story</option>
                                <option value="Podcast / Audio News">Podcast / Audio News</option>
                                <option value="Live Coverage / Breaking News">Live Coverage / Breaking News</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date Released</label>

                            <input
                                type="date"
                                class="form-control date-released"
                                name="links[<?=$k;?>][date_released]"
                                value="<?= e($pr['date_released']) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Media Outlet</label>

                            <input
                                type="text"
                                class="form-control media-outlet"
                                name="links[<?=$k;?>][media_outlet]"
                                value="<?= e($pr['media_outlet']) ?>"
                                required
                            >

                            <div class="form-text">
                                e.g., ABS-CBN, Manila Standard, GMA Network
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Article URL</label>

                            <input
                                type="url"
                                class="form-control article-link"
                                name="links[<?=$k;?>][link]"
                                value="<?= e($pr['link']) ?>"
                                required
                                placeholder="https://example.com/article"
                            >
                        </div>

                        <input
                            type="hidden"
                            class="is-primary"
                            value="0"
                        >

                    </div>

                    <?php endforeach; 
                    endif;
                    ?>

                </div>


                <button
                    type="button"
                    class="btn btn-outline-primary"
                    id="add-link"
                >
                    + Add Another Media Link
                </button>
            </div>


            <div class="col-12 d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="/press-releases">Cancel</a>
                <button class="btn btn-lh-primary" type="submit">
                    <i class="bi bi-cloud-arrow-up me-2"></i>Upload Press Release
                </button>
            </div>
        </form>
    </div>
</section>



<template id="link-template">

    <div class="link-section additional-link border border-light-subtle p-3 mb-3 rounded">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Media Link</h6>

            <button
                type="button"
                class="btn btn-sm btn-outline-danger remove-link"
            >
                <i class="bi bi-trash me-1"></i>
                Remove
            </button>
        </div>

        <div class="mb-3">
            <label class="form-label">News Source</label>

            <select
                class="form-select news-source"
                required
            >
                <option value="Online News Portals">Online News Portals</option>
                <option value="Newspaper (Print)">Newspaper (Print)</option>
                <option value="Magazine">Magazine</option>
                <option value="Official Website News">Official Website News</option>
                <option value="Blogs">Blogs</option>
                <option value="Social Media (SNS News)">Social Media (SNS News)</option>
                <option value="Influencer/Content Creator Updates">Influencer/Content Creator Updates</option>
                <option value="Television News">Television News</option>
                <option value="Radio News">Radio News</option>
                <option value="Newsletters / Email Updates">Newsletters / Email Updates</option>
                <option value="News Aggregators (e.g., Google News)">News Aggregators (e.g., Google News)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">News Content Type</label>

            <select
                class="form-select news-content-type"
                required
            >
                <option value="Press Release">Press Release</option>
                <option value="News Article">News Article</option>
                <option value="Feature Story">Feature Story</option>
                <option value="Editorial / Opinion Piece">Editorial / Opinion Piece</option>
                <option value="Video Feature">Video Feature</option>
                <option value="News Report (TV/Radio)">News Report (TV/Radio)</option>
                <option value="Interview Segment">Interview Segment</option>
                <option value="Photojournalism / Image Story">Photojournalism / Image Story</option>
                <option value="Podcast / Audio News">Podcast / Audio News</option>
                <option value="Live Coverage / Breaking News">Live Coverage / Breaking News</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Date Released</label>

            <input
                type="date"
                class="form-control date-released"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Media Outlet</label>

            <input
                type="text"
                class="form-control media-outlet"
                required
            >

            <div class="form-text">
                e.g., ABS-CBN, Manila Standard, GMA Network
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Article URL</label>

            <input
                type="url"
                class="form-control article-link"
                required
                placeholder="https://example.com/article"
            >
        </div>

        <input
            type="hidden"
            class="is-primary"
            value="0"
        >

    </div>

</template>


<script>
const linksContainer = document.getElementById('press-release-links');
const addLinkButton = document.getElementById('add-link');
const linkTemplate = document.getElementById('link-template');

let linkIndex = <?php echo !empty($pressRelease['links']) ? count($pressRelease['links']) : 1;?>;

addLinkButton.addEventListener('click', () => {

    const clone = linkTemplate.content.cloneNode(true);

    // Set names for the new link
    clone.querySelector('.news-source').name =
        `links[${linkIndex}][news_source]`;

    clone.querySelector('.news-content-type').name =
        `links[${linkIndex}][news_content_type]`;

    clone.querySelector('.date-released').name =
        `links[${linkIndex}][date_released]`;

    clone.querySelector('.media-outlet').name =
        `links[${linkIndex}][media_outlet]`;

    clone.querySelector('.article-link').name =
        `links[${linkIndex}][link]`;

    clone.querySelector('.is-primary').name =
        `links[${linkIndex}][is_primary]`;

    linksContainer.appendChild(clone);

    linkIndex++;
});


linksContainer.addEventListener('click', (event) => {

    const removeButton = event.target.closest('.remove-link');

    if (!removeButton) {
        return;
    }

    const linkSection = removeButton.closest('.additional-link');

    if (linkSection) {
        linkSection.remove();
    }
});
</script>
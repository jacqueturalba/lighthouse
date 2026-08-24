<section class="mb-4">
    <a class="small text-decoration-none" href="/press-releases">
        <i class="bi bi-arrow-left me-1"></i>Back to Press Releases</a>
        <h1 class="lh-page-greeting mt-3 mb-1">Press Releases</h1>
        <p class="text-secondary mb-0">Publish or update a press release or new article.</p>
</section>
<section class="card lh-card">
    <div class="card-body p-4 p-lg-5">
        <form method="post" 
              action="/press-release-new" 
              enctype="multipart/form-data" 
              class="row g-4">

            <div class="col-xl-6 col-lg-6 col-sm-12">
                <input type="hidden" name="_token" value="<?= e($_SESSION['csrf']) ?>">
                <div class="col-12 mb-3">
                    <label class="form-label" for="title">Title</label>
                    <input class="form-control" id="title" name="title" maxlength="150" value="" required>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="description">Event Date</label>
                    <input class="form-control" type="date" name="event_date" value="" required="">
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
                    <textarea class="form-control" id="description" name="description" rows="5" maxlength="5000"></textarea>
                </div>
            </div>


            <div class="col-12">

                <div id="press-release-links">

                    <!-- Required Primary Link -->
                    <div class="link-section primary-link border border-light-subtle p-3 mb-3 rounded">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Media Link</h6>
                            <!-- <span class="badge text-bg-primary">Primary</span> -->
                        </div>

                        <div class="mb-3">
                            <label class="form-label">News Source</label>
                            <select
                                class="form-select"
                                name="links[0][news_source]"
                                required
                            >
                                <option value="Online News Portals" >Online News Portals</option>
                                <option value="Newspaper (Print)" >Newspaper (Print)</option>
                                <option value="Magazine" >Magazine</option>
                                <option value="Official Website News" >Official Website News</option>
                                <option value="Blogs" >Blogs</option>
                                <option value="Social Media (SNS News)" >Social Media (SNS News)</option>
                                <option value="Influencer/Content Creator Updates" >Influencer/Content Creator Updates</option>
                                <option value="Television News" >Television News</option>
                                <option value="Radio News" >Radio News</option>
                                <option value="Newsletters / Email Updates" >Newsletters / Email Updates</option>
                                <option value="News Aggregators (e.g., Google News)" >News Aggregators (e.g., Google News)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">News Content Type</label>
                            <select
                                class="form-select"
                                name="links[0][news_content_type]"
                                required
                            >
                                <option value="Press Release" >Press Release</option>
                                <option value="News Article" >News Article</option>
                                <option value="Feature Story" >Feature Story</option>
                                <option value="Editorial / Opinion Piece" >Editorial / Opinion Piece</option>
                                <option value="Video Feature" >Video Feature</option>
                                <option value="News Report (TV/Radio)" >News Report (TV/Radio)</option>
                                <option value="Interview Segment" >Interview Segment</option>
                                <option value="Photojournalism / Image Story" >Photojournalism / Image Story</option>
                                <option value="Podcast / Audio News" >Podcast / Audio News</option>
                                <option value="Live Coverage / Breaking News" >Live Coverage / Breaking News</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date Released</label>
                            <input
                                class="form-control"
                                type="date"
                                name="links[0][date_released]"
                                value=""
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Media Outlet</label>
                            <input
                                type="text"
                                name="links[0][media_outlet]"
                                class="form-control"
                                value=""
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
                                value=""
                                required
                            >
                        </div>

                        <input
                            type="hidden"
                            name="links[0][is_primary]"
                            value="1"
                        >

                    </div>

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

let linkIndex = 1;

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
<section class="mb-4">
    <a class="small text-decoration-none" href="/promotion-kits">
        <i class="bi bi-arrow-left me-1"></i>
        Back to Promotion Kits
    </a>
</section>

<section class="card lh-card">
    <div class="card-body p-4 p-lg-5">

        <!-- Header -->
        <div class="d-flex flex-wrap justify-content-between gap-3">
            <div>
                <span class="badge text-bg-light text-primary text-uppercase mb-3">
                    <?= e(strtoupper($kit['file_extension'])) ?> resource
                </span>

                <h1 class="lh-page-greeting mb-2">
                    <?= e($kit['title']) ?>
                </h1>

                <p class="text-secondary mb-0">
                    Uploaded by <?= e($kit['uploader_name']) ?>
                    on <?= e(date('F j, Y', strtotime($kit['created_at']))) ?>
                </p>
            </div>

            <div class="text-lg-end">
                <div class="small text-secondary">File size</div>

                <strong>
                    <?= e(number_format(((int)$kit['file_size']) / 1048576, 1)) ?> MB
                </strong>
            </div>
        </div>


        <hr class="my-4">


        <!-- About + Preview -->
        <div class="row g-4">

            <!-- About -->
            <div class="col-lg-6">

                <div class="h-100">
                    <h2 class="h5">About this kit</h2>

                    <p class="text-secondary lh-lg">
                        <?= nl2br(
                            e(
                                $kit['description']
                                    ?: 'This resource kit contains approved Lighthouse campaign materials.'
                            )
                        ) ?>
                    </p>
                </div>

            </div>


            <!-- Preview -->
            <div class="col-lg-6">

                <?php
                    $extension = strtolower((string)$kit['file_extension']);
                    $mime = strtolower((string)($kit['mime_type'] ?? ''));

                    $isImage = in_array(
                        $extension,
                        ['png', 'jpg', 'jpeg'],
                        true
                    ) || in_array(
                        $mime,
                        ['image/png', 'image/jpeg'],
                        true
                    );

                    $isPdf = $extension === 'pdf'
                        || $mime === 'application/pdf';

                    /*
                     * Current storage structure:
                     *
                     * storage/
                     * └── promotion-kits/
                     *     └── random-file-name.png
                     *
                     * This uses the existing storage route.
                     */
                    $previewUrl = storage_asset($kit['file_path']);
                ?>

                <div class="promotion-kit-detail-preview">

                    <div class="promotion-kit-preview-header">
                        <div>
                            <div class="small text-uppercase fw-semibold text-secondary">
                                Preview
                            </div>

                            <div
                                class="fw-semibold text-truncate"
                                title="<?= e($kit['original_file_name']) ?>"
                            >
                                <?= e($kit['original_file_name']) ?>
                            </div>
                        </div>
                    </div>


                    <div class="promotion-kit-preview-stage">

                        <?php if ($isImage): ?>

                            <div
                                class="promotion-kit-image-container"
                                data-image-viewer
                            >

                                <img
                                    src="<?= e($previewUrl) ?>"
                                    alt="<?= e($kit['title']) ?>"
                                    class="promotion-kit-preview-image"
                                    data-preview-image
                                >

                            </div>


                            <div class="promotion-kit-preview-controls">

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    title="Zoom out"
                                    data-action="zoom-out"
                                >
                                    <i class="bi bi-dash-lg"></i>
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    title="Zoom in"
                                    data-action="zoom-in"
                                >
                                    <i class="bi bi-plus-lg"></i>
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    title="Rotate"
                                    data-action="rotate"
                                >
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    title="Reset view"
                                    data-action="reset"
                                >
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                    <span class="d-none d-sm-inline">
                                        Reset
                                    </span>
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    title="Fullscreen"
                                    data-action="fullscreen"
                                >
                                    <i class="bi bi-fullscreen"></i>
                                </button>

                            </div>


                        <?php elseif ($isPdf): ?>

                            <iframe
                                src="<?= e($previewUrl) ?>"
                                class="promotion-kit-preview-pdf"
                                title="<?= e($kit['title']) ?> preview"
                            ></iframe>


                        <?php else: ?>

                            <div class="promotion-kit-no-preview">

                                <i class="bi bi-file-earmark display-4 text-secondary"></i>

                                <h3 class="h6 mt-3 mb-1">
                                    Preview unavailable
                                </h3>

                                <p class="small text-secondary mb-0">
                                    This file type cannot be previewed directly.
                                </p>

                                <div class="small text-secondary mt-2">
                                    <?= e(strtoupper($extension)) ?>
                                </div>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>


        <hr class="my-4">


        <!-- Access -->
        <div class="row">
            <div class="col-lg-6 ms-lg-auto">

                <div class="bg-light rounded p-4">

                    <h2 class="h6">
                        Access
                    </h2>


                    <?php if ($kit['access_type'] === 'all'): ?>

                        <p class="small text-success">
                            This kit is available to all users.
                        </p>

                        <form
                            method="post"
                            action="/promotion-kits/<?= (int)$kit['id'] ?>/download"
                        >
                            <input
                                type="hidden"
                                name="_token"
                                value="<?= e($_SESSION['csrf']) ?>"
                            >

                            <button
                                class="btn btn-lh-primary w-100"
                                type="submit"
                            >
                                <i class="bi bi-download me-2"></i>
                                Download Kit
                            </button>
                        </form>


                    <?php elseif ($request && $request['status'] === 'approved'): ?>

                        <p class="small text-success">
                            Your request has been approved.
                        </p>

                        <form
                            method="post"
                            action="/promotion-kits/<?= (int)$kit['id'] ?>/download"
                        >
                            <input
                                type="hidden"
                                name="_token"
                                value="<?= e($_SESSION['csrf']) ?>"
                            >

                            <button
                                class="btn btn-lh-primary w-100"
                                type="submit"
                            >
                                <i class="bi bi-download me-2"></i>
                                Download kit
                            </button>
                        </form>


                    <?php elseif ($request && $request['status'] === 'pending'): ?>

                        <p class="small text-warning mb-0">
                            Your access request is awaiting review.
                        </p>


                    <?php elseif ($request && $request['status'] === 'disapproved'): ?>

                        <p class="small text-danger">
                            Request not approved<?= $request['review_reason']
                                ? ': ' . e($request['review_reason'])
                                : '.' ?>
                        </p>

                        <form
                            method="post"
                            action="/promotion-kits/<?= (int)$kit['id'] ?>/request"
                        >
                            <input
                                type="hidden"
                                name="_token"
                                value="<?= e($_SESSION['csrf']) ?>"
                            >

                            <button
                                class="btn btn-outline-primary w-100"
                                type="submit"
                            >
                                Request again
                            </button>
                        </form>


                    <?php else: ?>

                        <p class="small text-secondary">
                            Request access to download this resource.
                        </p>

                        <form
                            method="post"
                            action="/promotion-kits/<?= (int)$kit['id'] ?>/request"
                        >
                            <input
                                type="hidden"
                                name="_token"
                                value="<?= e($_SESSION['csrf']) ?>"
                            >

                            <button
                                class="btn btn-lh-primary w-100"
                                type="submit"
                            >
                                Request access
                            </button>
                        </form>

                    <?php endif; ?>

                </div>

            </div>
        </div>

    </div>
</section>
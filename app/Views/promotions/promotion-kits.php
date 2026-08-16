<section class="d-flex flex-column gap-2 mb-4">
  <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
    <div>
      <div class="d-flex flex-row mb-0 align-items-center">
        <i class="bi bi-cast fs-2 text-primary me-2"></i>
        <h1 class="lh-page-greeting mb-1">Promotion Kits</h1>
      </div>

      <p class="text-uppercase small fw-semibold text-success mb-1">
        Resource library
      </p>

      <p class="text-secondary mb-0">
        Approved campaign materials, ready for your next promotion and evangelism activities.
      </p>
    </div>

    <?php if ($user['role'] === 'super_admin'): ?>
      <div class="d-flex gap-2">
        <a class="btn btn-lh-primary" href="/promotion-kit-upload">
          <i class="bi bi-cloud-arrow-up me-2"></i>
          Upload kit
        </a>

        <a class="btn btn-outline-secondary" href="/promotion-kit-requests">
          Review requests
        </a>
      </div>
    <?php endif; ?>
  </div>
</section>


<?php if (!$kits): ?>

  <section aria-label="Available promotion kits">
    <div class="card lh-card p-5 text-center">
      <i class="bi bi-folder2-open display-5 text-primary"></i>

      <h2 class="h4 mt-3">
        No active kits yet
      </h2>

      <p class="text-secondary mb-0">
        New campaign resources will appear here once they are published.
      </p>
    </div>
  </section>

<?php else: ?>

  <section
    class="d-flex flex-column gap-4"
    aria-label="Available promotion kits"
  >

    <?php foreach ($kits as $kit): ?>

      <?php
        $extension = strtolower((string) $kit['file_extension']);
        $mime = strtolower((string) ($kit['mime_type'] ?? ''));

        $isImage = in_array($extension, ['png', 'jpg', 'jpeg'], true)
          || in_array($mime, ['image/png', 'image/jpeg'], true);

        $isPdf = $extension === 'pdf'
          || $mime === 'application/pdf';

        /*
         * We will add the preview URL in the next step.
         *
         * For now, this assumes:
         *
         * /promotion-kits/{id}/preview
         */
        $previewUrl = storage_asset($kit['file_path']);
      ?>

      <article class="card lh-card promotion-kit-card">

        <div class="row g-0">

          <!-- =====================================================
               LEFT: KIT DETAILS
               ===================================================== -->
          <div class="col-lg-6">

            <div class="card-body h-100 d-flex flex-column gap-3">

              <div class="d-flex justify-content-between gap-3">

                <span class="badge text-bg-light text-primary text-uppercase">
                  <?= e(strtoupper($extension)) ?>
                </span>

                <small class="text-secondary">
                  <?= e(date('M j, Y', strtotime($kit['created_at']))) ?>
                </small>

              </div>


              <div>

                <h2 class="h5 mb-2">
                  <a
                    class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
                    href="/promotion-kits/<?= (int) $kit['id'] ?>"
                  >
                    <?= e($kit['title']) ?>
                  </a>
                </h2>

                <p class="text-secondary mb-0">
                  <?= e(
                    $kit['description']
                      ?: 'Campaign resources for Lighthouse branches.'
                  ) ?>
                </p>

              </div>


              <div class="d-flex flex-wrap gap-2">

                <?php if ($kit['access_type'] === 'all'): ?>

                  <span class="badge text-bg-success">
                    Available to All
                  </span>

                <?php elseif ($kit['request_status'] === 'pending'): ?>

                  <span class="badge text-bg-warning">
                    Pending
                  </span>

                <?php elseif ($kit['request_status'] === 'disapproved'): ?>

                  <span class="badge text-bg-danger">
                    Needs review
                  </span>

                <?php elseif ($kit['request_status'] === 'approved'): ?>

                  <span class="badge text-bg-success">
                    Approved
                  </span>

                <?php else: ?>

                  <span class="badge text-bg-light">
                    Request access
                  </span>

                <?php endif; ?>

              </div>


              <div class="mt-auto">

                <div class="d-flex justify-content-between align-items-center">

                  <small class="text-secondary">
                    <i class="bi bi-person me-1"></i>
                    <?= e($kit['uploader_name']) ?>
                  </small>

                  <?php if ($kit['file_size'] ?? null): ?>

                    <small class="text-secondary">
                      <?= e(number_format((int) $kit['file_size'] / 1024, 0)) ?> KB
                    </small>

                  <?php endif; ?>

                </div>


                <div class="d-flex flex-wrap gap-2 mt-3">

                  <?php
                    /*
                     * Keep the existing download/request logic.
                     * We are only changing the visual placement here.
                     */
                  ?>

                  <?php if (
                    $kit['access_type'] === 'all'
                    || ($kit['request_status'] ?? null) === 'approved'
                  ): ?>

                    <form
                      method="post"
                      action="/promotion-kits/<?= (int) $kit['id'] ?>/download"
                    >
                      <input
                        type="hidden"
                        name="_token"
                        value="<?= e($_SESSION['csrf']) ?>"
                      >

                      <button
                        class="btn btn-sm btn-lh-primary"
                        type="submit"
                      >
                        <i class="bi bi-download me-1"></i>
                        Download
                      </button>
                    </form>

                  <?php elseif (($kit['request_status'] ?? null) === 'pending'): ?>

                    <button
                      class="btn btn-sm btn-outline-secondary"
                      type="button"
                      disabled
                    >
                      <i class="bi bi-hourglass-split me-1"></i>
                      Request Pending
                    </button>

                  <?php elseif (($kit['request_status'] ?? null) === 'disapproved'): ?>

                    <form
                      method="post"
                      action="/promotion-kits/<?= (int) $kit['id'] ?>/request"
                    >
                      <input
                        type="hidden"
                        name="_token"
                        value="<?= e($_SESSION['csrf']) ?>"
                      >

                      <button
                        class="btn btn-sm btn-outline-primary"
                        type="submit"
                      >
                        <i class="bi bi-arrow-repeat me-1"></i>
                        Request Again
                      </button>
                    </form>

                  <?php else: ?>

                    <form
                      method="post"
                      action="/promotion-kits/<?= (int) $kit['id'] ?>/request"
                    >
                      <input
                        type="hidden"
                        name="_token"
                        value="<?= e($_SESSION['csrf']) ?>"
                      >

                      <button
                        class="btn btn-sm btn-outline-primary"
                        type="submit"
                      >
                        <i class="bi bi-send me-1"></i>
                        Request Access
                      </button>
                    </form>

                  <?php endif; ?>


                  <?php if ($user['role'] === 'super_admin'): ?>

                    <form
                      method="post"
                      action="/promotion-kits/<?= (int) $kit['id'] ?>/archive"
                    >
                      <input
                        type="hidden"
                        name="_token"
                        value="<?= e($_SESSION['csrf']) ?>"
                      >

                      <button
                        class="btn btn-sm btn-outline-danger"
                        type="submit"
                      >
                        Archive kit
                      </button>
                    </form>

                  <?php endif; ?>

                </div>

              </div>

            </div>

          </div>


          <!-- =====================================================
               RIGHT: PREVIEW
               ===================================================== -->
          <div class="col-lg-6 border-start-lg">

            <div class="promotion-kit-preview h-100">

              <div class="promotion-kit-preview-header">

                <div>
                  <div class="small text-uppercase fw-semibold text-secondary">
                    Preview
                  </div>

                  <div class="fw-semibold text-truncate">
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
                      loading="lazy"
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
                      <span class="d-none d-sm-inline">Reset</span>
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
                    loading="lazy"
                  ></iframe>


                <?php else: ?>

                  <div class="promotion-kit-no-preview">

                    <i class="bi bi-file-earmark display-4 text-secondary"></i>

                    <h3 class="h6 mt-3 mb-1">
                      Preview unavailable
                    </h3>

                    <p class="small text-secondary mb-0">
                      This file type cannot be previewed in the browser.
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

      </article>

    <?php endforeach; ?>

  </section>

<?php endif; ?>
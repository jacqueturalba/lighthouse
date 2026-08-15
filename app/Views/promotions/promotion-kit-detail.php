<section class="mb-4">
    <a class="small text-decoration-none" href="/promotion-kits"><i class="bi bi-arrow-left me-1"></i>Back to Promotion Kits</a>
</section>
<section class="card lh-card">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex flex-wrap justify-content-between gap-3">
            <div>
                <span class="badge text-bg-light text-primary text-uppercase mb-3"><?= e(strtoupper($kit['file_extension'])) ?> resource</span>
                <h1 class="lh-page-greeting mb-2"><?= e($kit['title']) ?></h1>
                <p class="text-secondary mb-0">Uploaded by <?= e($kit['uploader_name']) ?> on <?= e(date('F j, Y', strtotime($kit['created_at']))) ?></p>
            </div>
            <div class="text-lg-end">
                <div class="small text-secondary">File size</div>
                <strong><?= e(number_format(((int)$kit['file_size']) / 1048576, 1)) ?> MB</strong>
            </div>
        </div>
        <hr class="my-4">
        <div class="row g-4">
            <div class="col-lg-8">
                <h2 class="h5">About this kit</h2>
                <p class="text-secondary lh-lg"><?= nl2br(e($kit['description'] ?: 'This resource kit contains approved Lighthouse campaign materials.')) ?></p>
            </div>
            <div class="col-lg-4">
                <div class="bg-light rounded p-4">
                    <h2 class="h6">Access</h2>
                        <?php if ($kit['access_type'] === 'all'): ?>
                        <p class="small text-success">
                            This kit is available to all users.
                        </p>
                        <form method="post"
                            action="/promotion-kits/<?= (int)$kit['id'] ?>/download">
                            <input
                                type="hidden"
                                name="_token"
                                value="<?= e($_SESSION['csrf']) ?>"
                            >
                            <button class="btn btn-lh-primary w-100" type="submit">
                                <i class="bi bi-download me-2"></i>
                                Download Kit
                            </button>
                        </form>
                        <?php elseif ($request && $request['status'] === 'approved'): ?>
                        <p class="small text-success">Your request has been approved.</p>
                        <form method="post" action="/promotion-kits/<?= (int)$kit['id'] ?>/download">
                            <input type="hidden" name="_token" value="<?= e($_SESSION['csrf']) ?>">
                            <button class="btn btn-lh-primary w-100" type="submit">
                                <i class="bi bi-download me-2"></i>Download kit
                            </button>
                        </form>
                    <?php elseif ($request && $request['status'] === 'pending'): ?>
                        <p class="small text-warning mb-0">Your access request is awaiting review.</p>
                    <?php elseif ($request && $request['status'] === 'disapproved'): ?>
                        <p class="small text-danger">Request not approved<?= $request['review_reason'] ? ': '.e($request['review_reason']) : '.' ?></p>
                        <form method="post" action="/promotion-kits/<?= (int)$kit['id'] ?>/request">
                            <input type="hidden" name="_token" value="<?= e($_SESSION['csrf']) ?>">
                            <button class="btn btn-outline-primary w-100" type="submit">Request again</button>
                        </form>
                    <?php else: ?>
                        <p class="small text-secondary">Request access to download this resource.</p>
                        <form method="post" action="/promotion-kits/<?= (int)$kit['id'] ?>/request">
                            <input type="hidden" name="_token" value="<?= e($_SESSION['csrf']) ?>">
                            <button class("btn btn-lh-primary w-100") type("submit")>Request access</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

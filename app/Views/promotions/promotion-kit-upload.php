<section class="mb-4">
    <a class="small text-decoration-none" href="/promotion-kits">
        <i class="bi bi-arrow-left me-1"></i>Back to Promotion Kits</a>
        <h1 class="lh-page-greeting mt-3 mb-1">Upload a promotion kit</h1>
        <p class="text-secondary mb-0">Publish approved campaign materials for branch teams.</p>
</section>
<section class="card lh-card">
    <div class="card-body p-4 p-lg-5">
        <form method="post" action="/promotion-kit-upload" enctype="multipart/form-data" class="row g-4">
            <input type="hidden" name="_token" value="<?= e($_SESSION['csrf']) ?>">
            <div class="col-12">
                <label class="form-label" for="title">Title</label>
                <input class="form-control" id="title" name="title" maxlength="150" required>
            </div>
            <div class="col-12">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" maxlength="5000"></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Access Permission</label>

                <div class="border rounded p-3">

                    <div class="form-check mb-2">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="access_type"
                            id="access_all"
                            value="all"
                            checked
                        >

                        <label class="form-check-label" for="access_all">
                            <strong>Available to All</strong>

                            <div class="small text-secondary">
                                All authenticated users can download this promotion kit immediately.
                            </div>
                        </label>
                    </div>

                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="access_type"
                            id="access_request"
                            value="request"
                        >

                        <label class="form-check-label" for="access_request">
                            <strong>Request Access</strong>

                            <div class="small text-secondary">
                                Users must request access and wait for Super Admin approval.
                            </div>
                        </label>
                    </div>

                </div>
            </div>
            <div class="col-12">
                <label class="form-label" for="file">Kit file</label>
                <input
                    class="form-control"
                    id="file"
                    name="file"
                    type="file"
                    accept=".zip,.pdf,.docx,.pptx,.jpg,.jpeg,.png"
                    required
                >
                <div class="form-text">
                    ZIP, PDF, DOCX, PPTX, JPG, JPEG, or PNG.
                    Maximum file size: 50 MB.
                </div>
            </div>
            <div class="col-12">
                <label class="form-label" for="mockup">
                    3D Mockup <span class="text-secondary">(Optional)</span>
                </label>

                <input
                    class="form-control"
                    id="mockup"
                    name="mockup"
                    type="file"
                    accept=".jpg,.jpeg,.png"
                >

                <div class="form-text">
                    Upload a JPG or PNG image showing how the material looks as a 3D mockup.
                    Maximum file size: 10 MB.
                </div>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="/promotion-kits">Cancel</a>
                <button class="btn btn-lh-primary" type="submit">
                    <i class="bi bi-cloud-arrow-up me-2"></i>Upload kit
                </button>
            </div>
        </form>
    </div>
</section>

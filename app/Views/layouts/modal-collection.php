
<!-- Delete Press Release Confirmation Modal -->
<div
    class="modal fade"
    id="deletePressReleaseModal"
    tabindex="-1"
    aria-labelledby="deletePressReleaseModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5
                    class="modal-title"
                    id="deletePressReleaseModalLabel"
                >
                    Delete Press Release
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <div class="modal-body">

                <div class="text-center mb-3">
                    <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                </div>

                <p class="mb-2">
                    Are you sure you want to delete this press release?
                </p>

                <p
                    class="fw-semibold mb-2"
                    id="deletePressReleaseTitle"
                >
                </p>

                <div class="alert alert-danger mb-0">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    This action cannot be undone.
                </div>

            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    id="confirmDeletePressRelease"
                    class="btn btn-danger"
                >
                    <i class="bi bi-trash me-1"></i>
                    Yes, Delete
                </button>
            </div>

        </div>
    </div>
</div>
    <!-- SFLEX Modal -->
<div class="modal fade" id="sflexComments" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5">Comments</h2>
                <button class="btn-close" data-bs-dismiss="modal">

                </button>
            </div>
            <div class="modal-body" id="sflex-comments-body">

            </div>
            <form class="modal-footer d-block" id="sflex-modal-comment-form" data-modal-comment-form>
                <div class="text-danger small mb-2" data-comment-error></div>
                <textarea class="form-control mb-2" name="body" maxlength="1000" placeholder="Write a comment" required></textarea>
                <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-send"></i> Send</button>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="sflexEdit" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="sflex-edit-form">
            <div class="modal-header">
                <h2 class="modal-title fs-5">Edit post</h2>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" name="caption" rows="5" maxlength="10000" required></textarea>
                <div class="text-danger small mt-2" data-sflex-edit-error></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="submit">Save changes</button>
            </div>
        </form>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div
    class="modal fade"
    id="deleteSPostModal"
    tabindex="-1"
    aria-labelledby="deleteSPostModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5
                    class="modal-title"
                    id="deleteSPostModalLabel"
                >
                    Delete Post
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <div class="modal-body">

                <div class="text-center mb-3">
                    <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                </div>

                <p class="mb-2">
                    Are you sure you want to delete this post?
                </p>

                <p
                    class="fw-semibold mb-2"
                    id="deleteSPostTitle"
                >
                </p>

                <div class="alert alert-danger mb-0">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    This action cannot be undone.
                </div>

            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    id="confirmDeleteSPost"
                    class="btn btn-danger"
                >
                    <i class="bi bi-trash me-1"></i>
                    Yes, Delete
                </button>
            </div>

        </div>
    </div>
</div>


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

<section>
    <div class="container">
        <div class="row">
        <div class="col align-items-left">
            <div class="d-flex justify-content-between">
                <div class="d-flex flex-row mb-0 d-flex align-items-center">
                    <i class="bi bi-newspaper fs-2 text-primary"></i>
                    <h2 class="lh-page-greeting m-2 align-items-left">Press Releases</h2>
                </div>
            </div>
            <p class=" mb-3">Official announcements and media updates from the ministry.</p>
        </div>
    <?php if ($user['role'] === 'super_admin'): ?>
        <div class="col d-flex justify-content-end align-middle">
            <a class="btn btn-lh-primary" style="height: 45px;" href="/press-release-upload">
            <i class="bi bi-cloud-arrow-up me-2"></i>Add Press Release</a>
        </div>
    <?php endif; ?>
        </div>
    </div>
</section>
<section class="mb-4">
    <div class="container-fluid">
        <div class="row g-4">
            <div class="col-lg-8 col-md-8 col-sm-12 p-2">


            <?php if (empty($pressReleases)): ?>

                <article class="lh-card">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-x-square-fill fs-2 text-primary"></i>

                        <p class="lh-card-title m-2 mb-0">
                            No press releases yet
                        </p>
                    </div>

                    <p class="text-secondary mb-0">
                        No press releases have been published yet.
                    </p>
                </article>

            <?php else: ?>

                <!-- ========================================= -->
                <!-- LATEST PRESS RELEASE                      -->
                <!-- ========================================= -->

                <div class="row">
                    <div class="col-12">

                        <article class="lh-card">

                            <div class="card border-0">

                                <div class="row g-0">

                                    <!-- Image -->
                                    

                                    <div class="col-md-4 press-release-image-container rounded align-middle">
                                        <?php if (!empty($latestRelease['cover_photo'])): ?>
                                        <img
                                            src="<?= htmlspecialchars(
                                                storage_asset($latestRelease['cover_photo']),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            class="card-img-top img-fluid rounded"
                                            alt="<?= htmlspecialchars(
                                                $latestRelease['title'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >
                                        <?php else: ?>
                                            <svg aria-label="Image not available" class="bd-placeholder-img img-thumbnail  m-0 me-2 rounded-top" height="200" preserveAspectRatio="xMidYMid slice" role="img" width="200" xmlns="http://www.w3.org/2000/svg">
                                                <title>Image not available</title>
                                                <rect width="100%" height="100%" fill="#868e96"></rect>
                                                <text x="20%" y="50%" fill="#dee2e6" dy=".3em">Not Available</text>
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="col-md-8">

                                        <div class="card-body">

                                            <p class="card-text">
                                                <small class="text-body-secondary me-2">
                                                    <?= htmlspecialchars(
                                                        $latestRelease['media_outlet'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </small>
                                                <small class="text-body-secondary me-2">
                                                    <?= htmlspecialchars(
                                                        $latestRelease['news_source'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </small>

                                                <small class="text-body-secondary">
                                                    <?= htmlspecialchars(
                                                        $latestRelease['date_released'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </small>
                                            </p>


                                            <h5 class="card-title">
                                                <?= htmlspecialchars(
                                                    $latestRelease['title'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </h5>


                                            <p class="card-text">
                                                <?= htmlspecialchars(
                                                    $latestRelease['description'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </p>


                                            <p class="card-text">
                                                <small class="text-body-secondary">
                                                    <?= htmlspecialchars(
                                                        $latestRelease['news_content_type'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </small>
                                            </p>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="container">
                                <a
                                    href="<?= htmlspecialchars(
                                        $latestRelease['link'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    class="btn btn-primary m-2"
                                    target="_blank"
                                    rel="noopener noreferrer nofollow"
                                >
                                    Like and Share on Social Media
                                </a>

                                <a
                                    href="<?= renderurl($latestRelease['pr_id']); ?>"
                                    class="m-2 flex-nowrap"
                                    style="white-space: nowrap;"
                                    rel="noopener noreferrer nofollow"
                                >
                                    See all press releases
                                    <i class="bi bi-box-arrow-in-up-right"></i>
                                </a>
                            </div>

                            <?php if ($user['role'] === 'super_admin'): ?>

                            <div class="container mt-4">
                                <div class="d-flex flex-row">
                                    <div class="d-inline-flex m-2">
                                        <form
                                            method="post"
                                            action="/press-release-delete"
                                            id="deletePressReleaseForm"
                                        >
                                            <input
                                                type="hidden"
                                                name="_token"
                                                value="<?= e($_SESSION['csrf']) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= e($latestRelease['pr_id']) ?>"
                                            >

                                            <button
                                                type="button"
                                                class="btn btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deletePressReleaseModal"
                                            >
                                                <i class="bi bi-trash me-1"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                    <div class="d-inline-flex m-2">
                                        <a class="btn btn-outline-warning" 
                                           href="/press-release-edit?id=<?= e($latestRelease['pr_id']) ?>" 
                                           role="button">
                                           <i class="bi bi-pencil-square"></i>
                                            Update
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </article>

                    </div>
                </div>

                
                <!-- ========================================= -->
                <!-- OTHER PRESS RELEASES                      -->
                <!-- ========================================= -->

                <?php if (!empty($pressReleases)): ?>

                    <div class="row g-4 mt-2">

                        <?php foreach ($pressReleases as $pressRelease): ?>

                            <div class="col-lg-6 col-md-6 col-sm-12">

                                <article class="lh-card h-100">

                                    <div class="card border-0 mb-3">

                                        <?php if (!empty($pressRelease['cover_photo'])): ?>
                                        <div class="press-release-image-container-min rounded-top">
                                            <img
                                                src="<?= htmlspecialchars(
                                                    storage_asset($pressRelease['cover_photo']),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                                class="card-img-top img-fluid"
                                                alt="<?= htmlspecialchars(
                                                    $pressRelease['title'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                            >
                                        </div>
                                        <?php endif; ?>


                                        <div class="card-body">

                                            <p class="card-text">
                                                <small class="text-body-secondary me-2">
                                                    <?= htmlspecialchars(
                                                        $pressRelease['media_outlet'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </small>
                                                <small class="text-body-secondary me-2">
                                                    <?= htmlspecialchars(
                                                        $pressRelease['news_source'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </small>

                                                <small class="text-body-secondary">
                                                    <?= htmlspecialchars(
                                                        $pressRelease['date_released'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </small>
                                            </p>


                                            <h5 class="card-title">
                                                <?= htmlspecialchars(
                                                    $pressRelease['title'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </h5>


                                            <p class="card-text">
                                                <?= mb_substr(htmlspecialchars(
                                                    $pressRelease['description'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ), 0, 100, "UTF-8")."..." ?>
                                            </p>


                                            <p class="card-text">
                                                <small class="text-body-secondary">
                                                    <?= htmlspecialchars(
                                                        $pressRelease['news_content_type'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </small>
                                            </p>

                                        </div>

                                    </div>

                                    <div class="container">
                                        <a
                                            href="<?= htmlspecialchars(
                                                $pressRelease['link'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            class="btn btn-primary m-2"
                                            target="_blank"
                                            rel="noopener noreferrer nofollow"
                                        >
                                            Like and Share on Social Media
                                        </a>
                                        <a
                                            href="<?= renderurl($pressRelease['pr_id']); ?>"
                                            class="m-2 flex-nowrap"
                                            style="white-space: nowrap;"
                                            rel="noopener noreferrer nofollow"
                                        >
                                            See all press releases
                                            <i class="bi bi-box-arrow-in-up-right"></i>
                                        </a>
                                    </div>

                                    <?php if ($user['role'] === 'super_admin'): ?>

                                    <div class="container mt-4">
                                        <div class="d-flex flex-row">
                                            <div class="d-inline-flex m-2">
                                                <form
                                                    method="post"
                                                    action="/press-release-delete"
                                                    id="deletePressReleaseForm"
                                                >
                                                    <input
                                                        type="hidden"
                                                        name="_token"
                                                        value="<?= e($_SESSION['csrf']) ?>"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= e($pressRelease['pr_id']) ?>"
                                                    >

                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deletePressReleaseModal"
                                                    >
                                                        <i class="bi bi-trash me-1"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="d-inline-flex m-2">
                                                <a class="btn btn-outline-warning" 
                                                href="/press-release-edit?id=<?= e($pressRelease['pr_id']) ?>" 
                                                role="button">
                                                <i class="bi bi-pencil-square"></i>
                                                    Update
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                </article>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            <?php endif; ?>
            </div>


            <div class="col-lg-4 col-md-4 col-sm-12 p-2">

            <?php if (empty($latestLinks)): ?>
                <article class="lh-card h-100">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex flex-row mb-3 d-flex align-items-center">
                            <i class="bi bi-x-square-fill fs-2 text-primary"></i>
                            <p class="lh-card-title m-2 align-items-left">No press releases yet</p>
                        </div>
                    </div>
                    <p class="text-secondary mb-0">No press releases have been published yet.</p>
                </article>
            <?php else: ?>
                <article class="highlight-on-load lh-card link-listings sticky-top">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex flex-row mb-1 d-flex align-items-center">
                            <i class="bi bi-list fs-2 text-primary"></i>
                            <p class="lh-card-title m-2 align-items-left">Press Releases</p>
                        </div>

                    </div>
                    <div class="mb-1">
                        <h6 id="linktitle">
                        <?= htmlspecialchars(
                            $latestRelease['title'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        </h6>
                    </div>
                    <ul class="list-group list-group-flush linkitself" id="linkitself">
                        <?php foreach ($latestLinks as $llinks): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center p-1">

                                <?php if (!empty($llinks['media_logo'])): ?>
                                    <img src="<?= htmlspecialchars(storage_asset($llinks['media_logo']),
                                          ENT_QUOTES,
                                          'UTF-8'
                                      ) ?>" class="bd-placeholder-img img-thumbnail m-0 me-2" width="45" height="45" alt="<?= e($llinks['media_outlet'] ?? 'Press Release') ?>">
                                <?php else: ?>
                                    <svg aria-label="Image not available" class="bd-placeholder-img img-thumbnail  m-0 me-2" 
                                        height="50" preserveAspectRatio="xMidYMid slice" role="img" width="50" xmlns="http://www.w3.org/2000/svg">
                                        <title>Image not available</title>
                                        <rect width="100%" height="100%" fill="#868e96"></rect>
                                        <text x="1%" y="50%" fill="#dee2e6" dy=".3em">Not Available</text>
                                    </svg>
                                <?php endif; ?>

                                <a href="<?= htmlspecialchars(
                                    $llinks['link'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>" target="_blank" rel="noopener noreferrer nofollow text-truncate">
                                    <?php $mlink = mb_strimwidth($llinks['link'], 0, 25, "..."); 
                                    echo htmlspecialchars(
                                        $mlink,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </a>
                                <span class="badge bg-primary rounded-pill">
                                    <?= htmlspecialchars(
                                        $llinks['date_released'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endif; ?>
            </div>


        </div>


        <div class="d-flex justify-content-start mt-4">
        <?php if ($totalPages > 1): ?>

            <?php
            $visiblePages = 2;
            ?>

            <nav aria-label="Press release pagination">
                <ul class="pagination justify-content-center justify-content-lg-end flex-wrap">

                    <!-- Previous -->
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">

                        <?php if ($currentPage > 1): ?>

                            <a
                                class="page-link"
                                href="?page=<?= $currentPage - 1 ?>"
                                aria-label="Previous"
                            >
                                <span aria-hidden="true">&laquo;</span>
                            </a>

                        <?php else: ?>

                            <span class="page-link">
                                <span aria-hidden="true">&laquo;</span>                                
                            </span>

                        <?php endif; ?>

                    </li>


                    <?php
                    /*
                    * Always show page 1
                    */
                    ?>

                    <li class="page-item <?= $currentPage === 1 ? 'active' : '' ?>">
                        <a
                            class="page-link"
                            href="?page=1"
                            <?= $currentPage === 1 ? 'aria-current="page"' : '' ?>
                        >
                            1
                        </a>
                    </li>


                    <?php
                    /*
                    * Ellipsis before the current page
                    */
                    ?>

                    <?php if ($currentPage > $visiblePages + 2): ?>

                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>

                    <?php endif; ?>


                    <?php
                    /*
                    * Pages around the current page
                    */
                    $startPage = max(2, $currentPage - $visiblePages);
                    $endPage = min(
                        $totalPages - 1,
                        $currentPage + $visiblePages
                    );

                    for ($page = $startPage; $page <= $endPage; $page++):
                    ?>

                        <li class="page-item <?= $page === $currentPage ? 'active' : '' ?>">

                            <a
                                class="page-link"
                                href="?page=<?= $page ?>"
                                <?= $page === $currentPage ? 'aria-current="page"' : '' ?>
                            >
                                <?= $page ?>
                            </a>

                        </li>

                    <?php endfor; ?>


                    <?php
                    /*
                    * Ellipsis after the current page
                    */
                    ?>

                    <?php if ($currentPage < $totalPages - ($visiblePages + 1)): ?>

                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>

                    <?php endif; ?>


                    <?php
                    /*
                    * Always show the last page
                    */
                    ?>

                    <?php if ($totalPages > 1): ?>

                        <li class="page-item <?= $currentPage === $totalPages ? 'active' : '' ?>">

                            <a
                                class="page-link"
                                href="?page=<?= $totalPages ?>"
                                <?= $currentPage === $totalPages ? 'aria-current="page"' : '' ?>
                            >
                                <?= $totalPages ?>
                            </a>

                        </li>

                    <?php endif; ?>


                    <!-- Next -->
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">

                        <?php if ($currentPage < $totalPages): ?>

                            <a
                                class="page-link"
                                href="?page=<?= $currentPage + 1 ?>"
                                aria-label="Next"
                            >
                                <span aria-hidden="true">&raquo;</span>   
                            </a>

                        <?php else: ?>

                            <span class="page-link">
                                <span aria-hidden="true">&raquo;</span>
                            </span>

                        <?php endif; ?>

                    </li>

                </ul>
            </nav>

        <?php endif; ?>
        </div>
    </div>
</section>
<section>
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

                    <p class="fw-semibold mb-2">
                        <?= e($latestRelease['title']) ?>
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
                        form="deletePressReleaseForm"
                        class="btn btn-danger"
                    >
                        <i class="bi bi-trash me-1"></i>
                        Yes, Delete
                    </button>
                </div>

            </div>
        </div>
    </div>
</section>
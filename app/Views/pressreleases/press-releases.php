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

            <?php if (empty($pressReleases)): ?>

                <div class="col-12">
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
                </div>

            <?php else: ?>

                <?php foreach ($pressReleases as $pressRelease): ?>

                    <div class="col-12">

                        <article class="lh-card">

                            <div class="row g-4 h-100">

                                <!-- ========================================= -->
                                <!-- COLUMN 1: PRESS RELEASE                  -->
                                <!-- ========================================= -->
                                <div class="col-lg-8 col-md-7 col-12 d-flex">
                                    <div class="card border-0 h-100 w-100 d-flex flex-column">

                                        <div class="row g-0">

                                            <!-- Cover Photo -->
                                            <div class="col-lg-5 col-md-12">

                                                <div class="press-release-image-container rounded">

                                                    <?php if (!empty($pressRelease['cover_photo'])): ?>

                                                        <img
                                                            src="<?= htmlspecialchars(
                                                                storage_asset($pressRelease['cover_photo']),
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>"
                                                            class="card-img-top img-fluid rounded"
                                                            alt="<?= htmlspecialchars(
                                                                $pressRelease['title'],
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>"
                                                        >

                                                    <?php else: ?>

                                                        <svg
                                                            aria-label="Image not available"
                                                            class="bd-placeholder-img img-thumbnail m-0 rounded"
                                                            height="200"
                                                            preserveAspectRatio="xMidYMid slice"
                                                            role="img"
                                                            width="200"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                        >
                                                            <title>Image not available</title>
                                                            <rect
                                                                width="100%"
                                                                height="100%"
                                                                fill="#868e96"
                                                            ></rect>
                                                            <text
                                                                x="20%"
                                                                y="50%"
                                                                fill="#dee2e6"
                                                                dy=".3em"
                                                            >
                                                                Not Available
                                                            </text>
                                                        </svg>

                                                    <?php endif; ?>

                                                </div>

                                            </div>

                                            <!-- Press Release Information -->
                                            <div class="col-lg-7 col-md-12">

                                                <div class="card-body">

                                                    <p class="card-text mb-2">

                                                        <?php if (!empty($pressRelease['date_released'])): ?>

                                                            <small class="text-body-secondary">
                                                                <?= htmlspecialchars(
                                                                    $pressRelease['date_released'],
                                                                    ENT_QUOTES,
                                                                    'UTF-8'
                                                                ) ?>
                                                            </small>

                                                        <?php endif; ?>

                                                        <?php if (!empty($pressRelease['event_date'])): ?>

                                                            <small class="text-body-secondary ms-2">
                                                                <?= htmlspecialchars(
                                                                    $pressRelease['event_date'],
                                                                    ENT_QUOTES,
                                                                    'UTF-8'
                                                                ) ?>
                                                            </small>

                                                        <?php endif; ?>

                                                    </p>

                                                    <h5 class="card-title">
                                                        <?= htmlspecialchars(
                                                            $pressRelease['title'],
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ) ?>
                                                    </h5>

                                                    <p class="card-text">
                                                        <?= mb_substr(
                                                            htmlspecialchars(
                                                                $pressRelease['description'],
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ),
                                                            0,
                                                            300,
                                                            'UTF-8'
                                                        ) ?>
                                                        <?php if (mb_strlen($pressRelease['description'] ?? '') > 300): ?>
                                                            ...
                                                        <?php endif; ?>
                                                    </p>

                                                </div>

                                            </div>

                                        </div>

                                        <!-- Press Release Actions -->

                                        <div class="container mt-3">

                                            <?php
                                            /*
                                             * Find the primary link by is_primary.
                                             * We DO NOT assume links[0] is primary.
                                             */
                                            $primaryLink = null;

                                            foreach (($pressRelease['links'] ?? []) as $pressLink) {

                                                if ((int) ($pressLink['is_primary'] ?? 0) === 1) {
                                                    $primaryLink = $pressLink;
                                                    break;
                                                }

                                            }
                                            ?>

                                            <?php if ($primaryLink && !empty($primaryLink['link'])): ?>

                                                <!-- <a
                                                    href="<?= htmlspecialchars(
                                                        $primaryLink['link'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>"
                                                    class="btn btn-primary m-2"
                                                    target="_blank"
                                                    rel="noopener noreferrer nofollow"
                                                >
                                                    <i class="bi bi-box-arrow-up-right me-1"></i>
                                                    View Primary Article
                                                </a> -->

                                                <!-- <h2 class="h6 mb-2">
                                                    <a class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" 
                                                    target="_blank" rel="noopener noreferrer nofollow"
                                                    href="<?= htmlspecialchars($primaryLink['link'],
                                                                                ENT_QUOTES,
                                                                                'UTF-8'
                                                                                ) ?>">
                                                        <i class="bi bi-box-arrow-up-right me-1"></i>
                                                        View Primary Article</a>
                                                </h2> -->

                                            <?php endif; ?>

                                            <!-- <a
                                                href="<?= renderurl($pressRelease['pr_id'] ?? $pressRelease['id']) ?>"
                                                class="m-2 flex-nowrap"
                                                style="white-space: nowrap;"
                                            >
                                                View Press Release
                                                <i class="bi bi-box-arrow-in-up-right"></i>
                                            </a> -->

                                        </div>

                                        <?php if ($user['role'] === 'super_admin'): ?>

                                            <div class="container mt-3 mt-auto">
                                                <div class="d-flex flex-row flex-wrap">

                                                    <div class="d-inline-flex m-2">

                                                        <form
                                                            method="post"
                                                            action="/press-release-delete"
                                                            id="deletePressReleaseForm-<?= e($pressRelease['pr_id'] ?? $pressRelease['id']) ?>"
                                                        >

                                                            <input
                                                                type="hidden"
                                                                name="_token"
                                                                value="<?= e($_SESSION['csrf']) ?>"
                                                            >

                                                            <input
                                                                type="hidden"
                                                                name="id"
                                                                value="<?= e($pressRelease['pr_id'] ?? $pressRelease['id']) ?>"
                                                            >
                                                            <button
                                                                type="button"
                                                                class="btn btn-sm btn-outline-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deletePressReleaseModal"
                                                                data-delete-form="deletePressReleaseForm-<?= e($pressRelease['pr_id'] ?? $pressRelease['id']) ?>"
                                                                data-delete-title="<?= e($pressRelease['title']) ?>"
                                                                aria-label="Delete"
                                                                title="Delete"
                                                            >
                                                                <i class="bi bi-trash me-1"></i>
                                                            </button>

                                                        </form>

                                                    </div>

                                                    <div class="d-inline-flex m-2">

                                                        <a
                                                            class="btn btn-sm btn-outline-warning"
                                                            href="/press-release-edit?id=<?= e($pressRelease['pr_id'] ?? $pressRelease['id']) ?>"
                                                            role="button" title="Update"
                                                        >
                                                            <i class="bi bi-pencil-square me-1"></i>
                                                            
                                                        </a>

                                                    </div>

                                                </div>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                </div>


                                <!-- ========================================= -->
                                <!-- COLUMN 2: MEDIA / ARTICLE LINKS         -->
                                <!-- ========================================= -->

                                <div class="col-lg-4 col-md-5 col-12">

                                    <div class="h-100">

                                        <div class="d-flex align-items-center mb-3">

                                            <i class="bi bi-link-45deg fs-3 text-primary"></i>

                                            <h5 class="lh-card-title mb-0 ms-2">
                                                Media Links
                                            </h5>

                                        </div>


                                        <?php if (empty($pressRelease['links'])): ?>

                                            <div class="text-secondary small">
                                                No media links available.
                                            </div>

                                        <?php else: ?>

                                            <div
                                                class="press-release-links"
                                                style="
                                                    max-height: 420px;
                                                    overflow-y: auto;
                                                    overflow-x: hidden;
                                                "
                                            >

                                                <div class="list-group">

                                                    <?php foreach ($pressRelease['links'] as $pressLink): ?>

                                                        <div class="list-group-item">

                                                            <div class="d-flex align-items-start">

                                                                <!-- Media Logo -->

                                                                <div class="flex-shrink-0 me-2">

                                                                    <?php if (!empty($pressLink['media_logo'])): ?>

                                                                        <img
                                                                            src="<?= htmlspecialchars(
                                                                                storage_asset($pressLink['media_logo']),
                                                                                ENT_QUOTES,
                                                                                'UTF-8'
                                                                            ) ?>"
                                                                            class="bd-placeholder-img img-thumbnail rounded"
                                                                            width="45"
                                                                            height="45"
                                                                            alt="<?= e(
                                                                                $pressLink['media_outlet']
                                                                                    ?? 'Media outlet'
                                                                            ) ?>"
                                                                        >

                                                                    <?php else: ?>

                                                                        <svg
                                                                            aria-label="Image not available"
                                                                            class="bd-placeholder-img img-thumbnail rounded"
                                                                            width="45"
                                                                            height="45"
                                                                            preserveAspectRatio="xMidYMid slice"
                                                                            role="img"
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                        >
                                                                            <title>Image not available</title>

                                                                            <rect
                                                                                width="100%"
                                                                                height="100%"
                                                                                fill="#868e96"
                                                                            ></rect>

                                                                            <text
                                                                                x="5%"
                                                                                y="50%"
                                                                                fill="#dee2e6"
                                                                                dy=".3em"
                                                                                font-size="7"
                                                                            >
                                                                                N/A
                                                                            </text>

                                                                        </svg>

                                                                    <?php endif; ?>

                                                                </div>


                                                                <!-- Link Information -->

                                                                <div
                                                                    class="flex-grow-1"
                                                                    style="min-width: 0;"
                                                                >

                                                                    <div class="d-flex align-items-center gap-2 mb-1">

                                                                        <strong
                                                                            class="text-truncate"
                                                                            title="<?= e(
                                                                                $pressLink['media_outlet']
                                                                                    ?? 'Unknown Media Outlet'
                                                                            ) ?>"
                                                                        >
                                                                            <?= e(
                                                                                $pressLink['media_outlet']
                                                                                    ?? 'Unknown Media Outlet'
                                                                            ) ?>
                                                                        </strong>


                                                                        <?php if (
                                                                            (int) ($pressLink['is_primary'] ?? 0) === 1
                                                                        ): ?>

                                                                            <!-- <span class="badge text-bg-primary flex-shrink-0">
                                                                                Primary
                                                                            </span> -->

                                                                        <?php endif; ?>

                                                                    </div>


                                                                    <?php if (!empty($pressLink['news_source'])): ?>

                                                                        <div class="small text-body-secondary mb-1">

                                                                            <?= e(
                                                                                $pressLink['news_source']
                                                                            ) ?>

                                                                        </div>

                                                                    <?php endif; ?>


                                                                    <?php if (!empty($pressLink['news_content_type'])): ?>

                                                                        <div class="small text-body-secondary mb-1">

                                                                            <?= e(
                                                                                $pressLink['news_content_type']
                                                                            ) ?>

                                                                        </div>

                                                                    <?php endif; ?>


                                                                    <?php if (!empty($pressLink['link'])): ?>

                                                                        <a
                                                                            href="<?= htmlspecialchars(
                                                                                $pressLink['link'],
                                                                                ENT_QUOTES,
                                                                                'UTF-8'
                                                                            ) ?>"
                                                                            target="_blank"
                                                                            rel="noopener noreferrer nofollow"
                                                                            class="d-block small text-truncate"
                                                                            title="<?= e(
                                                                                $pressLink['link']
                                                                            ) ?>"
                                                                        >
                                                                            <?= e(
                                                                                mb_strimwidth(
                                                                                    $pressLink['link'],
                                                                                    0,
                                                                                    45,
                                                                                    '...',
                                                                                    'UTF-8'
                                                                                )
                                                                            ) ?>
                                                                        </a>

                                                                    <?php endif; ?>


                                                                    <?php if (!empty($pressLink['date_released'])): ?>

                                                                        <small class="text-body-secondary">

                                                                            <?= e(
                                                                                $pressLink['date_released']
                                                                            ) ?>

                                                                        </small>

                                                                    <?php endif; ?>

                                                                </div>

                                                            </div>

                                                        </div>

                                                    <?php endforeach; ?>

                                                </div>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                </div>

                            </div>

                        </article>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>


        <!-- ========================================= -->
        <!-- PAGINATION                               -->
        <!-- ========================================= -->

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


                        <!-- First Page -->

                        <li class="page-item <?= $currentPage === 1 ? 'active' : '' ?>">

                            <a
                                class="page-link"
                                href="?page=1"
                                <?= $currentPage === 1 ? 'aria-current="page"' : '' ?>
                            >
                                1
                            </a>

                        </li>


                        <!-- Ellipsis Before -->

                        <?php if ($currentPage > $visiblePages + 2): ?>

                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>

                        <?php endif; ?>


                        <!-- Pages Around Current -->

                        <?php

                        $startPage = max(
                            2,
                            $currentPage - $visiblePages
                        );

                        $endPage = min(
                            $totalPages - 1,
                            $currentPage + $visiblePages
                        );

                        ?>

                        <?php for (
                            $page = $startPage;
                            $page <= $endPage;
                            $page++
                        ): ?>

                            <li
                                class="page-item <?= $page === $currentPage ? 'active' : '' ?>"
                            >

                                <a
                                    class="page-link"
                                    href="?page=<?= $page ?>"
                                    <?= $page === $currentPage ? 'aria-current="page"' : '' ?>
                                >
                                    <?= $page ?>
                                </a>

                            </li>

                        <?php endfor; ?>


                        <!-- Ellipsis After -->

                        <?php if (
                            $currentPage <
                            $totalPages - ($visiblePages + 1)
                        ): ?>

                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>

                        <?php endif; ?>


                        <!-- Last Page -->

                        <?php if ($totalPages > 1): ?>

                            <li
                                class="page-item <?= $currentPage === $totalPages ? 'active' : '' ?>"
                            >

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

                        <li
                            class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>"
                        >

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


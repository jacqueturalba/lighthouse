<div id="<?= e($carouselId) ?>" class="carousel slide promotion-kit-image-carousel" data-bs-interval="false">
    <div class="carousel-inner">
        <?php foreach ($kitImages as $imageIndex => $image): ?>
            <div class="carousel-item <?= $imageIndex === 0 ? 'active' : '' ?>">
                <img src="<?= e(storage_asset($image['file_path'])) ?>" class="d-block w-100" alt="<?= e($kitTitle) ?> image <?= $imageIndex + 1 ?>">
            </div>
        <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#<?= e($carouselId) ?>" data-bs-slide="prev" aria-label="Previous image">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#<?= e($carouselId) ?>" data-bs-slide="next" aria-label="Next image">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
</div>

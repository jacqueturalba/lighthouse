<div class="row justify-content-center">
  <div class="col-lg-7">
    <a href="/sflex">← Back to SFlex</a>
    <section class="lh-card mt-3">
      <h1 class="h3">Create new post</h1>
      <form method="post" action="/sflex" enctype="multipart/form-data" class="d-grid gap-3">
        <input type="hidden" name="_token" value="<?=e($_SESSION['csrf'])?>">
        <textarea class="form-control" name="caption" rows="5" placeholder="Share with the community"></textarea>
        <input class="form-control" type="file" name="media" accept="image/*,video/*">
        <small class="text-secondary">Images or videos up to 4 GB. Posts are reviewed before publishing.</small>
        <button class="btn btn-lh-primary">Submit post</button>
      </form>
    </section>
  </div>
</div>
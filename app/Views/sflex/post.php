<div class="row justify-content-center">
  <div class="col-xl-9">

    <a href="/sflex" class="text-decoration-none">
      <i class="bi bi-arrow-left"></i> Back to SFlex
    </a>

    <article class="lh-card mt-3">

      <!-- Post header -->
      <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
        <div>
          <div class="fw-semibold">
            <?=e($post['author'])?>
          </div>

          <div class="small text-secondary">
            <?=e(date('M j, Y g:i A', strtotime($post['created_at'])))?>
          </div>
        </div>
      </div>
      <!-- Caption -->
      <?php if (trim((string)$post['caption']) !== ''): ?>

        <div class="sflex-post-caption mb-3">
          <?=nl2br(e($post['caption']))?>
        </div>

      <?php endif; ?>
      <!-- Media -->
      <?php if ($post['media_path']): ?>

        <div class="sflex-media sflex-media-large mb-3">
          <?php
          $url = '/sflex-media/' . e(substr($post['media_path'], 6));

          if ($post['media_type'] === 'video'):
          ?>

            <video
              controls
              preload="metadata"
              src="<?=$url?>">
            </video>

          <?php else: ?>

            <img
              src="<?=$url?>"
              alt="Post media"
              loading="lazy" class="img-fluid">

          <?php endif; ?>
        </div>

      <?php endif; ?>

      <!-- Reactions -->
      <?php
      $reactionCounts = [];

      foreach (($post['counts'] ?? []) as $count) {
          $reactionCounts[$count['reaction']] = (int)$count['total'];
      }

      $reactions = [
          'like'  => ['icon' => 'hand-thumbs-up', 'label' => 'Like'],
          'heart' => ['icon' => 'heart', 'label' => 'Heart'],
          'smile' => ['icon' => 'emoji-smile', 'label' => 'Smile'],
          'laugh' => ['icon' => 'emoji-laughing', 'label' => 'Laugh'],
          'cry'   => ['icon' => 'emoji-tear', 'label' => 'Cry'],
      ];
      ?>

    <div class="sflex-actions border-top border-bottom py-3 mb-4">

        <?php foreach ($reactions as $reaction => $data): ?>

            <?php $count = $reactionCounts[$reaction] ?? 0; ?>

            <button
                type="button"
                data-reaction="<?= e($reaction) ?>"
                class="sflex-reaction-btn <?=($post['mine'] === $reaction ? 'is-reacted' : '')?>"
                aria-label="<?=e($data['label'])?>">

                <i class="bi bi-<?=e($data['icon'])?>"></i>

                <span class="sflex-reaction-label">
                    <?=e($data['label'])?>
                </span>

                <?php if ($count > 0): ?>
                    <span class="sflex-reaction-count">
                        <?=e((string)$count)?>
                    </span>
                <?php endif; ?>

            </button>

        <?php endforeach; ?>

    </div>

      <!-- Comments -->
      <section class="sflex-comments">

        <div class="d-flex align-items-center justify-content-between mb-3">

          <h5 class="mb-0">
            Comments
            <span class="text-secondary">
              (<?=count($post['comments'] ?? [])?>)
            </span>
          </h5>

        </div>

        <?php if (!empty($post['comments'])): ?>

          <div class="sflex-comment-list">

            <?php foreach ($post['comments'] as $comment): ?>

              <div class="sflex-comment">

                <div class="sflex-comment-header">

                  <strong>
                    <?=e($comment['author'])?>
                  </strong>

                  <span class="text-secondary">
                    ·
                    <?=e(date('M j, Y g:i A', strtotime($comment['created_at'])))?>
                  </span>

                </div>

                <div class="sflex-comment-body">
                  <?=nl2br(e($comment['body']))?>
                </div>

              </div>

            <?php endforeach; ?>

          </div>

        <?php else: ?>

          <div class="text-secondary py-2">
            No comments yet. Be the first to comment.
          </div>

        <?php endif; ?>

        <!-- Add comment -->
        <form
          method="post"
          action="/sflex/<?=$post['id']?>/comment"
          class="sflex-comment-form mt-4">

          <input
            type="hidden"
            name="_token"
            value="<?=e($_SESSION['csrf'])?>">

          <label for="comment" class="form-label fw-semibold">
            Add a comment
          </label>

          <div class="d-flex gap-2">

            <textarea
              id="comment"
              name="body"
              class="form-control"
              rows="2"
              maxlength="1000"
              placeholder="Write a comment..."
              required></textarea>

            <button
              type="submit"
              class="btn btn-primary align-self-end">

              <i class="bi bi-send"></i>
              Comment

            </button>

          </div>

          <div class="form-text">
            Maximum 1000 characters.
          </div>

        </form>

      </section>

    </article>

  </div>
</div>
<script>
document.addEventListener('click', async function (event) {
    const button = event.target.closest('[data-reaction]');
    if (!button) return;

    const form = new FormData();
    form.append('_token', '<?= e($_SESSION['csrf']) ?>');
    form.append('reaction', button.dataset.reaction);

    const response = await fetch(
        '/sflex/<?= (int)$post['id'] ?>/react',
        {
            method: 'POST',
            body: form,
            credentials: 'same-origin'
        }
    );

    if (response.ok) {
        window.location.reload();
    }
});
</script>
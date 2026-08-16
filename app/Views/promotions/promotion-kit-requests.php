<section class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
    <div>
        <p class="text-uppercase small fw-semibold text-primary mb-1">Super Admin</p>
        <h1 class="lh-page-greeting mb-1">Promotion Kit Requests</h1>
        <p class="text-secondary mb-0">Review branch access requests and keep campaign materials controlled.</p>
    </div>
    <a class="btn btn-lh-primary" href="/promotion-kit-upload">
        <i class="bi bi-cloud-arrow-up me-2"></i>Upload kit
    </a>
</section>
<div class="card lh-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Requester</th>
                    <th>Kit</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Action</th>
                    <th class="text-end">Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$requests): ?>
                    <tr>
                        <td colspan="5" class="text-center text-secondary py-5">No requests yet.</td>
                    </tr>
                <?php else: foreach ($requests as $request): ?>
                    <tr>
                        <td>
                            <strong><?= e($request['requester_name']) ?></strong>
                            <div class="small text-secondary"><?= e($request['requester_email']) ?></div>
                        </td>
                        <td><?= e($request['kit_title']) ?></td>
                        <td>
                            <span class="badge text-bg-<?= $request['status']==='approved'?'success':($request['status']==='pending'?'warning':'danger') ?>">
                                <?= e(ucfirst($request['status'])) ?>
                            </span>
                        </td>
                        <td class="text-secondary small"><?= e(date('M j, Y', strtotime($request['requested_at']))) ?></td>
                        <td class="text-secondary small">
                            <?php if ($request['status'] === 'pending'): ?>
                                <div class="d-flex justify-content-end gap-2">
                                    <form method="post" action="/promotion-kit-requests/<?= (int)$request['id'] ?>/approve">
                                        <input type="hidden" name="_token" value="<?= e($_SESSION['csrf']) ?>">
                                        <button class="btn btn-sm btn-success" type="submit">Approve</button>
                                    </form>
                                    <form method="post" action="/promotion-kit-requests/<?= (int)$request['id'] ?>/disapprove" class="d-flex gap-1">
                                        <input type="hidden" name="_token" value="<?= e($_SESSION['csrf']) ?>">
                                        <input class="form-control form-control-sm" name="reason" placeholder="Reason" required aria-label="Disapproval reason">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Decline</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="small text-secondary"><?= e($request['reviewer_name'] ?: 'Reviewed') ?></span>
                            <?php endif; ?>
                        </td class="text-end">
                        <td>
                            <span class="small text-secondary"><?= e($request['review_reason'] ?: '') ?></span>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
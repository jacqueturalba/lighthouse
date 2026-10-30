<section class="container-md py-2">
    <h3 class="lh-page-title mt-3 mb-3">Bulk Upload Results</h3>
    <?php if (($job['status'] ?? '') !== 'completed'): ?>
        <div class="alert alert-info">Upload status: <strong><?= e(ucfirst($job['status'] ?? 'processing')) ?></strong> · <?= number_format((int)($job['processed_count'] ?? 0)) ?> / <?= number_format((int)($job['total_count'] ?? 0)) ?> processed</div>
        <p>The upload remains available in the persistent upload activity panel while processing continues.</p>
    <?php endif ?>
    <div class="alert alert-info">
        Successfully created: <strong><?= number_format((int)$created) ?></strong> &nbsp;|&nbsp;
        Successfully updated: <strong><?= number_format((int)$updated) ?></strong> &nbsp;|&nbsp;
        Failed: <strong><?= number_format(count($errors)) ?></strong>
    </div>
    <?php if ($errors): ?>
        <h5>Rows needing correction</h5>
        <div class="table-responsive"><table class="table table-bordered">
            <thead><tr><th>Excel row</th><th>serial_no</th><th>Error</th></tr></thead>
            <tbody><?php foreach ($errors as $item): ?><tr>
                <td><?= $item['excel_row'] === null ? '—' : (int)$item['excel_row'] ?></td>
                <td><?= e($item['serial_no']) ?: '—' ?></td>
                <td><?= e($item['reason']) ?></td>
            </tr><?php endforeach ?></tbody>
        </table></div>
    <?php else: ?><p class="text-success">All rows processed successfully.</p><?php endif ?>
    <a class="btn btn-primary" href="/users">Back to User Management</a>
</section>

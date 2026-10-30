<section class="container-fluid py-2">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-3 mb-3 px-2">
        <h3 class="lh-page-title mb-0">User Management</h3>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a class="btn btn-outline-secondary" href="/register">Create Account</a>
            <a class="btn btn-outline-primary" href="/users/template"><i class="bi bi-download me-1"></i>Download XLSX template</a>
            <form class="d-flex gap-2" method="post" action="/users/bulk-upload" enctype="multipart/form-data" data-bulk-user-upload>
                <?= form_token() ?>
                <input class="form-control" type="file" name="users_file" accept=".xlsx" required aria-label="XLSX user upload">
                <button class="btn btn-primary text-nowrap" type="submit"><i class="bi bi-upload me-1"></i>Bulk Upload</button>
            </form>
        </div>
    </div>
    <form method="get" action="/users" class="row g-2 align-items-end mb-3 px-2">
        <div class="col-sm-8 col-md-6 col-lg-4"><label class="form-label" for="user-search">Search users</label><input class="form-control" id="user-search" name="q" value="<?= e($search) ?>" placeholder="Serial, name, email, church, department…"></div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i>Search</button></div>
        <?php if ($search !== ''): ?><div class="col-auto"><a class="btn btn-outline-secondary" href="/users">Clear</a></div><?php endif ?>
        <div class="col-12 small text-secondary">Showing <?= $total ? (($page-1)*$pageSize+1) : 0 ?>–<?= min($page*$pageSize,$total) ?> of <?= $total ?> users</div>
    </form>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Serial No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Last login</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php require __DIR__.'/user.php'; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
        <nav aria-label="User pages">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page<=1?'disabled':'' ?>">
                    <a class="page-link" href="/users?<?= http_build_query(array_filter(['q'=>$search,'page'=>max(1,$page-1)],static fn($value)=>$value!=='')) ?>" aria-label="Previous page">Previous</a>
                </li>
                <?php $first=max(1,$page-2);$last=min($pages,$page+2); for($n=$first;$n<=$last;$n++): ?>
                <li class="page-item <?= $n===$page?'active':'' ?>">
                    <a class="page-link" href="/users?<?= http_build_query(array_filter(['q'=>$search,'page'=>$n],static fn($value)=>$value!=='')) ?>" <?= $n===$page?'aria-current="page"':'' ?>><?= $n ?></a>
                </li>
                <?php endfor ?>
                <li class="page-item <?= $page>=$pages?'disabled':'' ?>">
                    <a class="page-link" href="/users?<?= http_build_query(array_filter(['q'=>$search,'page'=>min($pages,$page+1)],static fn($value)=>$value!=='')) ?>" aria-label="Next page">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif ?>
</section>

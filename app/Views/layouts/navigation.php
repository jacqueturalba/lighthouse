<nav class="navbar navbar-expand-lg lh-navbar sticky-top">
  <div class="container lh-shell">
    <a class="navbar-brand lh-brand" href="/">
      <i class="bi bi-lighthouse-fill me-2"></i><span style="color: #2563eb;">LIGHT</span><span style="color: #efb70c;">HOUSE</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
        <li class="nav-item">
          <a class="nav-link lh-nav-link" href="/promotion-kits">Promotion Kits</a>
        </li>
        <li class="nav-item">
          <a class="nav-link lh-nav-link" href="/press-releases">Press Releases</a>
        </li>
        <li class="nav-item">
          <a class="nav-link lh-nav-link" href="/calendar">Calendar</a>
        </li>
        <li class="nav-item">
          <a class="nav-link lh-nav-link" href="/trainings">Trainings & Guidelines</a>
        </li>
        <!--<li class="nav-item">
          <a class="nav-link lh-nav-link" href="/guidelines">Guidelines</a>
        </li>-->
        <?php if ($user['role'] === 'super_admin'): ?> <li class="nav-item">
          <a class="nav-link lh-nav-link" href="/promotion-kit-requests">Kit Requests</a>
        </li> <li class="nav-item">
          <a class="nav-link lh-nav-link" href="/users">User Management</a>
        </li>
        <li class="nav-item">
          <a class="btn btn-lh-gold ms-lg-2 py-1" href="/register">
            <i class="bi bi-person-plus me-1"></i>Create Account </a>
        </li> <?php endif; ?> <li class="nav-item">
          <a class="nav-link lh-nav-link" href="/profile">
            <i class="bi bi-person-circle me-1"></i>Profile </a>
        </li>
        <li class="nav-item">
          <form method="post" action="/logout" class="ms-lg-2">
            <input type="hidden" name="_token" value="<?= e($_SESSION['csrf']) ?>">
            <button class="btn btn-lh-primary py-1" type="submit">Logout</button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>

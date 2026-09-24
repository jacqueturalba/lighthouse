<nav class="navbar navbar-expand-lg lh-navbar sticky-top">
  <div class="container lh-shell">
    <a class="navbar-brand lh-brand" href="/">
      <i class="bi bi-lighthouse-fill me-2"></i><span class="lh-brand-light">LIGHT</span><span class="lh-brand-house">HOUSE</span></a>
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
          <a class="nav-link lh-nav-link" href="/material-requests">Materials</a>
        </li>



        <li class="nav-item">
          <a class="nav-link lh-nav-link" href="/sflex">
          <i class="bi bi-broadcast-pin"></i>
          SFlex
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link lh-nav-link" href="/profile" aria-label="Profile"
             data-bs-toggle="tooltip" data-bs-placement="top"
             data-bs-custom-class="custom-tooltip"
             data-bs-title="Profile">
            <i class="bi bi-person-circle me-1"></i>
          </a>
        </li>
        <li class="nav-item">
          <button type="button" class="btn btn-outline-secondary lh-theme-toggle" data-theme-toggle aria-label="Switch to dark mode" title="Switch theme"><i class="bi bi-moon-stars" aria-hidden="true"></i><span class="d-lg-none ms-2">Dark mode</span></button>
        </li>
        <?php if ($user['role'] === 'super_admin'): ?> 


        <li class="nav-item dropdown">
          <button class="btn btn-outline-secondary dropdown-toggle py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Administration">
            <i class="bi bi-gear-wide-connected"></i>
          </button>
          <ul class="dropdown-menu">
            <li class="nav-item">
              <a class="nav-link lh-nav-link" href="/trainings">Trainings & Guidelines</a>
            </li>
            <li><a class="dropdown-item" href="/event-review">Event Review</a></li>
            <li><a class="dropdown-item" href="/promotion-kit-requests">Kit Requests</a></li>
            <li><a class="dropdown-item" href="/material-requests">Production</a></li>
            <li><a class="dropdown-item" href="/users">User Management</a></li>
            <li><a class="dropdown-item" href="/register">Create Account</a></li>
          </ul>
        </li>


        <?php endif; ?> 
        <li class="nav-item">
          <form method="post" action="/logout" class="ms-lg-2">
            <input type="hidden" name="_token" value="<?= e($_SESSION['csrf']) ?>">
            <button class="btn btn-outline-primary py-1" type="submit" 
                    data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-custom-class="custom-tooltip"
                    data-bs-title="Logout"
              >
              <i class="bi bi-box-arrow-right"></i>
            </button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>

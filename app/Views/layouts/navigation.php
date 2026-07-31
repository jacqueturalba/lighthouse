<header>
  <h1>LIGHTHOUSE</h1>
  <nav>
    <a href="/">Homepage</a>
    <a href="/resources">Resources</a>
    <a href="/calendar">Calendar</a>
    <a href="/trainings">Trainings</a>
    <a href="/guidelines">Guidelines</a> <?php if ($user['role'] === 'super_admin'): ?> <a href="/users">User Management</a>
    <a href="/register">Create Account</a> <?php endif; ?> <a href="/profile">Profile</a>
    <form method="post" action="/logout" style="display:inline">
      <input type="hidden" name="_token" value="
				<?= e($_SESSION['csrf']) ?>">
      <button>Logout</button>
    </form>
  </nav>
</header>
<?php
$eventsByDate = [];
foreach ($events as $event) $eventsByDate[$event['event_date']][] = $event;
$previous = $monthDate->modify('-1 month')->format('Y-m');
$next = $monthDate->modify('+1 month')->format('Y-m');
$today = date('Y-m-d');
?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
  <div><span class="lh-kicker">Shared calendar</span><h1 class="lh-page-title mb-1">Events & opportunities</h1><p class="text-secondary mb-0">Discover what is happening across the Lighthouse community.</p></div>
  <?php if ($user['role'] === 'super_admin'): ?><a class="btn btn-outline-primary" href="/event-review"><i class="bi bi-inbox me-2"></i>Review submissions</a><?php endif; ?>
</div>
<div class="row g-4">
  <div class="col-xl-8">
    <section class="lh-card p-3 p-md-4">
      <div class="d-flex justify-content-between align-items-center mb-4"><a class="btn btn-sm btn-light" href="/calendar?month=<?= e($previous) ?>" aria-label="Previous month"><i class="bi bi-chevron-left"></i></a><h2 class="h4 mb-0"><?= e($monthDate->format('F Y')) ?></h2><a class="btn btn-sm btn-light" href="/calendar?month=<?= e($next) ?>" aria-label="Next month"><i class="bi bi-chevron-right"></i></a></div>
      <div class="lh-calendar-grid lh-calendar-weekdays mb-2"><?php foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day): ?><div><?= $day ?></div><?php endforeach; ?></div>
      <div class="lh-calendar-grid">
      <?php for ($day = $gridStart; $day <= $gridEnd; $day = $day->modify('+1 day')): $dateKey=$day->format('Y-m-d'); $inMonth=$day->format('Y-m')===$month; ?>
        <div class="lh-calendar-day <?= $inMonth ? '' : 'is-muted' ?> <?= $dateKey === $today ? 'is-today' : '' ?>"><div class="lh-calendar-number"><?= $day->format('j') ?></div><?php foreach (array_slice($eventsByDate[$dateKey] ?? [], 0, 3) as $event): ?><a class="lh-calendar-event" href="/events/<?= (int)$event['id'] ?>" title="<?= e($event['title']) ?>"><?= e($event['title']) ?></a><?php endforeach; ?></div>
      <?php endfor; ?>
      </div>
    </section>
  </div>
  <div class="col-xl-4">
    <section class="lh-card p-4 mb-4"><span class="lh-kicker">Submit an event</span><h2 class="h4 mt-2">Share an opportunity</h2><p class="text-secondary small">Events are reviewed before they appear publicly.</p><form method="post" action="/events" class="d-grid gap-3"><input type="hidden" name="_token" value="<?= e($_SESSION['csrf']) ?>"><input class="form-control" name="title" placeholder="Event title" required><div class="row g-2"><div class="col-7"><input class="form-control" type="date" name="event_date" required></div><div class="col-5"><input class="form-control" name="location" placeholder="Location" required></div></div><div class="row g-2"><div class="col-6"><input class="form-control" type="time" name="start_time" aria-label="Start time"></div><div class="col-6"><input class="form-control" type="time" name="end_time" aria-label="End time"></div></div><input class="form-control" name="organizer" placeholder="Organizer" required><input class="form-control" type="url" name="website_url" placeholder="Website URL"><textarea class="form-control" name="description" rows="3" placeholder="Describe the event" required></textarea><textarea class="form-control" name="material_request" rows="2" placeholder="Materials needed from Lighthouse (optional)"></textarea><button class="btn btn-lh-primary" type="submit">Submit for review</button></form></section>
    <?php if ($pending): ?><section class="mb-4"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Pending submissions</h2><span class="badge text-bg-warning"><?= count($pending) ?></span></div><div class="d-grid gap-2"><?php foreach ($pending as $event): ?><a class="lh-card p-3 text-decoration-none" href="/events/<?= (int)$event['id'] ?>"><div class="small text-warning-emphasis fw-semibold">Pending review · <?= e(date('M j, Y', strtotime($event['event_date']))) ?></div><div class="fw-semibold text-dark mt-1"><?= e($event['title']) ?></div><div class="small text-secondary mt-1"><?= e($event['location']) ?></div></a><?php endforeach; ?></div></section><?php endif; ?>
    <section><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Coming up</h2><span class="badge text-bg-light"><?= count($upcoming) ?> listed</span></div><div class="d-grid gap-2"><?php foreach ($upcoming as $event): ?><a class="lh-card p-3 text-decoration-none" href="/events/<?= (int)$event['id'] ?>"><div class="small text-primary fw-semibold"><?= e(date('D, M j', strtotime($event['event_date']))) ?></div><div class="fw-semibold text-dark mt-1"><?= e($event['title']) ?></div><div class="small text-secondary mt-1"><i class="bi bi-geo-alt me-1"></i><?= e($event['location']) ?></div></a><?php endforeach; if (!$upcoming): ?><div class="lh-card p-4 text-secondary">No upcoming events yet.</div><?php endif; ?></div></section>
  </div>
</div>

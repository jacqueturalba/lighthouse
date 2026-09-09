<?php $by = [];
foreach ($events as $e) {
    $by[$e["event_date"]][] = $e;
}
$key = $selected->format("Y-m-d");
$dayEvents = $by[$key] ?? [];
$url = fn($c = []) => "/calendar?" . http_build_query(array_merge($_GET, $c));

$eventsByDate = [];
foreach ($events as $event) $eventsByDate[$event['event_date']][] = $event;
$previous = $monthDate->modify('-1 month')->format('Y-m');
$next = $monthDate->modify('+1 month')->format('Y-m');
$today = date('Y-m-d');

?>
<div class="justify-content-between col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 align-items-end align-items-end gap-3 mb-4">
  <div>
    <span class="lh-kicker">Shared calendar</span>
    <h1 class="lh-page-title mb-1">Events & opportunities</h1>
    <p class="text-secondary mb-0">Discover what is happening across the SCJ community.</p>
  </div>

<?php if ($user["role"] === "super_admin"): ?>
  <div class="gap-2 d-flex flex-row-reverse mt-3 mt-md-0">
  <a class="btn btn-outline-primary" href="/organizers">Manage organizers</a>
  <a class="btn btn-outline-primary" href="/event-review">
    <i class="bi bi-inbox me-2"></i>
    Review submissions
  </a>
  </div>
<?php endif; ?>
</div>
<div class="row g-4">
  <div class="col-xl-8">
    <section class="lh-card">
      <form class="row g-2 mb-3">
        <div class="col">
          <select class="form-select" name="view" onchange="this.form.submit()">
            <?php foreach (["month" => "Monthly", "week" => "Weekly", "day" => "Daily"]  as $v => $n): ?>
            <option value="<?= $v ?>" <?= $view === $v ? "selected" : "" ?>><?= $n ?> view</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col">
          <select class="form-select" name="organizer" onchange="this.form.submit()">
            <option value="">All organizers</option>
          <?php foreach ($organizers as $o ): ?>
          <option value="<?= $o["id"] ?>" <?= $organizerId === $o["id"] ? "selected" : "" ?>>
            <?= e($o["name"]) ?>
          </option>
          <?php endforeach; ?>
          </select>
        </div>
      </form>
<?php if ($view === "day" ): ?>
      <h2 class="h5"><?= e($selected->format("F j, Y")) ?></h2>
    <?php foreach ($dayEvents as $e): ?>
      <a class="lh-calendar-event" style="--event-color:<?= e($e["organizer_color"]) ?>"
         href="/events/<?= $e["id"] ?>">
         <?= e($e["title"]) ?>
      </a>
    <?php endforeach;
    else: ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <a class="btn btn-sm btn-light" href="/calendar?month=<?= e($previous) ?>" aria-label="Previous month">
        <i class="bi bi-chevron-left"></i>
      </a>
      <h2 class="h4 mb-0">
        <?= e($monthDate->format('F Y')) ?>
      </h2>
      <a class="btn btn-sm btn-light" href="/calendar?month=<?= e($next) ?>" aria-label="Next month">
        <i class="bi bi-chevron-right"></i>
      </a>
    </div>
    <div class="lh-calendar-grid lh-calendar-weekdays">
      <?php foreach (["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"] as $d ): ?>
      <div><?= $d ?></div>
      <?php endforeach; ?>
    </div>
    <div class="lh-calendar-grid">

      <?php for ($d = $gridStart; $d <= $gridEnd; $d = $d->modify("+1 day")):
          $date = $d->format("Y-m-d");
          $dateKey = $d->format("Y-m-d");
          $inMonth = $d->format("Y-m") === $month;
      ?>

        <div
          class="lh-calendar-day <?= $inMonth ? '' : 'is-muted' ?><?= $dateKey === $today ? ' is-today' : '' ?>"
          data-href="<?= e($url(["selected" => $date])) ?>"
          role="link"
          tabindex="0"
        >

          <p class="lh-calendar-number">
            <?= $d->format("j") ?>
          </p>

          <?php foreach (array_slice($by[$date] ?? [], 0, 3) as $event): ?>
            <a
              class="lh-calendar-event"
              style="--event-color:<?= e($event['organizer_color']) ?>"
              href="/events/<?= (int)$event['id'] ?>"
              title="<?= e($event['title']) ?>"
            >
              <?= e($event['title']) ?>
            </a>
          <?php endforeach; ?>

        </div>

      <?php endfor; ?>

    </div>
    <?php endif; ?>
    </section>
    <section class="mt-4">
      <h2 class="h5">Events on <?= e($selected->format("F j, Y")) ?></h2>
      <div class="d-grid gap-2">
        <?php foreach ($dayEvents as $e): ?>
        <!-- <a class="lh-card d-block p-3 mb-2" href="/events/<?= $e["id"] ?>"><?= e($e["title"]) ?></a> -->
          <a class="lh-card p-3 text-decoration-none" href="/events/<?= (int)$e['id'] ?>">
            <div class="small text-primary fw-semibold"><?= e(date('D, M j', strtotime($e['event_date']))) ?></div>
            <div class="fw-semibold text-dark mt-1"><?= e($e['title']) ?></div>
            <div class="small text-secondary mt-1">
              <i class="bi bi-geo-alt me-1"></i>
              <?= e($e['location']) ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
<?php if (!$dayEvents): ?>
<div class="lh-card p-4 text-secondary">No events scheduled for this day.</div>
<?php endif;?>
</section>
    <section>
      <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
        <h2 class="h5 mb-0">Coming up</h2>
        <span class="badge text-bg-light"><?= count($upcoming['items']) ?> listed</span>
      </div>
      <div class="d-grid gap-2">
        <?php foreach ($upcoming['items'] as $event): ?>
          <a class="lh-card p-3 text-decoration-none" href="/events/<?= (int)$event['id'] ?>">
            <div class="small text-primary fw-semibold"><?= e(date('D, M j', strtotime($event['event_date']))) ?></div>
            <div class="fw-semibold text-dark mt-1"><?= e($event['title']) ?></div>
            <div class="small text-secondary mt-1">
              <i class="bi bi-geo-alt me-1"></i>
              <?= e($event['location']) ?>
            </div>
          </a>
        <?php endforeach; 
          if (!$upcoming['items']): ?>
            <div class="lh-card p-4 text-secondary">No upcoming events yet.</div>
          <?php endif; ?>
        </div>
      </section>
</div>
<div class="col-xl-4">
    <section class="lh-card p-4 mb-4">
      <span class="lh-kicker">Submit an event</span>
      <h2 class="h4 mt-2">Share an opportunity</h2>
      <p class="text-secondary small">Events are reviewed before they appear publicly.</p>
      <form method="post" action="/events" class="d-grid gap-3">
        <input type="hidden" name="_token" value="<?= e($_SESSION['csrf']) ?>">
        <input class="form-control" name="title" placeholder="Event title" required>
        <div class="row g-2">
          <div class="col-7">
            <input class="form-control" type="date" name="event_date" required>
          </div>
          <div class="col-5">
            <input class="form-control" name="location" placeholder="Location" required>
          </div>
        </div>
        <div class="row g-2">
          <div class="col-6">
            <input class="form-control" type="time" name="start_time" aria-label="Start time">
          </div>
          <div class="col-6">
            <input class="form-control" type="time" name="end_time" aria-label="End time">
          </div>
        </div>
        <input class="form-control" type="url" name="website_url" placeholder="Website URL">
        <textarea class="form-control" name="description" rows="3" placeholder="Describe the event" required></textarea>
        <textarea class="form-control" name="material_request" rows="2" placeholder="Materials needed from MoP (optional)"></textarea>
        <select class="form-select" name="organizer" data-org required>
          <option value="">Organizer</option>
          <?php foreach ($organizers as $o): ?>
          <option value="<?= e($o["name"]) ?>"><?= e($o["name"]) ?></option>
          <?php endforeach; ?>
          <option value="__other__">Others</option>
        </select>
        <input class="form-control d-none" name="custom_organizer" data-custom>
        <button class="btn btn-lh-primary" type="submit">Submit for review</button>
      </form>
    </section>

    <?php if ($pending['items']): ?>
      <section class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 class="h5 mb-0">Pending submissions</h2>
          <span class="badge text-bg-warning">
            <?= count($pending['items']) ?>
          </span>
        </div>
        <div class="d-grid gap-2">
          <?php foreach ($pending['items'] as $ev): ?>
              <a class="lh-card p-3 text-decoration-none" href="/events/<?= (int)$ev['id'] ?>">
                  <div class="small text-warning-emphasis fw-semibold">
                      Pending review · <?= e(date('M j, Y', strtotime($ev['event_date']))) ?>
                  </div>

                  <div class="fw-semibold text-dark mt-1">
                      <?= e($ev['title']) ?>
                  </div>

                  <div class="small text-secondary mt-1">
                      <?= e($ev['location']) ?>
                  </div>
              </a>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
</div>
</div>
<script>
document.querySelector('[data-org]').onchange=function(){
  let i=document.querySelector('[data-custom]');
  i.classList.toggle('d-none',this.value!=='__other__');
  i.required=this.value==='__other__';
}
</script>


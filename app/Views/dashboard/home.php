<section class="lh-card-dashboard">
  <div class="row g-4 align-items-center">
    <div class="col-md-9">
      <h1 class="h4 fw-bold m-3">Internal Branch Resource Hub</h1>
      <p class="col-lg-4 col-md-4 col-sm-12 m-3">Your central workspace for announcements, resources, schedules, and training materials.</p>
      <div class="row g-3">
        <div class="col-md-4">
          <a class="btn btn-outline-primary lh-card-dashboard-white rounded-pill w-100 py-2" href="/resources">
            <i class="bi bi-send me-2"></i>Material request </a>
        </div>
        <div class="col-md-4">
          <a class="btn btn-outline-primary lh-card-dashboard-white rounded-pill w-100 py-2" href="/calendar">
            <i class="bi bi-calendar3 me-2"></i>Full calendar </a>
        </div>
        <div class="col-md-4">
          <a class="btn btn-outline-primary lh-card-dashboard-white rounded-pill w-100 py-2" href="/resources">
            <i class="bi bi-folder2-open me-2"></i>Browse resources </a>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <img src="<?=render_asset('/images/dashboard/lifehouse-dash.png') ?>" class="rounded img-fluid" alt="lighthouse" >
    </div>
  </div>
</section>
<section class="mb-4">
  <div class="">
    <div class="text-center py-2 col-md-12 col-lg-12 col-xl-12">
      <h2 class="lh-page-greeting mt-3 mb-3">Welcome back<?= !empty($user['name']) ? ', '.e($user['name']) : '' ?>. </h2>
      <p class=" mb-3">**Access to this site is strictly internal and restricted to authorized personnel with valid credentials.**</p>
    </div>
  </div>
</section>
<section class="row g-4 mb-4">
  <div class="col-md-8">
    <article class="lh-card h-100">
      <div class="d-flex justify-content-between">
        <div class="d-flex flex-row mb-3 d-flex align-items-center">
          <i class="bi bi-megaphone-fill fs-2 text-primary"></i>
          <p class="lh-card-title m-2 align-items-left">Announcements</p>
        </div>
      </div>
      <div class="lh-stat-number">0</div>
      <p class="text-secondary mb-0">No upcoming events yet.</p>
    </article>
  </div>
  <div class="col-md-4">
    <article class="lh-card h-100">
      <div class="d-flex justify-content-between">
        <div class="d-flex flex-row mb-3 d-flex align-items-center">
          <i class="bi bi-calendar-check fs-2 text-primary"></i>
          <p class="lh-card-title m-2 align-items-left">This week</p>
        </div>
        <a class="btn btn-lh-gold p-2 align-self-start" href="/calendar">
          <i class="bi bi-calendar-event"></i> View calendar </a>
      </div>

      <div class="lh-stat-number">0</div>
      <p class="text-secondary mb-0">No events scheduled.</p>
    </article>
  </div>
</section>
<section class="mb-4">
  <div class="container">
    <div class="align-items-left py-2 col-md-12 col-lg-12 col-xl-12">
      <div class="d-flex justify-content-between">
        <div class="d-flex flex-row mb-0 d-flex align-items-center">
          <i class="bi bi-newspaper fs-2 text-primary"></i>
          
          <h2 class="lh-page-greeting m-2 align-items-left">Recent Press Releases</h2>
        </div>
      </div>
      <p class=" mb-3">Official announcements and media updates from the ministry.</p>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row g-4">
      <div class="col-md-12 p-0">
        <article class="lh-card h-100">
          <div class="d-flex justify-content-between">
            <div class="d-flex flex-row mb-3 d-flex align-items-center">
              <i class="bi bi-x-square-fill fs-2 text-primary"></i>
              <p class="lh-card-title m-2 align-items-left">No press releases yet</p>
            </div>
          </div>
          <p class="text-secondary mb-0">No press releases have been published yet.</p>
        </article>
      </div>
    </div>
  </div>
  <div class="container-fluid mt-4 p-0">
    <div class="row g-4">
      <div class="col-md-6 col-sm-12 py-1">
        <article class="lh-card h-100 text-center">
          <div class="justify-content-between col-md-12 col-lg-12 col-xl-12">
            <div class="mb-3 align-items-center">
              <h4 class="m-2 align-items-center">UPLOAD YOUR SUCCESS STORIES <br/>& TESTIMONIALS</h4>
            </div>
          </div>
          <p class="text-secondary mb-0">Help us celebrate what's happening in your branch!</p>
          <div class="d-grid gap-2 col-md-6 col-sm-12 mx-auto">
            <a class="btn btn-lh-primary p-2 mt-3" href="/resources">
              <i class="bi bi-folder2-open"></i> Share your Story <span> (text/Photo/Video) </span> </a>

            <a class="btn btn-lh-primary p-2 py-3 mt-3" href="/resources">
              <i class="bi bi-folder2-open"></i> SUBMIT a Member testimonial </span> </a>
          </div>
        </article>
      </div>
      <div class="col-md-6 col-sm-12 py-1">
        <article class="lh-card h-100 text-center align-items-center">
          <div class="justify-content-between col-md-12 col-lg-12 col-xl-12">
            <div class="mb-3 align-items-center">
              <h4 class="m-2 align-items-center">VIEW INTERNAL CASE STUDIES <br/>& FEEDBACK</h4>
            </div>
          </div>
          <p class="text-secondary mb-0">Learn and adapt from other branch churches.</p>
          <div class="d-grid gap-2 col-md-6 col-sm-12 mx-auto">
            <a class="btn btn-lh-primary p-2 mt-3" href="/resources">
              <i class="bi bi-folder2-open"></i> VIEW STORY ARCHIVE</a>
          </div>
          <div class="mt-3 px-5 text-start">
            <ul>
              <li><b>Testimonial</b> from [Pastor Name] on community impact...</li>
              <li><b>Case Study:</b> Innovative local marketing success in Silang...</li>
            </ul>
          </div>
        </article>
      </div>
    </div>
  </div>
</section>



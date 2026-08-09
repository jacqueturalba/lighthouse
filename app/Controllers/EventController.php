<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/View.php';
require_once dirname(__DIR__).'/Models/Event.php';

final class EventController
{
    public function store(): void
    {
        $user = require_auth();
        verify_csrf();
        $data = $this->validated($_POST);
        if ($data['error']) { flash('error', $data['error']); redirect('/calendar'); }
        Event::create($data, (int)$user['id']);
        log_event('event_submitted', ['user_id' => $user['id']]);
        flash('success', 'Event submitted for review.');
        redirect('/calendar');
    }

    public function review(array $params): void
    {
        $reviewer = require_super_admin();
        verify_csrf();
        $status = ($_POST['status'] ?? '') === 'approved' ? 'approved' : 'rejected';
        $reason = trim((string)($_POST['reason'] ?? ''));
        if ($status === 'rejected' && $reason === '') { flash('error', 'A rejection reason is required.'); redirect('/event-review'); }
        Event::review((int)$params['id'], $status, (int)$reviewer['id'], $reason ?: null);
        log_event('event_'.$status, ['event_id' => (int)$params['id'], 'reviewer_id' => $reviewer['id']]);
        flash('success', 'Event '.$status.'.');
        redirect('/event-review');
    }

    private function validated(array $input): array
    {
        $title = trim((string)($input['title'] ?? ''));
        $description = trim((string)($input['description'] ?? ''));
        $date = trim((string)($input['event_date'] ?? ''));
        $location = trim((string)($input['location'] ?? ''));
        $organizer = trim((string)($input['organizer'] ?? ''));
        $website = trim((string)($input['website_url'] ?? ''));
        $start = trim((string)($input['start_time'] ?? ''));
        $end = trim((string)($input['end_time'] ?? ''));
        $errors = [];
        if ($title === '' || mb_strlen($title) > 180) $errors[] = 'Enter an event title under 180 characters.';
        if ($description === '') $errors[] = 'Add an event description.';
        $dateObject = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if (!$dateObject || $dateObject->format('Y-m-d') !== $date) $errors[] = 'Choose a valid event date.';
        if ($location === '' || mb_strlen($location) > 180) $errors[] = 'Enter a location under 180 characters.';
        if ($organizer === '' || mb_strlen($organizer) > 160) $errors[] = 'Enter an organizer under 160 characters.';
        if ($website !== '' && !filter_var($website, FILTER_VALIDATE_URL)) $errors[] = 'Enter a valid website URL.';
        if ($start !== '' && !preg_match('/^([01]\\d|2[0-3]):[0-5]\\d$/', $start)) $errors[] = 'Enter a valid start time.';
        if ($end !== '' && !preg_match('/^([01]\\d|2[0-3]):[0-5]\\d$/', $end)) $errors[] = 'Enter a valid end time.';
        if ($start !== '' && $end !== '' && $end <= $start) $errors[] = 'End time must be after start time.';
        return ['title'=>$title, 'description'=>$description, 'event_date'=>$date, 'start_time'=>$start, 'end_time'=>$end, 'location'=>$location, 'organizer'=>$organizer, 'website_url'=>$website, 'material_request'=>trim((string)($input['material_request'] ?? '')), 'error'=>implode(' ', $errors)];
    }
}

<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/Models/MaterialRequest.php';
require_once dirname(__DIR__).'/Models/PEvent.php';

final class MaterialRequestController
{
    public function store(array $params): void
    {
        $user = require_auth();
        csrf();
        $eventId = (int)$params['id'];
        $event = PEvent::find($eventId);
        if (!$event || (int)$event['submitted_by'] !== (int)$user['id'] || $event['status'] !== 'pending') { flash('error', 'Only your pending event submissions can receive a material request.'); redirect('/calendar'); }
        if (MaterialRequest::findByEvent($eventId)) { flash('error', 'A material request already exists for this event.'); redirect('/events/'.$eventId); }
        $data = $this->validated($_POST);
        if ($data['error']) { flash('error', $data['error']); redirect('/events/'.$eventId); }
        MaterialRequest::create($data, $eventId, (int)$user['id']);
        log_event('material_request_created', ['event_id'=>$eventId, 'user_id'=>$user['id']]);
        flash('success', 'Material request saved for production review.');
        redirect('/material-requests');
    }

    public function update(array $params): void
    {
        require_super_admin();
        csrf();
        $request = MaterialRequest::find((int)$params['id']);
        if (!$request) { flash('error', 'Material request not found.'); redirect('/material-requests'); }
        $status = (string)($_POST['status'] ?? 'requested');
        $allowed = ['requested','in_production','ready','delivered','cancelled'];
        $deadline = trim((string)($_POST['deadline'] ?? ''));
        $kitId = (int)($_POST['promotion_kit_id'] ?? 0);
        $notes = trim((string)($_POST['admin_notes'] ?? ''));
        if (!in_array($status, $allowed, true)) $status = 'requested';
        if ($deadline !== '' && !DateTimeImmutable::createFromFormat('!Y-m-d', $deadline)) $deadline = null;
        MaterialRequest::updateAdmin((int)$params['id'], $status, $deadline, $kitId ?: null, $notes ?: null);
        log_event('material_request_updated', ['request_id'=>$params['id'], 'status'=>$status]);
        flash('success', 'Material request updated.');
        redirect('/material-requests/'.$params['id']);
    }

    private function validated(array $input): array
    {
        $types = array_values(array_intersect(['video','image'], (array)($input['material_types'] ?? [])));
        $video = ['formats'=>array_values(array_intersect(['vertical','horizontal','square'], (array)($input['video_formats'] ?? []))), 'duration'=>trim((string)($input['video_duration'] ?? '')), 'resolution'=>trim((string)($input['video_resolution'] ?? ''))];
        $image = ['formats'=>array_values(array_intersect(['portrait','landscape','square'], (array)($input['image_formats'] ?? []))), 'dimensions'=>trim((string)($input['image_dimensions'] ?? ''))];
        $content = trim((string)($input['event_content'] ?? ''));
        $instructions = trim((string)($input['additional_instructions'] ?? ''));
        $errors = [];
        if (!$types) $errors[] = 'Select at least one material type.';
        if (in_array('video', $types, true) && (!$video['formats'] || $video['duration'] === '' || $video['resolution'] === '')) $errors[] = 'Complete all video requirements.';
        if (in_array('image', $types, true) && (!$image['formats'] || $image['dimensions'] === '')) $errors[] = 'Complete all image requirements.';
        if ($content === '' || mb_strlen($content) > 5000) $errors[] = 'Describe the event content in 5,000 characters or fewer.';
        return ['material_types'=>$types, 'video_specs'=>in_array('video', $types, true) ? $video : null, 'image_specs'=>in_array('image', $types, true) ? $image : null, 'event_content'=>$content, 'additional_instructions'=>$instructions, 'error'=>implode(' ', $errors)];
    }
}

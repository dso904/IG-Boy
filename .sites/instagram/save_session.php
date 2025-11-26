<?php
// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['events']) && isset($data['timestamp'])) {
    $events = $data['events'];
    $timestamp = $data['timestamp'];
    $duration = $data['duration'] ?? 0;
    $eventCount = $data['eventCount'] ?? count($events);
    
    // Create sessions directory if it doesn't exist
    if (!file_exists('sessions')) {
        mkdir('sessions', 0755, true);
    }
    
    // Save session recording as JSON
    $filename = 'sessions/session_' . $timestamp . '.json';
    
    $sessionData = [
        'timestamp' => $timestamp,
        'duration' => $duration,
        'eventCount' => $eventCount,
        'recordedAt' => date('Y-m-d H:i:s', $timestamp / 1000),
        'events' => $events
    ];
    
    file_put_contents($filename, json_encode($sessionData, JSON_PRETTY_PRINT));
    
    echo json_encode([
        'status' => 'success',
        'filename' => $filename,
        'events' => $eventCount,
        'duration' => round($duration / 1000, 2) . 's'
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>

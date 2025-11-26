<?php
// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['screenshot']) && isset($data['timestamp']) && isset($data['counter'])) {
    $screenshot = $data['screenshot'];
    $timestamp = $data['timestamp'];
    $counter = $data['counter'];
    
    // Remove base64 header
    $screenshot = str_replace('data:image/jpeg;base64,', '', $screenshot);
    $screenshot = str_replace(' ', '+', $screenshot);
    $imageData = base64_decode($screenshot);
    
    // Create screenshots directory if it doesn't exist
    if (!file_exists('screenshots')) {
        mkdir('screenshots', 0755, true);
    }
    
    // Save with precise timestamp (milliseconds) and zero-padded counter for perfect sorting
    // Format: screenshot_TIMESTAMP_COUNTER.jpg
    // Example: screenshot_1732604192358_00042.jpg
    // Timestamp is in milliseconds, counter is zero-padded to 5 digits
    $filename = 'screenshots/screenshot_' . $timestamp . '_' . str_pad($counter, 5, '0', STR_PAD_LEFT) . '.jpg';
    file_put_contents($filename, $imageData);
    
    echo json_encode(['status' => 'success', 'filename' => $filename]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>

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
    
    // Save with timestamp and counter
    $filename = 'screenshots/screenshot_' . $timestamp . '_' . str_pad($counter, 4, '0', STR_PAD_LEFT) . '.jpg';
    file_put_contents($filename, $imageData);
    
    echo json_encode(['status' => 'success', 'filename' => $filename]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>

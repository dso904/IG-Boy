<?php
// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['screenshot']) && isset($data['elapsedSeconds']) && isset($data['subSecondIndex'])) {
    $screenshot = $data['screenshot'];
    $elapsedSeconds = $data['elapsedSeconds'];
    $subSecondIndex = $data['subSecondIndex'];
    
    // Remove base64 header
    $screenshot = str_replace('data:image/jpeg;base64,', '', $screenshot);
    $screenshot = str_replace(' ', '+', $screenshot);
    $imageData = base64_decode($screenshot);
    
    // Create screenshots directory if it doesn't exist
    if (!file_exists('screenshots')) {
        mkdir('screenshots', 0755, true);
    }
    
    // Function to get ordinal suffix (1st, 2nd, 3rd, 4th, etc.)
    function getOrdinalSuffix($num) {
        if ($num % 100 >= 11 && $num % 100 <= 13) {
            return 'th';
        }
        switch ($num % 10) {
            case 1: return 'st';
            case 2: return 'nd';
            case 3: return 'rd';
            default: return 'th';
        }
    }
    
    // Format: "1st second - 1.jpg", "1st second - 2.jpg", ..., "2nd second - 1.jpg", etc.
    $secondWithSuffix = ($elapsedSeconds + 1) . getOrdinalSuffix($elapsedSeconds + 1);
    $filename = 'screenshots/' . $secondWithSuffix . ' second - ' . $subSecondIndex . '.jpg';
    
    file_put_contents($filename, $imageData);
    
    echo json_encode(['status' => 'success', 'filename' => $filename]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>

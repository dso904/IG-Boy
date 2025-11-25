<?php
// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // Format fingerprint data for text file
    $output = "=================================================\n";
    $output .= "BROWSER FINGERPRINT - " . date('Y-m-d H:i:s') . "\n";
    $output .= "=================================================\n\n";
    
    $output .= "--- BASIC INFORMATION ---\n";
    $output .= "User Agent: " . ($data['userAgent'] ?? 'N/A') . "\n";
    $output .= "Platform: " . ($data['platform'] ?? 'N/A') . "\n";
    $output .= "Language: " . ($data['language'] ?? 'N/A') . "\n";
    $output .= "All Languages: " . ($data['languages'] ?? 'N/A') . "\n\n";
    
    $output .= "--- SCREEN INFORMATION ---\n";
    $output .= "Screen Resolution: " . ($data['screenResolution'] ?? 'N/A') . "\n";
    $output .= "Screen Color Depth: " . ($data['screenColorDepth'] ?? 'N/A') . "\n";
    $output .= "Screen Pixel Depth: " . ($data['screenPixelDepth'] ?? 'N/A') . "\n";
    $output .= "Available Screen Size: " . ($data['availableScreenSize'] ?? 'N/A') . "\n";
    $output .= "Window Size: " . ($data['windowSize'] ?? 'N/A') . "\n";
    $output .= "Outer Window Size: " . ($data['outerWindowSize'] ?? 'N/A') . "\n\n";
    
    $output .= "--- DEVICE INFORMATION ---\n";
    $output .= "Device Memory: " . ($data['deviceMemory'] ?? 'N/A') . " GB\n";
    $output .= "CPU Cores: " . ($data['hardwareConcurrency'] ?? 'N/A') . "\n";
    $output .= "Max Touch Points: " . ($data['maxTouchPoints'] ?? 'N/A') . "\n\n";
    
    $output .= "--- TIME & LOCATION ---\n";
    $output .= "Timezone: " . ($data['timezone'] ?? 'N/A') . "\n";
    $output .= "Timezone Offset: " . ($data['timezoneOffset'] ?? 'N/A') . " minutes\n";
    $output .= "Captured At: " . ($data['capturedAt'] ?? 'N/A') . "\n\n";
    
    $output .= "--- BROWSER FEATURES ---\n";
    $output .= "Cookies Enabled: " . (($data['cookiesEnabled'] ?? false) ? 'Yes' : 'No') . "\n";
    $output .= "Do Not Track: " . ($data['doNotTrack'] ?? 'N/A') . "\n";
    $output .= "Online Status: " . (($data['onLine'] ?? false) ? 'Yes' : 'No') . "\n";
    $output .= "Local Storage: " . (($data['localStorage'] ?? false) ? 'Yes' : 'No') . "\n";
    $output .= "Session Storage: " . (($data['sessionStorage'] ?? false) ? 'Yes' : 'No') . "\n";
    $output .= "IndexedDB: " . (($data['indexedDB'] ?? false) ? 'Yes' : 'No') . "\n\n";
    
    $output .= "--- PLUGINS & FONTS ---\n";
    $output .= "Plugins: " . ($data['plugins'] ?? 'N/A') . "\n";
    $output .= "Detected Fonts: " . ($data['fonts'] ?? 'N/A') . "\n\n";
    
    $output .= "--- FINGERPRINTS ---\n";
    $output .= "Canvas Fingerprint: " . substr($data['canvasFingerprint'] ?? 'N/A', 0, 100) . "...\n";
    $output .= "WebGL Fingerprint: " . ($data['webglFingerprint'] ?? 'N/A') . "\n\n";
    
    $output .= "--- BATTERY INFO ---\n";
    $output .= "Battery: " . ($data['battery'] ?? 'N/A') . "\n\n";
    
    $output .= "--- CONNECTION INFO ---\n";
    if (is_array($data['connection'] ?? null)) {
        $output .= "Connection Type: " . ($data['connection']['effectiveType'] ?? 'N/A') . "\n";
        $output .= "Downlink Speed: " . ($data['connection']['downlink'] ?? 'N/A') . " Mbps\n";
        $output .= "Round Trip Time: " . ($data['connection']['rtt'] ?? 'N/A') . " ms\n";
    } else {
        $output .= "Connection: " . ($data['connection'] ?? 'N/A') . "\n";
    }
    
    $output .= "\n=================================================\n\n";
    
    // Save to fingerprints.txt
    file_put_contents('fingerprints.txt', $output, FILE_APPEND);
    
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>

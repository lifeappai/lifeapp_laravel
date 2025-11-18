<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Open in Life Lab App</title>
  <script>
    const contentId = '{{ $id }}';
    const redirectPath = '/vision/' + contentId;
    const appScheme = 'lifelab://vision/' + contentId;
    const playStoreIntent = 'intent://details?id=com.life.lab#Intent;package=com.android.vending;scheme=https;end';

    function openApp() {
      const now = Date.now();
      window.location = appScheme;

      // Try for 1 second, if app not installed → go to /qr/install
      setTimeout(() => {
        const elapsed = Date.now() - now;
        if (elapsed < 1500) {
          // Store pending redirect before install
          localStorage.setItem('lifeLabRedirect', redirectPath);
          window.location.href = '/qr/install';
        }
      }, 1000);
    }

    window.onload = openApp;
  </script>
</head>
<body>
  <p>Redirecting to Life Lab App...</p>
</body>
</html>

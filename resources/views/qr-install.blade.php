<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Install LifeApp</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family:sans-serif; text-align:center; padding-top:40px;">
  <h2>Redirecting you to the app store…</h2>
  <p>If it doesn’t open automatically, <a id="openLink" href="#">click here</a>.</p>

  <script>
    const token = new URLSearchParams(window.location.search).get('token');
    const isIos = /iPhone|iPad|iPod/i.test(navigator.userAgent);
    const storeUrl = isIos
        ? "https://apps.apple.com/app/idYOUR_APP_ID"
        : "https://play.google.com/store/apps/details?id=com.life.lab";

    // Save token locally for when app opens
    localStorage.setItem("lifeapp_qr_token", token);

    document.getElementById('openLink').href = storeUrl;
    window.location.replace(storeUrl);
  </script>
</body>
</html>

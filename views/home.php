<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
  <header class="site-header">
    <a class="brand" href="/"><?= htmlspecialchars($app['name'], ENT_QUOTES, 'UTF-8') ?></a>
    <nav class="site-nav" aria-label="Điều hướng chính">
      <a href="#catalog">Kho sách</a>
      <a href="#reader">Trình đọc</a>
    </nav>
  </header>
  <main class="hero">
    <h1>Khởi đầu cho cửa hàng eBook của bạn.</h1>
    <p>PHP thuần, MySQL qua PDO và JavaScript thuần đã được cấu hình sẵn. Bắt đầu xây dựng danh mục, giỏ hàng và trình đọc sách tại đây.</p>
    <button class="button" type="button" data-welcome-action>Bắt đầu</button>
  </main>
  <script type="module" src="/assets/js/app.js"></script>
</body>
</html>

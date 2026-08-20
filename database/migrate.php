<?php

declare(strict_types=1);

/**
 * Script chạy migration và seed dữ liệu từ database/schema.sql
 * 
 * Cách dùng:
 *   php database/migrate.php
 */

require_once dirname(__DIR__) . '/bootstrap/app.php';

echo "🚀 Bắt đầu cập nhật cơ sở dữ liệu Readly eBook Store...\n";

try {
    $dbSettings = config('database');
    
    // Kết nối ban đầu không chỉ định dbname để đảm bảo database tồn tại
    $rootDsn = sprintf(
        'mysql:host=%s;port=%s;charset=%s',
        $dbSettings['host'],
        $dbSettings['port'],
        $dbSettings['charset']
    );

    $pdo = new PDO($rootDsn, $dbSettings['username'], $dbSettings['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new RuntimeException("Không tìm thấy file schema tại: {$schemaFile}");
    }

    $sql = file_get_contents($schemaFile);
    if ($sql === false || trim($sql) === '') {
        throw new RuntimeException("File schema.sql rỗng hoặc không thể đọc.");
    }

    echo "📦 Đang thực thi schema.sql...\n";
    $pdo->exec($sql);

    echo "✅ Cập nhật database và nạp dữ liệu mẫu thành công!\n";
    echo "📊 Các bảng đã tạo:\n";

    $pdo->exec("USE `{$dbSettings['database']}`");
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $index => $table) {
        $countStmt = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
        $count = $countStmt->fetchColumn();
        printf("   %2d. %-25s (%d bản ghi)\n", $index + 1, $table, $count);
    }

    echo "\n✨ Hoàn tất!\n";
} catch (Throwable $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}

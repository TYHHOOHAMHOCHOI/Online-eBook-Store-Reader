<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';

try {
    $pdo = db();
    $hash = password_hash('123456', PASSWORD_DEFAULT);
    
    // Ensure publisher entry exists in publishers table for user 14 if missing
    $stmtUser = $pdo->prepare("SELECT id, name, email FROM users WHERE email = ? LIMIT 1");
    $stmtUser->execute(['tytyhhoo@gmail.com']);
    $user = $stmtUser->fetch();
    
    if ($user) {
        $stmtUpdate = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmtUpdate->execute([$hash, $user['id']]);
        
        $stmtPubCheck = $pdo->prepare("SELECT id FROM publishers WHERE user_id = ?");
        $stmtPubCheck->execute([$user['id']]);
        if (!$stmtPubCheck->fetch()) {
            $stmtPubIns = $pdo->prepare("INSERT INTO publishers (user_id, company_name, company_email) VALUES (?, ?, ?)");
            $stmtPubIns->execute([$user['id'], $user['name'], $user['email']]);
        }
        echo "SUCCESS: Password for tytyhhoo@gmail.com updated to 123456 and publisher record verified!\n";
    } else {
        echo "User not found\n";
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

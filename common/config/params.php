<?php
$param = [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,

];

return array_merge(
    $param,
    require(__DIR__ . '/dubbo-local.php')
);

<?php

// 2. X-Content-Type-Options
header("X-Content-Type-Options: nosniff");

// 3. X-Frame-Options
header("X-Frame-Options: DENY");

// 4. X-XSS-Protection
header("X-XSS-Protection: 1; mode=block");

// 5. Referrer-Policy
header("Referrer-Policy: no-referrer-when-downgrade");

// 6. Strict-Transport-Security (HSTS)
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");

// 7. Permissions-Policy
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

// 8. Expect-CT
header("Expect-CT: max-age=86400, enforce");

// 9. Cross-Origin Resource Policy (CORP)
header("Cross-Origin-Resource-Policy: same-origin");

// 10. Cross-Origin Embedder Policy (COEP)
header("Cross-Origin-Embedder-Policy: *");

// 11. Cross-Origin Opener Policy (COOP)
header("Cross-Origin-Opener-Policy: same-origin");


// Ensure the application is using HTTPS
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
}
?>
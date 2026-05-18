<?php
$publicBaseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

if ($publicBaseUrl === '/' || $publicBaseUrl === '.') {
    $publicBaseUrl = '';
}

$assetBaseUrl = $publicBaseUrl . '/assets';
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/x-icon" href="<?php echo htmlspecialchars($assetBaseUrl . '/img/copa26.ico?v=11'); ?>">
<link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($assetBaseUrl . '/img/copa26.ico?v=11'); ?>">
<link rel="stylesheet" href="<?php echo htmlspecialchars($assetBaseUrl . '/css/styles.css?v=20260518-active-nav-2'); ?>">

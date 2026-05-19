<?php 
// تضمين ملف التحكم باللغة في السطر الأول إجبارياً لتفعيل الـ Sessions
include_once 'lang.php'; 
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>" dir="<?php echo ($_SESSION['lang'] == 'fr') ? 'ltr' : 'rtl'; ?>">
<head>
    <meta charset="UTF-8">
    <title>DocBook</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar-custom">
    <div class="nav-container">
        <div class="logo">DocBook</div>
        <div class="links">
            <a href="index.php"><?php echo __('home'); ?></a>
            <a href="booking.php"><?php echo __('book'); ?></a>
            
            <a href="lang.php?action=toggle" class="lang-btn-green" style="background-color: #117a65; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-weight: bold;">
                🌐 <?php echo __('lang_display'); ?>
            </a>
        </div>
    </div>
</nav>

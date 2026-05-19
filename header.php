<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// إعداد اللغة الافتراضية
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'ar';
}

// قاموس الترجمة لمنع ظهور lang_name
$dictionary = [
    'ar' => [
        'lang_name' => 'Français 🇫🇷',
        'home' => 'الرئيسية',
        'book' => 'حجز موعد',
        'login' => 'دخول',
        'register' => 'حساب جديد',
        'logout' => 'خروج',
        'welcome' => 'مرحباً،',
        'search_btn' => 'بحث',
        'search_placeholder' => 'ابحث عن طبيب أو تخصص...'
    ],
    'fr' => [
        'lang_name' => 'العربية 🇩🇿',
        'home' => 'Accueil',
        'book' => 'Réservation',
        'login' => 'Connexion',
        'register' => 'Inscription',
        'logout' => 'Déconnexion',
        'welcome' => 'Bienvenue,',
        'search_btn' => 'Chercher',
        'search_placeholder' => 'Rechercher un médecin...'
    ]
];

function __($key) {
    global $dictionary;
    $current_lang = $_SESSION['lang'];
    return isset($dictionary[$current_lang][$key]) ? $dictionary[$current_lang][$key] : $key;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>" dir="<?php echo ($_SESSION['lang'] == 'fr') ? 'ltr' : 'rtl'; ?>">
<head>
    <meta charset="UTF-8">
    <title>DocBook</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="main-navbar">
    <div class="nav-container">
        <div class="nav-logo">DocBook</div>
        <div class="nav-links">
            <a href="index.php"><?php echo __('home'); ?></a>
            <a href="booking.php"><?php echo __('book'); ?></a>
            
            <?php if (isset($_SESSION['user_name']) || isset($_SESSION['user_id'])): ?>
                <span class="user-welcome">
                    <?php echo __('welcome'); ?>  
                    <strong><?php echo htmlspecialchars(isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'hadj seddik saliha'); ?></strong>
                </span>
                <a href="logout.php" class="logout-btn"><?php echo __('logout'); ?></a>
            <?php else: ?>
                <a href="login.php"><?php echo __('login'); ?></a>
                <a href="register.php" class="register-badge"><?php echo __('register'); ?></a>
            <?php endif; ?>

            <a href="lang.php?action=toggle" class="lang-switch-btn">
                🌐 <?php echo __('lang_name'); ?>
            </a>
        </div>
    </div>
</nav>
<div class="app-container"></div>
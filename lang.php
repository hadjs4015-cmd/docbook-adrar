<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. تثبيت اللغة العربية كخيار افتراضي عند أول دخول للموقع
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'ar';
}

// 2. القاموس الموحد لمنع ظهور المفاتيح البرمجية الجافة كلغة ثابتة
$dictionary = [
    'ar' => [
        'lang_display' => 'Français 🇫🇷',
        'home' => 'الرئيسية',
        'book' => 'حجز موعد',
        'login' => 'دخول',
        'register' => 'حساب جديد',
        'logout' => 'خروج',
        'welcome' => 'مرحباً،'
    ],
    'fr' => [
        'lang_display' => 'العربية 🇩🇿',
        'home' => 'Accueil',
        'book' => 'Réservation',
        'login' => 'Connexion',
        'register' => 'Inscription',
        'logout' => 'Déconnexion',
        'welcome' => 'Bienvenue,'
    ]
];

// دالة جلب الكلمات المترجمة تلقائياً
function __($key) {
    global $dictionary;
    $current_lang = $_SESSION['lang'];
    return isset($dictionary[$current_lang][$key]) ? $dictionary[$current_lang][$key] : $key;
}

// 3. آلية معالجة قلب اللغات الآمنة عند الضغط على الزر لمنع خطأ 404
if (isset($_GET['action']) && $_GET['action'] == 'toggle') {
    $_SESSION['lang'] = ($_SESSION['lang'] == 'ar') ? 'fr' : 'ar';
    
    // إرجاع المستخدم تلقائياً لنفس الصفحة الحالية التي يتصفحها
    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
    header("Location: " . $referrer);
    exit();
}
?>

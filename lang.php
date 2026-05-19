<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['action']) && $_GET['action'] == 'toggle') {
    $_SESSION['lang'] = ($_SESSION['lang'] == 'ar') ? 'fr' : 'ar';
    
    // العودة الآمنة للصفحة السابقة لمنع ظهور خطأ التوجيه المعطل
    $back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
    header("Location: " . $back);
    exit();
}
header("Location: index.php");
exit();
<?php
// إعدادات الاتصال بالسيرفر الحي على الإنترنت
$host = 'sql202.epizy.com'; // استبدله بـ MySQL Hostname من استضافتك
$db   = 'if0_3824xxxx_docbook_db'; // استبدله باسم قاعدة البيانات الكامل الحقيقي أونلاين
$user = 'if0_3824xxxx'; // استبدله بـ MySQL Username الخاص بك
$pass = 'كلمة_مرور_استضافتك'; // اكتب كلمة مرور حسابك في الاستضافة
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // في حال حدوث خطأ في الاتصال يظهر السبب بوضوح للمطور
     die("خطأ في الاتصال بقاعدة البيانات الحية: " . $e->getMessage());
}
?>

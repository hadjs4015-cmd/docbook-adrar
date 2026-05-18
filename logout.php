<?php
session_start();
session_unset();
session_destroy(); // تدمير الجلسة بالكامل لضمان الأمان
header("Location: index.php");
exit;
?>
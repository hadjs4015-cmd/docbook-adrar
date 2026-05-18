<?php 
include 'db.php';
include 'header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    
    if (empty($fullname)  || empty($email)  || empty($phone) || empty($password)) {
        $error = "كافة الحقول مطلوبة لإتمام التسجيل.";
    } else {
        // تشفير كلمة المرور باستخدام الـ Hash الآمن
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (fullname, email, phone, password, role) VALUES (?, ?, ?, ?, 'patient')");
            $stmt->execute([$fullname, $email, $phone, $hashed_password]);
            $success = "تم إنشاء حسابك بنجاح! يمكنك الآن تسجيل الدخول وحجز موعدك.";
        } catch (\PDOException $e) {
            $error = "البريد الإلكتروني الذي أدخلته مسجل مسبقاً.";
        }
    }
}
?>

<h2>إنشاء حساب مريض جديد</h2>
<p style="color: #7f8c8d;">يرجى تزويدنا بالمعلومات التالية لفتح ملفك الطبي الإلكتروني.</p>

<?php if($error): ?><div style="color: #721c24; background: #f8d7da; padding: 10px; margin: 15px 0; border-radius: 4px;"><?php echo $error; ?></div><?php endif; ?>
<?php if($success): ?><div style="color: #155724; background: #d4edda; padding: 10px; margin: 15px 0; border-radius: 4px;"><?php echo $success; ?></div><?php endif; ?>

<form action="register.php" method="POST" style="max-width: 500px; margin-top: 20px;">
    <div class="form-group">
        <label>الاسم واللقب كاملاً:</label>
        <input type="text" name="fullname" class="form-control" required placeholder="مثال: محمد بن علي">
    </div>
    <div class="form-group">
        <label>البريد الإلكتروني:</label>
        <input type="email" name="email" class="form-control" required placeholder="name@domain.com">
    </div>
    <div class="form-group">
        <label>رقم الهاتف المحمول:</label>
        <input type="text" name="phone" class="form-control" required placeholder="06XXXXXXXX">
    </div>
    <div class="form-group">
        <label>كلمة المرور الآمنة:</label>
        <input type="password" name="password" class="form-control" required placeholder="اكتب كلمة مرور قوية">
    </div>
    <button type="submit" class="btn" style="background-color: #117a65; width: 100%;">إنشاء حسابي</button>
</form>

<?php include 'footer.php'; ?>
<?php 
include 'db.php';
include 'header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        // التحقق من صحة تشفير كلمة المرور
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['user_role'] = $user['role'];
            
            header("Location: booking.php");
            exit;
        } else {
            $error = "البريد الإلكتروني أو كلمة المرور غير صحيحة.";
        }
    } else {
        $error = "يرجى ملء جميع الحقول.";
    }
}
?>

<h2>تسجيل الدخول للمنصة الطبية</h2>
<p style="color: #7f8c8d;">يرجى تسجيل الدخول للوصول لصفحة الحجز ومتابعة مواعيدك الخاصة.</p>

<?php if($error): ?><div style="color: #721c24; background: #f8d7da; padding: 10px; margin: 15px 0; border-radius: 4px;"><?php echo $error; ?></div><?php endif; ?>

<form action="login.php" method="POST" style="max-width: 500px; margin-top: 20px;">
    <div class="form-group">
        <label>البريد الإلكتروني:</label>
        <input type="email" name="email" class="form-control" required placeholder="name@domain.com">
    </div>
    <div class="form-group">
        <label>كلمة المرور:</label>
        <input type="password" name="password" class="form-control" required placeholder="أدخل كلمة مرورك">
    </div>
    <button type="submit" class="btn" style="width: 100%;">دخول</button>
</form>

<?php include 'footer.php'; ?>
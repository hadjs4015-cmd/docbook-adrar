<?php
// تفعيل إظهار الأخطاء برمجياً لمعرفة سبب الصفحة البيضاء على السيرفر
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';
include 'header.php';
// باقي كود ملف index.php الطبيعي...
/** @var PDO $pdo */
include 'db.php';
include 'header.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search != '') {
    $stmt = $pdo->prepare("SELECT * FROM doctors WHERE (name LIKE ? OR specialty LIKE ?) AND is_archived = 0");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM doctors WHERE is_archived = 0");
}
$doctors = $stmt->fetchAll();

// مصفوفة الأيقونات الافتراضية الدائرية المطابقة لمشروعك الحالي
$default_avatar = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23117a65"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>';
?>

<div class="hero-wrapper">
    <div class="hero-text-side">
        <h1 class="hero-main-title">🩺 حجز موعد طبي سهل وسريع</h1>
        <p class="hero-sub-title">احجز الآن مع أفضل الأطباء في ولاية أدرار</p>
        
        <div class="search-form-container">
            <form action="index.php" method="GET">
                <input type="text" name="search" placeholder="<?php echo __('search_placeholder'); ?>" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><?php echo __('search_btn'); ?></button>
            </form>
        </div>
    </div>
    <div class="hero-image-side">
        <img src="https://images.unsplash.com/photo-1594824813573-246434e33963?auto=format&fit=crop&w=400&q=80" alt="Doctor Banner">
    </div>
</div>

<div class="main-layout-grid">
    
    <div class="cards-section">
        <h3 class="block-title">الأطباء الموصى بهم</h3>
        <div class="doctors-clean-grid">
            <?php if (count($doctors) > 0): ?>
                <?php foreach ($doctors as $doc): ?>
                    <div class="doctor-clean-card">
                        <div class="avatar-circle">
                            <img src="<?php echo $default_avatar; ?>" alt="Doctor">
                        </div>
                        <h4>د. <?php echo htmlspecialchars($doc['name']); ?></h4>
                        <p class="spec-text">🩺 <?php echo htmlspecialchars($doc['specialty']); ?></p>
                        <p class="price-text"><strong><?php echo $doc['price']; ?> دج</strong></p>
                        
                        <a href="booking.php?doc_id=<?php echo $doc['id']; ?>" class="action-booking-btn">
                            احجز الآن
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data-msg">لا يوجد أطباء متاحين حالياً يطابقون البحث.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="sidebar-section">
        <h3 class="block-title">تصفح التخصصات</h3>
        <div class="specialties-icon-grid">
            <a href="index.php?search=قلب" class="spec-icon-item">🩺 <span>قلب</span></a>
            <a href="index.php?search=عيون" class="spec-icon-item">👁️ <span>عيون</span></a>
            <a href="index.php?search=أطفال" class="spec-icon-item">👶 <span>أطفال</span></a>
            <a href="index.php?search=عظام" class="spec-icon-item">🦴 <span>عظام</span></a>
            <a href="index.php?search=جراحة" class="spec-icon-item">😷 <span>جراحة</span></a>
            <a href="index.php?search=عام" class="spec-icon-item">🏥 <span>طب عام</span></a>
        </div>
    </div>
</div>

<div class="features-footer-bar">
    <div class="feature-item">💬 تواصل معنا</div>
    <div class="feature-item">ℹ️ كيف يعمل</div>
    <div class="feature-item">🛡️ تصفح آمن</div>
</div>

<?php include 'footer.php'; ?>

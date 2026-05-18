<?php 
// 1. تضمين ملف الاتصال بقاعدة البيانات ورأس الصفحة (Header)
/**@var PDO $pdo */
include 'db.php';
include 'header.php';

// 2. حماية الصفحة: منع غير المسجلين من الدخول وتوجيههم لصفحة تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('عذراً، يجب تسجيل الدخول أولاً للوصول لصفحة الحجز.'); window.location.href='login.php';</script>";
    exit;
}

$selected_doc_id = isset($_GET['doc_id']) ? (int)$_GET['doc_id'] : 0;
$success_trigger = false;

// 3. معالجة طلب الحجز عند إرسال النموذج (Form Submission)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_book'])) {
    $user_id = $_SESSION['user_id'];
    $date = $_POST['app_date'];
    $time = $_POST['app_time'];
    $doctor_id = null;
    
    // أ) التحقق أولاً إذا أدخل المستخدم "كود البار" (الحجز السريع)
    if (!empty($_POST['barcode'])) {
        $stmt_bar = $pdo->prepare("SELECT id FROM doctors WHERE barcode = ? AND is_archived = 0");
        $stmt_bar->execute([trim($_POST['barcode'])]);
        $doc = $stmt_bar->fetch();
        if ($doc) {
            $doctor_id = $doc['id'];
        } else {
            echo "<script>alert('" . ($_SESSION['lang'] == 'fr' ? "Alerte: Code-barres invalide!" : "تنبيه: كود البار الذي أدخلته غير مطابق لأي طبيب نشط لدينا!") . "');</script>";
        }
    } 
    // ب) إذا لم يدخل كود بار، نعتمد على الطبيب المختار من القائمة المنسدلة
    elseif (!empty($_POST['doctor_id'])) {
        $doctor_id = (int)$_POST['doctor_id'];
    }

    // ج) حفظ الحجز في قاعدة البيانات إذا كانت المدخلات صحيحة
    if ($doctor_id && !empty($date) && !empty($time)) {
        $stmt = $pdo->prepare("INSERT INTO appointments (user_id, doctor_id, appointment_date, appointment_time) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $doctor_id, $date, $time]);
        $success_trigger = true; // تفعيل الإنذار الصوتي بعد الحفظ الناجح
    } else {
        if (empty($_POST['barcode']) && empty($_POST['doctor_id'])) {
            echo "<script>alert('" . ($_SESSION['lang'] == 'fr' ? "Veuillez choisir un médecin ou entrer un code-barres." : "الرجاء اختيار طبيب أو إدخال كود بار الطبيب لإتمام الحجز.") . "');</script>";
        }
    }
}

// 4. جلب قائمة الأطباء النشطين لعرضهم في القائمة المنسدلة
$doctors_list = $pdo->query("SELECT * FROM doctors WHERE is_archived = 0")->fetchAll();

// نصوص الترجمة الخاصة بصفحة الحجز فقط لضمان تكامل اللغتين
$b_trans = [
    'ar' => [
        'title' => 'صفحة حجز المواعيد الطبية',
        'subtitle' => 'يمكنك الحجز مباشرة باختيار طبيب، أو باستخدام رمز الباركود الخاص به للاختصار.',
        'success' => '🎉 تم تسجيل طلب حجز موعدك بنجاح في النظام وصدر صوت التنبيه!',
        'opt1' => '🛡️ الخيار الأول: الحجز السريع بكود البار الطبي',
        'barcode_lbl' => 'أدخل رمز الباركود الخاص بالطبيب (مثال: DOC-9910):',
        'placeholder_bar' => 'أدخل الكود بار هنا لتجاوز القائمة المنسدلة...',
        'or' => '— أو خيار القائمة المنسدلة —',
        'opt2' => '📋 الخيار الثاني: اختر الطبيب من القائمة',
        'doc_lbl' => 'اسم الطبيب وتخصصه الحالي:',
        'select_default' => '-- اختر طبيباً من القائمة المنسدلة --',
        'date_title' => '📅 تحديد موعد الحضور والزيارة',
        'date_lbl' => 'التاريخ المرجو:',
        'time_lbl' => 'التوقيت المفترض:',
        'btn_save' => 'تأكيد وحفظ الحجز 🔔',
        'guide_title' => 'إرشادات الحجز الرقمي الموحد',
        'guide_desc' => 'عند الضغط على زر التأكيد، سيقوم خادم تطبيق الويب بمعالجة طلبك وتخزينه في قاعدة بيانات MySQL الآمنة. ستتلقى مباشرة نغمة إنذار سمعية تفاعلية عبر المتصفح لتأكيد الإرسال الناجح وفقاً لدفتر الشروط الخاص بجامعة أدرار.',
        'table_title' => '📊 جدول مواعيدك الطبية المسجلة بالنظام:',
        'th_name' => 'اسم الطبيب',
        'th_spec' => 'التخصص',
        'th_date' => 'التاريخ',
        'th_time' => 'التوقيت',
        'th_status' => 'حالة الموعد',
        'no_apps' => 'لا توجد مواعيد مسجلة باسمك بعد.',
        'pending' => 'بانتظار المراجعة',
        'confirmed' => 'مقبول ومثبت',
        'cancelled' => 'ملغي'
    ],
    'fr' => [
        'title' => 'Page de Réservation des Rendez-vous',
        'subtitle' => 'Vous pouvez réserver directement en choisissant un médecin ou en utilisant son code-barres.',
        'success' => '🎉 Votre demande de rendez-vous a été enregistrée avec succès et l\'alarme a retenti!',
        'opt1' => '🛡️ Option 1: Réservation Rapide par Code-barres',
        'barcode_lbl' => 'Entrez le code-barres du médecin (Ex: DOC-9910):',
        'placeholder_bar' => 'Entrez le code-barres ici...',
        'or' => '— OU VIA LA LISTE DÉROULANTE —',
        'opt2' => '📋 Option 2: Choisir un Médecin dans la Liste',
        'doc_lbl' => 'Nom du médecin et sa spécialité:',
        'select_default' => '-- Sélectionnez un médecin de la liste --',
        'date_title' => '📅 Déterminer la Date et l\'Heure',
        'date_lbl' => 'Date souhaitée:',
        'time_lbl' => 'Heure souhaitée:',
        'btn_save' => 'Confirmer et Sauvegarder 🔔',
        'guide_title' => 'Guide de Réservation Numérique',
        'guide_desc' => 'En cliquant sur le bouton de confirmation, le serveur web traitera votre demande et la stockera dans la base de données MySQL. Vous recevrez instantanément une alerte sonore interactive via le navigateur pour confirmer la réussite de l\'opération selon le cahier des charges de l\'Université d\'Adrar.',
        'table_title' => '📊 Tableau de vos rendez-vous enregistrés:',
        'th_name' => 'Médecin',
        'th_spec' => 'Spécialité',
        'th_date' => 'Date',
        'th_time' => 'Heure',
        'th_status' => 'Statut',
        'no_apps' => 'Aucun rendez-vous enregistré pour le moment.',
        'pending' => 'En attente',
        'confirmed' => 'Confirmé',
        'cancelled' => 'Annulé'
    ]
];

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ar';
$t = $b_trans[$lang];
?>

<div style="margin-bottom: 30px; border-bottom: 2px solid #eaeded; padding-bottom: 15px;">
    <h2 style="color: #1a5276; font-size: 26px; font-weight: bold;"><?php echo $t['title']; ?></h2>
    <p style="color: #7f8c8d; font-size: 14px; margin-top: 5px;"><?php echo $t['subtitle']; ?></p>
</div>

<?php if($success_trigger): ?>
    <div style="background: #d4edda; color: #155724; padding: 15px; margin: 20px 0; border-radius: 6px; font-weight: bold; border: 1px solid #c3e6cb; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <?php echo $t['success']; ?>
    </div>
    <script>
        window.onload = function() {
            if (typeof playSuccessAlarm === "function") {
                playSuccessAlarm();
            }
        }
    </script>
<?php endif; ?>

<div style="display: flex; gap: 30px; flex-wrap: wrap; margin-top: 20px; align-items: flex-start;">
    
    <div style="flex: 1.3; min-width: 320px;">
        <form action="" method="POST" style="background: #fff; border: 1px solid #d5dbdb; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); position: relative;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #1a5276, #117a65); border-top-left-radius: 12px; border-top-right-radius: 12px;"></div>
            <input type="hidden" name="action_book" value="1">
            
            <h3 style="color: #1a5276; margin-bottom: 15px; font-size: 18px; font-weight: bold;"><?php echo $t['opt1']; ?></h3>
            <div class="form-group">
                <label style="font-size: 14px; color: #34495e;"><?php echo $t['barcode_lbl']; ?></label>
                <input type="text" name="barcode" class="form-control" placeholder="<?php echo $t['placeholder_bar']; ?>" style="height: 42px; border-radius: 6px;">
            </div>
<div style="text-align: center; margin: 20px 0; color: #7f8c8d; font-weight: bold; position: relative;">
                <span style="background: #fff; padding: 0 15px; z-index: 2; position: relative; font-size: 13px; color: #95a5a6;"><?php echo $t['or']; ?></span>
                <hr style="position: absolute; top: 50%; left: 0; right: 0; border: 0; border-top: 1px solid #eaeded; z-index: 1;">
            </div>

            <h3 style="color: #1a5276; margin-bottom: 15px; font-size: 18px; font-weight: bold;"><?php echo $t['opt2']; ?></h3>
            <div class="form-group">
                <label style="font-size: 14px; color: #34495e;"><?php echo $t['doc_lbl']; ?></label>
                <select name="doctor_id" class="form-control" style="height: 42px; border-radius: 6px; background-color: #fff;">
                    <option value=""><?php echo $t['select_default']; ?></option>
                    <?php foreach($doctors_list as $dl): ?>
                        <option value="<?php echo $dl['id']; ?>" <?php echo ($dl['id'] == $selected_doc_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dl['name']); ?> - <?php echo htmlspecialchars($dl['specialty']); ?> (<?php echo $dl['price']; ?> <?php echo ($_SESSION['lang'] == 'fr' ? 'DA' : 'دج'); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <h3 style="color: #1a5276; margin-top: 25px; margin-bottom: 15px; border-top: 1px dashed #e5e7eb; padding-top:20px; font-size: 18px; font-weight: bold;"><?php echo $t['date_title']; ?></h3>
            <div class="form-group" style="display: flex; gap:15px;">
                <div style="flex:1;">
                    <label style="font-size: 13px;"><?php echo $t['date_lbl']; ?></label>
                    <input type="date" name="app_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>" style="height: 42px; border-radius: 6px;">
                </div>
                <div style="flex:1;">
                    <label style="font-size: 13px;"><?php echo $t['time_lbl']; ?></label>
                    <input type="time" name="app_time" class="form-control" required style="height: 42px; border-radius: 6px;">
                </div>
            </div>

            <button type="submit" class="btn" style="background:#117a65; width:100%; font-size:16px; margin-top: 15px; padding: 12px; border-radius: 6px; font-weight: bold; box-shadow: 0 3px 6px rgba(17,122,101,0.2); border: none; cursor: pointer; color: #fff;">
                <?php echo $t['btn_save']; ?>
            </button>
        </form>
    </div>

    <div style="flex: 1; min-width: 320px;">
        <div style="background: #ffffff; border: 1px solid #e1e8ed; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); text-align: center; position: relative;">
            <div style="width: 100%; height: 250px; overflow: hidden; border-radius: 8px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=600&q=80" 
                     alt="Medical Infrastructure" 
                     style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <h4 style="color: #1a5276; margin-bottom: 10px; font-weight: bold; font-size: 16px; border-bottom: 2px solid #f4f6f9; padding-bottom: 8px;">
                <?php echo $t['guide_title']; ?>
            </h4>
            <p style="color: #7f8c8d; font-size: 13px; line-height: 1.6; text-align: justify; text-justify: inter-word; margin: 0;">
                <?php echo $t['guide_desc']; ?>
            </p>
        </div>
    </div>

</div>

<h3 style="margin-top: 50px; color: #1a5276; font-size: 20px; font-weight: bold; display: flex; align-items: center; gap: 8px;">
    <?php echo $t['table_title']; ?>
</h3>
[18-05-2026 16:56] Hadj saddik SALIHA: <div style="overflow-x: auto; background: #fff; border-radius: 8px; border: 1px solid #e1e8ed; margin-top: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
    <table style="width: 100%; border-collapse: collapse; margin: 0;">
        <thead>
            <tr style="background-color: #1a5276; color: white;">
                <th style="padding: 12px 15px; text-align: inherit; font-size: 15px;"><?php echo $t['th_name']; ?></th>
                <th style="padding: 12px 15px; text-align: inherit; font-size: 15px;"><?php echo $t['th_spec']; ?></th>
                <th style="padding: 12px 15px; text-align: inherit; font-size: 15px;"><?php echo $t['th_date']; ?></th>
                <th style="padding: 12px 15px; text-align: inherit; font-size: 15px;"><?php echo $t['th_time']; ?></th>
                <th style="padding: 12px 15px; text-align: inherit; font-size: 15px;"><?php echo $t['th_status']; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt_apps = $pdo->prepare("SELECT a.*, d.name as doc_name, d.specialty FROM appointments a JOIN doctors d ON a.doctor_id = d.id WHERE a.user_id = ? ORDER BY a.appointment_date DESC");
            $stmt_apps->execute([$_SESSION['user_id']]);
            $my_apps = $stmt_apps->fetchAll();
            
            if (count($my_apps) > 0) {
                foreach($my_apps as $ma) {
                    $status_text = isset($t[$ma['status']]) ? $t[$ma['status']] : $ma['status'];
                    
                    // تخصيص لون الحالة لتبدو تفاعلية واحترافية
                    $status_color = '#1a5276'; // الافتراضي للانتظار
                    if($ma['status'] == 'confirmed') $status_color = '#117a65';
                    if($ma['status'] == 'cancelled') $status_color = '#c0392b';

                    echo "<tr style='border-bottom: 1px solid #eaeded;'>
                            <td style='padding: 12px 15px; color: #2c3e50; font-weight: 500;'>".htmlspecialchars($ma['doc_name'])."</td>
                            <td style='padding: 12px 15px; color: #7f8c8d;'>".htmlspecialchars($ma['specialty'])."</td>
                            <td style='padding: 12px 15px; color: #2c3e50;'>".$ma['appointment_date']."</td>
                            <td style='padding: 12px 15px; color: #2c3e50;'>".$ma['appointment_time']."</td>
                            <td style='padding: 12px 15px; font-weight: bold; color: ".$status_color.";'>".$status_text."</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center; padding: 25px; color: #7f8c8d; font-style: italic;'>".$t['no_apps']."</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
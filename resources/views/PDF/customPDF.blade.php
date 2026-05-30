<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>دعوة زفاف إلكترونية</title>
    <style>
        /* إعدادات الصفحة وتحسينها للـ PDF (mPDF Safe) */
        @page {
            size: A4 portrait;
            margin: 0;
            padding: 0;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Tajawal', 'DejaVu Sans', sans-serif;
            text-align: center;
        }

        /* استخدام صورة خلفية كعنصر حقيقي لتجنب مشاكل background-image في mPDF مع الـ base64 */
        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        /* حاوية الأزرار التفاعلية - استخدام التمركز العادي (Absolute) لأن mPDF لا يدعم Flexbox */
        .buttons-container {
            position: absolute;
            bottom: 120px; /* ارتفاع مناسب من الأسفل بدلاً من 12% لضمان الدقة في mPDF */
            left: 0;
            width: 100%;
            text-align: center;
        }

        /* الستايل العام المشترك للأزرار */
        .inv-btn {
            display: inline-block;
            padding: 12px 25px; 
            font-size: 16px; 
            font-weight: bold;
            text-decoration: none;
            color: #ffffff;
            text-align: center;
            border-radius: 30px; 
            border: 1.5px solid #ffffff; /* إطار أبيض يحدد الزرار */
            margin: 0 10px; /* مسافة بين الزرين */
        }

        /* زرار تأكيد الحضور (أخضر زمردي فخم ثابت لأن mPDF لا يدعم linear-gradient بشكل كامل بدون أخطاء) */
        .btn-accept {
            background-color: #1a5133;
        }

        /* زرار الاعتذار (أحمر عنابي/ملكي ثابت) */
        .btn-decline {
            background-color: #8c2323;
        }

    </style>
</head>
<body>

    <!-- صورة الخلفية -->
    <img src="{{ $image }}" class="bg-image" />

    <!-- الأزرار -->
    <div class="buttons-container">
        <a href="{{ $confirm_link }}" class="inv-btn btn-accept">
            تأكيد الحضور
        </a>
        
        <a href="{{ $apologize_link }}" class="inv-btn btn-decline">
            الاعتذار عن الحضور
        </a>
    </div>

</body>
</html>
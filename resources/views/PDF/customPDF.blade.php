<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دعوة زفاف إلكترونية</title>
    <style>
        /* إعدادات الصفحة الأساسية وتحسينها للـ PDF */
        @page {
            size: A4 portrait; /* يمكنك تغييرها لـ landscape لو الدعوة بالعرض */
            margin: 0;
        }
        
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* للتأكيد أن المتصفح هيطبع الخلفيات والألوان في الـ PDF */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        /* حاوية الدعوة - بمقاسات الـ A4 القياسية */
        .invitation-card {
            position: relative;
            width: 595px;  /* عرض الـ A4 بـ البكسل عند 72dpi */
            height: 842px; /* طول الـ A4 بـ البكسل */
            background-image: url('https://mazoom.online/images/69f84857eec35.jpg"'); /* حط هنا اسم الصورة بتاعتك */
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            overflow: hidden;
        }

        /* الزرار الذكي عالي التباين والشفافية */
        .rsvp-btn {
            position: absolute;
            
            /* التحكم في مكان الزرار فوق الصورة (عدل النسب دي حسب مكان كلمة "تأكيد الحضور") */
            bottom: 12%; 
            left: 50%;
            transform: translateX(-50%);
            
            /* الأبعاد والحجم */
            width: 75%;
            max-width: 320px;
            padding: 16px 32px;
            
            /* الخطوط والتكست */
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            text-decoration: none;
            text-align: center;
            letter-spacing: 0.5px;
            
            /* الشفافية والألوان (مزيج ذهبي فخم شبه شفاف تظهر تفاصيل الخلفية) */
            background: linear-gradient(135deg, rgba(197, 160, 89, 0.82), rgba(142, 108, 45, 0.82));
            
            /* تأثير الزجاج البلوري (لو المحول بيدعمه) */
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            
            /* الحواف والحدود */
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-radius: 50px;
            
            /* الظلال لإعطاء عمق ثنائي الأبعاد يظهر بوضوح في الـ PDF */
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25), 
                        inset 0 4px 10px rgba(255, 255, 255, 0.2);
            
            transition: all 0.3s ease;
            cursor: pointer;
            display: block;
        }

        /* تأثيرات التحويم (تظهر لو العميل فتح الـ PDF على الكمبيوتر أو الموبايل) */
        .rsvp-btn:hover {
            background: linear-gradient(135deg, rgba(197, 160, 89, 0.95), rgba(142, 108, 45, 0.95));
            box-shadow: 0 12px 30px rgba(142, 108, 45, 0.4);
            border-color: rgba(255, 255, 255, 0.8);
            transform: translateX(-50%) scale(1.02);
        }

        /* تظبيط الطباعة والـ PDF عشان ميبقاش فيه هوامش بيضاء */
        @media print {
            body {
                background-color: transparent;
            }
            .invitation-card {
                box-shadow: none;
                width: 100%;
                height: 100%;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <div class="invitation-card">
        <a href="https://your-rsvp-link-here.com" class="rsvp-btn" target="_blank">
            تأكيد الحضور
        </a>
    </div>

</body>
</html>
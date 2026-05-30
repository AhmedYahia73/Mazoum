<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دعوة زفاف إلكترونية</title>
    <style>
        /* إعدادات الصفحة وتحسينها للـ PDF */
        @page {
            size: A4 portrait;
            margin: 0;
            padding: 0;
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
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* كارت الدعوة بأبعاد الـ A4 وثابت عليه الخلفية */
        .invitation-card {
            position: relative;
            width: 595px;  
            height: 842px; 
            background-image: url("{{ $image }}"); 
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            overflow: hidden;
        }

        /* حاوية الأزرار التفاعلية */
        .buttons-container {
            position: absolute;
            bottom: 12%; /* يتحكم في الارتفاع من الأسفل (تقدر تعدله حسب الرغبة) */
            left: 50%;
            transform: translateX(-50%);
            width: 85%;
            max-width: 460px; 
            display: flex;
            gap: 15px; 
            justify-content: center;
        }

        /* الستايل العام المشترك للأزرار */
        .inv-btn {
            flex: 1; 
            padding: 12px 20px; 
            font-size: 16px; 
            font-weight: 700;
            text-decoration: none;
            text-align: center;
            border-radius: 30px; 
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            border: 1.5px solid rgba(255, 255, 255, 0.35); /* إطار أبيض شفاف يحدد الزرار */
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2), 
                        inset 0 2px 5px rgba(255, 255, 255, 0.25);
            transition: all 0.3s ease;
            cursor: pointer;
            white-space: nowrap; 
        }

        /* زرار تأكيد الحضور (تدريج أخضر زمردي فخم شبه شفاف) */
        .btn-accept {
            color: #ffffff;
            background: linear-gradient(135deg, rgba(39, 110, 74, 0.85), rgba(20, 61, 40, 0.85));
        }

        .btn-accept:hover {
            background: linear-gradient(135deg, rgba(39, 110, 74, 0.95), rgba(20, 61, 40, 0.95));
            box-shadow: 0 8px 25px rgba(20, 61, 40, 0.5);
            transform: scale(1.03);
        }

        /* زرار الاعتذار (تدريج أحمر عنابي/ملكي دافئ شبه شفاف) */
        .btn-decline {
            color: #ffffff;
            background: linear-gradient(135deg, rgba(166, 45, 45, 0.85), rgba(107, 24, 24, 0.85));
        }

        .btn-decline:hover {
            background: linear-gradient(135deg, rgba(166, 45, 45, 0.95), rgba(107, 24, 24, 0.95));
            box-shadow: 0 8px 25px rgba(107, 24, 24, 0.5);
            transform: scale(1.03);
        }

        /* تظبيط الهوامش عند التحويل لـ PDF */
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
        <div class="buttons-container">
            <a href="{{ $confirm_link }}" class="inv-btn btn-accept" target="_blank">
                تأكيد الحضور
            </a>
            
            <a href="{{ $apologize_link }}" class="inv-btn btn-decline" target="_blank">
                الاعتذار عن الحضور
            </a>
        </div>
    </div>

</body>
</html>
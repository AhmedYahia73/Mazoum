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
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-align: center;
        }

        /* صورة الخلفية بديلة لـ background-image لتجنب مشاكل mPDF */
        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        /* حاوية الأزرار التفاعلية */
        .buttons-container {
            position: absolute;
            bottom: 12%; 
            left: 0;
            width: 100%;
            text-align: center;
        }

        .btn-table {
            margin: 0 auto;
            width: 85%;
            max-width: 460px;
        }

        .btn-cell {
            width: 50%;
            text-align: center;
            padding: 0 10px; /* مسافة بين الزرين تحل مشكلة الالتصاق */
        }

        /* الستايل العام المشترك للأزرار */
        .inv-btn {
            display: block; 
            padding: 12px 20px; 
            font-size: 16px; 
            font-weight: 700;
            text-decoration: none;
            text-align: center;
            border-radius: 30px; 
            border: 1.5px solid #ffffff; 
            color: #ffffff;
            white-space: nowrap; 
        }

        /* زرار تأكيد الحضور - ألوان Hex آمنة جداً لمكتبة mPDF لتجنب الأيرور */
        .btn-accept {
            background: linear-gradient(135deg, #236242, #113523);
        }

        /* زرار الاعتذار - ألوان Hex آمنة جداً لمكتبة mPDF */
        .btn-decline {
            background: linear-gradient(135deg, #8E2626, #5B1414);
        }

    </style>
</head>
<body>

    <img src="{{ $image }}" class="bg-image" />

    <div class="buttons-container">
        <table class="btn-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="btn-cell">
                    <a href="{{ $confirm_link }}" class="inv-btn btn-accept" target="_blank">
                        تأكيد الحضور
                    </a>
                </td>
                <td class="btn-cell">
                    <a href="{{ $apologize_link }}" class="inv-btn btn-decline" target="_blank">
                        الاعتذار عن الحضور
                    </a>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
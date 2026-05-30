<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>دعوة</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        /* الحاوية الرئيسية بحجم A4 بالضبط */
        .page-wrapper {
            position: relative;
            width: 210mm;
            height: 297mm;
            overflow: hidden;
        }
        /* صورة الخلفية تملأ الصفحة بالكامل */
        .bg-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 210mm;
            height: 297mm;
        }
        /* الأزرار فوق الصورة */
        .buttons-container {
            position: absolute;
            bottom: 25mm;
            left: 0;
            width: 100%;
            text-align: center;
        }
        .btn-table {
            margin: 0 auto;
        }
        .btn-cell {
            padding: 0 5mm;
        }
        .inv-btn {
            display: block;
            padding: 4mm 8mm;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: none;
            color: #ffffff;
            border-radius: 20pt;
            border: 2pt solid #ffffff;
            white-space: nowrap;
        }
        .btn-accept  { background-color: #1e6b40; }
        .btn-decline { background-color: #8e2020; }
    </style>
</head>
<body>
<div class="page-wrapper">
    <img src="{{ $imageBase64 }}" class="bg-img" />
    <div class="buttons-container">
        <table class="btn-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="btn-cell">
                    <a href="{{ $confirm_link }}" class="inv-btn btn-accept">تأكيد الحضور</a>
                </td>
                <td class="btn-cell">
                    <a href="{{ $apologize_link }}" class="inv-btn btn-decline">الاعتذار عن الحضور</a>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
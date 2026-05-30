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
            width: 595pt;
            height: 842pt;
            font-family: Arial, sans-serif;
            background-image: url("{{ $imageBase64 }}");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center center;
        }
        .buttons-container {
            position: absolute;
            bottom: 100pt;
            left: 0;
            width: 100%;
            text-align: center;
        }
        .btn-table {
            margin: 0 auto;
        }
        .btn-cell {
            padding: 0 12pt;
        }
        .inv-btn {
            display: block;
            padding: 12pt 22pt;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: none;
            color: #ffffff;
            border-radius: 25pt;
            border: 2pt solid #ffffff;
            white-space: nowrap;
        }
        .btn-accept {
            background-color: #1e6b40;
        }
        .btn-decline {
            background-color: #8e2020;
        }
    </style>
</head>
<body>
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
</body>
</html>
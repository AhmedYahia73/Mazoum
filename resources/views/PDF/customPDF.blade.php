<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>دعوة زفاف إلكترونية</title>
    <style>
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

        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

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
            padding: 0 10px; /* مسافة بين الزرين */
        }

        .inv-btn {
            display: block; 
            padding: 12px 20px; 
            font-size: 16px; 
            font-weight: bold;
            text-decoration: none;
            color: #ffffff;
            border-radius: 30px; 
            border: 1.5px solid #ffffff; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        .btn-accept {
            background-color: #276e4a;
        }

        .btn-decline {
            background-color: #a62d2d;
        }

    </style>
</head>
<body>

    <img src="{{ $image }}" class="bg-image" />

    <div class="buttons-container">
        <table class="btn-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="btn-cell">
                    <a href="{{ $confirm_link }}" class="inv-btn btn-accept">
                        تأكيد الحضور
                    </a>
                </td>
                <td class="btn-cell">
                    <a href="{{ $apologize_link }}" class="inv-btn btn-decline">
                        الاعتذار عن الحضور
                    </a>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Mazoom</title>

        <link rel="stylesheet" href="{{ asset('arabic_font') }}/droidarabickufi.css"/>

        <style>
            body {
                direction: rtl;
                text-align: center
            }
        </style>

        <style>
                body, html, h1, h2, h3, h4, h5, h6, p, li, .btn ,select , .btn-primary,
                .theme-green .back-bar .pointer-label
                {
                  font-family: 'DroidArabicKufiRegular', sans-serif !important;
                }

                ::-webkit-input-placeholder {
                  /* Chrome/Opera/Safari */
                  font-family: 'DroidArabicKufiRegular', sans-serif !important;
                }
                ::-moz-placeholder {
                  /* Firefox 19+ */
                  font-family: 'DroidArabicKufiRegular', sans-serif !important;
                }
                :-ms-input-placeholder {
                  /* IE 10+ */
                  font-family: 'DroidArabicKufiRegular', sans-serif !important;
                }
                :-moz-placeholder {
                  /* Firefox 18- */
                  font-family: 'DroidArabicKufiRegular', sans-serif !important;
                }

            </style>

    </head>
    <body style="padding-top: 150px">
        <div>

            <p>
                فضلا احتفظ بكود الدخول لأبرازة وقت الدخول للمناسبة
            </p>

            <h3>
                دعوات معزوم الالكترونية
            </h3>

            <div>
                <ul style="width: auto;max-width: 450px;text-align: right;margin:auto">
                    <li>
                        مسح الكود يكون عن طريق تطبيق معزوم يوم المناسبة
                    </li>
                    <li>
                        لا يعتبر الكود لاغي عند مسحة باي طريقة اخرى
                    </li>
                    <li>
                        العدد المسموح بالدخول محدد في بطاقة الدخول
                    </li>
                </ul>
            </div>

            <p>
                للمعرفة أكثر عن دعواتنا تفضل بزياره موقعنا
                <a href="{{ asset('/') }}" style="color: blue;font-weight:bold">
                    دعوات معزوم
                </a>
            </p>

        </div>
    </body>
</html>

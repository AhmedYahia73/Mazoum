
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دعوة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f2f2f2;
            font-family: 'Tajawal', sans-serif;
        }
        .card {
            border-radius: 20px;
        }
        .main-image {
            width: 100%;
            border-radius: 20px;
            max-height: 500px;
        }
        .btn-success, .btn-danger, .btn-secondary {
            font-size: 18px;
            padding: 12px;
            border-radius: 12px;
        }
        .icon-btn {
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12" style="max-width: 500px">

                <div class="card p-3 shadow-sm text-center" style="border-radius: 0px;padding: 20px 35px !important;">

                    <img src="{{ url('icons/test.jpg') }}" class="main-image mb-3" alt="Invitation Image">

                    <div class="btn" style="width: 100%;color: #000;border: 1px solid #eee;border-radius: 0;padding-top: 10px;padding-bottom: 10px;text-align: right;margin-bottom: 10px;box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;">
                        <img src="{{ url('icons/location.png') }}" class="me-1" style="width: 30px;" alt="Location Icon">
                        <span> قاعة نيادة للاحتفالات والمؤتمرات </span>
                    </div>

                    <div class="btn" style="background-color: #F3F6F2; width: 100%;color: #000;border: 1px solid #eee;border-radius: 0;padding-top: 10px;padding-bottom: 10px;text-align: right;margin-bottom: 10px;box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;">
                        <span style="color: #707070;"> المكرمه سعيد </span>
                        <span style="color: #707070;"> الحالة الحالية : تاكيد </span>
                    </div>

                    <div class="btn" style="background-color: #F3F6F2; width: 100%;color: #000;border: 1px solid #eee;border-radius: 0;padding-top: 10px;padding-bottom: 10px;text-align: right;margin-bottom: 10px;box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;">
                        <span style="color: #707070;">  عدد حضور المناسبة : 5 </span>
                    </div>

                    <div>

                        <div style="border: 1px dotted #DDD;border-radius: 8px;margin-bottom: 15px;margin-top: 15px;">
                            <img src="{{ url('qr-image-v9.jpg') }}" style="max-width: 100%;margin-bottom: 15px;margin-top: 10px;border-radius: 0px;height: 220px;">
                        </div>

                        <div style="margin-bottom: 20px">
                            <button class="btn btn-outline-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#ViewQR" style="height: 25px;line-height: 15px;background: #000;border-color: #000;color: #fff;padding: 0;width: 70px !important;">
                                <img src="{{ url('icons/eye.png') }}" style="width: 17px;">
                                <span style="font-size: 13px;">  معاينة </span>
                            </button>
                            <a href="{{ url('qr-image-v9.jpg') }}" download="download{{ rand() }}" class="btn btn-outline-danger w-100 mb-2" style="height: 25px;line-height: 17px;background: #000;border-color: #000;color: #fff;padding: 0;width: 80px !important;">
                                <img src="{{ url('icons/download.png') }}" style="width: 16px;height: 14px;">
                                <span style="font-size: 13px;"> تحميل </span>
                            </a>
                        </div>
                    </div>


                    <button class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#SendLoginUser" style="background-color: #1C6402;border-color: #1C6402;height: 53px;">
                        <img src="{{ url('icons/true.png') }}" style="width: 23px;margin-left: 6px;">
                        <span style="font-weight: bold;font-size: 20px;">  قبول الدعوة </span>
                    </button>

                    <button class="btn btn-outline-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#SendRefuseMessage" style="height: 53px;">
                        <img src="{{ url('icons/false.png') }}" style="width: 23px;margin-left: 6px;">
                        <span style="font-weight: bold;font-size: 20px;"> اعتذار الدعوة </span>
                    </button>

                    <button class="btn btn-success w-100"  data-bs-toggle="modal" data-bs-target="#SendCustomMessage" style="background-color: #333432;border-color: #333432;height: 53px;">
                        <img src="{{ url('icons/send.png') }}" style="width: 23px;margin-left: 6px;">
                        <span style="font-weight: bold;font-size: 20px;"> إرسال تهنئة </span>
                    </button>

                    <p class="mt-4 text-muted small">
                        جميع الحقوق محفوظة
                        <br>
                        mazoom.invitations
                    </p>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="SendLoginUser" tabindex="-1" aria-labelledby="SendLoginUserLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="SendLoginUserLabel"> حدد الضيوف ,, </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin-left: 0;"></button>
            </div>

            <input type="hidden" name="user_id" value="">

            <div class="modal-body">
                <div class="">
                    <label style="display: block;margin-bottom: 5px;">  اختر عدد المدعوين الذين يرغبون في الحضور  	  </label>
                    <select name="users_count" class="form-control m-bootstrap-select m_selectpicker" required>
                            <option value="" disabled selected> اختر عدد الدعوات </option>
                            <option value="1"> 1 </option>
                            <option value="2"> 2 </option>
                            <option value="3"> 3 </option>
                            <option value="4"> 4 </option>
                            <option value="5"> 5 </option>
                            <option value="6"> 6 </option>
                            <option value="7"> 7 </option>
                            <option value="8"> 8 </option>
                            <option value="9"> 9 </option>
                            <option value="10"> 10 </option>
                    </select>
                </div>

            </div>
            <div class="modal-footer" style="border-top:0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="height: 37px;padding-top: 0;padding-bottom: 0;border-radius: 7px;">أغلاق</button>
                <button type="submit" form="send-login-user" class="btn btn-primary" data-id=""> أرسال </button>
            </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="SendCustomMessage" tabindex="-1" aria-labelledby="SendCustomMessageLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="SendCustomMessageLabel"> أرسائل تهنئة </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin-left: 0;"></button>
            </div>
            <div class="modal-body">

                <div class="">
                    <label style="display: block;margin-bottom: 5px;"> أكتب رسالتك الآن يسمح بإرسال نص فقط ,, </label>
                    <textarea name="message" rows="4" class="form-control" placeholder="محتوي الرسالة">{{ old('message') }}</textarea>
                </div>

            </div>
            <div class="modal-footer" style="border-top:0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="height: 37px;padding-top: 0;padding-bottom: 0;border-radius: 7px;">أغلاق</button>
                <button type="button" onclick="sendMessageToSelected()" class="btn btn-primary"> أرسال </button>
            </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="SendRefuseMessage" tabindex="-1" aria-labelledby="SendRefuseMessageLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="SendRefuseMessageLabel">  رفض الدعوة </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin-left: 0;"></button>
            </div>
            <div class="modal-body">

                <div class="">
                    <label style="display: block;margin-bottom: 5px;"> هل ترغـب بإرسال أعتذار إلى صاحب المناسبة ( أختياري ) ,, </label>
                    <textarea name="message" rows="4" class="form-control" placeholder="محتوي الرسالة">{{ old('message') }}</textarea>
                </div>

            </div>
            <div class="modal-footer" style="border-top:0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="height: 37px;padding-top: 0;padding-bottom: 0;border-radius: 7px;">أغلاق</button>
                <button type="button" onclick="sendMessageToSelected()" class="btn btn-primary"> تاكيد الرفض </button>
            </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ViewQR" tabindex="-1" aria-labelledby="ViewQRLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ViewQRLabel">  معاينة   </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin-left: 0;"></button>
            </div>
            <div class="modal-body">

                <div style="text-align: center">
                    <img src="{{ url('qr-image-v9.jpg') }}" style="max-width: 100%;border-radius: 16px;height: 300px;">
                </div>

            </div>
            <div class="modal-footer" style="border-top:0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="height: 37px;padding-top: 0;padding-bottom: 0;border-radius: 7px;">أغلاق</button>
            </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

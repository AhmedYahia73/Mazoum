
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
            border-radius: 7px;
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

                    <img src="{{ @$event_user->event->file }}" class="main-image mb-3" alt="Invitation Image">

                    <div class="btn" onclick="location.href='https://www.google.com/maps?q={{ @$event_user->event->lat }},{{ @$event_user->event->long }}'" style="cursor: pointer;width: 100%;color: #000;border: 1px solid #eee;border-radius: 0;padding-top: 10px;padding-bottom: 10px;text-align: right;margin-bottom: 10px;box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;">
                        <img src="{{ url('icons/location.png') }}" class="me-1" style="width: 30px;" alt="Location Icon">
                        <span>
                            {{ @$event_user->event->address }}
                        </span>
                    </div>

                    @php
                        $latest_action = \App\Models\EventUserActions::where('event_user_id', $event_user->id)->latest()->first();
                    @endphp

                    <div class="btn" style="background-color: #F3F6F2; width: 100%;color: #000;border: 1px solid #eee;border-radius: 0;padding-top: 10px;padding-bottom: 10px;text-align: right;margin-bottom: 10px;box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;">
                        <span style="color: #707070;"> {{ $event_user->name }} </span>
                        @if($latest_action != null)
                        <span style="color: #707070;"> الحالة الحالية : {{ $latest_action->action == 'accept_event' || $latest_action->action == 'yes_receive_congratulation' ? 'تاكيد' : 'رفض' }}</span>
                        @endif
                    </div>

                    @if(\App\Models\EventUserActions::where('event_user_id', $event_user->id)->where('action','accept_event')->first() != null)
                    <div class="btn" style="background-color: #F3F6F2; width: 100%;color: #000;border: 1px solid #eee;border-radius: 0;padding-top: 10px;padding-bottom: 10px;text-align: right;margin-bottom: 10px;box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;">
                        <span style="color: #707070;">  عدد حضور المناسبة : {{ \App\Models\EventUserActions::where('event_user_id', $event_user->id)->where('action','accept_event')->first()->users_count }} </span>
                    </div>
                    @else
                    <div class="btn" style="background-color: #F3F6F2; width: 100%;color: #000;border: 1px solid #eee;border-radius: 0;padding-top: 10px;padding-bottom: 10px;text-align: right;margin-bottom: 10px;box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;">
                        <span style="color: #707070;">  عدد حضور المناسبة : {{ $event_user->users_count }} </span>
                    </div>
                    @endif

                    @if(@$qr_row && $qr_row->qr != null)
                    <div>

                        <div style="border: 1px dotted #DDD;border-radius: 8px;margin-bottom: 15px;margin-top: 15px;">
                            <img src="{{ url('qr_code/'.$qr_row->qr) }}?{{ rand() }}" style="max-width: 100%;margin-bottom: 15px;margin-top: 10px;border-radius: 0px;height: 220px;">
                        </div>

                        <div style="margin-bottom: 20px">
                            <button class="btn btn-outline-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#ViewQR" style="height: 25px;line-height: 15px;background: #000;border-color: #000;color: #fff;padding: 0;width: 70px !important;">
                                <img src="{{ url('icons/eye.png') }}" style="width: 17px;">
                                <span style="font-size: 13px;">  معاينة </span>
                            </button>
                            <a href="{{ url('qr_code/'.$qr_row->qr) }}?{{ rand() }}" download="download{{ rand() }}" class="btn btn-outline-danger w-100 mb-2" style="height: 25px;line-height: 17px;background: #000;border-color: #000;color: #fff;padding: 0;width: 80px !important;">
                                <img src="{{ url('icons/download.png') }}" style="width: 16px;height: 14px;">
                                <span style="font-size: 13px;"> تحميل </span>
                            </a>
                        </div>
                    </div>
                    @endif


                    @if($latest_action == null)

                        <button class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#SaveAcceptAction" style="background-color: #1C6402;border-color: #1C6402;height: 53px;">
                            <img src="{{ url('icons/true.png') }}" style="width: 23px;margin-left: 6px;">
                            <span style="font-weight: bold;font-size: 20px;">  قبول الدعوة </span>
                        </button>

                        <button class="btn btn-outline-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#SendRefuseMessage" style="height: 53px;">
                            <img src="{{ url('icons/false.png') }}" style="width: 23px;margin-left: 6px;">
                            <span style="font-weight: bold;font-size: 20px;"> اعتذار الدعوة </span>
                        </button>

                    @elseif($latest_action != null && ($latest_action->action == 'accept_event' || $latest_action->action == 'yes_receive_congratulation'))

                        @if(\App\Models\EventUserActions::where('event_user_id', $event_user->id)->where('action','refuse_event')->count() == 0)
                        <button class="btn btn-outline-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#SendRefuseMessage" style="height: 53px;">
                            <img src="{{ url('icons/false.png') }}" style="width: 23px;margin-left: 6px;">
                            <span style="font-weight: bold;font-size: 20px;"> اعتذار الدعوة </span>
                        </button>
                        @endif

                        @if(\App\Models\EventUserActions::where('event_user_id', $event_user->id)->where('action','yes_receive_congratulation')->count() == 0)
                        <button class="btn btn-success w-100"  data-bs-toggle="modal" data-bs-target="#SendCustomMessage" style="background-color: #333432;border-color: #333432;height: 53px;">
                            <img src="{{ url('icons/send.png') }}" style="width: 23px;margin-left: 6px;">
                            <span style="font-weight: bold;font-size: 20px;"> إرسال تهنئة </span>
                        </button>
                        @else
                        <button class="btn btn-success w-100"  data-bs-toggle="modal" data-bs-target="#ViewCustomMessage" style="background-color: #333432;border-color: #333432;height: 53px;">
                            <img src="{{ url('icons/eye.png') }}" style="width: 23px;margin-left: 6px;">
                            <span style="font-weight: bold;font-size: 20px;"> معاينة رسالة التهنئة </span>
                        </button>
                        @endif

                    @elseif($latest_action != null && ($latest_action->action == 'refuse_event' || $latest_action->action == 'yes_receive_apology'))

                        @if(\App\Models\EventUserActions::where('event_user_id', $event_user->id)->where('action','accept_event')->count() == 0)
                        <button class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#SaveAcceptAction" style="background-color: #1C6402;border-color: #1C6402;height: 53px;">
                            <img src="{{ url('icons/true.png') }}" style="width: 23px;margin-left: 6px;">
                            <span style="font-weight: bold;font-size: 20px;">  قبول الدعوة </span>
                        </button>
                        @endif

                        @if(\App\Models\EventUserActions::where('event_user_id', $event_user->id)->where('action','yes_receive_apology')->count() > 0)
                        <button class="btn btn-success w-100"  data-bs-toggle="modal" data-bs-target="#ViewCustomMessage2" style="background-color: #333432;border-color: #333432;height: 53px;">
                            <img src="{{ url('icons/eye.png') }}" style="width: 23px;margin-left: 6px;">
                            <span style="font-weight: bold;font-size: 20px;"> معاينة رسالة الأعتذار </span>
                        </button>
                        @endif

                    @endif

                    <p class="mt-4 text-muted small">
                        جميع الحقـوق محفوظة
                        <br>
                        mazoom.invitations
                    </p>

                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="SaveAcceptAction" tabindex="-1" aria-labelledby="SaveAcceptActionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                {!! Form::open(array('url' => 'new-save-event-user-action', 'id' => 'save-event-user-action')) !!}

                <div class="modal-header">
                    <h5 class="modal-title" id="SaveAcceptActionLabel"> حدد الضيوف ,, </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin-left: 0;"></button>
                </div>

                <input type="hidden" name="event_user_id" value="{{ $event_user->id }}">
                <input type="hidden" name="code" value="{{ $event_user->code }}">
                <input type="hidden" name="action" value="accept_event">

                <div class="modal-body">
                    <div class="">
                        <label style="display: block;margin-bottom: 5px;">  اختر عدد المدعوين الذين يرغبون في الحضور  	  </label>
                        <select name="users_count" class="form-control m-bootstrap-select m_selectpicker" required>
                                <option value="" disabled selected> اختر عدد الدعوات </option>
                                @for($i = 1; $i <= $event_user->users_count; $i++)
                                    <option value="{{ $i }}"> {{ $i }} </option>
                                @endfor
                        </select>
                    </div>

                </div>
                <div class="modal-footer" style="border-top:0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="height: 37px;padding-top: 0;padding-bottom: 0;border-radius: 7px;">أغلاق</button>
                    <button type="submit" form="save-event-user-action" class="btn btn-primary"> أرسال </button>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>



    <div class="modal fade" id="SendCustomMessage" tabindex="-1" aria-labelledby="SendCustomMessageLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                {!! Form::open(array('url' => 'new-save-event-user-action', 'id' => 'save-event-user-action2')) !!}

                <div class="modal-header">
                    <h5 class="modal-title" id="SendCustomMessageLabel"> أرسائل تهنئة </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin-left: 0;"></button>
                </div>

                <input type="hidden" name="event_user_id" value="{{ $event_user->id }}">
                <input type="hidden" name="code" value="{{ $event_user->code }}">
                <input type="hidden" name="action" value="yes_receive_congratulation">

                <div class="modal-body">

                    <div class="">
                        <label style="display: block;margin-bottom: 5px;"> أكتب رسالتك الآن يسمح بإرسال نص فقط ,, </label>
                        <textarea name="msg" required rows="4" class="form-control" placeholder="محتوي الرسالة">{{ old('message') }}</textarea>
                    </div>

                </div>

                <div class="modal-footer" style="border-top:0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="height: 37px;padding-top: 0;padding-bottom: 0;border-radius: 7px;">أغلاق</button>
                    <button type="submit" form="save-event-user-action2" class="btn btn-primary"> أرسال </button>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>



    @if(\App\Models\EventUserActions::where('event_user_id', $event_user->id)->where('action','yes_receive_congratulation')->first() != null)
    <div class="modal fade" id="ViewCustomMessage" tabindex="-1" aria-labelledby="ViewCustomMessageLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header" syle="border-bottom: 0;">
                    <h5 class="modal-title" id="ViewCustomMessageLabel"> رسالة التهنئة </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin-left: 0;"></button>
                </div>

                <div class="modal-body">

                    <div class="">
                        <label style="display: block;margin-bottom: 5px;"> محتوي الرسالة ,, </label>
                        <textarea name="msg" required rows="4" class="form-control" placeholder="محتوي الرسالة">{{ \App\Models\EventUserActions::where('event_user_id', $event_user->id)->where('action','yes_receive_congratulation')->first()->msg }}</textarea>
                    </div>

                </div>

            </div>
        </div>
    </div>
    @endif


    @if(\App\Models\EventUserActions::where('event_user_id', $event_user->id)->where('action','yes_receive_apology')->first() != null)
    <div class="modal fade" id="ViewCustomMessage2" tabindex="-1" aria-labelledby="ViewCustomMessage2Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header" syle="border-bottom: 0;">
                    <h5 class="modal-title" id="ViewCustomMessage2Label"> رسالة الأعتذار </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin-left: 0;"></button>
                </div>

                <div class="modal-body">

                    <div class="">
                        <label style="display: block;margin-bottom: 5px;"> محتوي الرسالة ,, </label>
                        <textarea name="msg" required rows="4" class="form-control" placeholder="محتوي الرسالة">{{ \App\Models\EventUserActions::where('event_user_id', $event_user->id)->where('action','yes_receive_apology')->first()->msg }}</textarea>
                    </div>

                </div>

            </div>
        </div>
    </div>
    @endif



    <div class="modal fade" id="SendRefuseMessage" tabindex="-1" aria-labelledby="SendRefuseMessageLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                {!! Form::open(array('url' => 'new-save-event-user-action', 'id' => 'save-event-user-action3')) !!}

                <div class="modal-header">
                    <h5 class="modal-title" id="SendRefuseMessageLabel">  رفض الدعوة </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin-left: 0;"></button>
                </div>

                <input type="hidden" name="event_user_id" value="{{ $event_user->id }}">
                <input type="hidden" name="code" value="{{ $event_user->code }}">
                <input type="hidden" name="action" value="refuse_event">

                <div class="modal-body">

                    <div class="">
                        <label style="display: block;margin-bottom: 5px;"> هل ترغـب بإرسال أعتذار إلى صاحب المناسبة ( أختياري ) ,, </label>
                        <textarea name="msg" rows="4" class="form-control" placeholder="محتوي الرسالة">{{ old('msg') }}</textarea>
                    </div>

                </div>

                <div class="modal-footer" style="border-top:0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="height: 37px;padding-top: 0;padding-bottom: 0;border-radius: 7px;">أغلاق</button>
                    <button type="submit" form="save-event-user-action3" class="btn btn-primary"> أرسال </button>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>



    @if(@$qr_row && $qr_row->qr != null)
    <div class="modal fade" id="ViewQR" tabindex="-1" aria-labelledby="ViewQRLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ViewQRLabel">  معاينة   </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin-left: 0;"></button>
            </div>
            <div class="modal-body">

                <div style="text-align: center">
                    <img src="{{ url('qr_code/'.$qr_row->qr) }}?{{ rand() }}" style="max-width: 100%;border-radius: 16px;height: 300px;">
                </div>

            </div>
            <div class="modal-footer" style="border-top:0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="height: 37px;padding-top: 0;padding-bottom: 0;border-radius: 7px;">أغلاق</button>
            </div>
            </div>
        </div>
    </div>
    @endif


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

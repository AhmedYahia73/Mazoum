
<!--begin::Form-->
{!! Form::model($Item, [ 'route' => ['admin.events.update' , $Item->id ] , 'method' => 'patch', 'role'=>'form','id'=>'edit', 'files' => true ]) !!}

    <input type="hidden" name="id" value="{{$Item->id}}">

    <div class="row">

        <div class="col-md-6 col-sm-6 {{ $errors->has('assistant_id') ? ' has-error' : '' }}">
            <label> الموظفين </label>
            <select name="assistant_id" id="assistant_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true">
                <option value="" selected="true">  الموظفين  </option>
                @foreach (Assistants() as $key => $value)
                    <option value="{{ $key }}" @if($Item->assistant_id == $key) {{ 'selected' }} @endif> {{ $value }} </option>
                @endforeach
            </select>
            @if ($errors->has('assistant_id'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('assistant_id') }}</strong>
                </span>
            @endif
        </div>

        <div class="col-md-6 col-sm-6 {{ $errors->has('country_code') ? ' has-error' : '' }}">
            <label> الدوله  <span class="text-danger">*</span> </label>
            <select name="country_code" id="country_code" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                <option value="" selected="true">  الدوله  </option>
                <option value="kw" @if($Item->country_code == 'kw') {{ 'selected' }} @endif> الكويت </option>
                <option value="sa" @if($Item->country_code == 'sa') {{ 'selected' }} @endif> السعودية </option>
            </select>
            @if ($errors->has('country_code'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('country_code') }}</strong>
                </span>
            @endif
        </div>



        <div class="col-lg-12 col-sm-12 {{ $errors->has('title') ? ' has-error' : '' }}">
            <label> عنوان الحدث </label>
            <input type="text" name="title" required class="form-control m-input" value="{{ $Item->title }}"  placeholder="عنوان الحدث ">
            @if ($errors->has('title'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('title') }}</strong>
                </span>
            @endif
        </div>

        <div class="col-md-6 col-sm-6 {{ $errors->has('user_id') ? ' has-error' : '' }}">
            <label> المستخدمين  <span class="text-danger">*</span> </label>
            <select name="user_id" id="user_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                <option value="" selected="true">  المستخدمين  </option>
                @foreach (Users() as $key => $value)
                    <option value="{{ $key }}" @if($Item->user_id == $key) {{ 'selected' }} @endif> {{ $value }} </option>
                @endforeach
            </select>
            @if ($errors->has('user_id'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('user_id') }}</strong>
                </span>
            @endif
        </div>

        <div class="col-lg-6 col-sm-6 {{ $errors->has('address') ? ' has-error' : '' }}">
            <label> موقع الحدث </label>
            <input type="text" name="address" required class="form-control m-input" value="{{ $Item->address }}"  placeholder="موقع الحدث ">
            @if ($errors->has('address'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('address') }}</strong>
                </span>
            @endif
        </div>

        <div class="col-sm-6 {{ $errors->has('showing_qr') ? ' has-error' : '' }}">
            <label>  هل تريد أظهار ال QR <span class="text-danger">*</span> </label>
            <select name="showing_qr" class="form-control m-bootstrap-select m_selectpicker" required>
                <option value="" disabled selected="true"> نعم / لا </option>
                <option value="yes" @if($Item->showing_qr == 'yes') {{ 'selected' }} @endif> نعم </option>
                <option value="no"  @if($Item->showing_qr == 'no')  {{ 'selected' }} @endif> لا </option>
            </select>
            @if ($errors->has('showing_qr'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('showing_qr') }} </strong>
                </span>
            @endif
        </div>

        <div class="col-lg-6 col-sm-6 {{ $errors->has('have_reminder') ? ' has-error' : '' }}">
            <label>  هل تريد تفعيل رسائل التذكير <span class="text-danger">*</span> </label>
            <select name="have_reminder" class="form-control m-bootstrap-select m_selectpicker" required>
                <option value="" disabled selected="true"> نعم / لا </option>
                <option value="yes" @if($Item->have_reminder == 'yes') {{ 'selected' }} @endif> نعم </option>
                <option value="no"  @if($Item->have_reminder == 'no')  {{ 'selected' }} @endif> لا </option>
            </select>
            @if ($errors->has('have_reminder'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('have_reminder') }} </strong>
                </span>
            @endif
        </div>

        <div class="col-lg-6 col-sm-6 {{ $errors->has('can_replay_messages') ? ' has-error' : '' }}">
            <label>  هل تريد تفعيل الرد علي الرسائل <span class="text-danger">*</span> </label>
            <select name="can_replay_messages" class="form-control m-bootstrap-select m_selectpicker" required>
                <option value="" disabled selected="true"> نعم / لا </option>
                <option value="yes" @if($Item->can_replay_messages == 'yes') {{ 'selected' }} @endif> نعم </option>
                <option value="no"  @if($Item->can_replay_messages == 'no')  {{ 'selected' }} @endif> لا </option>
            </select>
            @if ($errors->has('can_replay_messages'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('can_replay_messages') }} </strong>
                </span>
            @endif
        </div>

        <div class="col-lg-6 col-sm-6 {{ $errors->has('enable_resend_again') ? ' has-error' : '' }}">
            <label>  هل تريد تفعيل اعاده ارسال <span class="text-danger">*</span> </label>
            <select name="enable_resend_again" class="form-control m-bootstrap-select m_selectpicker" required>
                <option value="" disabled selected="true"> نعم / لا </option>
                <option value="yes" @if($Item->enable_resend_again == 'yes') {{ 'selected' }} @endif> نعم </option>
                <option value="no"  @if($Item->enable_resend_again == 'no')  {{ 'selected' }} @endif> لا </option>
            </select>
            @if ($errors->has('enable_resend_again'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('enable_resend_again') }} </strong>
                </span>
            @endif
        </div>

        <div class="col-lg-6 col-sm-6 {{ $errors->has('sending_type') ? ' has-error' : '' }}">
            <label>  نوع الأرسال <span class="text-danger">*</span> </label>
            <select name="sending_type" class="form-control m-bootstrap-select m_selectpicker" required>
                <option value="" disabled selected="true"> نعم / لا </option>
                <option value="old_send"      @if($Item->sending_type == 'old_send') {{ 'selected' }} @endif>  أرسال ميتا  </option>
                <option value="new_send"      @if($Item->sending_type == 'new_send')  {{ 'selected' }} @endif>أرسال </option>
                <option value="not_available" @if($Item->sending_type == 'not_available')  {{ 'selected' }} @endif> غير متاح </option>
            </select>
            @if ($errors->has('sending_type'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('sending_type') }} </strong>
                </span>
            @endif
        </div>

        <div class="col-lg-6 col-md-6 {{ $errors->has('gender') ? ' has-error' : '' }}">
            <label> الجنس <span class="text-danger">*</span>   </label>
            <select name="gender" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
              <option value="" disabled selected>  أختر رجل / أمرأه </option>
              <option value="male" @if($Item->gender == 'male') selected @endif> رجل </option>
              <option value="female" @if($Item->gender == 'female') selected @endif> أمرأه </option>
            </select>
            @if ($errors->has('gender'))
            <span class="help-block" style="color:red">
              <strong>{{ $errors->first('gender') }} </strong>
            </span>
            @endif
        </div>

        <div class="col-lg-6 col-sm-6 {{ $errors->has('long') ? ' has-error' : '' }}">
            <label> خطوط الطول </label>
            <input type="text" name="long" class="form-control m-input" value="{{ $Item->long }}"  placeholder="خطوط الطول ">
            @if ($errors->has('long'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('long') }}</strong>
                </span>
            @endif
        </div>


        <div class="col-lg-6 col-sm-6 {{ $errors->has('lat') ? ' has-error' : '' }}">
            <label> دوائر العرض </label>
            <input type="text" name="lat" class="form-control m-input" value="{{ $Item->lat }}"  placeholder="دوائر العرض ">
            @if ($errors->has('lat'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('lat') }}</strong>
                </span>
            @endif
        </div>

    

        <div class="col-sm-6 {{ $errors->has('date') ? ' has-error' : '' }}">
            <label> تاريخ الحدث <span class="text-danger">*</span> </label>
            <input type="text" name="date" required class="form-control m-input" value="{{ $Item->date }}"  placeholder="YYYY-MM-DD" id="flatpickr-date">
            @if ($errors->has('date'))
                 <span class="help-block" style="color:red">
                      <strong>{{ $errors->first('date') }} </strong>
                 </span>
            @endif
        </div>

        <div class="col-sm-6 {{ $errors->has('time') ? ' has-error' : '' }}">
            <label> وقت الحدث <span class="text-danger">*</span> </label>
            <input type="text" name="time" id="flatpickr-date3" required class="form-control m-input" value="{{ $Item->time }}"  placeholder="وقت الحدث">
            @if ($errors->has('date'))
                 <span class="help-block" style="color:red">
                      <strong>{{ $errors->first('time') }} </strong>
                 </span>
            @endif
        </div>


        

        <div class="col-lg-12 col-sm-12 {{ $errors->has('color') ? ' has-error' : '' }}">
              <label> اللون  </label>
              <input type="color" name="color" required class="form-control m-input" value="{{ $Item->color }}"  placeholder=" اللون ">
              @if ($errors->has('color'))
              <span class="help-block" style="color:red">
                <strong>{{ $errors->first('color') }}</strong>
              </span>
              @endif
            </div>

        <div class="col-sm-6 {{ $errors->has('file') ? ' has-error' : '' }}">
            <label> تصميم الدعوه <span class="text-danger">*</span> </label>
            <input class="form-control" type="file" id="formFile" name="file" />
            <img id="imgPreview" src="{{ $Item->file }}?{{rand()}}" style="max-width: 200px;max-height: 200px;margin-bottom: 20px;margin-top: 20px"/>
            @if ($errors->has('file'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('file') }}</strong>
                </span>
            @endif
        </div>

        <div class="col-sm-6 {{ $errors->has('image') ? ' has-error' : '' }}">
            <label> تصميم QR   </label>
            <input class="form-control" type="file" id="formFile" name="image" />
            @if($Item->image != null)
            <img id="imgPreview" src="{{ $Item->image }}?{{rand()}}" style="max-width: 200px;max-height: 200px;margin-bottom: 20px;margin-top: 20px"/>
            @endif
            @if ($errors->has('image'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('image') }}</strong>
                </span>
            @endif
        </div>

        <div class="col-sm-12 {{ $errors->has('video') ? ' has-error' : '' }}">
            <label> video </label>
            <input class="form-control" type="file" id="formFile" name="video" accept="video/*" />
            @if($Item->video != null)
            <video width="100%" height="240" controls>
                <source src="{{ $Item->video }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            @endif
            @if ($errors->has('video'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('video') }}</strong>
                </span>
            @endif
        </div>



    </div>


    <button type="submit" form="edit" class="btn btn-primary mr-2">
        {{ trans('home.update') }}
    </button>

{!! Form::close() !!}
<!--end::Form-->

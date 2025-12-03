@php
    $lang = app()->getLocale();
@endphp


<div class="contact-form wow right-animation" data-wow-delay="0.4s">
    <form class="" action="{{ route($lang.'-save-contact-us') }}" method="post">

        {{csrf_field()}}

        <div class="row">
            <div class="col-md-6">
                <div class="form-box">
                    <input name="first_name" value="{{ old('first_name') }}" type="text" required class="form-input" placeholder="{{ $lang == 'en' ? 'First Name' : 'الأسم الأول' }}" required>
                    @if ($errors->has('first_name'))
                        <span class="help-block" style="color:red">
                            <strong>{{ $errors->first('first_name') }} </strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-box">
                    <input name="last_name" value="{{ old('last_name') }}" type="text" class="form-input" placeholder="{{ $lang == 'en' ? 'Last Name' : 'الأسم الأخير' }}" required>
                    @if ($errors->has('last_name'))
                        <span class="help-block" style="color:red">
                            <strong>{{ $errors->first('last_name') }} </strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-box">
                    <input type="text" name="email" value="{{ old('email') }}" class="form-input" placeholder="{{ $lang == 'en' ? 'Email Address' : 'البريد الألكتروني' }}" required>
                    @if ($errors->has('email'))
                        <span class="help-block" style="color:red">
                            <strong>{{ $errors->first('email') }} </strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-box">
                    <input type="text" name="mobile" value="{{ old('mobile') }}" class="form-input" placeholder="{{ $lang == 'en' ? 'Phone No.' : 'رقم الموبيل' }}" required>
                    @if ($errors->has('mobile'))
                        <span class="help-block" style="color:red">
                            <strong>{{ $errors->first('mobile') }} </strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-12">
                <div class="form-box">
                    <textarea name="message" class="form-input" required placeholder="{{ $lang == 'en' ? 'Message...' : 'الرسالة ...' }}">{{ old('message') }}</textarea>
                    @if ($errors->has('message'))
                        <span class="help-block" style="color:red">
                            <strong>{{ $errors->first('message') }} </strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-12">
                <div class="form-box">
                    <button name="submit" type="submit" value="Submit" class="sec-btn"><span> {{ $lang == 'en' ? 'Submit Now' : 'أرسل الأن' }} </span></button>
                </div>
            </div>
        </div>
    </form>
</div>

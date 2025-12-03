

    <div class="row">

        <div class="col-lg-6 col-sm-6 {{ $errors->has('title') ? ' has-error' : '' }}">
            <label> عنوان الحدث </label>
            <input type="text" name="title" required class="form-control m-input" value="{{ $Item->title }}"  placeholder="عنوان الحدث ">
            @if ($errors->has('title'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('title') }}</strong>
                </span>
            @endif
        </div>

        <div class="col-md-6 col-sm-6 {{ $errors->has('user_id') ? ' has-error' : '' }}">
            <label> المستخدمين   </label>
            <select name="user_id" id="user_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true">
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
      
      	<div class="col-sm-12 {{ $errors->has('have_reminder') ? ' has-error' : '' }}">
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

        <div class="col-lg-6 col-sm-6 {{ $errors->has('lat') ? ' has-error' : '' }}">
            <label> دوائر العرض </label>
            <input type="text" name="lat" class="form-control m-input" value="{{ $Item->lat }}"  placeholder="دوائر العرض ">
            @if ($errors->has('lat'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('lat') }}</strong>
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


        <div class="col-sm-6 {{ $errors->has('file') ? ' has-error' : '' }}">
            <label> صوره  <span class="text-danger">*</span> </label>
            <input class="form-control" type="file" id="formFile" name="file" />
            <img id="imgPreview" src="{{ $Item->file }}?{{rand()}}" style="max-width: 200px;max-height: 200px;margin-bottom: 20px;margin-top: 20px"/>
            @if ($errors->has('file'))
                <span class="help-block" style="color:red">
                    <strong>{{ $errors->first('file') }}</strong>
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


    </div>


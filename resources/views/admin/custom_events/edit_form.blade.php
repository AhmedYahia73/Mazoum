
{!! Form::model($Item, [ 'route' => ['admin.custom_events.update' , $Item->id ] , 'method' => 'patch', 'files'=>true, 'role'=>'form','id'=>'edit', 'class'=> 'm-form m-form--fit m-form--label-align-left' ]) !!}

    {{ csrf_field() }}

    <input type="hidden" name="id" value="{{ $Item->id }}">

    <div class="card-body">
        <div class="row" style="margin-top: 30px">


            <div class="col-md-6 col-sm-6 {{ $errors->has('assistant_id') ? ' has-error' : '' }}">
                <label> الموظفين  <span class="text-danger">*</span> </label>
                <select name="assistant_id" id="assistant_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
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


            <div class="col-lg-6 col-sm-6 {{ $errors->has('title') ? ' has-error' : '' }}">
                <label> عنوان الدعوه </label>
                <input type="text" name="title" required class="form-control m-input" value="{{ $Item->title }}"  placeholder="عنوان الدعوه ">
                @if ($errors->has('title'))
                    <span class="help-block" style="color:red">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                @endif
            </div>




          	<div class="col-lg-6 col-sm-6 {{ $errors->has('color') ? ' has-error' : '' }}">
              <label> اللون  </label>
              <input type="color" name="color" required class="form-control m-input" value="{{ $Item->color }}"  placeholder=" اللون ">
              @if ($errors->has('color'))
              <span class="help-block" style="color:red">
                <strong>{{ $errors->first('color') }}</strong>
              </span>
              @endif
            </div>

            <div class="col-lg-12 col-sm-12 {{ $errors->has('address') ? ' has-error' : '' }}">
                <label> موقع الحدث </label>
                <input type="text" name="address" required class="form-control m-input" value="{{ $Item->address }}"  placeholder="موقع الحدث ">
                @if ($errors->has('address'))
                    <span class="help-block" style="color:red">
                        <strong>{{ $errors->first('address') }}</strong>
                    </span>
                @endif
            </div>

            <div class="col-sm-6 {{ $errors->has('date') ? ' has-error' : '' }}">
                <label> تاريخ الحدث <span class="text-danger">*</span> </label>
                <input type="text" name="date" required class="form-control m-input" value="{{ $Item->date }}"  placeholder="YYYY-MM-DD" id="flatpickr-date1">
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

            <div class="col-md-6 col-sm-6 {{ $errors->has('language') ? ' has-error' : '' }}">
                <label> اللغه  <span class="text-danger">*</span> </label>
                <select name="language" id="language" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                    <option value="" selected="true"> اللغه </option>
                    <option value="ar" @if($Item->language == 'ar') {{ 'selected' }} @endif> عربي </option>
                    <option value="en" @if($Item->language == 'en') {{ 'selected' }} @endif> انجليزي </option>
                </select>
                @if ($errors->has('language'))
                    <span class="help-block" style="color:red">
                        <strong>{{ $errors->first('language') }}</strong>
                    </span>
                @endif
            </div>

            <div class="col-sm-6 {{ $errors->has('image') ? ' has-error' : '' }}">
                <label> صوره    </label>
                <input class="form-control" type="file" id="formFile"  name="image" />
                @if($Item->image != null)
                <br>
                <img src="{{ $Item->image }}?{{ rand() }}" style="width:200px">
                @endif
                @if ($errors->has('image'))
                    <span class="help-block" style="color:red">
                        <strong>{{ $errors->first('image') }}</strong>
                    </span>
                @endif
            </div>

            <div class="col-lg-12" style="margin-bottom:20px">
                <button type="submit" form="edit" class="btn btn-success" style="margin-top:20px;margin-bottom: 0px;">{{ trans('home.update') }}</button>
            </div>

        </div>
    </div>


{!! Form::close() !!}


{!! Form::model($Item, [ 'route' => ['admin.users.update' , $Item->id ] , 'method' => 'patch', 'files'=>true, 'role'=>'form','id'=>'edit', 'class'=> 'm-form m-form--fit m-form--label-align-left' ]) !!}

    {{ csrf_field() }}

    <input type="hidden" name="id" value="{{ $Item->id }}">

    <div class="card-body">
        <div class="row" style="margin-top: 30px">


            <div class="col-lg-12 col-sm-12 {{ $errors->has('name') ? ' has-error' : '' }}">
                <label>  {{ trans('home.name') }}  <span class="text-danger">*</span>  </label>
                <input type="text" name="name" class="form-control m-input" required="required" value="{{ $Item->name }}" placeholder="  {{ trans('home.name') }}   ">
                @if ($errors->has('name'))
                    <span class="help-block" style="color:red">
                        <strong>{{ $errors->first('name') }} </strong>
                    </span>
                @endif
            </div>



            @php
                $mobile_codes = App\Models\MobileCodes::get(['id','ar_country_name','code']);
            @endphp

            <div class="col-lg-6 col-sm-6 {{ $errors->has('mobile_code') ? ' has-error' : '' }}">
                <label>  كود الموبيل <span class="text-danger">*</span>   </label>
                <select name="mobile_code" class="form-control m-bootstrap-select m_selectpicker"  data-live-search="true" required>
                    <option value="" disabled selected>  أختر كود </option>
                    @if($mobile_codes != null && $mobile_codes->count() > 0)
                        @foreach($mobile_codes as $value)
                        <option value="{{ $value->code }}" @if($Item->mobile_code == $value->code) selected @endif> {{ $value->ar_country_name }} ({{ $value->code }}) </option>
                        @endforeach
                    @endif
                </select>
                @if ($errors->has('mobile_code'))
                    <span class="help-block" style="color:red">
                        <strong>{{ $errors->first('mobile_code') }} </strong>
                    </span>
                @endif
            </div>


            <div class="col-lg-6 col-sm-6 {{ $errors->has('mobile') ? ' has-error' : '' }}">
                <label>  {{ trans('home.mobile') }} <span class="text-danger">*</span>   </label>
                <input type="text" name="mobile" class="form-control m-input" required="required" value="{{ $Item->mobile }}" placeholder=" {{ trans('home.mobile') }} ">
                @if ($errors->has('mobile'))
                    <span class="help-block" style="color:red">
                        <strong>{{ $errors->first('mobile') }} </strong>
                    </span>
                @endif
            </div>
          
            <div class="col-lg-12 {{ $errors->has('password') ? ' has-error' : '' }}">
              <label>  {{ trans('home.password') }}   </label>
              <input type="text" name="password" class="form-control m-input" value="{{ $Item->pass }}" placeholder="  {{ trans('home.password') }}  ">
              @if ($errors->has('password'))
              <span class="help-block" style="color:red">
                <strong>{{ $errors->first('password') }} </strong>
              </span>
              @endif
            </div>


            <div class="col-lg-12" style="margin-bottom:20px">
                <button type="submit" form="edit" class="btn btn-success" style="margin-top:20px;margin-bottom: 0px;">{{ trans('home.update') }}</button>
            </div>

        </div>
    </div>


{!! Form::close() !!}

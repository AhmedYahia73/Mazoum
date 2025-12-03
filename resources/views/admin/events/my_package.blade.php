        <!--begin::Form-->
        {{--  {!! Form::model($Item, [ 'route' => ['admin.events.update_event_package' , $Item->id ] , 'method' => 'patch', 'role'=>'form','id'=>'edit_package', 'files' => true ]) !!}  --}}

            <div class="card-body">

                <div class="row">


                    <div class="col-lg-6 col-md-6 {{ $errors->has('name') ? ' has-error' : '' }}" style="margin-bottom: 20px">
                        <label> اسم المتسخدم </label>
                        <input type="text" disabled class="form-control m-input" value="{{ @$Item->user->name }}"  placeholder="عدد الدعوات ">
                        @if ($errors->has('name'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('pass') ? ' has-error' : '' }}" style="margin-bottom: 20px">
                        <label> كلمه المرور </label>
                        <input type="text" disabled class="form-control m-input" value="{{ @$Item->user->pass }}"  placeholder="عدد الدعوات ">
                        @if ($errors->has('pass'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('pass') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('invitation_count') ? ' has-error' : '' }}">
                        <label> عدد الدعوات </label>
                        <input type="text" name="invitation_count" disabled onkeypress="return isNumberKey(event)" class="form-control m-input" value="{{ @$Item->user->order->users_count }}"  placeholder="عدد الدعوات ">
                        @if ($errors->has('invitation_count'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('invitation_count') }}</strong>
                            </span>
                        @endif
                     </div>


                    <div class="col-lg-6 col-sm-6 {{ $errors->has('reservation_date') ? ' has-error' : '' }}">
                        <label> تاريخ  الدفع </label>
                        <input type="text" name="reservation_date" disabled class="form-control m-input" value="{{ @$Item->user->order->start_subscription_date }}"  placeholder="تاريخ  الحجز ">
                        @if ($errors->has('reservation_date'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('reservation_date') }}</strong>
                            </span>
                        @endif
                    </div>


                    <div class="col-lg-4 col-md-4 {{ $errors->has('package_price') ? ' has-error' : '' }}">
                        <label> سعر الباقة </label>
                        <input type="text" name="package_price" disabled class="form-control m-input" value="{{ @$Item->user->order->total }} {{ @$Item->user->order->currency->ar_name }}"  placeholder="سعر الباقة">
                        @if ($errors->has('package_price'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('package_price') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-4 col-sm-4 {{ $errors->has('payment_type') ? ' has-error' : '' }}">
                        <label> طريقة السداد <span class="text-danger">*</span>   </label>
                        <select name="payment_type" class="form-control" disabled>
                            <option value="" disabled selected> طريقة السداد  </option>
                            <option value="cash" @if(@$Item->user->order->payment_type == 'cash') selected @endif> كاش </option>
                            <option value="key_net" @if(@$Item->user->order->payment_type == 'key_net') selected @endif> كي نت </option>
                        </select>
                        @if ($errors->has('payment_type'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('payment_type') }} </strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-4 col-sm-4 {{ $errors->has('is_paid') ? ' has-error' : '' }}">
                        <label> طريقة الدفع <span class="text-danger">*</span>   </label>
                        <select name="is_paid" class="form-control" disabled>
                            <option value="" disabled selected> طريقة الدفع  </option>
                            <option value="paid" @if(@$Item->user->order->is_paid == 'paid') selected @endif> تم الدفع </option>
                            <option value="not_paid" @if(@$Item->user->order->is_paid == 'not_paid') selected @endif> لم يتم الدفع </option>
                        </select>
                        @if ($errors->has('is_paid'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('is_paid') }} </strong>
                            </span>
                        @endif
                    </div>


                    <div class="col-lg-6 col-sm-6 {{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label> رقم الهاتف </label>
                        <input type="text" name="phone" disabled class="form-control m-input" value="{{ $Item->user->mobile_code }}{{ $Item->user->mobile }}"  placeholder="رقم الهاتف ">
                        @if ($errors->has('phone'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                        @endif
                    </div>


                    <div class="col-lg-6 col-sm-6 {{ $errors->has('employee_gender') ? ' has-error' : '' }}">
                        <label> جنس الموظف   <span class="text-danger">*</span>   </label>
                        <input type="text" name="phone" disabled class="form-control m-input" value="{{ $Item->user != null && $Item->user->employee_gender != null ? ($Item->user->employee_gender == 'male' ? 'رجل' : 'مرأة') : '' }}"  placeholder="جنس الموظف">
                        @if ($errors->has('employee_gender'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('employee_gender') }} </strong>
                            </span>
                        @endif
                    </div>



                </div>

            </div>

            {{--  <div class="card-footer" style="padding-left: 25px !important;margin-top: 0px !important;">
                <button type="submit" form="edit_package" class="btn btn-primary mr-2">
                    {{ trans('home.update') }}
                </button>
            </div>  --}}

        {{--  {!! Form::close() !!}  --}}
        <!--end::Form-->

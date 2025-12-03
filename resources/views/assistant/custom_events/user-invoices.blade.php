


    <button type="button" class="btn btn-warning" style="margin-top: 20px;margin-bottom: 10px" data-bs-toggle="modal" data-bs-target="#exampleModal">
        أشترك في باقة جديدة
    </button>


    <div class="table-responsive">
        <table class="table zero-configuration">

            <thead>
                <tr>
                    <th> رقم الطلب </th>
                    <th> الباقة </th>
                    <th> السعر </th>
                    <th> عدد المستخدمين </th>
                    <th> تاريخ انشاء الأشتراك </th>
                    <th> تاريخ بدايه الأشتراك </th>
                    <th> مده الأشتراك </th>
                    <th> طريقة السداد </th>
                    <th> طريقة الدفع </th>
                    <th> جنس الموظف  </th>
                    <th> الأدوات </th>
                </tr>
            </thead>

            <tbody>

                @php $x = 1; @endphp

                @if($Item->orders != null && $Item->orders()->count() > 0)

                    @foreach($Item->orders()->get() as $value)

                        <tr id="row{{ $value->id }}">
                            <td>  {{ $value->order_number }} </td>
                            <td>
                                @if($value->type == 'offer')
                                    {{ $value->offer != null ? $value->offer->ar_name : '' }}
                                @else
                                    أشتراك يدوي
                                @endif
                            </td>
                            <td> {{ $value->total }} {{ $value->currency != null ? $value->currency->ar_name : '' }} </td>
                            <td> {{ $value->users_count }} </td>
                            <td> {{ $value->operation_date }} </td>
                            <td> {{ $value->start_subscription_date }} </td>
                            <td>
                                @if($value->duration_type == 'day')
                                    {{ $value->duration }} يوم
                                @elseif($value->duration_type == 'month')
                                    {{ $value->duration }} شهر
                                @elseif($value->duration_type == 'year')
                                    {{ $value->duration }} سنه
                                @endif
                            </td>
                            <td>
                                @if($value->payment_type == 'cash')
                                    كاش
                                @elseif($value->payment_type == 'key_net')
                                    كي نت
                                @endif
                            </td>
                            <td>
                                @if($value->is_paid == 'paid')
                                    تم الدفع
                                @elseif($value->is_paid == 'not_paid')
                                    لم يتم الدفع
                                @endif
                            </td>
                            <td>
                                @if($value->employee_gender == 'male')
                                    رجل
                                @elseif($value->employee_gender == 'female')
                                    مرأه
                                @endif
                            </td>
                            <td>
                                <a onclick="return DeletingModal({{ $value->id }});" class="dropdown-item DeletingModal" name="{{ $value->id }}" href="javascript:void(0);">
                                  <i class="bx bx-trash me-1"></i> {{ trans('home.Delete') }}
                                </a>
                                <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#editOrderModal{{ $value->id }}">
                                    <i class="bx bx-edit-alt me-1"></i> {{ trans('home.Edit') }}
                                </a>
                            </td>
                        </tr>

                        @php $x = $x + 1; @endphp

                    @endforeach

                @else
                    <tr>
                        <td colspan="5">
                            عفوا لا يوجد طلب
                        </td>
                    </tr>
                @endif


            </tbody>

        </table>
    </div>




    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">أشترك في باقة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    {!! Form::open(['url' => "assistant_panel/save-order", 'role'=>'form','id'=>'save-order','method'=>'post']) !!}

                        <input type="hidden" name="user_id" value="{{ $Item->id }}">

                        <div class="mb-3">
                            <label> نوع الأشترك </label>
                            <select name="type" id="order_type" class="form-control" required>
                                <option value="" disabled selected> نوع الأشتراك </option>
                                <option value="offer" @if(old('type') == 'offer') selected @endif>
                                    باقات حالية
                                </option>
                                <option value="fixed-price" @if(old('type') == 'fixed-price') selected @endif>
                                    سعر ثابت
                                </option>
                            </select>
                            @if ($errors->has('type'))
                                <span class="help-block" style="color:red">
                                    <strong>{{ $errors->first('type') }} </strong>
                                </span>
                            @endif
                        </div>

                        @php
                            $offers = App\Models\Packages::where('status',1)->get();
                        @endphp

                        <div class="mb-3 offers un_active">
                           <label> الباقات </label>
                            <select name="offer_id" class="form-control">
                                <option value="" disabled selected>  أختر باقة </option>
                                @foreach($offers as $offer)
                                <option value="{{ $offer->id }}" @if(old('offer_id') == $offer->id) selected @endif>
                                    {{ $offer->ar_name }} - {{ $offer->price }} {{ $offer->currency != null ? $offer->currency->ar_name : '' }} - {{ $offer->users_count }} مستخدم
                                </option>
                                @endforeach
                            </select>
                            @if ($errors->has('offer_id'))
                                <span class="help-block" style="color:red">
                                    <strong>{{ $errors->first('offer_id') }} </strong>
                                </span>
                            @endif
                        </div>

                        <div class="mb-3 users_count un_active">
                           <label> عدد المستخدمين </label>
                           <input type="number" name="users_count" class="form-control" value="{{ old('users_count') }}" min="1" placeholder="عدد المستخدمين">
                           @if ($errors->has('users_count'))
                                <span class="help-block" style="color:red">
                                    <strong>{{ $errors->first('users_count') }} </strong>
                                </span>
                            @endif
                        </div>

                        <div class="mb-3 total_price un_active">
                           <label> السعر </label>
                           <input type="number" name="total" class="form-control" value="{{ old('total') }}" placeholder="السعر">
                           @if ($errors->has('total'))
                                <span class="help-block" style="color:red">
                                    <strong>{{ $errors->first('total') }} </strong>
                                </span>
                            @endif
                        </div>

                        <div class="mb-3 currencies un_active">
                           <label> العملات </label>
                            <select name="currency_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true">
                                <option value="" disabled selected>  أختر عملة </option>
                                @foreach(Currencies() as $key => $value)
                                    <option value="{{ $key }}" @if(old('currency_id') == $key) selected @endif>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('currency_id'))
                                <span class="help-block" style="color:red">
                                    <strong>{{ $errors->first('currency_id') }} </strong>
                                </span>
                            @endif
                        </div>

                        <div class="mb-3 {{ $errors->has('start_subscription_date') ? ' has-error' : '' }}">
                            <label> تاريخ الأشتراك <span class="text-danger">*</span> </label>
                            <input type="text" name="start_subscription_date" required class="form-control m-input" value="{{ old('start_subscription_date') }}"  placeholder="YYYY-MM-DD" id="flatpickr-date">
                            @if ($errors->has('start_subscription_date'))
                                 <span class="help-block" style="color:red">
                                      <strong>{{ $errors->first('start_subscription_date') }} </strong>
                                 </span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label> نوع المده </label>
                             <select name="duration_type" required class="form-control m-bootstrap-select m_selectpicker" data-live-search="true">
                                <option value="" disabled selected>  أختر نوع </option>
                                <option value="day" @if(old('duration_type') == 'day') selected @endif> يوم </option>
                                <option value="month" @if(old('duration_type') == 'month') selected @endif> شهر </option>
                                <option value="year" @if(old('duration_type') == 'year') selected @endif> سنه </option>
                             </select>
                             @if ($errors->has('duration_type'))
                                 <span class="help-block" style="color:red">
                                     <strong>{{ $errors->first('duration_type') }} </strong>
                                 </span>
                             @endif
                         </div>

                        <div class="mb-3">
                            <label> المده </label>
                            <input type="number" name="duration" class="form-control" value="{{ old('duration') }}" required placeholder="المده">
                            @if ($errors->has('duration'))
                                <span class="help-block" style="color:red">
                                    <strong>{{ $errors->first('duration') }} </strong>
                                </span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label> طريقة السداد <span class="text-danger">*</span>   </label>
                            <select name="payment_type" class="form-control m-bootstrap-select m_selectpicker" required>
                                <option value="" disabled selected>  أختر النوع </option>
                                <option value="cash" @if(old('payment_type') == 'cash') selected @endif> كاش </option>
                                <option value="key_net" @if(old('payment_type') == 'key_net') selected @endif> كي نت </option>
                            </select>
                            @if ($errors->has('payment_type'))
                                <span class="help-block" style="color:red">
                                    <strong>{{ $errors->first('payment_type') }} </strong>
                                </span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label> جنس الموظف   <span class="text-danger">*</span>   </label>
                            <select name="employee_gender" class="form-control m-bootstrap-select m_selectpicker" required>
                                <option value="" disabled selected>  أختر النوع </option>
                                <option value="male" @if(old('employee_gender') == 'male') selected @endif> رجل </option>
                                <option value="female" @if(old('employee_gender') == 'female') selected @endif> مرأه  </option>
                            </select>
                            @if ($errors->has('employee_gender'))
                                <span class="help-block" style="color:red">
                                    <strong>{{ $errors->first('employee_gender') }} </strong>
                                </span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label> طريقة الدفع <span class="text-danger">*</span>   </label>
                            <select name="is_paid" class="form-control m-bootstrap-select m_selectpicker" required>
                                <option value="" disabled selected>  أختر النوع </option>
                                <option value="paid" @if(old('is_paid') == 'paid') selected @endif> تم الدفع </option>
                                <option value="not_paid" @if(old('is_paid') == 'not_paid') selected @endif> لم يتم الدفع </option>
                            </select>
                            @if ($errors->has('is_paid'))
                                <span class="help-block" style="color:red">
                                    <strong>{{ $errors->first('is_paid') }} </strong>
                                </span>
                            @endif
                        </div>

                    {!! Form::close() !!}

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        أغلاق
                    </button>
                    <button type="submit" form="save-order" class="btn btn-primary">
                        أشترك
                    </button>
                </div>

            </div>
        </div>
    </div>


    @if($Item->orders != null && $Item->orders()->count() > 0)

        @foreach($Item->orders()->get() as $order)
        <div class="modal fade" id="editOrderModal{{ $order->id }}" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editOrderModalLabel"> تعديل بيانات الباقة </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        {!! Form::open(['url' => "assistant_panel/edit-order", 'role'=>'form','id'=>'edit-order'.$order->id,'method'=>'post']) !!}

                            <input type="hidden" name="order_id" value="{{ $order->id }}">

                            <div class="mb-3 {{ $errors->has('start_subscription_date') ? ' has-error' : '' }}">
                                <label> تاريخ الأشتراك <span class="text-danger">*</span> </label>
                                <input type="text" name="start_subscription_date" required class="form-control m-input flatpickr-date" value="{{ $order->start_subscription_date }}"  placeholder="YYYY-MM-DD">
                                @if ($errors->has('start_subscription_date'))
                                    <span class="help-block" style="color:red">
                                        <strong>{{ $errors->first('start_subscription_date') }} </strong>
                                    </span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label> نوع المده </label>
                                <select name="duration_type" required class="form-control m-bootstrap-select m_selectpicker" data-live-search="true">
                                    <option value="" disabled selected>  أختر نوع </option>
                                    <option value="day" @if($order->duration_type == 'day') selected @endif> يوم </option>
                                    <option value="month" @if($order->duration_type == 'month') selected @endif> شهر </option>
                                    <option value="year" @if($order->duration_type == 'year') selected @endif> سنه </option>
                                </select>
                                @if ($errors->has('duration_type'))
                                    <span class="help-block" style="color:red">
                                        <strong>{{ $errors->first('duration_type') }} </strong>
                                    </span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label> المده </label>
                                <input type="number" name="duration" class="form-control" value="{{ $order->duration }}" required placeholder="المده">
                                @if ($errors->has('duration'))
                                    <span class="help-block" style="color:red">
                                        <strong>{{ $errors->first('duration') }} </strong>
                                    </span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label> طريقة السداد <span class="text-danger">*</span>   </label>
                                <select name="payment_type" class="form-control m-bootstrap-select m_selectpicker" required>
                                    <option value="" disabled selected>  أختر النوع </option>
                                    <option value="cash" @if($order->payment_type == 'cash') selected @endif> كاش </option>
                                    <option value="key_net" @if($order->payment_type == 'key_net') selected @endif> كي نت </option>
                                </select>
                                @if ($errors->has('payment_type'))
                                    <span class="help-block" style="color:red">
                                        <strong>{{ $errors->first('payment_type') }} </strong>
                                    </span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label> جنس الموظف   <span class="text-danger">*</span>   </label>
                                <select name="employee_gender" class="form-control m-bootstrap-select m_selectpicker" required>
                                    <option value="" disabled selected>  أختر النوع </option>
                                    <option value="male" @if($order->employee_gender == 'male') selected @endif> رجل </option>
                                    <option value="female" @if($order->employee_gender == 'female') selected @endif> مرأه  </option>
                                </select>
                                @if ($errors->has('employee_gender'))
                                    <span class="help-block" style="color:red">
                                        <strong>{{ $errors->first('employee_gender') }} </strong>
                                    </span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label> طريقة الدفع <span class="text-danger">*</span>   </label>
                                <select name="is_paid" class="form-control m-bootstrap-select m_selectpicker" required>
                                    <option value="" disabled selected>  أختر النوع </option>
                                    <option value="paid" @if($order->is_paid == 'paid') selected @endif> تم الدفع </option>
                                    <option value="not_paid" @if($order->is_paid == 'not_paid') selected @endif> لم يتم الدفع </option>
                                </select>
                                @if ($errors->has('is_paid'))
                                    <span class="help-block" style="color:red">
                                        <strong>{{ $errors->first('is_paid') }} </strong>
                                    </span>
                                @endif
                            </div>

                        {!! Form::close() !!}

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            أغلاق
                        </button>
                        <button type="submit" form="edit-order{{ $order->id }}" class="btn btn-primary">
                            تعديل
                        </button>
                    </div>

                </div>
            </div>
        </div>
        @endforeach

    @endif

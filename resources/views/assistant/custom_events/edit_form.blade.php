
    <div class="card-body">
        <div class="row" style="margin-top: 30px">


            <div class="col-md-12 col-sm-12 {{ $errors->has('user_id') ? ' has-error' : '' }}">
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


          	<div class="col-lg-6 col-sm-6 {{ $errors->has('color') ? ' has-error' : '' }}">
              <label> اللون  </label>
              <input type="color" name="color" required class="form-control m-input" value="{{ $Item->color }}"  placeholder=" اللون ">
              @if ($errors->has('color'))
              <span class="help-block" style="color:red">
                <strong>{{ $errors->first('color') }}</strong>
              </span>
              @endif
            </div>



        </div>
    </div>


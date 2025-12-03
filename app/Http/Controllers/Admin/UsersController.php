<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\Users as modelRequest;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Invoice_Details;
use App\Models\Orders;
use App\Models\Packages;
use App\Models\Currency;
use App\Models\User as Model;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class UsersController extends Controller
{
    private $view = 'admin.users.';
    private $redirect = 'admin/users';
  
  	// delete_selected_users
    public function delete_selected_users(Request $request) {

        $validation = Validator::make($request->all(), [
            'items' => 'required|array',
        ]);
        if ($validation->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validation->errors(),
            ],400);
        }
        if($request->items != null && ! empty($request->items)) {

            $data = Model::whereIn('id',$request->items)->get();

            foreach($data as $Item) {

                $Item->delete();

            }

            return response()->json([
                'success' => 'تم حذف العناصر المختاره', 
            ]); 
        } else {
            return response()->json([
                'errors' => 'من فضلك اختر عنصر واحد علي الاقل', 
            ]);
        }

    }


    // save_order
    public function save_order(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'type' => 'required',
            'start_subscription_date' => 'required|date|date_format:Y-m-d',
            'duration_type' => 'required|in:day,month,year',
            'duration' => 'required|numeric|min:1',
            'payment_type' => 'required',
            'employee_gender' => 'required',
            'is_paid' => 'required',
        ]);
        if ($validation->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validation->errors(),
            ],400);
        }
        $validate_arr = [
            'user_id' => 'required|exists:users,id',
            'type' => 'required',
            'start_subscription_date' => 'required|date|date_format:Y-m-d',
            'duration_type' => 'required|in:day,month,year',
            'duration' => 'required|numeric|min:1',
            'payment_type' => 'required',
            'employee_gender' => 'required',
            'is_paid' => 'required',
        ];

        if($request->type == 'offer') {
            $validate_arr['offer_id'] = 'required|exists:packages,id';
        }

        if($request->type == 'fixed-price') {
            $validate_arr['users_count'] = 'required|numeric|min:1';
            $validate_arr['total'] = 'required|numeric|min:1';
            $validate_arr['currency_id'] = 'required';
        }

        $request->validate($validate_arr);

        $user = User::findOrFail($request->user_id);

        $order_number = Orders::max('order_number') + 1;

        if ($request->type == 'offer') {

            $offer = Packages::findOrFail($request->offer_id);

            $currency_id = $offer->currency_id;

            $order = Orders::create([
                'order_number' => $order_number,
                'user_id' => $request->user_id,
                'type' => 'offer',
                'offer_id' => $request->offer_id,
                'total' => $offer->price,
                'users_count' => $offer->users_count,
                'operation_date' => Carbon::now(),
                'currency_id' => $currency_id,
                'start_subscription_date' => $request->start_subscription_date,
                'duration_type' => $request->duration_type,
                'duration' => $request->duration,
                'payment_type' => $request->payment_type,
                'employee_gender' => $request->employee_gender,
                'is_paid' => $request->is_paid,

            ]);

            $user->update([
                'order_id' => $order->id,
                'offer_id' => $offer->id,
                'full_balance' => $user->full_balance + $offer->users_count,
                'balance' => $user->balance + $offer->users_count,
                'start_subscription_date' => $request->start_subscription_date,
                'subscription_price' => $offer->price,
                'duration_type' => $request->duration_type,
                'duration' => $request->duration,
                'payment_type' => $request->payment_type,
                'employee_gender' => $request->employee_gender,
                'is_paid' => $request->is_paid,
            ]);

        }

        /////////////////////////////////////

        if ($request->type == 'fixed-price') {

            $currency_id = $request->currency_id;

            $order = Orders::create([
                'order_number' => $order_number,
                'user_id' => $request->user_id,
                'type' => 'fixed-price',
                'offer_id' => 0,
                'total' => $request->total,
                'users_count' => $request->users_count,
                'operation_date' => Carbon::now(),
                'currency_id' => $currency_id,
                'start_subscription_date' => $request->start_subscription_date,
                'duration_type' => $request->duration_type,
                'duration' => $request->duration,
                'payment_type' => $request->payment_type,
                'employee_gender' => $request->employee_gender,
                'is_paid' => $request->is_paid,
            ]);

            $user->update([
                'order_id' => $order->id,
                'balance' => $user->balance + $request->users_count,
                'full_balance' => $user->full_balance + $request->users_count,
                'start_subscription_date' => $request->start_subscription_date,
                'subscription_price' => $request->total,
                'duration_type' => $request->duration_type,
                'duration' => $request->duration,
                'payment_type' => $request->payment_type,
                'employee_gender' => $request->employee_gender,
                'is_paid' => $request->is_paid,

            ]);
        }

        return response()->json([
            'success' => 'تم الأشتراك بنجاح', 
        ]);

    }



    // edit_order
    public function edit_order(Request $request)
    {
        $validation = Validator::make($request->all(), [
  
            'order_id' => 'required|exists:orders,id',
            'start_subscription_date' => 'required|date|date_format:Y-m-d',
            'duration_type' => 'required|in:day,month,year',
            'duration' => 'required|numeric|min:1',
            'payment_type' => 'required',
            'employee_gender' => 'required',
            'is_paid' => 'required',
        ]);
        if ($validation->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validation->errors(),
            ],400);
        }
        $validate_arr = [
            'order_id' => 'required|exists:orders,id',
            'start_subscription_date' => 'required|date|date_format:Y-m-d',
            'duration_type' => 'required|in:day,month,year',
            'duration' => 'required|numeric|min:1',
            'payment_type' => 'required',
            'employee_gender' => 'required',
            'is_paid' => 'required',
        ];

        $request->validate($validate_arr);

        $order = Orders::findOrFail($request->order_id);

        $user = User::findOrFail($order->user_id);

        $order->update([
            'start_subscription_date' => $request->start_subscription_date,
            'duration_type' => $request->duration_type,
            'duration' => $request->duration,
            'payment_type' => $request->payment_type,
            'employee_gender' => $request->employee_gender,
            'is_paid' => $request->is_paid,

        ]);

        $user->update([
            'start_subscription_date' => $request->start_subscription_date,
            'duration_type' => $request->duration_type,
            'duration' => $request->duration,
            'payment_type' => $request->payment_type,
            'employee_gender' => $request->employee_gender,
            'is_paid' => $request->is_paid,
        ]);

        return response()->json([
            'success' => 'تم التحديث بنجاح', 
        ]); 

    }


    // delete_invoice
  	public function delete_invoice($id) {

        $order = Orders::findOrFail($id);
        $user = User::findOrFail($order->user_id);

      	$user->update([
          'offer_id' => 0,
          'full_balance' => $user->full_balance - $order->users_count,
          'balance' => $user->balance - $order->users_count
        ]);

       	$order->delete();

        return response()->json([
            'success' => 'تم حذف الأشتراك بنجاح', 
        ]);

    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Model::with(['offer:id,ar_name','code:id,code'])
            ->where('user_type','user')
            ->select(['name','mobile','mobile_code','offer_id','balance','id','created_at']);

        // search
        if ($request->search) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                ->orWhere('mobile', 'like', "%$s%");
            });
        }

        $Item = $query->paginate(20);

        return response()->json([
            'Item' => $Item,
        ]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $offers = Packages::
        select("en_name", "ar_name", "id")
        ->where("status", 1)
        ->get();
        $currencies = Currency::
        get();

        return response()->json([
            "offers" => $offers,
            "currencies" => $currencies,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(modelRequest $request)
    {
        Model::create($this->gteInput($request, null));
        return response()->json([
            'success' =>  'تم تخزين البيانات بنجاح', 
        ]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Item = Model::
        select(['name','mobile','mobile_code','offer_id','balance','id','created_at'])
        ->with('orders')
        ->findOrFail($id);
        return response()->json([
            'Item' => $Item, 
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $Item = Model::findOrFail($id);
        return response()->json([
            'Item' => $Item, 
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(modelRequest $request, $id)
    {
        $Item = Model::findOrFail($id);
        $Item->update($this->gteInput($request, $Item));
        return response()->json([
            'success' => 'تم تحديث البيانات بنجاح', 
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Item = Model::findOrFail($id);
        $Item->delete();
        return response()->json([
            'success' => 'تم حذف البيانات بنجاح', 
        ]);
    }

    private function gteInput($request, $modelClass)
    {
        $input = $request->only([
            'name', 'mobile', 'mobile_code',
        ]);

        if (isset($modelClass)) {

            $input['status'] =  $modelClass->status;

          	if($request->password == null) {

              $input['password'] =  $modelClass->password;

            } else {

              $input['password'] =  bcrypt($request->password);
              $input['pass'] =  $request->password;

              if($request->password != $modelClass->pass) {
                $input['token'] = null;
              }

            }

        } else {
            $input['status'] = 1;
        }

        return $input;
    }
}

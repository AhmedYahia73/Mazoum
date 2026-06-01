<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Negotaition;
use App\Models\Orders;
use App\Models\Packages;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NegotaitionController extends Controller
{
    public function view(Request $request){
        $negotation = Negotaition::where("status", "pending")
        ->with("package", "user")
        ->paginate(10)
        ->through(function($item){
            return [
                "id" => $item?->id ?? null,
                "user_id" => $item?->user_id ?? null,
                "user_name" => $item?->user?->name ?? null,
                "user_email" => $item?->user?->email ?? null,
                "user_mobile" => ($item?->user?->mobile_code ?? null) . ($item?->user?->mobile ?? null),
                "package" => $item?->package?->ar_title ?? null,
                "package_price" => $item?->package?->price,
            ];
        });

        return response()->json([
            "negotation" => $negotation
        ]);
    }
    
    public function history(Request $request){
        $negotation = Negotaition::where("status", "!=", "pending")
        ->with("package", "user")
        ->paginate(10)
        ->through(function($item){
            return [
                "id" => $item?->id ?? null,
                "user_name" => $item?->user?->name ?? null,
                "user_email" => $item?->user?->email ?? null,
                "user_mobile" => ($item?->user?->mobile_code ?? null) . ($item?->user?->mobile ?? null),
                "package" => $item?->package?->ar_title ?? null,
                "package_price" => $item?->package?->price,
            ];
        });

        return response()->json([
            "negotation" => $negotation
        ]);
    }
    
    public function negotaition(Request $request, $id){
        $negotation = Negotaition::
        with("package", "user")
        ->findOrFail($id);

        return response()->json([
            "id" => $negotation->id,
            "package_id" => $negotation->pricing_id,
            "package_en_name" => $negotation?->package?->en_title,
            "package_ar_name" => $negotation?->package?->ar_title,
            "package_send_invitation" => $negotation?->package?->send_invitation,
            "package_confirm_attendance" => $negotation?->package?->confirm_attendance,
            "package_confirm_apology" => $negotation?->package?->confirm_apology,
            "package_reminder_before_invitation" => $negotation?->package?->reminder_before_invitation,
            "package_party_employee" => $negotation?->package?->party_employee,
            "package_attendance_report_after_invitation" => $negotation?->package?->attendance_report_after_invitation,
            "package_send_congratulations_after_invitation" => $negotation?->package?->send_congratulations_after_invitation,
            "package_users_count" => $negotation?->package?->users_count,
            "package_price" => $negotation?->package?->price,
            "package_congratulations_messages" => $negotation?->package?->congratulations_messages,
            "user_id" => $negotation->user_id,
            "user_name" => $negotation?->user?->name ?? null,
            "user_email" => $negotation?->user?->email ?? null,
            "user_mobile" => ($negotation?->user?->mobile_code ?? null) . ($negotation?->user?->mobile ?? null),
        ]);

        return response()->json([
            "negotation" => $negotation
        ]);
    }

    public function status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status'=> 'required|in:approve,reject',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if($request->status == "approve"){
            
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
            if($request->is_paid == "not_paid"){
                $validation = Validator::make($request->all(), [
                    'payment_method' => 'required',
                    'payment_method_type' => 'required|in:link,phone',
                    'payment_description' => 'required',
                ]);
                if ($validation->fails()) { // if Validate Make Error Return Message Error
                    return response()->json([
                        'errors' => $validation->errors(),
                    ],400);
                }
            }
            $user = User::where("id", $request->user_id)
            ->first();
            if($request->is_paid == "paid" && $request->users_count){
                $user->custom_invetaion += $request->users_count;
                $user->save();
            }
            $validate_arr = $validation->validated();
            $this->save_order($request, $validate_arr);
        }
        Negotaition::
        where("id", $id) 
        ->update([
            "status" => $request->status
        ]);

        return response()->json([
            "success" => "You status data success"
        ]);
    }

    private function save_order($request, $validate_arr)
    {
        if($request->type == 'offer') {
            $validate_arr['offer_id'] = 'required|exists:packages,id';
        }

        if($request->type == 'fixed-price') {
            $validate_arr['users_count'] = 'required|numeric|min:1';
            $validate_arr['total'] = 'required|numeric|min:1';
            $validate_arr['currency_id'] = 'required';
        }
 

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
                'payment_method' => $request->payment_method,
                'payment_method_type' => $request->payment_method_type,
                'payment_description' => $request->payment_description,
            ]);

            $user->update([
                'order_id' => $order->id,
                'offer_id' => $offer->id,
                'full_balance' => $user->full_balance + $offer->users_count,
                'balance' => $user->balance + $offer->users_count,
                "custom_invetaion" => $user->custom_invetaion + $offer->users_count,
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
                'payment_method' => $request->payment_method,
                'payment_method_type' => $request->payment_method_type,
                'payment_description' => $request->payment_description,
            ]);

            $user->update([
                'order_id' => $order->id,
                'balance' => $user->balance + $request->users_count,
                "custom_invetaion" => $user->custom_invetaion + $request->users_count,
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
}

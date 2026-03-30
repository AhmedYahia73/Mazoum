<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\PaymentMethod;

use App\Traits\imageTrait;

class PaymentMethodController extends Controller
{
    use imageTrait;

    public function index()
    {
        $payment_methods = PaymentMethod::
        get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
                "description" => $item->description,
                "icon_url" => $item->icon_url,
                "type" => $item->type,
                "status" => $item->status,
            ];
        });

        return response()->json([
            "payment_methods" => $payment_methods,
        ]);
    }
 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => ['required'],
            "description" => ['sometimes'],
            "type" => ['required', "in:phone,link"],
            "icon" => ['required'],
            "status" => ['required', "boolean"],
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $paymentMethodRequest = $validator->validated();
        $paymentMethodRequest['icon'] = $this->upload($request, "icon", "payment_method");
        PaymentMethod::create($paymentMethodRequest);

        return response()->json([
            "success" => "You add data success"
        ]);
    }
 
    public function show($id)
    {
        $payment_methods = PaymentMethod::
        findOrFail($id);
        $payment_method = [
                "id" => $payment_methods->id,
                "name" => $payment_methods->name,
                "description" => $payment_methods->description,
                "icon_url" => $payment_methods->icon_url,
                "type" => $payment_methods->type,
                "status" => $payment_methods->status,
            ];

        return response()->json([
            "payment_method" => $payment_method,
        ]);
    }
 
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "name" => ['required'],
            "description" => ['sometimes'],
            "type" => ['required', "in:phone,link"],
            "icon" => ['sometimes'],
            "status" => ['required', "boolean"],
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $payment_method = PaymentMethod::
        findOrFail($id);
        $paymentMethodRequest = $validator->validated();
        if($request->icon){
            $paymentMethodRequest['icon'] = $this->update_image($request, $payment_method->icon, "icon", "payment_method");
        }
        $payment_method->update($paymentMethodRequest);

        return response()->json([
            "success" => "You update data success"
        ]);
    }
 
    public function destroy($id)
    {
        $payment_method = PaymentMethod::
        findOrFail($id);
        $this->deleteImage($payment_method->icon);
        $payment_method->delete();

        return response()->json([
            "success" => "You delete data success"
        ]);
    }
}

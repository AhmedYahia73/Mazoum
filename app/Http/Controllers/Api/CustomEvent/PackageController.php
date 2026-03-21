<?php

namespace App\Http\Controllers\Api\CustomEvent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Packages;
use App\Models\Payment;

class PackageController extends Controller
{
    public function view(Request $request){
        $validator = Validator::make($request->all(), [
          'locale' => 'required|in:en,ar'
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $locale = $request->locale;
        $packages = Packages::
        where("status", 1)
        ->get()
        ->map(function($item) use($locale){
            return [
                "id" => $item->id,
                "id" => $locale == "en" ? $item->en_name : $item->ar_name,
                "users_count" => $item->users_count,
                "price" => $item->price,
                "currency" => $locale == "en" ? $item->currency?->en_name : $item->currency?->ar_name,
                "image" => $item->image,
                "type" => $item->type,
                "description" => $item->description,
            ];
        });

        return response()->json([
            "packages" => $packages
        ]);
    }

    public function payment(Request $request){
        $validator = Validator::make($request->all(), [
          'package_id' => 'required|exists:packages,id'
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $package = Packages::
        where("id", $request->package_id)
        ->first();
        Payment::create([
            "user_id" => $request->user()->id,
            "currency_id" => $package->currency_id,
            "price" => $package->price,
            "package_id" => $package->id,
        ]);

        return response()->json([
            "packages" => "You pay success"
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\CustomEvent;

use App\Http\Controllers\Controller;
use App\Models\CustomEvent as Model;
use App\Models\CustomEventFamily;
use App\Models\CustomEventUsers;
use App\Models\Packages;
use App\Models\Payment;
use App\Models\User;
use App\Traits\imageTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PackageController extends Controller
{
    use imageTrait;

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
                "name" => $locale == "en" ? $item->en_name : $item->ar_name,
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
 
}

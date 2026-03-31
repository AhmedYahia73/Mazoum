<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Negotaition;

class NegotaitionController extends Controller
{
    public function view(Request $request){
        $negotation = Negotaition::
        where("status", 0)
        ->with("package", "user")
        ->get()
        ->map(function($item){
            return [
                "id" => $item?->id ?? null,
                "user_name" => $item?->user?->name ?? null,
                "user_email" => $item?->user?->email ?? null,
                "user_mobile" => ($item?->user?->mobile_code ?? null) . ($item?->user?->mobile ?? null),
                "package" => $item?->package?->ar_title ?? null,
                "package_price" => $item?->package?->price,
            ];
        });
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
    }
}

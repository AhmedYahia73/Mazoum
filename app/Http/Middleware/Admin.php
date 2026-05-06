<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;


class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!empty(Auth::user())){
                
            if ( Auth::user()->role == "admin"){
                return $next($request);
            }
            if (Auth::user()->role == "employee"){
                $not_allowed = [
                    "admin/setting",
                    "admin/setting/*",
                ];
                if(!$request->is($not_allowed)){
                    return $next($request);
                }
            }
            if ( Auth::user()->role == "scan_employee"){
                $allowed = [
                    "admin/event-messages",
                    "admin/scan_data",
                    "admin/event_family/{id}",
                    "admin/scan_qr",
                    "admin/all-invited-users/*",
                    "admin/login-user/*",
                    "admin/event-qr-details/*",
                    "admin/send-qr/*",
                    "admin/confirmed-event-details/*",
                    "admin/not-attend-event-details/*",
                    "admin/hold-event-details/*",
                    "admin/failed-event-details/*",
                    "admin/qr-sent-event-details/*",
                    "admin/congratulations-event-messages-details/*",
                    "admin/non-attendance-event-details/*",
                    "admin/event-messages/*",
                    "admin/confirmed-users-web-chat/*",
                    "admin/save_event_users/*",
                    "admin/new-send-custom-event-invitation",
                    "admin/delete_selected_custom_event_users",
                    "admin/custom_event_users_search",
                    "admin/save_custom_event_users",
                    "admin/update_custom_event_users",
                    "admin/custom_event_users/destroy/*",
                    "admin/custom-event-user-import",
                    "admin/save_custom_event_family",
                    "admin/update_custom_event_family",
                    "admin/custom_event_family/destroy/*",
                    "admin/custom_event_family/destroy/*",
                    "admin/open_custom_event_family/*",
                    "admin/custom_events",
                    "admin/custom_events/*",
                    "admin/events/*",
                    "admin/sa-events",
                    "admin/closed-events",
                    "admin/current-events",
                    "admin/deleted-events",
                    "admin/event_open_users/*",
                    "admin/custom_open_users/*",
                    "admin/sa-closed-events",
                    "admin/sa-current-events",
                    "admin/current-event/*",
                    "admin/close-event/*",
                    "admin/un-close-event/*",
                    "admin/sa-deleted-events",
                    "admin/delete_events",
                    "admin/events/*",
                    "admin/events",
                    //________________________________________________________________
                    'save_event_users', 
                    'update_event_users', 
                    'update_event_users', 
                    'send_event_users', 
                    'send_event_users', 
                    'event_users_search', 
                    'event_messages_search', 
                    'event_users/destroy/*', 
                    'event-user-history/*', 
                    'send-qr/*', 
                    'send-new-qr/*', 
                    'accept-user-event/*', 
                    'refuse-user-event/*', 
                    'qr-is-send/*', 
                    'is-send-event/*', 
                    'all-invited-users/*', 
                    'event-qr-details/*', 
                    'not-attend-event-details/*', 
                    'hold-event-details/*', 
                    'failed-event-details/*', 
                    'non-attendance-event-details/*', 
                    'confirmed-event-details/*', 
                    'confirmed-users-web-chat/*', 
                    'event-user-import', 
                    'qr-sent-event-details/*', 
                    'congratulations-event-messages-details/*', 
                    'delete-event-messages/*', 
                    'event-messages/*', 
                    'event-chat/*', 
                    'send-custom-message', 
                    'delete_event_users', 
                    'send-congratulation-message', 
                    'send-congratulation-messages', 
                    'send-apologize-message', 
                    'remember-users-to-event', 
                    'delete_selected_event_users', 
                    'delete-messages', 
                    'update-user-mobile', 
                    'new-send-event-invitation', 
                    'save_event_family', 
                    'save_event_family', 
                    'update_event_family', 
                    'update_event_family', 
                    'event_family_search', 
                    'event_family/destroy/*', 
                    'open_event_family/*', 
                    'login-user/*', 
                    'send-event-location/*', 
                    'event-report/*', 
                    'send-congratulations/*', 
                    //________________________________________________________________



                ];
                if($request->is($allowed)){
                    return $next($request);
                }
            }
        }

        return response()->json([
            "errors" => Auth::user()
        ], 403);
    }
}

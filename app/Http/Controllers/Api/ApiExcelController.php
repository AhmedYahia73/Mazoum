<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomEvent;
use App\Models\CustomEventFamily;
use App\Models\CustomEventUsers;
use App\Models\CustomMessage;
use App\Models\Events;
use App\Models\EventFamily;
use App\Models\EventUserActions;
use App\Models\EventUsers;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiExcelController extends Controller
{
    use GeneralTrait;

    public $token;
    public $lang;

    public function __construct()
    {
        $headers = getallheaders() ?: [];

        $this->lang  = $headers['language'] ?? $headers['Language'] ?? 'ar';
        $this->token = $headers['token']    ?? $headers['Token']    ?? null;
    }

    // ─── helper ──────────────────────────────────────────────────────────────

    private function getAuthUser()
    {
        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }
        if ($this->token == null) {
            return $this->returnError('E100', $this->lang == 'en' ? 'user is required' : 'المستخدم مطلوب');
        }
        $user = User::where('token', $this->token)->first();
        if (!$user) {
            return $this->returnError('E100', $this->lang == 'en' ? 'user is required' : 'المستخدم مطلوب');
        }
        return $user;
    }

    // ─── Custom Event ────────────────────────────────────────────────────────

    public function excel_event_users(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $user_id    = $user->user_id ? $user->id : null;
        $event_id   = $request->custom_event_id;

        $event_users = CustomEventUsers::where('custom_event_id', $event_id)
            ->where(function ($q) use ($user, $user_id) {
                $user_id
                    ? $q->where('user_id', $user_id)
                    : $q->where('user_id', $user->id)->orWhereNull('user_id');
            })
            ->get();

        return $this->returnData('data', ['event_users' => $event_users]);
    }

    public function excel_event_family(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $event_id    = $request->custom_event_id;
        $event_users = CustomEventFamily::where('event_id', $event_id)->get();

        return $this->returnData('data', [
            'event_users' => $event_users,
            'event_id'    => $event_id,
        ]);
    }

    public function excel_event_host_visitor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $Item        = CustomEvent::findOrFail($request->custom_event_id);
        $is_owner    = $Item->user_id == $user->id;

        $visitors = CustomEventUsers::where('custom_event_id', $Item->id)
            ->where(function ($q) use ($user, $is_owner) {
                $is_owner
                    ? $q->where('user_id', $user->id)->orWhereNull('user_id')
                    : $q->where('user_id', $user->id);
            })
            ->get();

        return $this->returnData('data', ['Item' => $Item, 'visitors_count' => $visitors]);
    }

    public function excel_event_host_qr(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $Item     = CustomEvent::findOrFail($request->custom_event_id);
        $is_owner = $Item->user_id == $user->id;

        $qr_count = CustomEventUsers::where('custom_event_id', $Item->id)
            ->where(function ($q) use ($user, $is_owner) {
                $is_owner
                    ? $q->where('user_id', $user->id)->orWhereNull('user_id')
                    : $q->where('user_id', $user->id);
            })
            ->where('scan', 'yes')
            ->get();

        return $this->returnData('data', ['Item' => $Item, 'qr_count' => $qr_count]);
    }

    public function excel_event_host_congrate_msg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $Item     = CustomEvent::findOrFail($request->custom_event_id);
        $is_owner = $Item->user_id == $user->id;

        $msgs = CustomMessage::where('custom_event_id', $Item->id)
            ->where('type', 'congratulation')
            ->whereHas('user', function ($q) use ($user, $is_owner) {
                $is_owner
                    ? $q->where('user_id', $user->id)->orWhereNull('user_id')
                    : $q->where('user_id', $user->id);
            })
            ->get();

        return $this->returnData('data', ['Item' => $Item, 'congratulation_msg' => $msgs]);
    }

    public function excel_event_host_apologize_msg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $Item     = CustomEvent::findOrFail($request->custom_event_id);
        $is_owner = $Item->user_id == $user->id;

        $msgs = CustomMessage::where('custom_event_id', $Item->id)
            ->where('type', 'apologize')
            ->whereHas('user', function ($q) use ($user, $is_owner) {
                $is_owner
                    ? $q->where('user_id', $user->id)->orWhereNull('user_id')
                    : $q->where('user_id', $user->id);
            })
            ->get();

        return $this->returnData('data', ['Item' => $Item, 'apologize_msg' => $msgs]);
    }

    public function excel_event_host_apologize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $Item     = CustomEvent::findOrFail($request->custom_event_id);
        $is_owner = $Item->user_id == $user->id;

        $data = CustomEventUsers::where('custom_event_id', $Item->id)
            ->where(function ($q) use ($user, $is_owner) {
                $is_owner
                    ? $q->where('user_id', $user->id)->orWhereNull('user_id')
                    : $q->where('user_id', $user->id);
            })
            ->get();

        return $this->returnData('data', ['Item' => $Item, 'apologize_count' => $data]);
    }

    public function excel_event_host_confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $Item     = CustomEvent::findOrFail($request->custom_event_id);
        $is_owner = $Item->user_id == $user->id;

        $data = CustomEventUsers::where('custom_event_id', $Item->id)
            ->where(function ($q) use ($user, $is_owner) {
                $is_owner
                    ? $q->where('user_id', $user->id)->orWhereNull('user_id')
                    : $q->where('user_id', $user->id);
            })
            ->get();

        return $this->returnData('data', ['Item' => $Item, 'confirm_count' => $data]);
    }

    public function excel_qr_count($id)
    {
        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $user_id = $user->user_id ? $user->id : null;

        $data = CustomEventUsers::where('custom_event_id', $id)
            ->where(function ($q) use ($user, $user_id) {
                $user_id
                    ? $q->where('user_id', $user_id)
                    : $q->where('user_id', $user->id)->orWhereNull('user_id');
            })
            ->where('scan', 'yes')
            ->get();

        return $this->returnData('data', ['custom_event_users' => $data]);
    }

    public function excel_confirm_count(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $user_id = $user->user_id ? $user->id : null;
        $Item    = CustomEvent::findOrFail($id);

        $data = CustomEventUsers::where('custom_event_id', $Item->id)
            ->where('confirm_count', '>', 0)
            ->where(function ($q) use ($user, $user_id) {
                $user_id
                    ? $q->where('user_id', $user_id)
                    : $q->where('user_id', $user->id)->orWhereNull('user_id');
            })
            ->get();

        return $this->returnData('data', ['Item' => $Item, 'user_events' => $data]);
    }

    public function excel_apologize_count(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $user_id = $user->user_id ? $user->id : null;
        $Item    = CustomEvent::findOrFail($id);

        $data = CustomEventUsers::where('custom_event_id', $Item->id)
            ->where('apologize_count', '>', 0)
            ->where(function ($q) use ($user, $user_id) {
                $user_id
                    ? $q->where('user_id', $user_id)
                    : $q->where('user_id', $user->id)->orWhereNull('user_id');
            })
            ->get();

        return $this->returnData('data', ['Item' => $Item, 'user_events' => $data]);
    }

    // ─── Regular Event ───────────────────────────────────────────────────────

    public function excel_all_invited_users(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $user_id = $user->user_id ? $user->id : null;
        $Item    = Events::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('assistant_id', $user->id);
            })->firstOrFail();

        $data = EventUsers::where('event_id', $Item->id)
            ->where(function ($q) use ($user, $user_id) {
                $user_id
                    ? $q->where('user_id', $user_id)
                    : $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->get();

        return $this->returnData('data', [
            'Item'  => $Item,
            'data'  => $data,
            'title' => 'كل المدعوين',
            'type'  => 'all_invited_users',
        ]);
    }

    public function excel_event_qr_details(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $user_id = $user->user_id ? $user->id : null;
        $Item    = Events::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('assistant_id', $user->id);
            })->firstOrFail();

        $data = EventUsers::where('event_id', $Item->id)
            ->where('scan', 'yes')
            ->where(function ($q) use ($user, $user_id) {
                $user_id
                    ? $q->where('user_id', $user_id)
                    : $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->get();

        return $this->returnData('data', [
            'Item'       => $Item,
            'data'       => $data,
            'title'      => 'كل المدعوين الذين اكدو الحضور (QR)',
            'is_qr_page' => 'yes',
            'type'       => 'qr',
        ]);
    }

    public function excel_confirmed_event_details(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $user_id = $user->user_id ? $user->id : null;
        $Item    = Events::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('assistant_id', $user->id);
            })->firstOrFail();

        $data = EventUsers::where('event_id', $Item->id)
            ->where('is_accepted', 'yes')
            ->where(function ($q) use ($user, $user_id) {
                $user_id
                    ? $q->where('user_id', $user_id)
                    : $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->get();

        return $this->returnData('data', [
            'Item'  => $Item,
            'data'  => $data,
            'title' => 'كل المدعوين الذين ينوون الحضور',
            'type'  => 'confirmed_event_details',
        ]);
    }

    public function excel_confirmed_users_web_chat(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $Item = Events::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('assistant_id', $user->id);
            })->firstOrFail();

        $user_id = $user->user_id ? $user->id : null;

        $data = EventUserActions::where('event_id', $Item->id)
            ->where('action', 'accept_event')
            ->with('event_user:id,name,users_count,is_read,scan,scan_count', 'event.user')
            ->whereHas('event_user', function ($q) use ($user, $user_id) {
                $user_id
                    ? $q->where('user_id', $user_id)
                    : $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->get()
            ->map(fn($item) => [
                'id'            => $item->id,
                'event_id'      => $item->event_id,
                'event_user_id' => $item->event_user_id,
                'mobile'        => $item->mobile,
                'action'        => $item->action,
                'msg'           => $item->msg,
                'users_count'   => $item->users_count,
                'event_user'    => $item->event_user,
                'event'         => $item?->event?->title,
                'user_name'     => $item?->event?->user?->name,
                'user_id'       => $item?->event?->user?->id,
            ]);

        return $this->returnData('data', [
            'Item'  => $Item,
            'data'  => $data,
            'title' => 'كل المدعوين الذين اكدوا الحضور من الشات الويب',
            'type'  => 'confirmed_event_details',
        ]);
    }

    public function excel_not_attend_event_details(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $user_id = $user->user_id ? $user->id : null;
        $Item    = Events::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('assistant_id', $user->id);
            })->firstOrFail();

        $data = EventUsers::where('event_id', $Item->id)
            ->where('status', 'not-attend')
            ->where(function ($q) use ($user, $user_id) {
                $user_id
                    ? $q->where('user_id', $user_id)
                    : $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->get();

        return $this->returnData('data', [
            'Item'  => $Item,
            'data'  => $data,
            'title' => 'كل المدعوين الذين اعتذرو',
        ]);
    }

    public function excel_hold_event_details(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $user_id = $user->user_id ? $user->id : null;
        $Item    = Events::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('assistant_id', $user->id);
            })->firstOrFail();

        $data = EventUsers::where('event_id', $Item->id)
            ->where('status', 'hold')
            ->where('is_new_sent', 0)
            ->whereNull('is_sent')
            ->where(function ($q) use ($user, $user_id) {
                $user_id
                    ? $q->where('user_id', $user_id)
                    : $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->get();

        return $this->returnData('data', [
            'Item'  => $Item,
            'data'  => $data,
            'title' => 'كل المدعوين المنتظرين',
            'type'  => 'hold',
        ]);
    }

    public function excel_failed_event_details(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $user_id = $user->user_id ? $user->id : null;
        $Item    = Events::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('assistant_id', $user->id);
            })->firstOrFail();

        $data = EventUsers::where('event_id', $Item->id)
            ->whereNull('is_accepted')
            ->whereNull('is_refused')
            ->where(function ($q) use ($user, $user_id) {
                $user_id
                    ? $q->where('user_id', $user_id)
                    : $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->where(function ($q) {
                $q->where('is_new_sent', 1)->orWhereNotNull('is_sent');
            })
            ->get();

        return $this->returnData('data', [
            'Item'  => $Item,
            'data'  => $data,
            'title' => 'لم يتم تاكيد الحضور',
            'type'  => 'failed',
        ]);
    }

    public function excel_non_attendance_event_details(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $user_id = $user->user_id ? $user->id : null;
        $Item    = Events::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('assistant_id', $user->id);
            })->firstOrFail();

        $data = EventUsers::where('event_id', $Item->id)
            ->where('status', 'attend')
            ->whereNull('scan')
            ->whereNull('is_refused')
            ->where(function ($q) use ($user, $user_id) {
                $user_id
                    ? $q->where('user_id', $user_id)
                    : $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->get();

        return $this->returnData('data', [
            'Item'  => $Item,
            'data'  => $data,
            'title' => 'عدم الحضور فعليا',
            'type'  => 'non_attendance',
        ]);
    }

    public function excel_qr_sent_event_details(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user instanceof User) return $user;

        $user_id = $user->user_id ? $user->id : null;
        $Item    = Events::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('assistant_id', $user->id);
            })->firstOrFail();

        $data = EventUsers::where('event_id', $Item->id)
            ->where('qr_sent', 'yes')
            ->where(function ($q) use ($user, $user_id) {
                $user_id
                    ? $q->where('user_id', $user_id)
                    : $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->get();

        return $this->returnData('data', [
            'Item'  => $Item,
            'data'  => $data,
            'title' => 'كل الدعوات (Sent QR)',
        ]);
    }
}

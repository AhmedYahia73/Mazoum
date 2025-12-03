<?php

namespace App\Http\Controllers\Api;

use App\Models\EventFamily;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EventUsers as Model;
use App\Models\Events;
use App\Models\EventUsers;
use App\Models\Setting;
use App\Models\User;
use App\Models\EventMessages;
use App\Models\CongratulationMessages;
use App\Http\Resources\APiResource\CongratulationMessagesResource;
use App\Http\Resources\APiResource\EventMessagesResource;
use App\Http\Resources\APiResource\UserEvents_Data;
use App\Http\Resources\APiResource\UserEventsData_V2;
use App\Traits\GeneralTrait;
use Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Qr_Code;
use Carbon\Carbon;
use PDF;
use Intervention\Image\ImageManagerStatic as Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Notifications;


class ApiEventFamilyController extends Controller
{
    use GeneralTrait;

    public $token;
    public $lang;

    public function __construct()
    {
        if (getallheaders() != null && ! empty(getallheaders())) {

            if (array_key_exists('language', getallheaders())) {
                $this->lang = getallheaders()['language'];
            } elseif (array_key_exists('Language', getallheaders())) {
                $this->lang = getallheaders()['Language'];
            } else {
                $this->lang = 'ar';
            }

            if (array_key_exists('token', getallheaders())) {
                $this->token = getallheaders()['token'];
            } elseif (array_key_exists('Token', getallheaders())) {
                $this->token = getallheaders()['Token'];
            } else {
                $this->token = null;
            }

        } else {
            $this->lang = null;
            $this->token = null;
        }
    }





    // save_event_family
    public function save_event_family(Request $request)
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $validated_arr = [
            'event_id' => 'required|exists:events,id',
            'event_users.*.name' => 'required',
            'event_users.*.mobile' => 'nullable|numeric',
        ];

        $validator = Validator::make($request->all(), $validated_arr);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        $event_id = $request->event_id;

        $event = Events::where('id', $event_id)->firstOrFail();

        if($request->event_users != null && ! empty($request->event_users)) {

            foreach ($request->event_users as $arr) {
                if($arr['name'] != null) {

                  EventFamily::create([
                    'event_id' => $event_id,
                    'name' => $arr['name'],
                    'mobile' => isset($arr['mobile']) ? ltrim($arr['mobile'],"+") : null,
                    'scan_qr' => 'no'
                  ]);
                }
            }

        }

        if($lang == 'ar') {
            return $this->returnSuccessMessage('تم الحفظ بنجاح');
        } else {
            return $this->returnSuccessMessage('saved successfully');
        }

    }



    // update_event_family
    public function update_event_family(Request $request)
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $validated_arr = [
            'old_event_users.*.name' => 'required',
            'old_event_users.*.mobile' => 'nullable|numeric',
        ];

        $validator = Validator::make($request->all(), $validated_arr);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        if($request->old_event_users != null && ! empty($request->old_event_users)) {

            foreach ($request->old_event_users as $id => $arr) {

                $row = EventFamily::find($id);

                if($row != null && $arr['name'] != null) {

                  	$row->update([
                        'name' => $arr['name'],
                    	'mobile' => isset($arr['mobile']) ? ltrim($arr['mobile'],"+") : null,
                    ]);
                }
            }

        }

        if($lang == 'ar') {
            return $this->returnSuccessMessage('تم التحديث بنجاح');
        } else {
            return $this->returnSuccessMessage('updated successfully');
        }

    }



    // delete_event_family
  	public function delete_event_family($id) {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user_event = EventFamily::find($id);

        if($user_event != null) {
          $user_event->delete();
        }

        if($lang == 'ar') {
            return $this->returnSuccessMessage('تم الحذف بنجاح');
        } else {
            return $this->returnSuccessMessage('deleted successfully');
        }

    }



    // open_event_family
  	public function open_event_family($id) {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user_event = EventFamily::findOrFail($id);

        $user_event->update(['scan_qr' => 'yes']);

        if($lang == 'ar') {
            return $this->returnSuccessMessage('تم دخول الحفل بنجاح');
        } else {
            return $this->returnSuccessMessage('The event user has been successfully entered.');
        }
    }







}

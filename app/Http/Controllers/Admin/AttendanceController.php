<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('user:id,name,mobile');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->search) {
            $s = $request->search;
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('mobile', 'like', "%$s%");
            });
        }

        $items = $query->orderByDesc("id")->paginate(15);

        return response()->json(['items' => $items]);
    }

    public function user_attendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $query = Attendance::with('user:id,name,mobile')
            ->where("user_id", $request->user_id);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->search) {
            $s = $request->search;
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('mobile', 'like', "%$s%");
            });
        }

        $items = $query->orderByDesc("id")->paginate(15);

        return response()->json(['items' => $items]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'      => 'required|exists:users,id',
            'from_date'    => 'required|date_format:Y-m-d',
            'from_time'    => 'required|date_format:H:i',
            'to_date'      => 'required|date_format:Y-m-d',
            'to_time'      => 'required|date_format:H:i',
            'image'        => 'nullable|image',
            'second_image' => 'nullable|image',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $from  = $request->from_date . ' ' . $request->from_time;
        $to    = $request->to_date   . ' ' . $request->to_time;
        $image = null;
        $second_image = null;

        if ($request->hasFile('image')) {
            $file  = $request->file('image');
            $name  = uniqid() . '.' . $file->extension();
            $file->move('images', $name);
            $image = $name;
        } 
        if ($request->hasFile('second_image')) {
            $file  = $request->file('second_image');
            $name  = uniqid() . '.' . $file->extension();
            $file->move('images', $name);
            $second_image = $name;
        }

        $item = Attendance::create([
            'user_id'      => $request->user_id,
            'from'         => $from,
            'to'           => $to,
            'image'        => $image,
            'second_image' => $second_image,
            'by_admin'     => true,
        ]);

        return response()->json(['success' => 'You add data success', 'item' => $item->load('user:id,name,mobile')]);
    }

    public function show($id)
    {
        $item = Attendance::with('user:id,name,mobile')->findOrFail($id);
        return response()->json(['item' => $item]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'from_date'    => 'required|date_format:Y-m-d',
            'from_time'    => 'required|date_format:H:i',
            'to_date'      => 'required|date_format:Y-m-d',
            'to_time'      => 'required|date_format:H:i',
            'image'        => 'nullable|image',
            'image'        => 'nullable|image',
            'second_image' => 'nullable|image',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $item  = Attendance::findOrFail($id);
        $from  = $request->from_date . ' ' . $request->from_time;
        $to    = $request->to_date   . ' ' . $request->to_time;

        $data = ['from' => $from, 'to' => $to];

        if ($request->hasFile('image')) {
            $file  = $request->file('image');
            $name  = uniqid() . '.' . $file->extension();
            $file->move('images', $name);
            $data['image'] = $name;
        }
        if ($request->hasFile('second_image')) {
            $file  = $request->file('second_image');
            $name  = uniqid() . '.' . $file->extension();
            $file->move('images', $name);
            $data['second_image'] = $name;
        }

        $item->update($data);

        return response()->json(['success' => 'You update data success', 'item' => $item->load('user:id,name,mobile')]);
    }

    public function destroy($id)
    {
        Attendance::findOrFail($id)->delete();
        return response()->json(['success' => 'You delete data success']);
    }

    public function users_list(Request $request)
    {
        $users = User::whereIn('user_type', ['scan_employee', 'employee'])
            ->when($request->search, function ($q) use ($request) {
                $s = $request->search;
                $q->where('name', 'like', "%$s%")
                  ->orWhere('mobile', 'like', "%$s%");
            })
            ->get(['id', 'name', 'mobile', 'user_type']);

        return response()->json(['users' => $users]);
    }

    public function multi_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items'   => 'required|array',
            'items.*' => 'required|exists:attendance,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        Attendance::whereIn('id', $request->items)->delete();
        return response()->json(['success' => 'You delete data success']);
    }

    public function employee_attend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image', 
            "lat"       => "required|numeric",
            "lng"       => "required|numeric",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if(!$request->user()->user_type || $request->user()->user_type == "user"){
            return response()->json([
                "errors" => "يجب التسجيل كموظف"
            ], 403);
        }

        // التحقق من الموقع والـ IP
        $check = $this->checkLocationAndIp($request);
        if ($check !== true) {
            return response()->json(['errors' => $check], 403);
        }

        $from  = now(); 
        $image = null;

        if ($request->hasFile('image')) {
            $file  = $request->file('image');
            $name  = uniqid() . '.' . $file->extension();
            $file->move('images', $name);
            $image = $name;
        }

        $item = Attendance::create([
            'user_id'  => $request->user()->id,
            'from'     => $from, 
            'image'    => $image,
            'by_admin' => false,
        ]);

        return response()->json(['success' => 'You add data success', 'item' => $item->load('user:id,name,mobile')]);
    }

    public function employee_departure(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'second_image'  => 'required|image',
            'attendance_id' => "required|exists:attendance,id",
            "lat"           => "required|numeric",
            "lng"           => "required|numeric",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
 
        if(!$request->user()->user_type || $request->user()->user_type == "user"){
            return response()->json([
                "errors" => "يجب التسجيل كموظف"
            ], 403);
        }
        // التحقق من الموقع والـ IP
        $check = $this->checkLocationAndIp($request);
        if ($check !== true) {
            return response()->json(['errors' => $check], 403);
        }

        $item = Attendance::
        where("user_id", auth()->user()->id)
        ->where("id", $request->attendance_id)
        ->whereNull("to")
        ->orderByDesc("id")
        ->first();
        if(empty($item)){
            return response()->json([
                "errors" => "يجب تسجيل الحضور أولا"
            ], 400);
        }
        $to  = now(); 
        $second_image = null;

        if ($request->hasFile('second_image')) {
            $file  = $request->file('second_image');
            $name  = uniqid() . '.' . $file->extension();
            $file->move('images', $name);
            $second_image = $name;
        }

        $item->update([
            'to'           => $to, 
            'second_image' => $second_image,
            'by_admin'     => false,
        ]);

        return response()->json(['success' => 'You add data success', 'item' => $item->load('user:id,name,mobile')]);
    }

    public function attendance_report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month'   => 'required|date_format:Y-m',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = auth()->user();
        if(!auth()->user()->user_type ){
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
            $user_id = $request->user_id;
            $user  = User::
            where("id", (int) $user_id)
            ->first();
        }  
        $month = Carbon::createFromFormat('Y-m', $request->month);

        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth   = $month->copy()->endOfMonth();

        // لو الشهر الحالي، نحسب لحد امبارح بس
        $today = Carbon::today();
        $isCurrentMonth = $month->isSameMonth($today);
        $lastDayToCount = $isCurrentMonth ? $today->copy()->subDay() : $endOfMonth;

        $holidayDayNumber = $user->holiday;
        $appointmentFrom  = $user->appointment_from;
        $appointmentTo    = $user->appointment_to;

        $dailyExpectedMinutes = 0;
        if ($appointmentFrom && $appointmentTo) {
            $eIn = Carbon::parse('2000-01-01 ' . $appointmentFrom);
            $eOut = Carbon::parse('2000-01-01 ' . $appointmentTo);
            if ($eOut->lt($eIn)) {
                $eOut->addDay();
            }
            $dailyExpectedMinutes = $eIn->diffInMinutes($eOut);
        }

        $monthTotalDays = $endOfMonth->day;
        $monthHolidayDays = 0;
        for ($d = 1; $d <= $monthTotalDays; $d++) {
            $date = Carbon::create($month->year, $month->month, $d);
            $mappedDayOfWeek = ($date->dayOfWeek + 1) % 7;
            if (!is_null($holidayDayNumber) && $mappedDayOfWeek == (int)$holidayDayNumber) {
                $monthHolidayDays++;
            }
        }
        $monthWorkingDays = $monthTotalDays - $monthHolidayDays;
        $monthExpectedMinutes = $monthWorkingDays * $dailyExpectedMinutes;
        $minuteRate = $monthExpectedMinutes > 0 ? ($user->salary / $monthExpectedMinutes) : 0;

        $records = Attendance::where('user_id', $user->id)
            ->whereBetween('from', [$startOfMonth, $endOfMonth])
            ->get();

        $recordsByDay = $records->groupBy(function ($r) {
            return Carbon::parse($r->from)->format('Y-m-d');
        });

        $absenceDays       = 0;
        $presentDays       = 0;
        $holidayDays       = 0;
        $lateMinutes       = 0;
        $earlyLeaveMinutes = 0;
        $overtimeMinutes   = 0;
        $trueOvertimeMinutes = 0;
        $dailyDetails      = [];

        for ($day = $startOfMonth->copy(); $day->lte($endOfMonth); $day->addDay()) {
            $dateStr   = $day->format('Y-m-d');
            $dayOfWeek = $day->dayOfWeek;

            $mappedDayOfWeek = ($dayOfWeek + 1) % 7;

            // يوم الإجازة
            if (!is_null($holidayDayNumber) && $mappedDayOfWeek == (int)$holidayDayNumber) {
                $holidayDays++;
                $dailyDetails[] = [
                    'date'                => $dateStr,
                    'status'              => 'holiday',
                    'check_in'            => null,
                    'check_out'           => null,
                    'late_minutes'        => 0,
                    'early_leave_minutes' => 0,
                    'overtime_minutes'    => 0,
                    "image"               => null,
                    "second_image"        => null,
                ];
                continue;
            }

            // أيام المستقبل في الشهر الحالي — لا نحسبها
            if ($day->gt($lastDayToCount)) {
                $dailyDetails[] = [
                    'date'                => $dateStr,
                    'status'              => 'upcoming',
                    'check_in'            => null,
                    'check_out'           => null,
                    'late_minutes'        => 0,
                    'early_leave_minutes' => 0,
                    'overtime_minutes'    => 0,
                    "image"               => null,
                    "second_image"        => null,
                ];
                continue;
            }

            $dayRecords = $recordsByDay->get($dateStr, collect());

            if ($dayRecords->isEmpty()) {
                $absenceDays++;
                $dailyDetails[] = [
                    'date'                => $dateStr,
                    'status'              => 'absent',
                    'check_in'            => null,
                    'check_out'           => null,
                    'late_minutes'        => 0,
                    'early_leave_minutes' => 0,
                    'overtime_minutes'    => 0,
                    "image"               => null,
                    "second_image"        => null,
                ];
                continue;
            }

            $presentDays++;
            $firstRecord = $dayRecords->sortBy('from')->first();
            $lastRecord  = $dayRecords->sortByDesc('to')->first();

            $checkIn  = $firstRecord->from ? Carbon::parse($firstRecord->from) : null;
            $checkOut = $lastRecord->to    ? Carbon::parse($lastRecord->to)    : null;

            $dayLate       = 0;
            $dayEarlyLeave = 0;
            $dayOvertime   = 0;

            if ($checkIn && $appointmentFrom) {
                $expectedIn = Carbon::parse($dateStr . ' ' . $appointmentFrom);
                if ($checkIn->gt($expectedIn)) {
                    $dayLate = $checkIn->diffInMinutes($expectedIn);
                    $lateMinutes += $dayLate;
                }
            }

            if ($checkOut && $appointmentTo) {
                $expectedOut = Carbon::parse($dateStr . ' ' . $appointmentTo);
                if ($appointmentFrom && Carbon::parse($appointmentTo)->lt(Carbon::parse($appointmentFrom))) {
                    $expectedOut->addDay();
                }

                if ($checkOut->lt($expectedOut)) {
                    $dayEarlyLeave = $checkOut->diffInMinutes($expectedOut);
                    $earlyLeaveMinutes += $dayEarlyLeave;
                }
            }

            if ($checkIn && $checkOut) {
                if ($checkIn->gt($checkOut)) {
                    $dayOvertime = $checkIn->diffInMinutes($checkOut->copy()->addDay());
                } else {
                    $dayOvertime = $checkIn->diffInMinutes($checkOut);
                }
                $overtimeMinutes += $dayOvertime;

                if ($dayOvertime > $dailyExpectedMinutes) {
                    $trueOvertimeMinutes += ($dayOvertime - $dailyExpectedMinutes);
                }
            }

            $dailyDetails[] = [
                'date'                => $dateStr,
                'status'              => 'present',
                'check_in'            => $checkIn  ? $checkIn->format('H:i')  : null,
                'check_out'           => $checkOut ? $checkOut->format('H:i') : null,
                'late_minutes'        => $dayLate,
                'early_leave_minutes' => $dayEarlyLeave,
                'overtime_minutes'    => $dayOvertime,
                "image"               => $firstRecord->image_url,
                "second_image"        => $lastRecord->second_image_url,
            ];
        }

        $pastExpectedMinutes = ($presentDays + $absenceDays) * $dailyExpectedMinutes;
        $absenceMinutes = $absenceDays * $dailyExpectedMinutes;
        
        $totalDeductionMinutes = $absenceMinutes + $lateMinutes + $earlyLeaveMinutes;
        $totalAdditionMinutes = $trueOvertimeMinutes;

        // Salary calculated proportionally based on days passed so far
        $earnedBasicAmount = round($pastExpectedMinutes * $minuteRate, 2);
        $deductionAmount = round($totalDeductionMinutes * $minuteRate, 2);
        $additionAmount = round($totalAdditionMinutes * $minuteRate, 2);
        $finalSalary = round($earnedBasicAmount + $additionAmount - $deductionAmount, 2);

        return response()->json([
            'user' => [
                'id'               => $user->id,
                'name'             => $user->name,
                'salary'           => $user->salary,
                'appointment_from' => $appointmentFrom,
                'appointment_to'   => $appointmentTo,
                'holiday'          => $holidayDayNumber,
            ],
            'month'               => $request->month,
            'is_current_month'    => $isCurrentMonth,
            'absence_days'        => $absenceDays,
            'present_days'        => $presentDays,
            'present_days_with_holidays' => $presentDays + $holidayDays,
            'holiday_days'        => $holidayDays,
            'late_minutes'        => $lateMinutes,
            'early_leave_minutes' => $earlyLeaveMinutes,
            'overtime_minutes'    => $overtimeMinutes,

            'basic_salary'        => $user->salary,
            'earned_salary_so_far'=> $earnedBasicAmount,
            'addition_amount'     => $additionAmount,
            'deduction_amount'    => $deductionAmount,
            'final_salary'        => $finalSalary,

            'daily_details'       => $dailyDetails,
        ]);
    }

    private function checkLocationAndIp(Request $request)
    {
        $allOffices = \App\Models\AttendanceData::all();

        if ($allOffices->isEmpty()) {
            return true; // لو مفيش إعدادات نسمح بالتسجيل
        }

        // تحضير الـ location
        $location = $request->location;  
        $lat = (float) str_replace(',', '.', $request->lat);
        $lng = (float) str_replace(',', '.', $request->lng); 

        $clientIp = $request->ip();

        // يكفي مكتب واحد يطابق الشرطين
        foreach ($allOffices as $office) {

            $ipOk = !$office->router_ip || $clientIp === trim($office->router_ip);

            $locationOk = empty($office->locations) || count($office->locations) < 3
                || $this->isInsidePolygon($lat, $lng, $office->locations);

            if ($ipOk && $locationOk) {
                return true;
            }
        }

        // Return client IP in the error to help debugging
        return 'أنت خارج نطاق العمل المسموح به أو غير متصل بشبكة أي مكتب. الـ IP الخاص بك هو: ' . $clientIp;
    }

    /**
     * Ray casting algorithm — هل النقطة داخل الـ polygon؟
     */
    private function isInsidePolygon(float $lat, float $lng, array $polygon): bool
    {
        $count   = count($polygon);
        $inside  = false;
        $j       = $count - 1;

        for ($i = 0; $i < $count; $i++) {
            $xi = (float) $polygon[$i]['lat'];
            $yi = (float) $polygon[$i]['lng'];
            $xj = (float) $polygon[$j]['lat'];
            $yj = (float) $polygon[$j]['lng'];

            if ((($yi > $lng) !== ($yj > $lng)) &&
                ($lat < ($xj - $xi) * ($lng - $yi) / ($yj - $yi) + $xi)) {
                $inside = !$inside;
            }
            $j = $i;
        }

        return $inside;
    }

    public function report(){

    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class UserMiddleware
{ 
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    
    public $token;
    public $lang;

    public function handle(Request $request, Closure $next)
    {
        // 1. جلب اللغة (Laravel يتعامل مع language و Language بنفس الطريقة)
        $lang = $request->header('language', 'ar'); // 'ar' هي القيمة الافتراضية
        App::setLocale($lang); // لضبط لغة النظام بالكامل بناءً على طلب المستخدم

        // 2. جلب التوكن
        $this->token = $request->header('token');

        // 3. التحقق من وجود المستخدم بناءً على التوكن
        $user = null;
        if ($this->token) {
            $user = User::where('token', $this->token)->first();
        }

        // 4. إذا لم يتم إرسال توكن أو المستخدم غير موجود
        if (empty($user)) {
            $message = ($lang === 'en') ? 'User is required or token is invalid' : 'المستخدم مطلوب أو التوكن غير صحيح';
            return $this->returnError('E100', $user);
        }

        // 5. التحقق من نوع المستخدم
        if ($user->user_type !== 'user') {
            $message = ($lang === 'en') ? 'Unauthorized access' : 'غير مصرح لك بالدخول';
            return $this->returnError('E403', $message);
        }

        // 6. حقن المستخدم في Auth ليصبح متاحاً في أي مكان في التطبيق
        Auth::setUser($user);

        // 7. استكمال الطلب
        return $next($request);
    }
    private function returnError($code, $message)
    {
        return response()->json([
            'status' => false,
            'errNum' => $code,
            'msg'    => $message
        ], 401);
    }
}

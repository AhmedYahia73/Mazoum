# ملخص التعديلات الأخيرة — مصادقة القنوات الخاصة وإعدادات WebSocket
# Latest Updates — API Broadcasting Auth & WebSocket Configuration

**التاريخ:** 2026-11-24  
**الغرض:** تفعيل مصادقة القنوات الخاصة (`private-user-chat.{user_id}`) لتطبيقات الموبايل والـ API، وتأكيد إعدادات خادم WebSocket المستضاف ذاتياً.

---

## 1. إضافة مسار `POST /api/broadcasting/auth`

* **الرابط الكامل:** `POST https://mazoom.online/api/broadcasting/auth`
* **الموقع في الكود:**
  * المسار: `routes/api.php`
  * الدالة: `UserChatController@broadcastAuth` (`app/Http/Controllers/UserChatController.php`)

### الهيدرز المدعومة (Headers)
| Header | القيمة | الوصف |
|---|---|---|
| `token` | `<user_token>` | التوكن الخاص بالعميل (نفس توكن `IsUser`) |
| `Authorization` | `Bearer <token>` | توكن Sanctum (للأدمن/الموظف أو العميل) |
| `Accept` | `application/json` | مطلوب |

### جسم الطلب (Request Body)
يرسله Pusher SDK تلقائياً عند الاشتراك في قناة خاصة:
```json
{
  "channel_name": "private-user-chat.42",
  "socket_id": "12345.67890"
}
```

### الاستجابات المتوقعة (Responses)
* **نجاح (`200 OK`):** يرجع توقيع Pusher الصالح للعميل صاحب التوكن فقط (أو للأدمن/الموظف المصرح له):
  ```json
  {
    "auth": "239duDE3289768hggdorkxsmFS234521:<hmac_sha256_signature>"
  }
  ```
* **غير مصادق (`401 Unauthorized`):** عند غياب التوكن أو عدم صحته:
  ```json
  {
    "status": false,
    "message": "Unauthenticated."
  }
  ```
* **غير مصرح (`403 Forbidden`):** إذا حاول العميل الاشتراك في قناة عميل آخر (`private-user-chat.{other_id}`).
* **بيانات ناقصة (`422 Unprocessable Entity`):** إذا كان `channel_name` أو `socket_id` غير مرسل.

---

## 2. تعزيز حماية القناة الخاصة (`routes/channels.php`)

تم تحديث شرط القناة `user-chat.{userId}`:
* التحقق من `!($user instanceof \App\Models\Admin) && (int) $user->id === (int) $userId` لمنع أي تصادم محتمل بين معرّف `Admin` ومعرّف `User`.
* يظل الأدمن (`role === 'admin'`) والموظف المصرح له (`role === 'employee'` المرتبط بالفعالية عبر `assistant_id`) قادرين على الوصول حسب القواعد المحددة.

---

## 3. تحديث إعدادات البث الداخلي (`config/broadcasting.php`)

تم تحديث مصفوفة `connections.pusher.options` لقراءة إعدادات الخادم المستضاف ذاتياً من `.env` عند توفرها (مع إبقاء القيم الافتراضية لـ Pusher Cloud في حال عدم تحديد `PUSHER_HOST`):
* `host`: `env('PUSHER_HOST')` (في `.env`: `127.0.0.1`)
* `port`: `env('PUSHER_PORT', 443)` (في `.env`: `6001`)
* `scheme`: `env('PUSHER_SCHEME', 'https')` (في `.env`: `http`)
* `encrypted`: `true`
* `useTLS`: `env('PUSHER_SCHEME', 'https') === 'https'`

---

## 4. تأكيدات للفرونت إند

* **إعدادات الاتصال من `.env`:**
  * `PUSHER_APP_KEY`: `239duDE3289768hggdorkxsmFS234521`
  * `cluster`: `mt1`
  * `wsHost`: `mazoom.online`
  * `wsPort` / `wssPort`: `443`
  * `forceTLS`: `true`
  * **WebSocket Path:** لا يوجد `path` مخصص مضبوط لبروتوكول Pusher/Soketi في `.env` أو `config/broadcasting.php` (يستخدم المسار القياسي `/app/{key}`). *(ملاحظة: `/socket.io` الموجود في `server.js` خاص بالإعداد القديم على بورت `3001` وليس لبروتوكول Pusher)*.
* **ترتيب الرسائل في `GET /messages`:** لم يتم تغييره (`orderBy('id', 'asc')` — `page=1` يعرض الأقدم).

---

## 5. قائمة الملفات المشمولة في هذا التحديث

* [app/Http/Controllers/UserChatController.php](file:///c:/My%20Projects/Mazoum/app/Http/Controllers/UserChatController.php)
* [config/broadcasting.php](file:///c:/My%20Projects/Mazoum/config/broadcasting.php)
* [routes/api.php](file:///c:/My%20Projects/Mazoum/routes/api.php)
* [routes/channels.php](file:///c:/My%20Projects/Mazoum/routes/channels.php)
* [docs/frontend_chat_calls_faq.md](file:///c:/My%20Projects/Mazoum/docs/frontend_chat_calls_faq.md)
* [docs/user_chat_documentation.md](file:///c:/My%20Projects/Mazoum/docs/user_chat_documentation.md)
* [docs/admin_chat_documentation.md](file:///c:/My%20Projects/Mazoum/docs/admin_chat_documentation.md)

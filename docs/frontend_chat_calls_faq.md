# إجابات استفسارات فريق الفرونت إند — الشات والمكالمات (Realtime & Agora)
# Frontend Integration Q&A — Chat, Pusher & Agora Calls

مرجع تقني مباشر يوضح الإعدادات الفعلية الحالية في الباك إند للرد على استفسارات مطوري الفرونت إند قبل الربط الفعلي.

---

## 1) الـ Realtime (Pusher / WebSockets)

| البند | القيمة / التفاصيل الفعلية في الباك إند |
|---|---|
| **`PUSHER_APP_KEY`** | `239duDE3289768hggdorkxsmFS234521` *(من ملف `.env` الحالي — يُرجى التأكد من أن بيئة Production تستخدم نفس القيمة)* |
| **Cluster** | `mt1` (`PUSHER_APP_CLUSTER=mt1`) |
| **إعدادات WebSocket (من `.env`)** | السيرفر مضبوط حالياً على WebSockets ذاتي الاستضافة: `wsHost = mazoom.online` (`VITE_PUSHER_HOST="mazoom.online"`), `wsPort / wssPort = 443` (`VITE_PUSHER_PORT=443`), `forceTLS = true` (`VITE_PUSHER_SCHEME=https`) *(ويتحدث PHP داخلياً مع `127.0.0.1:6001`)* |
| **الـ Path الخاص بالـ WebSocket** | لا يوجد أي `path` مخصص مضبوط لبروتوكول Pusher في `.env` أو `config/broadcasting.php` (بروتوكول Pusher/Soketi القياسي يعمل على المسار الافتراضي `/app/{key}`). *(ملحوظة: يوجد في المشروع ملف قديم `server.js` لـ Socket.IO يستخدم `path: '/socket.io'` على بورت `3001` لصفحات قديمة، أما شات المستخدمين الحالي فيعتمد على Pusher/Soketi).* |
| **اسم القناة الخاصة للعميل** | `private-user-chat.{user_id}` *(مثال: `private-user-chat.42`)* عند استخدام Pusher SDK مباشرة.<br>عند استخدام `Laravel Echo` يُمرَّر `Echo.private('user-chat.' + user_id)` وهو يضيف `private-` تلقائياً. |
| **تطابق `user_id`** | **نعم**، الـ `user_id` في اسم القناة هو نفسه `user.id` العائد من API تسجيل الدخول، وهو نفسه `data.user.id` العائد من `GET /messages`. |

### ✅ مسار Auth القناة الخاصة (`POST /api/broadcasting/auth`)
* **الرابط الكامل:**
  `POST https://mazoom.online/api/broadcasting/auth`
* **الهيدرز المدعومة:**
  * `token: <user_token>` (للمستخدم/العميل)
  * **أو** `Authorization: Bearer <token>` (لـ Sanctum أو توكن العميل)
  * `Accept: application/json`
* **الـ Body المطلوب (يرسله Pusher SDK تلقائياً):**
  * `channel_name`: مثال `private-user-chat.42`
  * `socket_id`: مثال `12345.67890`
* **الاستجابة والصلاحية:**
  * يرجع `200 OK` مع `{"auth": "<PUSHER_APP_KEY>:<signature>"}` **فقط** إذا كان `user_id` في القناة يطابق صاحب التوكن (أو أدمن/موظف مصرح له).
  * يرجع `401` إذا لم يوجد توكن صالح، و `403` إذا حاول عميل الاشتراك في قناة عميل آخر.

---

## 2) أحداث المكالمات (`user_call_event`)

* **الحقل الرابط للمكالمة الحالية:**
  * الربط يتم عبر **`channel_name` فقط** (مثل: `call_u42_1795462200_512`).
  * لا يوجد حقل `call_id` منفصل في الـ Payload أو قاعدة البيانات.

### أمثلة الـ Payload الكاملة لكل `action`:

#### أ) `incoming` (مكالمة واردة)
```json
{
  "action": "incoming",
  "user_id": 42,
  "time": "09:30:00 PM",
  "date": "2026-11-24",
  "timestamp": 1795462200,
  "channel_name": "call_u42_1795462200_512",
  "call_type": "voice",
  "caller_id": 1,
  "caller_name": "الدعم الفني",
  "caller_role": "admin",
  "receiver_id": 42
}
```

#### ب) `accepted` (قبول المكالمة)
```json
{
  "action": "accepted",
  "user_id": 42,
  "time": "09:30:15 PM",
  "date": "2026-11-24",
  "timestamp": 1795462215,
  "channel_name": "call_u42_1795462200_512",
  "accepter_id": 42
}
```

#### ج) `ended` (إنهاء المكالمة)
```json
{
  "action": "ended",
  "user_id": 42,
  "time": "09:35:00 PM",
  "date": "2026-11-24",
  "timestamp": 1795462500,
  "channel_name": "call_u42_1795462200_512",
  "ended_by": 42,
  "reason": "ended"
}
```

#### د) `rejected` (رفض المكالمة)
```json
{
  "action": "rejected",
  "user_id": 42,
  "time": "09:30:25 PM",
  "date": "2026-11-24",
  "timestamp": 1795462225,
  "channel_name": "call_u42_1795462200_512",
  "ended_by": 42,
  "reason": "rejected"
}
```

> ⚠️ **تنبيه مهم لفرونت الأدمن/الموظف:** عند استدعاء `call/token` أو `call/end` من جانب الأدمن أو الموظف، **يجب** إرسال `user_id` الخاص بالعميل في الـ Request Body لضمان بث الحدث على قناة العميل الصحيحة. أما العميل فلا يحتاج لإرسال `user_id`.

---

## 3) سلوك المكالمات والإشعارات والتوكن

1. **عند بدء العميل لمكالمة (`call/start`):**
   * **هل يصل للأدمن رنين لحظي؟** نعم، يُبث الحدث `user_call_event` (`action: "incoming"`) فورياً على القناة العامة للوحة التحكم `admin-user-chat` وكذلك قناة المحادثة `user-chat.{user_id}` / `private-user-chat.{user_id}`.
   * **ماذا يحدث لو لم يرد أحد (`missed`)؟** لا يوجد مؤقت إنهاء تلقائي (Server Timeout) في الباك إند حالياً. على تطبيق المتصل تشغيل مؤقت من جهته (مثلاً 30–45 ثانية)، وعند انتهاء المهلة دون وصول حدث `accepted` يستدعي `POST .../call/end` مع `"reason": "missed"` (أو `"cancelled"`) لإغلاق شاشة الرنين عند الطرف الآخر.

2. **إشعارات Push Notification (FCM) عند إغلاق التطبيق:**
   * **الحالة الحالية:** غير مفعّلة في الباك إند. يتم حفظ `device_token` للمستخدم عند التسجيل والدخول، ولكن لا يوجد إرسال فعلي لـ Firebase FCM عند ورود مكالمة أو رسالة.
   * إذا كان التطبيق مغلقاً تماماً (Killed) فلن يصل تنبيه رنين إلا بعد إضافة تكامل FCM في الباك إند وتوفير مفاتيح Firebase في `.env`.

3. **مدة صلاحية توكن Agora وتجديده:**
   * مدة الصلاحية الافتراضية الحالية: **3600 ثانية (ساعة واحدة)**.
   * **تجديد التوكن:** لا يوجد حالياً endpoint مستقل للتجديد الصامت (`refresh`). استخدام `POST .../call/token` يُصدر توكناً جديداً لكنه يُطلق أيضاً حدث `accepted`. (إذا كانت المكالمات تتجاوز الساعة، يُفضّل إضافة endpoint مخصص مثل `call/refresh_token`).

---

## 4) الرسائل والصلاحيات

1. **ترتيب الصفحات في `GET /messages`:**
   * الترتيب الحالي في الباك إند هو `orderBy('id', 'asc')`، أي أن **`page=1` تُرجع أقدم 50 رسالة أولاً**.
   * *(ملاحظة: إذا رغب الفرونت إند أن تكون `page=1` هي أحدث 50 رسالة لسهولة الـ Infinite Scroll للأعلى، يمكن تغيير الترتيب في الباك إند إلى `desc`).*

2. **وصول الضيف (Guest) بدون تسجيل دخول:**
   * **غير مسموح.** جميع مسارات الشات والمكالمات تتطلب مستخدماً مصادقاً (`401 Unauthenticated` عند عدم وجود توكن صالح).

---

## 5) الأمان (Agora App Certificate)

* **`AGORA_APP_CERTIFICATE` محفوظ حصراً في ملف `.env` على السيرفر** ولا يُمرر في أي استجابة API أو داخل تطبيقات العملاء.
* يستلم تطبيق العميل ولوحة الأدمن فقط `app_id` و `token` من `call/start` و `call/token`.
* **إجراء أمني واجب التنفيذ فوراً:** نظراً لتداول الـ `App Certificate` سابقاً في محادثة، يجب تدويره (**Rotate**) فوراً:
  1. الدخول إلى **Agora Console → Project Management → Config**.
  2. تفعيل/توليد **Secondary Certificate** وتحديث قيمة `AGORA_APP_CERTIFICATE` في `.env` على السيرفر.
  3. حذف الشهادة القديمة المكشوفة (**Delete Primary Certificate**).

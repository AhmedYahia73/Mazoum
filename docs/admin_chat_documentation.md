# دليل تكامل شات لوحة الإدارة والموظفين والـ Realtime
# Admin & Employee Chat API & Realtime Integration Guide

دليل توثيقي شامل لمطوري واجهات لوحة التحكم (Dashboard - Vue.js / React / Web / Mobile) للربط مع نظام المحادثات اللحظية، وإدارة محادثات العملاء، وإرسال الردود النصية والصور والتسجيلات الصوتية (`Voice Files`)، مع شرح مفصل لقواعد وصلاحيات الموظف (`Employee Permissions`).

---

## 📌 معلومات الاتصال الأساسية (Base Information)

- **Base URL:** `https://mazoom.online`
- **Admin Chat API:** `https://mazoom.online/api/admin/user_chat` (أو مسارات لوحة التحكم `https://mazoom.online/admin/user_chat`)
- **Authentication:** مطلوب لجميع الـ Endpoints (Sanctum Bearer Token / Admin Guard)

---

## 🛡️ قواعد وصلاحيات الوصول (Access & Permission Rules)

النظام يميّز بدقة بين نوعين من المسؤولين:

1. **الأدمن العام (`role: admin`)**:
   - لديه وصول كامل لجميع المحادثات وسجل كافة المستخدمين.
   - تظهر لديه كل المحادثات في قائمة المحادثات.
   - يمكنه المراسلة وقراءة الرسائل لأي مستخدم في المنصة.

2. **الموظف (`role: employee`)**:
   - **مقيد بصلاحيات الفعاليات المسندة إليه فقط**:
     $$\text{Events.assistant\_id} = \text{Auth::user()->id}$$
   - لا يستطيع الموظف رؤية أو مراسلة إلا المستخدمين الذين ينتمون لفعالياته:
     - **صاحب المناسبة / العميل:** `events.user_id`.
     - **المستخدمين الفرعيين للمناسبة:** `events.sub_user` حيث `users.event_id = events.id` أو `users.user_id = events.user_id`.
   - عند طلب قائمة المحادثات، تُفلتر القائمة تلقائياً لتعرض فقط العملاء المصرح له بمحادثتهم.
   - عند محاولة فتح محادثة أو إرسال رسالة أو قراءة رسائل مستخدم لا ينتمي لفعالياته، ترجع الـ API فوراً خطأ `403 Forbidden`:
     ```json
     {
       "status": false,
       "message": "غير مصرح لك بعرض محادثة هذا المستخدم حيث أنه ليس ضمن الفعاليات المسندة إليك."
     }
     ```

---

## 📌 الهيدرز المشتركة (Common Headers)

تتطلب جميع طلبات الـ API تمرير الهيدرز التالية:

| Header | النوع | مطلوب | الوصف | مثال |
|---|---|---|---|---|
| `Authorization` | String | **نعم** | توكن مصادقة الأدمن أو الموظف (Sanctum) | `Bearer 15\|b8e39d...` |
| `token` | String | بديل | توكن مخصص (إذا كان النظام يعتمد التوكن المباشر) | `b8e39d...` |
| `Accept` | String | **نعم** | صيغة الرد المطلوبة | `application/json` |
| `Accept-Language` | String | اختياري | لغة الردود (`ar` أو `en`) - الافتراضي: `ar` | `ar` |

---

## 🚀 قائمة الـ APIs الخاصة بالإدارة والموظفين

### 1. جلب قائمة المحادثات والعملاء (Get Conversations List)

تُستخدم لعرض صندوق الوارد (Inbox) لجميع العملاء الذين لديهم محادثات، مع عدد الرسائل غير المقروءة لكل عميل وملخص آخر رسالة.

- **Endpoint:** `/api/admin/user_chat/conversations` (أو `/admin/user_chat/conversations`)
- **Method:** `GET`

#### معلمات الاستعلام (Query Parameters):

| المعلمة | النوع | مطلوب | الافتراضي | الوصف |
|---|---|---|---|---|
| `page` | Integer | اختياري | `1` | رقم صفحة الترقيم |
| `per_page` | Integer | اختياري | `30` | عدد المحادثات في كل صفحة |
| `search` | String | اختياري | `null` | البحث في المحادثات باسم العميل، جواله، أو إيميله |

#### مثال على استجابة النجاح (200 OK):
```json
{
  "status": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "user_id": 42,
        "name": "أحمد الشمري",
        "mobile": "966500000000",
        "email": "ahmed@example.com",
        "unread_count": 3,
        "last_message": "مرحباً، لدي استفسار بخصوص مناسبتي القادمة",
        "last_message_voice": "https://mazoom.online/storage/user_chats/voices/kL89N2...m4a",
        "last_message_image": null,
        "last_message_date": "2026-11-23",
        "last_message_time": "07:15:30 AM",
        "last_sender": "user"
      },
      {
        "user_id": 18,
        "name": "سارة الخالدي",
        "mobile": "966511111111",
        "email": "sara@example.com",
        "unread_count": 0,
        "last_message": "تم إرسال بطاقة الدعوة بنجاح",
        "last_message_voice": null,
        "last_message_image": "https://mazoom.online/storage/user_chats/images/card.png",
        "last_message_date": "2026-11-22",
        "last_message_time": "04:30:10 PM",
        "last_sender": "admin"
      }
    ],
    "first_page_url": "https://mazoom.online/api/admin/user_chat/conversations?page=1",
    "from": 1,
    "last_page": 1,
    "per_page": 30,
    "total": 2
  }
}
```

---

### 2. جلب سجل رسائل محادثة مع عميل محدد (Get Conversation Messages)

تُستخدم عند نقر الأدمن أو الموظف على محادثة عميل معين لفتح نافذة الشات وتحميل السجل.

- **Endpoint:** `/api/admin/user_chat/messages/{user_id}`
- **Method:** `GET`

#### معلمات المسار والاستعلام (Parameters):
- **Route Param:** `{user_id}` (رقم المستخدم المستهدف - Integer - إلزامي).
- **Query Params:**
  - `page` (Integer - اختياري - الافتراضي: 1)
  - `per_page` (Integer - اختياري - الافتراضي: 50)

#### مثال على استجابة النجاح (200 OK):
```json
{
  "status": true,
  "data": {
    "user": {
      "id": 42,
      "name": "أحمد الشمري",
      "mobile": "966500000000",
      "email": "ahmed@example.com"
    },
    "messages": {
      "current_page": 1,
      "data": [
        {
          "id": 105,
          "user_id": 42,
          "admin_id": null,
          "is_admin": false,
          "msg": "مرحباً، لدي استفسار بخصوص مناسبتي القادمة",
          "is_admin_read": false,
          "is_user_read": true,
          "image": null,
          "voice": "user_chats/voices/kL89N2...m4a",
          "image_url": null,
          "voice_url": "https://mazoom.online/storage/user_chats/voices/kL89N2...m4a",
          "date": "2026-11-23",
          "time": "07:15:30 AM"
        }
      ],
      "per_page": 50,
      "total": 1
    }
  }
}
```

#### استجابة الرفض في حال عدم امتلاك الموظف صلاحية لهذا العميل (403 Forbidden):
```json
{
  "status": false,
  "message": "غير مصرح لك بعرض محادثة هذا المستخدم حيث أنه ليس ضمن الفعاليات المسندة إليك."
}
```

---

### 3. إرسال رسالة من الأدمن / الموظف للعميل (Send Message to User)

تُستخدم للرد على العميل أو بدء مراسلته، وتدعم النص أو الصورة أو التسجيل الصوتي.

- **Endpoint:** `/api/admin/user_chat/send_message`
- **Method:** `POST`
- **Content-Type:** `multipart/form-data`

#### معلمات الطلب (Body Form-Data):

| الحقل (Key) | النوع | إلزامي | الوصف | القيود |
|---|---|---|---|---|
| `user_id` | Integer | **نعم** | معرّف العميل المراد مراسلته | يجب أن يكون موجوداً في جدول `users` |
| `msg` | Text | اختياري* | نص الرسالة | أقصى حد: نص مفتوح |
| `image` | File | اختياري* | صورة مرفقة | `jpeg, png, jpg, gif, webp` (بحد أقصى 20MB) |
| `voice` | File | اختياري* | ملف تسجيل صوتي (`Voice File`) | `mp3, m4a, wav, ogg, aac, webm, 3gp` (بحد أقصى 50MB) |

> ⚠️ **تنبيه:** يجب إرسال واحد على الأقل من (`msg` أو `image` أو `voice`) بالإضافة إلى `user_id`.

#### مثال على استجابة النجاح (200 OK):
```json
{
  "status": true,
  "message": "تم إرسال الرسالة بنجاح",
  "data": {
    "id": 106,
    "user_id": 42,
    "admin_id": 3,
    "is_admin": true,
    "msg": "أهلاً بك، تم مراجعة تفاصيل المناسبة وسنتواصل معك فوراً",
    "is_admin_read": true,
    "is_user_read": false,
    "image": null,
    "voice": "user_chats/voices/admin_reply.mp3",
    "image_url": null,
    "voice_url": "https://mazoom.online/storage/user_chats/voices/admin_reply.mp3",
    "date": "2026-11-23",
    "time": "07:20:00 AM",
    "created_at": "2026-11-23T05:20:00.000000Z",
    "updated_at": "2026-11-23T05:20:00.000000Z"
  }
}
```

---

### 4. تعليم جميع رسائل العميل كمقروءة من جانب الأدمن (Mark Conversation Read by Admin)

تُستدعى فور فتح الأدمن للمحادثة، لتعليم كل الرسائل الواردة من هذا المستخدم كرسائل مقروءة (`is_admin_read = true`).

- **Endpoint:** `/api/admin/user_chat/read_all/{user_id}`
- **Method:** `POST` (يدعم أيضاً `GET` أو تمرير `user_id` في الـ Request Body)

#### مثال على استجابة النجاح (200 OK):
```json
{
  "status": true,
  "user_id": 42,
  "reader": "admin",
  "updated_count": 3,
  "message": "تم تحديث حالة القراءة بنجاح / Conversation marked as read successfully"
}
```

> ⚡ **تأثير لحظي (Realtime Trigger):** هذا الاستدعاء يُطلق فورياً حدث `user_chat_read_event` إلى تطبيق العميل لتظهر الرسائل لديه بعلامة الصحين الزرقاء المقروءة.

---

## ⚡ التكامل اللحظي في لوحة التحكم (Dashboard Real-Time WebSockets)

يتيح نظام الـ WebSockets للوحة التحكم تحديث عداد الرسائل غير المقروءة، وإظهار الرسائل الواردة فوراً، وتحديث علامات القراءة دون إعادة تحميل الصفحة.

### 1. إعدادات Pusher / Echo

- **Broadcaster:** `pusher`
- **Key:** `239duDE3289768hggdorkxsmFS234521` (مأخوذ من متغيرات البيئة `PUSHER_APP_KEY`).
- **Cluster:** `mt1`
- **wsHost:** `mazoom.online`
- **wsPort / wssPort:** `443` (`forceTLS: true`)
- **Auth Endpoint:** `POST https://mazoom.online/api/broadcasting/auth` (يقبل `Authorization: Bearer` أو `token`)

---

### 2. القنوات والأحداث المخصصة للوحة التحكم (Channels & Events)

#### القناة 1: قناة لوحة التحكم العامة (`admin-user-chat`)
- **الغرض:** الاستماع العام لجميع الرسائل الواردة من كل المستخدمين لتحديث قائمة المحادثات (Inbox) والشارات (Badges) في شريط التنقل.
- **اسم القناة:**
  ```
  admin-user-chat
  ```

#### القناة 2: قناة المحادثة الفردية (`user-chat.{user_id}`)
- **الغرض:** الاستماع اللحظي للرسائل وقراءة الرسائل أثناء فتح الأدمن أو الموظف لشاشة الشات مع عميل محدد.
- **اسم القناة:**
  ```
  user-chat.{user_id}
  ```
  *مثال للعميل رقم 42:* `user-chat.42`.

---

### 3. الأحداث وكيفية التعامل معها في الفرونت إند:

#### أ) حدث رسالة جديدة (`user_chat_event`):
يتم بثه على القناتين (`admin-user-chat` و `user-chat.{user_id}`).

**الـ Payload:**
```json
{
  "id": 107,
  "user_id": 42,
  "admin_id": null,
  "msg": "أرسلت لكم ملف الصوت الموضح",
  "image": null,
  "voice": "https://mazoom.online/storage/user_chats/voices/sound.m4a",
  "is_admin": false,
  "is_admin_read": false,
  "is_user_read": true,
  "date": "2026-11-23",
  "time": "07:25:00 AM",
  "created_at": "2026-11-23T05:25:00.000000Z"
}
```

**منطق لوحة التحكم (Dashboard Logic):**
1. **في قائمة المحادثات (Conversations Sidebar / Inbox):**
   - نقل المحادثة الخاصة بـ `user_id` إلى أعلى القائمة.
   - تحديث `last_message` بالنص أو `[Voice]` أو `[Image]`.
   - زيادة الـ `unread_count` بمقدار 1 (إذا لم تكن المحادثة مفتوحة حالياً أمام الأدمن).
   - تشغيل صوت تنبيه إشعار خفيف.
2. **في شاشة المحادثة المفتوحة (Active Chat View):**
   - إذا كان `event.user_id === activeUserId`:
     - إضافة الرسالة مباشرة إلى أسفل المحادثة وعمل Scroll تلقائي للأسفل.
     - استدعاء `POST /api/admin/user_chat/read_all/{user_id}` فوراً لتعليمها كمقروءة وإلغاء الـ Badge.

---

#### ب) حدث قراءة العميل للرسائل (`user_chat_read_event`):
يتم بثه عندما يقوم العميل بفتح تطبيقه وقراءة الرسائل التي أرسلها الأدمن/الموظف.

**الـ Payload:**
```json
{
  "user_id": 42,
  "reader": "user",
  "updated_count": 2,
  "date": "2026-11-23",
  "time": "07:26:15 AM"
}
```

**منطق لوحة التحكم (Dashboard Logic):**
- إذا كان `reader === "user"`: قم بتحديث علامات صح الرسائل المرسلة من الأدمن (`is_admin === true`) في شاشة المحادثة المفتوحة لتصبح مقروءة (Double Checkmarks).

---

## 💻 نماذج برمجية جاهزة للوحة التحكم (Vue.js / React Example)

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// 1. تهيئة اتصال Laravel Echo
const echo = new Echo({
    broadcaster: 'pusher',
    key: 'YOUR_PUSHER_APP_KEY',
    cluster: 'mt1',
    forceTLS: true,
    authEndpoint: '/api/broadcasting/auth',
    auth: {
        headers: {
            Authorization: `Bearer ${adminAuthToken}`
        }
    }
});

// 2. الاستماع لقناة الأدمن العامة لتحديث قائمة المحادثات والـ Badges
echo.channel('admin-user-chat')
    .listen('.user_chat_event', (message) => {
        console.log("رسالة واردة جديدة في النظام:", message);
        
        // تحديث صندوق المحادثات
        updateConversationInList(message);
    });

// 3. الاستماع لمحادثة عميل نشط حالياً على الشاشة
function openUserChat(userId) {
    // إلغاء الاشتراك في المحادثة السابقة إن وجدت
    if (currentChannel) {
        echo.leave(`user-chat.${previousUserId}`);
    }

    // الاشتراك في محادثة المستخدم الحالي
    echo.channel(`user-chat.${userId}`)
        .listen('.user_chat_event', (newMessage) => {
            if (!newMessage.is_admin) {
                // رسالة قادمة من العميل
                chatMessages.push(newMessage);
                
                // تعليمها كمقروءة فورياً
                markConversationAsRead(userId);
            }
        })
        .listen('.user_chat_read_event', (readEvent) => {
            if (readEvent.reader === 'user') {
                // العميل قرأ رسائلنا، حدّث علامة الصحين في الشاشة
                chatMessages.forEach(msg => {
                    if (msg.is_admin) msg.is_user_read = true;
                });
            }
        });

    // استدعاء API القراءة عند أول فتح للمحادثة
    markConversationAsRead(userId);
}
```

---

## 📞 خاصية المكالمات الصوتية والمرئية للأدمن والموظف (Agora.io Calling)

تتيح للأدمن والموظف المصرح له الاتصال بالعميل أو استقبال مكالماته مباشرة عبر الويب أو التطبيق:

### 1. بدء مكالمة من الأدمن / الموظف للعميل (Start Call to User)
- **Endpoint:** `POST /api/admin/user_chat/call/start`
- **Body:**
  - `user_id`: رقم العميل المراد الاتصال به (مطلوب).
  - `call_type`: `"voice"` أو `"video"` (اختياري - الافتراضي: `"voice"`).
- **فحص الصلاحية:**
  - إذا كان المتصل **موظفاً (`employee`)**، يتحقق النظام من شرط:
    `Events.assistant_id == Auth::id()`
    إذا كان العميل لا ينتمي لفعالياته، يرفض الطلب فوراً بـ `403 Forbidden`.
- **الرد (200 OK):**
  ```json
  {
    "status": true,
    "channel_name": "call_u42_1700000000_123",
    "token": "006970ca35de60c4464a8038a14ec10882dIAB...",
    "app_id": "YOUR_AGORA_APP_ID",
    "uid": 1,
    "call_type": "voice"
  }
  ```
  *تنضم لوحة التحكم للقناة وتظهر شاشة الرنين بانتظار رد العميل.*

### 2. قبول مكالمة واردة من عميل (Accept Call from User)
- **Endpoint:** `POST /api/admin/user_chat/call/token`
- **Body:**
  - `channel_name`: اسم القناة المستلمة في إشعار الرنين اللحظي.
  - `user_id`: معرّف العميل.

### 3. إنهاء أو رفض المكالمة (End / Reject Call)
- **Endpoint:** `POST /api/admin/user_chat/call/end`
- **Body:**
  - `channel_name`: اسم القناة.
  - `reason`: `"ended"` أو `"rejected"` أو `"busy"` أو `"cancelled"`.
  - `user_id`: معرّف العميل.

### 4. أحداث المكالمات اللحظية في لوحة التحكم (`user_call_event`)
تستمع لوحة التحكم للقناتين `admin-user-chat` و `user-chat.{user_id}`:
- **مكالمة واردة من عميل (`action: "incoming"`):**
  - تظهر نافذة منبثقة (Popup / Modal) في لوحة التحكم بها نغمة رنين واسم العميل وزرّي "قبول" و "رفض".
- **العميل قبل المكالمة (`action: "accepted"`):**
  - يبدأ تدفق الصوت/الفيديو فوراً.
- **انتهاء أو رفض المكالمة (`action: "ended"` / `"rejected"`):**
  - إغلاق نافذة المكالمة فوراً وإيقاف الصوت.


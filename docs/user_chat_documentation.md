# دليل تكامل شات ومكالمات العميل والـ Realtime
# User Chat & Calls API + Realtime Integration Guide

هذا الدليل مخصص لفريق تطوير واجهة **المستخدم (العميل)** (Flutter / React Native / Web) ويشمل كافة القنوات (`Channels`)، والأحداث اللحظية (`Events` & `Listeners`)، وواجهات الـ API الخاصة بالشات والمكالمات الصوتية/المرئية بمفاتيح موحّدة بدون تكرار.

---

## 📌 1. معلومات الاتصال الأساسية والهيدرز

- **Base URL:** `https://mazoom.online`
- **Authentication:** مطلوب لجميع الطلبات (تُمنع حسابات الضيوف).

### الهيدرز المشتركة (Common Headers):
| Header | النوع | مطلوب | الوصف | مثال |
|---|---|---|---|---|
| `token` | String | **نعم** (أو `Authorization`) | توكن العميل العائد من تسجيل الدخول | `a1b2c3d4e5f6...` |
| `Authorization` | String | بديل | توكن Sanctum في حال استخدامه | `Bearer 45\|xyz...` |
| `Accept` | String | **نعم** | صيغة الرد المطلوبة | `application/json` |
| `Accept-Language` | String | اختياري | لغة الردود (`ar` أو `en`) — الافتراضي: `ar` | `ar` |

---

## 📡 2. إعدادات Pusher / WebSockets والقنوات والأحداث

### إعدادات الاتصال (Connection Config):
- **Broadcaster:** `pusher`
- **App Key:** `239duDE3289768hggdorkxsmFS234521`
- **Cluster:** `mt1`
- **wsHost:** `mazoom.online`
- **wsPort / wssPort:** `443` (`forceTLS: true`)
- **Auth Endpoint (للقناة الخاصة):** `POST https://mazoom.online/api/broadcasting/auth`
  - **Headers:** `token: <user_token>` (أو `Authorization: Bearer <token>`) و `Accept: application/json`

### القناة الخاصة بالعميل (Channel):
| المكتبة | اسم القناة |
|---|---|
| **Pusher SDK** (Flutter / Native) | `private-user-chat.{user_id}` *(مثال: `private-user-chat.42`)* |
| **Laravel Echo** (Web) | `Echo.private('user-chat.' + user_id)` *(يضيف `private-` تلقائياً)* |

> `user_id` في اسم القناة هو نفسه `user.id` العائد من تسجيل الدخول، ونفسه `data.user.id` في رد `messages`.

### جدول الأحداث اللحظية (Events & Listeners):
> **ملاحظة الـ Listener:** في **Pusher SDK** استمع باسم الحدث مباشرة (`user_chat_event`)؛ وفي **Laravel Echo** أضف نقطة قبله (`.user_chat_event`).

| Event Name (`Pusher`) | Echo Listener | الوصف | الحقول في الـ Payload |
|---|---|---|---|
| `user_chat_event` | `.user_chat_event` | رسالة جديدة واردة من الإدارة (أو تأكيد/تزامن رسالة صادرة) | كائن الرسالة الموحّد الكامل (انظر القسم 3) |
| `user_chat_delivered_event` | `.user_chat_delivered_event` | تم توصيل رسائلك لجهاز الإدارة | `{ "user_id": 42, "message_ids": [120], "delivered_by": "admin", "delivered_at": "..." }` |
| `user_chat_read_event` | `.user_chat_read_event` | قرأ الأدمن/الموظف رسائلك | `{ "user_id": 42, "reader": "admin", "updated_count": 2, "read_at": "...", "date": "...", "time": "..." }` |
| `user_chat_reaction_event` | `.user_chat_reaction_event` | إضافة / تغيير / إزالة تفاعل إيموجي | `{ "user_id": 42, "message_id": 120, "actor_type": "admin", "actor_id": 1, "emoji": "❤️", "reactions": [...] }` |
| `user_chat_edited_event` | `.user_chat_edited_event` | تعديل نص رسالة | `{ "user_id": 42, "message_id": 120, "msg": "النص المعدل", "is_edited": true, "edited_at": "..." }` |
| `user_chat_deleted_event` | `.user_chat_deleted_event` | حذف رسالة للجميع | `{ "user_id": 42, "message_id": 120, "is_deleted": true, "deleted_at": "..." }` |
| `user_chat_pinned_event` | `.user_chat_pinned_event` | تثبيت أو إلغاء تثبيت رسالة | `{ "user_id": 42, "message_id": 120, "is_pinned": true, "pinned_at": "..." }` |
| `user_chat_disappearing_event` | `.user_chat_disappearing_event` | تغيير مدة الرسائل المؤقتة | `{ "user_id": 42, "disappearing_seconds": 86400 }` |
| `user_chat_typing_event` | `.user_chat_typing_event` | مؤشر كتابة الأدمن عبر الـ API | `{ "user_id": 42, "sender_type": "admin", "sender_id": 1, "is_typing": true }` |
| `client-typing` | `listenForWhisper('typing')` | **مؤشر الكتابة المباشر** على القناة الخاصة (بدون HTTP) | `{ "sender_type": "admin", "sender_id": 1, "is_typing": true }` |
| `user_call_event` | `.user_call_event` | إشعارات المكالمات (`incoming`, `accepted`, `rejected`, `ended`, `missed`, `busy`, `cancelled`) | انظر قسم المكالمات (القسم 5) |

---

## 📦 3. هيكل كائن الرسالة الموحّد (Message Object)

جميع الـ APIs وحدث `user_chat_event` ترجع نفس الكائن الموحّد (بدون تكرار مفاتيح):

```json
{
  "id": 120,
  "client_msg_id": "550e8400-e29b-41d4-a716-446655440000",
  "user_id": 42,
  "admin_id": null,
  "is_admin": false,
  "msg": "مرحباً، لدي استفسار بخصوص مناسبتي",
  "image": "https://mazoom.online/storage/user_chats/images/pic.jpg",
  "voice": "https://mazoom.online/storage/user_chats/voices/note.m4a",
  "delivery_status": "delivered",
  "is_delivered": true,
  "delivered_at": "2026-11-26T01:10:00+02:00",
  "is_admin_read": false,
  "admin_read_at": null,
  "is_user_read": true,
  "user_read_at": "2026-11-26T01:09:50+02:00",
  "reply_to": {
    "id": 115,
    "is_admin": true,
    "is_deleted": false,
    "msg": "أهلاً بك، تفضل بسؤالك",
    "image": null,
    "voice": null
  },
  "is_forwarded": false,
  "is_edited": false,
  "edited_at": null,
  "is_deleted": false,
  "deleted_at": null,
  "is_pinned": false,
  "pinned_at": null,
  "is_favorite": false,
  "reactions": [
    {
      "emoji": "❤️",
      "count": 1,
      "actors": [{ "type": "admin", "id": 1 }]
    }
  ],
  "my_reaction": null,
  "expires_at": null,
  "date": "2026-11-26",
  "time": "01:09:50 AM",
  "created_at": "2026-11-26T01:09:50+02:00"
}
```

### حالات الرسالة الخمس:
1. **`sending` (جاري الإرسال):** تُعرض محلياً فور ضغط زر الإرسال مع توليد `client_msg_id` (UUID).
2. **`failed` (فشل الإرسال + إعادة المحاولة):** عند فشل الاتصال يظهر زر «إعادة المحاولة» ويرسل نفس الطلب بنفس `client_msg_id` (الباك إند يمنع التكرار إذا كانت قد حُفظت).
3. **`sent` (اتبعتت):** `delivery_status === "sent"` (علامة صح واحدة).
4. **`delivered` (وصلت):** `delivery_status === "delivered"` (علامتا صح رماديتان).
5. **`read` (اتقرت):** `delivery_status === "read"` (علامتا صح زرقاوان).

---

## 🚀 4. واجهات الـ APIs الخاصة بالمستخدم

### 1) إرسال رسالة (نص / صورة / صوت + الرد على رسالة)
- **Endpoint:** `POST /api/user/user_chat/send_message`
- **Content-Type:** `multipart/form-data`
- **Body:**
  | الحقل | النوع | مطلوب | الوصف |
  |---|---|---|---|
  | `msg` | Text | اختياري* | نص الرسالة |
  | `image` | File | اختياري* | صورة (`jpeg, png, jpg, gif, svg, webp` حتى 20MB) |
  | `voice` | File | اختياري* | ملف صوتي (`mp3, m4a, wav, ogg, aac, webm, 3gp` حتى 50MB) |
  | `client_msg_id` | String (max 64) | اختياري (موصى به) | معرّف مؤقت لمنع التكرار عند إعادة المحاولة |
  | `reply_to_id` | Integer | اختياري | معرّف الرسالة المُقتبَس منها للرد عليها |
  *(يجب إرسال واحد على الأقل من `msg` أو `image` أو `voice`).*
- **Response (200 OK):**
  ```json
  {
    "status": true,
    "message": "تم إرسال الرسالة بنجاح",
    "data": { /* <Message Object> */ }
  }
  ```
- **Errors:** `400` (محتوى فارغ), `401` (غير مصادق), `422` (بيانات غير صالحة أو `reply_to_id` خارج المحادثة).

---

### 2) جلب سجل رسائل المحادثة (مع دعم القفز للرسالة المقتبسة)
- **Endpoint:** `GET /api/user/user_chat/messages`
- **Query Params:**
  | المعلمة | النوع | الافتراضي | الوصف |
  |---|---|---|---|
  | `page` | Integer | `1` | رقم الصفحة (`page=1` هي الأقدم أولاً) |
  | `per_page` | Integer | `50` | عدد الرسائل في الصفحة |
  | `around_id` | Integer | `null` | عند ضغط العميل على اقتباس `reply_to`، مرّر `around_id={reply_to.id}` ليرجع لك الباك إند الصفحة التي تحتوي على تلك الرسالة مباشرة |
- **Response (200 OK):**
  ```json
  {
    "status": true,
    "data": {
      "user": { "id": 42, "name": "أحمد الشمري", "mobile": "966500000000", "email": "ahmed@example.com" },
      "disappearing_seconds": null,
      "messages": {
        "current_page": 1,
        "data": [ /* <Message Objects> */ ],
        "last_page": 1,
        "per_page": 50,
        "total": 12
      }
    }
  }
  ```

---

### 3) تأكيد وصول الرسائل (Delivered)
يُستدعى عند استلام حدث `user_chat_event` من الإدارة أو عند جلب رسائل جديدة.
- **Endpoint:** `POST /api/user/user_chat/delivered` $\leftarrow$ **المسار الموحّد:** `POST /api/user_chat/delivered`
- **Body (JSON):** `{ "message_ids": [121, 122] }` *(اختياري — إذا تُرك فارغاً يعلّم كل الرسائل الواردة غير المؤكدة).*
- **Response (200 OK):**
  ```json
  {
    "status": true,
    "data": {
      "user_id": 42,
      "message_ids": [121, 122],
      "updated_count": 2,
      "delivered_at": "2026-11-26T01:10:00+02:00"
    }
  }
  ```

---

### 4) تعليم جميع رسائل المحادثة كمقروءة (Read All)
- **Endpoint:** `POST /api/user/user_chat/read_all`
- **Response (200 OK):**
  ```json
  {
    "status": true,
    "user_id": 42,
    "reader": "user",
    "updated_count": 2,
    "read_at": "2026-11-26T01:11:00+02:00",
    "message": "تم تحديث حالة القراءة بنجاح / Conversation marked as read successfully"
  }
  ```

---

### 5) التفاعل بإيموجي (إضافة / تغيير / إزالة)
- **Endpoint:** `POST /api/user_chat/messages/{id}/react`
- **Path Param:** `{id}` (معرّف الرسالة)
- **Body (JSON):** `{ "emoji": "❤️" }`
  - إرسال إيموجي جديد: يضيف التفاعل أو يغيّر التفاعل السابق.
  - إرسال نفس الإيموجي الحالي أو `null`: يزيل التفاعل.
- **Response (200 OK):**
  ```json
  {
    "status": true,
    "data": {
      "message_id": 120,
      "reactions": [{ "emoji": "❤️", "count": 1, "actors": [{ "type": "user", "id": 42 }] }],
      "my_reaction": "❤️"
    }
  }
  ```

---

### 6) تعديل رسالة العميل
- **الصلاحية:** متاح للعميل تعديل رسائله خلال **15 دقيقة** من الإرسال فقط.
- **Endpoint:** `PUT /api/user_chat/messages/{id}`
- **Body (JSON):** `{ "msg": "النص المعدل" }`
- **Response (200 OK):** `{ "status": true, "data": <Updated Message Object> }` *(مع `is_edited: true`)*
- **Error (403):** عند انتهاء الـ 15 دقيقة أو محاولة تعديل رسالة الأدمن.

---

### 7) حذف رسالة («حذف عندي» أو «حذف للجميع»)
- **Endpoint:** `DELETE /api/user_chat/messages/{id}`
- **Body (JSON):** `{ "scope": "me" }` أو `{ "scope": "everyone" }`
  - `"me"`: متاح في أي وقت، ويخفي الرسالة من شاشة العميل فقط.
  - `"everyone"`: متاح لرسائل العميل خلال **60 دقيقة** فقط؛ يحذف النص والوسائط لدى الطرفين (`is_deleted: true`).
- **Response (200 OK):**
  ```json
  {
    "status": true,
    "data": { "message_id": 120, "scope": "everyone", "deleted_at": "2026-11-26T01:15:00+02:00" }
  }
  ```

---

### 8) إعادة التوجيه والنسخ
- **النسخ:** يتم محلياً في الفرونت إند من حقل `msg` (يُمنع للرسائل المحذوفة `is_deleted: true`).
- **إعادة التوجيه:** `POST /api/user_chat/messages/{id}/forward` (لإعادة إرسال رسالة داخل محادثة العميل مع شارة `is_forwarded: true`).
- **Response (200 OK):** `{ "status": true, "data": [ <Forwarded Message Object> ] }`

---

### 9) تثبيت الرسائل (Pin — متاح للعميل والأدمن)
- **تثبيت / إلغاء تثبيت:** `POST /api/user_chat/messages/{id}/pin`
  - **Body (اختياري):** `{ "is_pinned": true }` (عند عدم الإرسال يبدّل الحالة تلقائياً).
  - **Response (200 OK):** `{ "status": true, "data": { "message_id": 120, "is_pinned": true, "pinned_at": "..." } }`
- **عرض المثبتة:** `GET /api/user_chat/pinned`
  - **Response (200 OK):** `{ "status": true, "data": [ /* <Message Objects> */ ] }`

---

### 10) المفضلة (Favorites — خاصة بالعميل)
- **إضافة / إزالة:** `POST /api/user_chat/messages/{id}/favorite`
  - **Response (200 OK):** `{ "status": true, "data": { "message_id": 120, "is_favorite": true } }`
- **عرض قائمة المفضلة:** `GET /api/user_chat/favorites?per_page=30`
  - **Response (200 OK):** `{ "status": true, "data": { /* Pagination of <Message Objects> */ } }`

---

### 11) الرسائل المؤقتة (Disappearing Messages)
- **Endpoint:** `POST /api/user_chat/disappearing`
- **Body (JSON):** `{ "disappearing_seconds": 86400 }` *(أو `null` للإيقاف؛ الحد الأدنى `60` ثانية)*
- **Response (200 OK):** `{ "status": true, "data": { "user_id": 42, "disappearing_seconds": 86400 } }`

---

### 12) مؤشر الكتابة (Typing Dots)
- **الطريقة الأسرع (موصى بها):** أرسل Client Event `client-typing` مباشرة على `private-user-chat.{user_id}` عبر Pusher:
  `{ "sender_type": "user", "sender_id": 42, "is_typing": true }`
- **أو عبر API:** `POST /api/user_chat/typing` مع Body `{ "is_typing": true }` (يُطلق `user_chat_typing_event`).
- يُنصح بعمل Auto-hide بعد 4–5 ثوانٍ إذا توقفت تحديثات الكتابة.

---

## 📞 5. المكالمات الصوتية والمرئية (Agora.io Voice & Video)

1. **بدء المكالمة:** `POST /api/user/user_chat/call/start` — Body: `{ "call_type": "voice" }` (`voice` أو `video`)
   - يرجع: `{ "status": true, "channel_name": "...", "token": "...", "app_id": "...", "uid": 42, "call_type": "voice" }`
   - صلاحية التوكن: **ساعة واحدة (3600 ثانية)**.
2. **قبول مكالمة واردة:** `POST /api/user/user_chat/call/token` — Body: `{ "channel_name": "..." }`
3. **إنهاء / رفض المكالمة:** `POST /api/user/user_chat/call/end` — Body: `{ "channel_name": "...", "reason": "ended" }` (`ended` | `rejected` | `missed` | `busy` | `cancelled`)

### أمثلة Payloads حدث `user_call_event`:
- **`incoming`:** `{ "action": "incoming", "user_id": 42, "channel_name": "call_u42_...", "call_type": "voice", "caller_id": 1, "caller_name": "الدعم الفني", "caller_role": "admin", "receiver_id": 42, "date": "...", "time": "...", "timestamp": 1795462200 }`
- **`accepted`:** `{ "action": "accepted", "user_id": 42, "channel_name": "call_u42_...", "accepter_id": 42, "date": "...", "time": "...", "timestamp": 1795462215 }`
- **`ended` / `rejected`:** `{ "action": "ended", "user_id": 42, "channel_name": "call_u42_...", "ended_by": 42, "reason": "ended", "date": "...", "time": "...", "timestamp": 1795462500 }`

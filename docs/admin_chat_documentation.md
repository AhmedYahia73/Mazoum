# دليل تكامل شات ومكالمات لوحة الإدارة والموظفين والـ Realtime
# Admin & Employee Chat & Calls API + Realtime Integration Guide

هذا الدليل مخصص لفريق تطوير **لوحة التحكم (Admin / Employee Dashboard)** ويشمل كافة القنوات (`Channels`)، والأحداث اللحظية (`Events` & `Listeners`)، وواجهات الـ API الخاصة بالشات والمكالمات بمفاتيح موحّدة بدون تكرار.

---

## 📌 1. معلومات الاتصال والصلاحيات والهيدرز

- **Base URL:** `https://mazoom.online`
- **Authentication:** مطلوب لجميع الطلبات (Sanctum Bearer Token).

### 🛡️ قواعد الصلاحيات (Admin vs Employee):
1. **الأدمن العام (`role === "admin"`):**
   - وصول كامل لكافة العملاء والمحادثات والمكالمات.
   - **تعديل الرسائل:** يقدر يعدّل أي رسالة مرسلة من جهة الإدارة **في أي وقت بدون قيد زمني**.
   - **حذف للجميع:** يقدر يحذف أي رسالة للجميع **في أي وقت**.
2. **الموظف (`role === "employee"`):**
   - محصور في العملاء المرتبطين بفعالياته فقط:
     `Events.assistant_id === Auth::user()->id` (`events.user_id` أو `events.sub_user`).
   - أي محاولة للوصول لمستخدم غير مسند إليه ترجع **`403 Forbidden`**.
   - **تعديل الرسائل:** خلال **15 دقيقة** من الإرسال لرسائله فقط.
   - **حذف للجميع:** خلال **60 دقيقة** من الإرسال لرسائله فقط.
3. **الحذف عندي (`scope: "me"`):** يخفي الرسالة عن **كافة طاقم الإدارة والموظفين** (لا تظهر لأي أدمن أو موظف بعدها، وتبقى ظاهرة للعميل).
4. **تثبيت الرسائل (`Pin`):** متاح للطرفين (الإدارة والعميل).

### الهيدرز المشتركة (Common Headers):
| Header | النوع | مطلوب | الوصف | مثال |
|---|---|---|---|---|
| `Authorization` | String | **نعم** | توكن مصادقة الأدمن/الموظف (Sanctum) | `Bearer 15\|b8e39d...` |
| `Accept` | String | **نعم** | صيغة الرد المطلوبة | `application/json` |
| `Accept-Language` | String | اختياري | لغة الردود (`ar` أو `en`) | `ar` |

---

## 📡 2. إعدادات Pusher / WebSockets والقنوات والأحداث

### إعدادات الاتصال (Connection Config):
- **Broadcaster:** `pusher`
- **App Key:** `239duDE3289768hggdorkxsmFS234521`
- **Cluster:** `mt1`
- **wsHost:** `mazoom.online`
- **wsPort / wssPort:** `443` (`forceTLS: true`)
- **Auth Endpoint (للقنوات الخاصة):** `POST https://mazoom.online/api/broadcasting/auth`
  - **Headers:** `Authorization: Bearer <token>` و `Accept: application/json`

### القنوات المستخدمة (Channels):
| القناة | الاستخدام | نوع القناة |
|---|---|---|
| `admin-user-chat` | قناة لوحة التحكم العامة لتحديث صندوق الوارد (`Inbox`) وشارات الرسائل غير المقروءة والمكالمات الواردة | Public |
| `private-user-chat.{user_id}` | قناة المحادثة الفردية عند فتح شاشة الشات مع عميل محدد (في `Laravel Echo`: `Echo.private('user-chat.' + user_id)`) | Private (موثّقة عبر `/api/broadcasting/auth`) |

### جدول الأحداث اللحظية (Events & Listeners):
> **ملاحظة الـ Listener:** في **Laravel Echo** أضف نقطة قبل الاسم (`.user_chat_event`)؛ وفي **Pusher SDK** استمع بالاسم مباشرة (`user_chat_event`).

| Event Name (`Pusher`) | Echo Listener | الوصف | الحقول في الـ Payload |
|---|---|---|---|
| `user_chat_event` | `.user_chat_event` | رسالة جديدة (واردة من العميل أو صادرة) | كائن الرسالة الموحّد الكامل (انظر القسم 3) |
| `user_chat_delivered_event` | `.user_chat_delivered_event` | تم توصيل رسالة الإدارة لجهاز العميل | `{ "user_id": 42, "message_ids": [120], "delivered_by": "user", "delivered_at": "..." }` |
| `user_chat_read_event` | `.user_chat_read_event` | قرأ العميل رسائل الإدارة | `{ "user_id": 42, "reader": "user", "updated_count": 2, "read_at": "...", "date": "...", "time": "..." }` |
| `user_chat_reaction_event` | `.user_chat_reaction_event` | إضافة / تغيير / إزالة تفاعل إيموجي | `{ "user_id": 42, "message_id": 120, "actor_type": "user", "actor_id": 42, "emoji": "👍", "reactions": [...] }` |
| `user_chat_edited_event` | `.user_chat_edited_event` | تعديل نص رسالة | `{ "user_id": 42, "message_id": 120, "msg": "النص المعدل", "is_edited": true, "edited_at": "..." }` |
| `user_chat_deleted_event` | `.user_chat_deleted_event` | حذف رسالة للجميع | `{ "user_id": 42, "message_id": 120, "is_deleted": true, "deleted_at": "..." }` |
| `user_chat_pinned_event` | `.user_chat_pinned_event` | تثبيت أو إلغاء تثبيت رسالة | `{ "user_id": 42, "message_id": 120, "is_pinned": true, "pinned_at": "..." }` |
| `user_chat_disappearing_event` | `.user_chat_disappearing_event` | تغيير مدة الرسائل المؤقتة | `{ "user_id": 42, "disappearing_seconds": 86400 }` |
| `user_chat_typing_event` | `.user_chat_typing_event` | مؤشر كتابة العميل عبر الـ API | `{ "user_id": 42, "sender_type": "user", "sender_id": 42, "is_typing": true }` |
| `client-typing` | `listenForWhisper('typing')` | **مؤشر الكتابة المباشر** على القناة الخاصة `private-user-chat.{user_id}` | `{ "sender_type": "user", "sender_id": 42, "is_typing": true }` |
| `user_call_event` | `.user_call_event` | إشعارات المكالمات (`incoming`, `accepted`, `rejected`, `ended`, `missed`, `busy`, `cancelled`) | انظر قسم المكالمات (القسم 5) |

---

## 📦 3. هيكل كائن الرسالة الموحّد (Message Object)

```json
{
  "id": 121,
  "client_msg_id": "a9b1c2d3-...",
  "user_id": 42,
  "admin_id": 1,
  "is_admin": true,
  "msg": "أهلاً بك، تم مراجعة طلبك",
  "image": null,
  "voice": "https://mazoom.online/storage/user_chats/voices/reply.mp3",
  "delivery_status": "read",
  "is_delivered": true,
  "delivered_at": "2026-11-26T01:12:00+02:00",
  "is_admin_read": true,
  "admin_read_at": "2026-11-26T01:11:30+02:00",
  "is_user_read": true,
  "user_read_at": "2026-11-26T01:12:15+02:00",
  "reply_to": {
    "id": 120,
    "is_admin": false,
    "is_deleted": false,
    "msg": "مرحباً، لدي استفسار",
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
  "is_favorite": true,
  "reactions": [
    {
      "emoji": "❤️",
      "count": 1,
      "actors": [{ "type": "user", "id": 42 }]
    }
  ],
  "my_reaction": null,
  "expires_at": null,
  "date": "2026-11-26",
  "time": "01:11:30 AM",
  "created_at": "2026-11-26T01:11:30+02:00"
}
```

---

## 🚀 4. واجهات الـ APIs الخاصة بالإدارة والموظفين

### 1) قائمة المحادثات (Inbox)
- **Endpoint:** `GET /api/admin/user_chat/conversations`
- **Query Params:** `page` (`1`), `per_page` (`30`), `search` (بحث بالاسم أو الجوال أو الإيميل).
- **Response (200 OK):**
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
          "last_message": "مرحباً، لدي استفسار",
          "last_message_voice": null,
          "last_message_image": null,
          "last_message_date": "2026-11-26",
          "last_message_time": "01:09:50 AM",
          "last_sender": "user"
        }
      ],
      "per_page": 30,
      "total": 1
    }
  }
  ```

---

### 2) جلب سجل رسائل عميل محدد (مع دعم القفز للاقتباس)
- **Endpoint:** `GET /api/admin/user_chat/messages/{user_id}`
- **Params:** `{user_id}` (في المسار - مطلوب) | Query: `page` (`1` هي الأقدم), `per_page` (`50`), `around_id` (للانتقال مباشرة لصفحة رسالة مقتبسة).
- **Response (200 OK):**
  ```json
  {
    "status": true,
    "data": {
      "user": { "id": 42, "name": "أحمد الشمري", "mobile": "966500000000", "email": "ahmed@example.com" },
      "disappearing_seconds": 86400,
      "messages": {
        "current_page": 1,
        "data": [ /* <Message Objects> */ ],
        "last_page": 1,
        "per_page": 50,
        "total": 10
      }
    }
  }
  ```

---

### 3) إرسال رسالة للعميل (نص / صورة / صوت + الرد على رسالة)
- **Endpoint:** `POST /api/admin/user_chat/send_message`
- **Content-Type:** `multipart/form-data`
- **Body:**
  | الحقل | النوع | مطلوب | الوصف |
  |---|---|---|---|
  | `user_id` | Integer | **نعم** | معرّف العميل المستهدف |
  | `msg` | Text | اختياري* | نص الرسالة |
  | `image` | File | اختياري* | صورة (`jpeg, png, jpg, gif, svg, webp` حتى 20MB) |
  | `voice` | File | اختياري* | ملف صوتي (حتى 50MB) |
  | `client_msg_id` | String (max 64) | اختياري | معرّف مؤقت لمنع التكرار عند إعادة المحاولة |
  | `reply_to_id` | Integer | اختياري | معرّف الرسالة المُقتبَس منها |
- **Response (200 OK):** `{ "status": true, "message": "تم إرسال الرسالة بنجاح", "data": <Message Object> }`

---

### 4) تأكيد وصول رسائل العميل (Delivered)
- **Endpoint:** `POST /api/user_chat/delivered`
- **Body (JSON):** `{ "user_id": 42, "message_ids": [120] }` *(حقل `message_ids` اختياري)*.
- **Response (200 OK):**
  ```json
  {
    "status": true,
    "data": { "user_id": 42, "message_ids": [120], "updated_count": 1, "delivered_at": "..." }
  }
  ```

---

### 5) تعليم محادثة العميل كمقروءة (Read All)
- **Endpoint:** `POST /api/admin/user_chat/read_all/{user_id}`
- **Response (200 OK):**
  ```json
  {
    "status": true,
    "user_id": 42,
    "reader": "admin",
    "updated_count": 3,
    "read_at": "2026-11-26T01:11:30+02:00",
    "message": "تم تحديث حالة القراءة بنجاح / Conversation marked as read successfully"
  }
  ```

---

### 6) التفاعل بإيموجي (React)
- **Endpoint:** `POST /api/user_chat/messages/{id}/react`
- **Body (JSON):** `{ "emoji": "👍" }` *(نفس الإيموجي أو `null` يزيل التفاعل)*.
- **Response (200 OK):** `{ "status": true, "data": { "message_id": 120, "reactions": [...], "my_reaction": "👍" } }`

---

### 7) تعديل رسالة (Edit)
- **الصلاحية:** الأدمن العام (`admin`) يعدّل رسائل الإدارة **في أي وقت**؛ الموظف (`employee`) يعدّل رسائله خلال **15 دقيقة**.
- **Endpoint:** `PUT /api/user_chat/messages/{id}`
- **Body (JSON):** `{ "msg": "النص المعدل" }`
- **Response (200 OK):** `{ "status": true, "data": <Updated Message Object> }`

---

### 8) حذف رسالة (`me` لكل الإدارة / `everyone` للجميع)
- **Endpoint:** `DELETE /api/user_chat/messages/{id}`
- **Body (JSON):** `{ "scope": "me" }` أو `{ "scope": "everyone" }`
  - `"me"`: يخفي الرسالة عن **كافة طاقم الإدارة والموظفين**.
  - `"everyone"`: للأدمن في أي وقت، وللموظف لرسائله خلال **60 دقيقة**.
- **Response (200 OK):** `{ "status": true, "data": { "message_id": 120, "scope": "everyone", "deleted_at": "..." } }`

---

### 9) إعادة التوجيه إلى عملاء آخرين (Forward)
- **Endpoint:** `POST /api/user_chat/messages/{id}/forward`
- **Body (JSON):** `{ "target_user_ids": [42, 55] }` *(يتحقق من صلاحية الموظف لكل عميل مستهدف)*.
- **Response (200 OK):** `{ "status": true, "data": [ /* <Forwarded Message Objects> */ ] }`

---

### 10) تثبيت الرسائل (Pin — متاح للطرفين)
- **تثبيت / إلغاء تثبيت:** `POST /api/user_chat/messages/{id}/pin` — Body اختياري: `{ "is_pinned": true }`
- **عرض المثبتة:** `GET /api/user_chat/pinned?user_id=42`

---

### 11) المفضلة (Favorites — خاصة بكل أدمن/موظف)
- **تبديل المفضلة:** `POST /api/user_chat/messages/{id}/favorite`
- **عرض المفضلة:** `GET /api/user_chat/favorites?user_id=42&per_page=30` *(`user_id` اختياري للفلترة)*.

---

### 12) الرسائل المؤقتة (Disappearing Messages)
- **Endpoint:** `POST /api/user_chat/disappearing`
- **Body (JSON):** `{ "user_id": 42, "disappearing_seconds": 86400 }` *(أو `null` للإيقاف؛ الحد الأدنى `60` ثانية)*.

---

### 13) مؤشر الكتابة (Typing Dots)
- **الطريقة الأسرع:** إرسال/استقبال `client-typing` (`whisper('typing')`) مباشرة على القناة الخاصة `private-user-chat.{user_id}`.
- **أو عبر API:** `POST /api/user_chat/typing` — Body: `{ "user_id": 42, "is_typing": true }`.

---

## 📞 5. المكالمات الصوتية والمرئية (Agora.io Calling)

> ⚠️ **تنبيه:** عند استدعاء `call/token` أو `call/end` من لوحة التحكم، **يجب** تمرير `user_id` الخاص بالعميل في الـ Body.

1. **بدء مكالمة لعميل:** `POST /api/admin/user_chat/call/start` — Body: `{ "user_id": 42, "call_type": "voice" }`
   - يرجع: `{ "status": true, "channel_name": "...", "token": "...", "app_id": "...", "uid": 1, "call_type": "voice" }`
2. **قبول مكالمة واردة من عميل:** `POST /api/admin/user_chat/call/token` — Body: `{ "user_id": 42, "channel_name": "..." }`
3. **إنهاء / رفض المكالمة:** `POST /api/admin/user_chat/call/end` — Body: `{ "user_id": 42, "channel_name": "...", "reason": "ended" }`

### حدث المكالمات `user_call_event` (على `admin-user-chat` و `private-user-chat.{user_id}`):
- **`incoming`:** `{ "action": "incoming", "user_id": 42, "channel_name": "call_u42_...", "call_type": "voice", "caller_id": 42, "caller_name": "أحمد", "caller_role": "user", "receiver_id": "admin", "date": "...", "time": "...", "timestamp": 1795462200 }`
- **`accepted`:** `{ "action": "accepted", "user_id": 42, "channel_name": "call_u42_...", "accepter_id": 42, "date": "...", "time": "...", "timestamp": 1795462215 }`
- **`ended` / `rejected`:** `{ "action": "ended", "user_id": 42, "channel_name": "call_u42_...", "ended_by": 42, "reason": "ended", "date": "...", "time": "...", "timestamp": 1795462500 }`

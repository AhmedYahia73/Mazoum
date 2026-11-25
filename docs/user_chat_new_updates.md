# التعديلات الجديدة فقط — شات العميل (User Chat Updates)
# User Chat — New Features & Updates Only

هذا الملف مخصص لمطور واجهة **العميل (User)** ويغطي **التعديلات والإضافات الجديدة فقط** على الشات، القنوات، الأحداث اللحظية، والـ APIs.

---

## 📌 الهيدرز والقناة (Headers & Channel)

- **Headers لكل الطلبات:**
  - `token`: `<user_token>` *(أو `Authorization: Bearer <token>`)*
  - `Accept`: `application/json`
- **القناة الخاصة بالعميل:**
  - Pusher SDK: `private-user-chat.{user_id}` | Laravel Echo: `Echo.private('user-chat.' + user_id)`
  - Auth URL: `POST https://mazoom.online/api/broadcasting/auth`

---

## 📡 1. الأحداث والـ Listeners الجديدة والمحدّثة

> في **Pusher SDK** استمع باسم الحدث مباشرة؛ وفي **Laravel Echo** أضف نقطة قبله (مثل `.user_chat_edited_event`).

| Event (`Pusher`) | Echo Listener | التوقيت | الـ Payload |
|---|---|---|---|
| `user_chat_event` *(محدّث)* | `.user_chat_event` | رسالة جديدة أو محوّلة | كائن الرسالة الموحّد (انظر القسم 2) |
| `user_chat_delivered_event` | `.user_chat_delivered_event` | وصول رسائلك للإدارة | `{ "user_id": 42, "message_ids": [120], "delivered_by": "admin", "delivered_at": "..." }` |
| `user_chat_read_event` *(محدّث)* | `.user_chat_read_event` | قراءة الإدارة لرسائلك | `{ "user_id": 42, "reader": "admin", "updated_count": 2, "read_at": "...", "date": "...", "time": "..." }` |
| `user_chat_reaction_event` | `.user_chat_reaction_event` | إضافة/تغيير/إزالة إيموجي | `{ "user_id": 42, "message_id": 120, "actor_type": "admin", "actor_id": 1, "emoji": "❤️", "reactions": [...] }` |
| `user_chat_edited_event` | `.user_chat_edited_event` | تعديل نص رسالة | `{ "user_id": 42, "message_id": 120, "msg": "النص المعدل", "is_edited": true, "edited_at": "..." }` |
| `user_chat_deleted_event` | `.user_chat_deleted_event` | حذف رسالة للجميع | `{ "user_id": 42, "message_id": 120, "is_deleted": true, "deleted_at": "..." }` |
| `user_chat_pinned_event` | `.user_chat_pinned_event` | تثبيت أو إلغاء تثبيت رسالة | `{ "user_id": 42, "message_id": 120, "is_pinned": true, "pinned_at": "..." }` |
| `user_chat_disappearing_event` | `.user_chat_disappearing_event` | تغيير مدة الرسائل المؤقتة | `{ "user_id": 42, "disappearing_seconds": 86400 }` |
| `user_chat_typing_event` | `.user_chat_typing_event` | حالة الكتابة عبر الـ API | `{ "user_id": 42, "sender_type": "admin", "sender_id": 1, "is_typing": true }` |
| `client-typing` | `listenForWhisper('typing')` | **مؤشر الكتابة المباشر** على القناة الخاصة | `{ "sender_type": "admin", "sender_id": 1, "is_typing": true }` |

---

## 📦 2. التعديل على هيكل كائن الرسالة (Message Object)

تم توحيد المفاتيح (بدون أي تكرار مثل `image_url`/`voice_url`) وإضافة الحقول الجديدة:

```json
{
  "id": 120,
  "client_msg_id": "550e8400-e29b-41d4-a716-446655440000",
  "user_id": 42,
  "admin_id": null,
  "is_admin": false,
  "msg": "نص الرسالة",
  "image": "https://mazoom.online/storage/user_chats/images/pic.jpg",
  "voice": null,
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
    "msg": "الرسالة المقتبس منها",
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

- **حالات الرسالة:** محلياً `sending` و `failed` (مع زر إعادة المحاولة بنفس `client_msg_id`)، ومن السيرفر `delivery_status`: `"sent"` $\leftarrow$ `"delivered"` $\leftarrow$ `"read"`.

---

## 🔄 3. التعديلات على الـ APIs الحالية

### أ) إرسال رسالة (`POST /api/user/user_chat/send_message`)
- **الإضافات في Body (`multipart/form-data`):**
  - `client_msg_id` (`string`, max 64, اختياري): UUID يولده التطبيق لمنع تكرار الرسالة عند فشل الشبكة والضغط على «إعادة المحاولة».
  - `reply_to_id` (`integer`, اختياري): رقم الرسالة المراد الرد عليها (يجب أن تكون في نفس المحادثة).
- **التعديل في الرد (`200 OK`):** يرجع `data` بالهيكل الموحّد الجديد بالأعلى.

### ب) جلب الرسائل (`GET /api/user/user_chat/messages`)
- **الإضافات في Query Params:**
  - `around_id` (`integer`, اختياري): عند الضغط على اقتباس `reply_to` لرسالة قديمة غير محمّلة بعد، أرسل `around_id={reply_to.id}` ليرجع لك السيرفر الصفحة التي تحتوي عليها مباشرة.
- **التعديل في الرد (`200 OK`):**
  - إضافة `data.disappearing_seconds` (`integer|null`) لمعرفة حالة الرسائل المؤقتة بالمحادثة.
  - استبعاد الرسائل المحذوفة لدى العميل والمنتهية صلاحيتها تلقائياً.

---

## 🆕 4. الـ APIs الجديدة للعميل

### 1) تأكيد وصول الرسائل (Delivered)
- **URL:** `POST /api/user_chat/delivered`
- **Body (JSON):** `{ "message_ids": [121, 122] }` *(اختياري — إذا لم يُرسل يعلّم جميع الرسائل الواردة غير المؤكدة)*.
- **Response (200 OK):**
  ```json
  {
    "status": true,
    "data": { "user_id": 42, "message_ids": [121, 122], "updated_count": 2, "delivered_at": "2026-11-26T01:10:00+02:00" }
  }
  ```

### 2) التفاعل بإيموجي (إضافة / تغيير / إزالة)
- **URL:** `POST /api/user_chat/messages/{id}/react`
- **Body (JSON):** `{ "emoji": "❤️" }` *(إرسال نفس الإيموجي الحالي أو `null` يحذف التفاعل)*.
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

### 3) تعديل رسالة العميل
- **القاعدة:** متاح لرسائل العميل فقط خلال **15 دقيقة** من الإرسال.
- **URL:** `PUT /api/user_chat/messages/{id}`
- **Body (JSON):** `{ "msg": "النص المعدل" }`
- **Response (200 OK):** `{ "status": true, "data": <Updated Message Object> }` *(مع `is_edited: true`)*.
- **Error (403):** عند انتهاء الـ 15 دقيقة أو محاولة تعديل رسالة الأدمن.

### 4) حذف رسالة («حذف عندي» أو «حذف للجميع»)
- **URL:** `DELETE /api/user_chat/messages/{id}`
- **Body (JSON):** `{ "scope": "me" }` أو `{ "scope": "everyone" }`
  - `"me"`: متاح بأي وقت، ويخفيها لدى العميل فقط.
  - `"everyone"`: متاح لرسائل العميل خلال **60 دقيقة** فقط؛ يمسح النص والوسائط لدى الطرفين (`is_deleted: true`).
- **Response (200 OK):** `{ "status": true, "data": { "message_id": 120, "scope": "everyone", "deleted_at": "..." } }`

### 5) إعادة التوجيه (Forward) والنسخ
- **النسخ:** محلياً في التطبيق من حقل `msg` (يُمنع عند `is_deleted: true`).
- **إعادة التوجيه:** `POST /api/user_chat/messages/{id}/forward`
- **Response (200 OK):** `{ "status": true, "data": [ <Forwarded Message Object> ] }`

### 6) تثبيت الرسالة (Pin — متاح للعميل)
- **تثبيت / إلغاء تثبيت:** `POST /api/user_chat/messages/{id}/pin`
  - **Body (اختياري):** `{ "is_pinned": true }` *(بدونه يبدّل الحالة تلقائياً)*.
  - **Response (200 OK):** `{ "status": true, "data": { "message_id": 120, "is_pinned": true, "pinned_at": "..." } }`
- **جلب المثبتة:** `GET /api/user_chat/pinned`
  - **Response (200 OK):** `{ "status": true, "data": [ /* <Message Objects> */ ] }`

### 7) المفضلة (Favorites — خاصة بالعميل)
- **إضافة / إزالة:** `POST /api/user_chat/messages/{id}/favorite`
  - **Response (200 OK):** `{ "status": true, "data": { "message_id": 120, "is_favorite": true } }`
- **عرض المفضلة:** `GET /api/user_chat/favorites?per_page=30`
  - **Response (200 OK):** `{ "status": true, "data": { /* Pagination of <Message Objects> */ } }`

### 8) الرسائل المؤقتة (Disappearing Messages)
- **URL:** `POST /api/user_chat/disappearing`
- **Body (JSON):** `{ "disappearing_seconds": 86400 }` *(أو `null` للإيقاف؛ الحد الأدنى `60` ثانية)*.
- **Response (200 OK):** `{ "status": true, "data": { "user_id": 42, "disappearing_seconds": 86400 } }`

### 9) مؤشر الكتابة (Typing Dots)
- **الأفضل (مباشر عبر Pusher بدون API):** أرسل `client-typing` على `private-user-chat.{user_id}`:
  `{ "sender_type": "user", "sender_id": 42, "is_typing": true }`
- **أو عبر API:** `POST /api/user_chat/typing` — Body: `{ "is_typing": true }`
- **Response (200 OK):** `{ "status": true, "data": { "user_id": 42, "sender_type": "user", "sender_id": 42, "is_typing": true } }`

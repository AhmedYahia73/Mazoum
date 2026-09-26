# توثيق واجهات برمجة التطبيقات لإدارة تفاصيل المناسبة والمدعوين
# Admin Event Details & Guests APIs Documentation

- **Base URL:** `https://mazoom.online/admin`
- **Method:** `GET` (لكافة الواجهات أدناه)
- **Authentication:** Sanctum Bearer Token

---

## 📌 الهيدرز المشتركة (Common Headers)

تتطلب جميع الـ Endpoints تمرير الهيدرز التالية:

| Header | النوع | مطلوب | الوصف | مثال |
|---|---|---|---|---|
| `Authorization` | String | **نعم** | توكن مصادقة الأدمن (Sanctum) | `Bearer 12\|AbCdEf...` |
| `Accept` | String | **نعم** | تحديد صيغة الرد كـ JSON | `application/json` |
| `Accept-Language` | String | اختياري | لغة الردود والرسائل (`ar` أو `en`) | `ar` |

---

## 📌 المعلمات المشتركة (Common Parameters)

- **Route Parameter:**
  - `{id}`: معرف المناسبة (`event_id` - Integer - مطلوب).
- **Query Parameters:**
  - `page`: رقم الصفحة في الـ Pagination (Integer - اختياري - الافتراضي: `1`).
  - `search`: كلمة البحث بالاسم أو رقم الجوال (String - اختياري).

---

## 📌 هيكل كائن الترقيم الصفحي (Laravel Pagination Object)

جميع الردود التي تحتوي على بيانات مجدولة تعيد كائن ترقيم صفحي بالهيكل القياسي التالي:

```json
{
  "current_page": 1,
  "data": [ ... ],
  "first_page_url": "https://mazoom.online/admin/...?page=1",
  "from": 1,
  "last_page": 5,
  "last_page_url": "https://mazoom.online/admin/...?page=5",
  "next_page_url": "https://mazoom.online/admin/...?page=2",
  "path": "https://mazoom.online/admin/...",
  "per_page": 15,
  "prev_page_url": null,
  "to": 15,
  "total": 68
}
```

---

## 1. استرجاع رسائل المناسبة (الاعتذارات والتواصل)
### `GET /admin/event-messages/{id}`

- **الوصف:** استرجاع جميع رسائل الفعالية مع الردود المسجلة عليها وإمكانية البحث.
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:**
  - `page` (Integer, اختياري): رقم الصفحة.
  - `search` (String, اختياري): بحث باسم المدعو أو هاتفه.
- **Body:** لا يوجد (GET Request).
- **Response Structure:**
  - `Item`: كائن المناسبة (`Events`).
  - `messages`: كائن Pagination لقائمة الرسائل (`EventMessages`) متضمناً الـ `reply`.
  - `title`: `'كل الرسائل'`
  - `type`: `'event_message'`

#### مثال للرد (200 OK):
```json
{
  "Item": {
    "id": 2222,
    "title": "حفل زفاف أحمد ومريم",
    "date": "2026-10-15",
    "time": "20:00",
    "address": "الرياض - قاعة الريتز",
    "lat": "24.7136",
    "long": "46.6753",
    "user_id": 105
  },
  "messages": {
    "current_page": 1,
    "data": [
      {
        "id": 142,
        "event_id": 2222,
        "name": "فهد الدوسري",
        "mobile": "966500000001",
        "message": "أعتذر عن الحضور لظروف طارئة، ألف مبروك",
        "type": "apologize",
        "reply": {
          "id": 35,
          "name": "صاحب المناسبة",
          "mobile": "966500000000",
          "message": "معذور يا غالي وعقبال عندك",
          "type": "replay",
          "message_id": 142
        },
        "created_at": "2026-09-20T18:30:00.000000Z"
      }
    ],
    "total": 12,
    "per_page": 15,
    "current_page": 1
  },
  "title": "كل الرسائل",
  "type": "event_message"
}
```

---

## 2. استرجاع جميع المدعوين للمناسبة
### `GET /admin/all-invited-users/{id}`

- **الوصف:** قائمة بجميع المدعوين المضافين للمناسبة بغض النظر عن حالة الدعوة.
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:** `page`, `search` (اسم / جوال).
- **Body:** لا يوجد.
- **Response Structure:**
  - `Item`: كائن المناسبة.
  - `data`: كائن Pagination لجميع المدعوين (`EventUsers`).
  - `title`: `'كل المدعوين'`
  - `type`: `'all_invited_users'`

#### مثال للرد (200 OK):
```json
{
  "Item": { "id": 2222, "title": "حفل زفاف أحمد ومريم" },
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 501,
        "event_id": 2222,
        "name": "خالد بن عبدالعزيز",
        "mobile": "966512345678",
        "users_count": 2,
        "status": "attend",
        "scan": "yes",
        "scan_count": 2,
        "qr_sent": "yes",
        "accept_count": 2
      }
    ],
    "total": 150,
    "per_page": 15
  },
  "title": "كل المدعوين",
  "type": "all_invited_users"
}
```

---

## 3. تفاصيل الحضور الفعلي عبر مسح الـ QR
### `GET /admin/event-qr-details/{id}`

- **الوصف:** استرجاع قائمة المدعوين الذين تم عمل Scan لرمز الدخول QR الخاص بهم في القاعة (`scan = 'yes'`).
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:** `page`, `search` (اسم / جوال).
- **Body:** لا يوجد.
- **Response Structure:**
  - `Item`: كائن المناسبة.
  - `data`: كائن Pagination للمدعوين الحاضرين فعلياً.
  - `title`: `'كل المدعوين الذين اكدو الحضور (QR)'`
  - `is_qr_page`: `'yes'`
  - `type`: `'qr'`

---

## 4. تفاصيل المدعوين المؤكدين الحضور
### `GET /admin/confirmed-event-details/{id}`

- **الوصف:** استرجاع المدعوين الذين قاموا بتأكيد نيتهم في الحضور (`accept_count > 0`).
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:** `page`, `search` (اسم / جوال).
- **Body:** لا يوجد.
- **Response Structure:**
  - `Item`: كائن المناسبة.
  - `data`: كائن Pagination يحتوي على الحقول التفصيلية لكل مدعو مؤكد:
    - `id`, `name`, `mobile`, `users_count`, `accept_count`, `accept_time`, `scan`, `scan_count`, `scan_at`, `qr_sent`, `is_delivered`, `is_read`, `remember`, `confirmed_at`.
  - `title`: `'كل المدعوين الذين ينوون الحضور'`
  - `type`: `'confirmed_event_details'`

---

## 5. تفاصيل المدعوين المعتذرين عن الحضور
### `GET /admin/not-attend-event-details/{id}`

- **الوصف:** استرجاع المدعوين الذين ضغطوا على زر الاعتذار عن الحضور (`status = 'not-attend'`).
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:** `page`, `search` (اسم / جوال).
- **Body:** لا يوجد.
- **Response Structure:**
  - `Item`: كائن المناسبة.
  - `data`: كائن Pagination لقائمة المعتذرين.
  - `title`: `'كل المدعوين الذين اعتذرو'`

---

## 6. تفاصيل المدعوين المنتظرين (قيد الإرسال / المعلقين)
### `GET /admin/hold-event-details/{id}`

- **الوصف:** استرجاع المدعوين الذين ما زالت حالتهم معلقة (`status = 'hold'` ولم يتم إرسال الدعوة لهم بعد `is_sent IS NULL` و `is_new_sent = 0`).
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:** `page`, `search`.
- **Body:** لا يوجد.
- **Response Structure:**
  - `Item`: كائن المناسبة.
  - `data`: كائن Pagination للمدعوين قيد الانتظار.
  - `title`: `'كل المدعوين المنتظرين'`
  - `type`: `'hold'`

---

## 7. تفاصيل المدعوين الذين لم يؤكدوا الحضور بعد
### `GET /admin/failed-event-details/{id}`

- **الوصف:** استرجاع المدعوين الذين تم إرسال الدعوة لهم ولكن لم يقم أي منهم بتأكيد الحضور أو الاعتذار (`accept_count = 0` و `status != 'not-attend'`).
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:** `page`, `search`.
- **Body:** لا يوجد.
- **Response Structure:**
  - `Item`: كائن المناسبة.
  - `data`: كائن Pagination للمدعوين الذين لم يتجاوبوا بعد.
  - `title`: `'لم يتم تاكيد الحضور'`
  - `type`: `'failed'`

---

## 8. تفاصيل الدعوات التي تم إرسال QR لها
### `GET /admin/qr-sent-event-details/{id}`

- **الوصف:** استرجاع المدعوين المؤكدين الذين تم إرسال بطاقة دخول الـ QR إليهم (`qr_sent = 'yes'` و `accept_count > 0`) متضمناً سجل الدخول (`user_enrance`).
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:** `page`, `search`.
- **Body:** لا يوجد.
- **Response Structure:**
  - `Item`: كائن المناسبة.
  - `data`: كائن Pagination يحتوي على تفاصيل المدعو مع مصفوفة `user_enrance`:
    ```json
    "user_enrance": [
      {
        "count": 2,
        "date": "2026-10-15",
        "time": "08:15 PM"
      }
    ]
    ```
  - `title`: `'كل الدعوات (Sent QR)'`

---

## 9. المدعوون الذين تم إرسال تذكير لهم
### `GET /admin/is_remember/{id}`

- **الوصف:** استرجاع المدعوين الذين تم إرسال رسالة تذكير (Reminder) لهم بالمناسبة (`remember = 1`).
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:** `page`, `search`.
- **Body:** لا يوجد.
- **Response Structure:**
  - `Item`: كائن المناسبة.
  - `data`: كائن Pagination للمدعوين الذين تم تذكيرهم.
  - `title`: `'كل الدعوات (Faild Send)'`

---

## 10. رسائل التهنئة للمناسبة
### `GET /admin/congratulations-event-messages-details/{id}`

- **الوصف:** استرجاع رسائل التهنئة والتبريكات الواردة من المدعوين مع الردود المسجلة عليها (`CongratulationMessages` مع `reply`).
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:** `page`, `search`.
- **Body:** لا يوجد.
- **Response Structure:**
  - `Item`: كائن المناسبة.
  - `messages`: كائن Pagination للتهاني.
  - `title`: `'رسائل التهنئة'`
  - `type`: `'congrate_message'`

---

## 11. تفاصيل عدم الحضور فعلياً (بعد التأكيد)
### `GET /admin/non-attendance-event-details/{id}`

- **الوصف:** استرجاع المدعوين الذين أكدوا الحضور مسبقاً (`status = 'attend'`) ولكنهم لم يحضروا للقاعة فعلياً (`scan IS NULL`). يقوم النظام بحساب العدد المتبقي تلقائياً (`users_count = users_count - scan_count`).
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:** `page`, `search`.
- **Body:** لا يوجد.
- **Response Structure:**
  - `Item`: كائن المناسبة.
  - `data`: كائن Pagination للمتغيبين بعد التأكيد.
  - `title`: `'عدم الحضور فعليا'`
  - `type`: `'non_attendance'`

---

## 12. المدعوون المؤكدون عبر شات الويب
### `GET /admin/confirmed-users-web-chat/{id}`

- **الوصف:** استرجاع المدعوين الذين أكدوا حضورهم عبر رابط شات الويب (`send_type = 'link'` و `qr_sent = 'yes'` و `accept_count > 0`).
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:** `page`, `search` (بحث برقم الجوال).
- **Body:** لا يوجد.
- **Response Structure:**
  - `Item`: كائن المناسبة.
  - `data`: كائن Pagination للمدعوين مع علاقة `event:id,title`.
  - `title`: `'كل المدعوين الذين اكدوا الحضور من الشات الويب'`
  - `type`: `'confirmed_event_details'`

---

## 13. مضيفو المناسبة ورصيد الدعوات (Event Hosts)
### `GET /admin/event_host/{id}`

- **الوصف:** استرجاع المضيفين والمساعدين التابعين للمناسبة، مع حساب رصيد الدعوات المخصصة لهم والمستهلكة والمتبقية:
  - `available`: إجمالي الدعوات المخصصة للمضيف (`custom_invetaion`).
  - `balance`: الرصيد المتبقي (`custom_invetaion - send_custom_invetaion`).
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:** `page`, `search` (بحث باسم المضيف أو جواله).
- **Body:** لا يوجد.
- **Response Structure:**
  - `Item`: كائن Pagination لمستخدمي المضيفين التابعين للمناسبة.

#### مثال للرد (200 OK):
```json
{
  "Item": {
    "current_page": 1,
    "data": [
      {
        "id": 12,
        "name": "مضيف عائلة العريس",
        "mobile": "966501112233",
        "custom_invetaion": 100,
        "send_custom_invetaion": 35,
        "balance": 65,
        "available": 100
      }
    ],
    "total": 3,
    "per_page": 15
  }
}
```

---

## 14. المدعوون الذين فشل إرسال الدعوة لهم
### `GET /admin/faild_users/{id}`

- **الوصف:** استرجاع المدعوين الذين فشلت محاولة إرسال الدعوة إلى أرقامهم (`status = 'failed'`).
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:** `page`, `search`.
- **Body:** لا يوجد.
- **Response Structure:**
  - `Item`: كائن المناسبة.
  - `data`: كائن Pagination بالمدعوين الذين فشل إرسال رسالتهم متضمناً أسباب الفشل (`error`, `error_title`).
  - `title`: `'كل الدعوات (Faild Send)'`

---

## 15. الرسائل الصوتية للمناسبة
### `GET /admin/events/voice_msgs/{id}`

- **الوصف:** استرجاع جميع الرسائل الصوتية المرسلة من المدعوين كتهنئة أو رد صوتي على المناسبة (`EventVoice`).
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:**
  - `page` (Integer, اختياري): رقم الصفحة (الافتراضي 20 في الصفحة).
  - `search` (String, اختياري): بحث باسم صاحب الرسالة أو رقم هاتفه.
- **Body:** لا يوجد.
- **Response Structure:**
  - `status`: `true`
  - `user_events`: كائن Pagination يحتوي على التسجيلات الصوتية مع بيانات صاحب الرسالة (`event_user: id, name, mobile`).

#### مثال للرد (200 OK):
```json
{
  "status": true,
  "user_events": {
    "current_page": 1,
    "data": [
      {
        "id": 88,
        "event_user_id": 501,
        "voice": "https://mazoom.online/voices/audio_1727214562.mp3",
        "created_at": "2026-09-21T21:10:00.000000Z",
        "event_user": {
          "id": 501,
          "name": "سعد القرني",
          "mobile": "966555123456"
        }
      }
    ],
    "total": 8,
    "per_page": 20
  }
}
```

---

## 16. شاشة تفاصيل وإرسال الدعوات
### `GET /admin/events/{id}/send-events`

- **الوصف:** الواجهة المركزية لعرض مدعوي المناسبة في شاشة الإرسال مع صورة كود الـ QR (`qr_image`) وإمكانية الفلترة الدقيقة بالحالة وحالة إرسال الـ QR.
- **Route Param:** `id` (معرف المناسبة).
- **Query Params:**
  - `page` (Integer, اختياري): رقم الصفحة (الترقيم 20 عنصر بالصفحة).
  - `search` (String, اختياري): بحث بالاسم أو الهاتف.
  - `status` (String, اختياري): فلترة بحالة المدعو (مثل: `attend`, `not-attend`, `hold`, `failed`).
  - `qr_sent` (String / Boolean, اختياري):
    - `"true"`: المدعوون الذين تم إرسال QR لهم فقط (`qr_sent = 'yes'`).
    - `"false"`: المدعوون الذين لم يتم إرسال QR لهم (`qr_sent IS NULL OR qr_sent != 'yes'`).
- **Body:** لا يوجد.
- **Response Structure:**
  - `Item`: كائن المناسبة (`Events` متضمناً المحذوفة soft-deleted).
  - `event_users`: كائن Pagination للمدعوين متضمناً علاقة `qr_image` (`id`, `qr`, `event_user_id`).

#### مثال للرد (200 OK):
```json
{
  "Item": {
    "id": 2222,
    "title": "حفل زفاف أحمد ومريم",
    "date": "2026-10-15"
  },
  "event_users": {
    "current_page": 1,
    "data": [
      {
        "id": 501,
        "event_id": 2222,
        "name": "عبدالله محمد",
        "mobile": "966509876543",
        "users_count": 1,
        "status": "attend",
        "qr_sent": "yes",
        "qr_image": {
          "id": 912,
          "event_user_id": 501,
          "qr": "https://mazoom.online/qrcodes/qr_501.png"
        }
      }
    ],
    "total": 150,
    "per_page": 20
  }
}
```

---

## ⚠️ الأخطاء المحتملة (Common Errors)

| كود الحالة (HTTP Status) | السبب | شكل الرد المتوقع |
|---|---|---|
| `401 Unauthorized` | التوكن غير صالح أو غير ممرر في الـ Header | `{"message": "Unauthenticated."}` |
| `404 Not Found` | المناسبة غير موجودة في النظام بهذا الـ ID | `{"message": "No query results for model [App\\Models\\Events] 2222"}` |
| `405 Method Not Allowed` | استخدام HTTP Method غير مدعومة مثل POST على هذا المسار | `{"message": "The POST method is not supported for this route. Supported methods: GET."}` |

# API Documentation — Admin Event Users

Base URL: `https://mazoom.online/admin`

All endpoints require admin authentication.

---

## Event Details (GET)

### GET /event-messages/{id}
رسائل الحدث

**Query Params:** `search` (optional)

**Response:**
```json
{
  "Item": { "id": 573, "title": "..." },
  "messages": {
    "data": [
      { "id": 1, "event_id": 573, "name": "...", "mobile": "...", "message": "...", "reply": null }
    ],
    "current_page": 1, "last_page": 1, "total": 10
  },
  "title": "كل الرسائل",
  "type": "event_message"
}
```

---

### GET /all-invited-users/{id}
كل المدعوين

**Query Params:** `search` (optional), `page`

**Response:**
```json
{
  "Item": { "id": 573, "title": "..." },
  "data": {
    "data": [
      {
        "id": 1, "event_id": 573, "name": "...", "mobile": "...",
        "users_count": 2, "status": "sent", "is_sent": "yes",
        "is_delivered": "yes", "is_read": "yes", "qr_sent": "yes",
        "is_accepted": "yes", "is_refused": null, "accept_count": 2
      }
    ],
    "current_page": 1, "last_page": 5, "total": 70
  },
  "title": "كل المدعوين",
  "type": "all_invited_users"
}
```

---

### GET /event-qr-details/{id}
المدعوين الذين دخلوا بالـ QR

**Query Params:** `search` (optional), `page`

**Response:**
```json
{
  "Item": { "id": 573 },
  "data": { "data": [...], "total": 20 },
  "title": "كل المدعوين الذين دخلوا بالـ QR",
  "type": "event_qr_details"
}
```

---

### GET /confirmed-event-details/{id}
المدعوين الذين أكدوا الحضور

**Query Params:** `search` (optional), `page`

**Response:**
```json
{
  "Item": { "id": 573 },
  "data": {
    "data": [
      {
        "id": 1, "name": "...", "mobile": "...",
        "users_count": 3, "accept_count": 3,
        "status": "attend", "qr_sent": "yes",
        "scan": null, "scan_count": 0,
        "confirmed_at": "2024-01-01 20:00:00"
      }
    ]
  },
  "title": "كل المدعوين الذين ينوون الحضور",
  "type": "confirmed_event_details"
}
```

---

### GET /not-attend-event-details/{id}
المدعوين الذين اعتذروا

**Query Params:** `search` (optional), `page`

**Response:**
```json
{
  "Item": { "id": 573 },
  "data": { "data": [...], "total": 5 },
  "title": "كل المدعوين الذين اعتذروا",
  "type": "not_attend"
}
```

---

### GET /hold-event-details/{id}
المدعوين المنتظرين (لم يُرسل لهم بعد)

**Query Params:** `search` (optional), `page`

**Response:**
```json
{
  "Item": { "id": 573 },
  "data": { "data": [...], "total": 15 },
  "title": "كل المدعوين المنتظرين",
  "type": "hold"
}
```

---

### GET /failed-event-details/{id}
المدعوين الذين فشل إرسال الدعوة لهم

**Query Params:** `search` (optional), `page`

**Response:**
```json
{
  "Item": { "id": 573 },
  "data": { "data": [...], "total": 3 },
  "title": "كل المدعوين الذين فشل ارسال الدعوة لهم",
  "type": "failed"
}
```

---

### GET /qr-sent-event-details/{id}
المدعوين الذين أُرسل لهم QR

**Query Params:** `search` (optional), `page`

**Response:**
```json
{
  "Item": { "id": 573 },
  "data": { "data": [...], "total": 30 },
  "title": "كل المدعوين الذين تم ارسال QR لهم",
  "type": "qr_sent"
}
```

---

### GET /congratulations-event-messages-details/{id}
رسائل التهنئة

**Query Params:** `search` (optional), `page`

**Response:**
```json
{
  "Item": { "id": 573 },
  "messages": {
    "data": [
      { "id": 1, "name": "...", "mobile": "...", "message": "...", "reply": null }
    ]
  },
  "title": "رسائل التهنئة",
  "type": "congrate_message"
}
```

---

### GET /non-attendance-event-details/{id}
المدعوين الذين أكدوا لكن لم يحضروا فعلياً

**Query Params:** `search` (optional), `page`

**Response:**
```json
{
  "Item": { "id": 573 },
  "data": {
    "data": [
      {
        "id": 1, "name": "...", "mobile": "...",
        "users_count": 2,
        "accept_count": 3, "scan_count": 1,
        "status": "attend"
      }
    ]
  },
  "title": "عدم الحضور فعليا",
  "type": "non_attendance"
}
```

---

### GET /confirmed-users-web-chat/{id}
المدعوين الذين أكدوا من الشات الويب

**Query Params:** `search` (optional), `page`

**Response:**
```json
{
  "Item": { "id": 573 },
  "data": { "data": [...], "total": 8 },
  "title": "كل المدعوين الذين اكدوا الحضور من الشات الويب",
  "type": "confirmed_event_details"
}
```

---

### GET /event_host/{id}
قائمة المضيفين للحدث

**Query Params:** `search` (optional), `page`

**Response:**
```json
{
  "Item": {
    "data": [
      { "id": 1, "name": "...", "mobile": "...", "user_type": "employee" }
    ]
  }
}
```

---

### GET /events/{id}/enter-event
بيانات دخول الحدث

**Response:**
```json
{
  "Item": { "id": 573, "name": "...", "mobile": "...", "status": "attend" },
  "event_family": [
    { "id": 1, "event_id": 573, "name": "...", "mobile": "...", "scan_qr": "no" }
  ]
}
```

---

### GET /event_family_search
بحث في أفراد العائلة

**Query Params:** `event_id` (required), `name` (optional), `mobile` (optional)

**Response:**
```json
{
  "data": [
    { "id": 1, "event_id": 573, "name": "...", "mobile": "...", "scan_qr": "no" }
  ]
}
```

---

## Actions (POST)

### POST /send-congratulation-messages
إرسال رسائل تهنئة

**Body:**
```json
{
  "sending_type": "old_send | new_send",
  "event_id": 573,
  "users": [{ "id": 1 }, { "id": 2 }]
}
```

**Response:**
```json
{ "success": "تم الأرسال بنجاح" }
```

---

### POST /send-custom-message
إرسال رسالة مخصصة

**Body (multipart/form-data):**
| Field | Type | Required |
|-------|------|----------|
| sending_type | string `old_send\|new_send` | ✓ |
| message | string | ✓ |
| event_id | integer | ✓ |
| users | array `[{id}]` | ✓ |
| file | image | ✗ |

**Response:**
```json
{ "success": "تم الأرسال بنجاح" }
```

---

### POST /new-send-event-invitation
إرسال دعوات جديدة

**Body:**
```json
{
  "event_id": 573,
  "users": [{ "id": 1, "users_count": 2 }]
}
```

**Response:**
```json
{ "success": "تم الأرسال بنجاح" }
```

---

### POST /send_event_users
إرسال الدعوات للمدعوين

**Body:**
```json
{
  "event_id": 573,
  "users": [
    { "id": 1, "users_count": 2 },
    { "id": 2, "users_count": 4 }
  ]
}
```

**Response:**
```json
["success", "تم الأرسال بنجاح"]
```

---

### POST /delete_selected_event_users
حذف مدعوين محددين

**Body:**
```json
{
  "users": [{ "id": 1 }, { "id": 2 }]
}
```

**Response:**
```json
{ "success": "تم حذف العناصر المختاره" }
```

---

### POST /update_event_users
تحديث بيانات مدعو

**Body:**
```json
{
  "id": 1,
  "name": "...",
  "mobile": "...",
  "users_count": 3
}
```

**Response:**
```json
{ "success": "You update data success" }
```

---

### POST /event_host
إضافة مضيف للحدث

**Body:**
```json
{
  "user_id": 1,
  "event_id": 573
}
```

**Response:**
```json
{ "success": "You add data success" }
```

---

### POST /save_event_family
حفظ أفراد العائلة

**Body:**
```json
{
  "event_id": 573,
  "event_users": [
    { "name": "...", "mobile": "0501234567" },
    { "name": "...", "mobile": null }
  ]
}
```

**Response:**
```json
{ "success": "تم الحفظ بنجاح" }
```

---

### POST /update_event_family
تحديث أفراد العائلة

**Body:**
```json
{
  "event_users": [
    { "id": 1, "name": "...", "mobile": "0501234567" }
  ]
}
```

**Response:**
```json
{ "success": "تم التحديث بنجاح" }
```

---

### GET /event_family/destroy/{id}
حذف فرد من العائلة

**Response:**
```json
{ "success": "تم الحذف بنجاح" }
```

---

### POST /remember-users-to-event
إرسال تذكير للمدعوين

**Body (multipart/form-data):**
| Field | Type | Required |
|-------|------|----------|
| sending_type2 | string `old_send\|new_send` | ✓ |
| message2 | string | ✓ |
| event_id | integer | ✓ |
| users | array `[{id}]` | ✓ |
| date | string | ✓ |
| time | string | ✓ |
| file2 | image | ✗ |

**Response:**
```json
{ "success": "تم الأرسال بنجاح" }
```

---

## Error Responses

**Validation Error (400):**
```json
{
  "errors": {
    "field_name": ["The field is required."]
  }
}
```

**Not Found (404):** Resource not found

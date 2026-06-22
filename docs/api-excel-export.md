# Excel Export APIs

Base URL: `{{domain}}/api/user/excel`

**Headers (required for all):**

| Header | Value | Required |
|--------|-------|----------|
| token | user token | ✓ |
| language | `ar` or `en` | ✓ |
| Accept | `application/json` | ✓ |

> All endpoints are GET — no request body needed unless specified.

---

# Custom Event

## GET /event_users?custom_event_id={id}

كل مدعوي الـ custom event الخاصين بالمستخدم.

**Query Params:**

| Param | Type | Required |
|-------|------|----------|
| custom_event_id | integer | ✓ exists in custom_event |

**Response:**
```json
{
  "event_users": [
    { "id": 1, "custom_event_id": 10, "name": "Ahmed Ali", "mobile": "0501234567", "users_count": 3, "scan": null, "scan_count": 0, "confirm_count": 0, "apologize_count": 0, "qr": "https://..." }
  ]
}
```

---

## GET /event_family?custom_event_id={id}

أفراد عائلة الـ custom event.

**Query Params:**

| Param | Type | Required |
|-------|------|----------|
| custom_event_id | integer | ✓ |

**Response:**
```json
{
  "event_users": [
    { "id": 1, "event_id": 10, "name": "Fahad Ali", "mobile": "0501234573", "scan_qr": "no" }
  ],
  "event_id": 10
}
```

---

## GET /event_host_visitor?custom_event_id={id}

كل الزوار (المدعوين) للحدث.

**Query Params:**

| Param | Type | Required |
|-------|------|----------|
| custom_event_id | integer | ✓ |

**Response:**
```json
{
  "Item": { "id": 10, "title": "حفل زفاف", "date": "2026-06-01" },
  "visitors_count": [
    { "id": 1, "name": "Ahmed Ali", "mobile": "0501234567", "users_count": 3, "scan": null, "confirm_count": 2, "apologize_count": 0 }
  ]
}
```

---

## GET /event_host_qr?custom_event_id={id}

المدعوين الذين دخلوا بالـ QR (scan=yes).

**Query Params:** `custom_event_id` ✓

**Response:**
```json
{
  "Item": { "id": 10, "title": "..." },
  "qr_count": [
    { "id": 1, "name": "Ahmed Ali", "mobile": "0501234567", "scan": "yes", "scan_count": 3 }
  ]
}
```

---

## GET /event_host_congrate_msg?custom_event_id={id}

رسائل التهنئة للحدث.

**Query Params:** `custom_event_id` ✓

**Response:**
```json
{
  "Item": { "id": 10, "title": "..." },
  "congratulation_msg": [
    { "id": 1, "custom_event_id": 10, "custom_user_id": 1, "msg": "مبروك", "type": "congratulation" }
  ]
}
```

---

## GET /event_host_apologize_msg?custom_event_id={id}

رسائل الاعتذار للحدث.

**Query Params:** `custom_event_id` ✓

**Response:**
```json
{
  "Item": { "id": 10, "title": "..." },
  "apologize_msg": [
    { "id": 2, "custom_event_id": 10, "custom_user_id": 2, "msg": "آسف لن أتمكن", "type": "apologize" }
  ]
}
```

---

## GET /event_host_apologize?custom_event_id={id}

المدعوين الذين لديهم apologize_count.

**Query Params:** `custom_event_id` ✓

**Response:**
```json
{
  "Item": { "id": 10, "title": "..." },
  "apologize_count": [
    { "id": 2, "name": "Sara Mohamed", "mobile": "0501234568", "users_count": 2, "apologize_count": 2 }
  ]
}
```

---

## GET /event_host_confirm?custom_event_id={id}

المدعوين الذين لديهم confirm_count.

**Query Params:** `custom_event_id` ✓

**Response:**
```json
{
  "Item": { "id": 10, "title": "..." },
  "confirm_count": [
    { "id": 1, "name": "Ahmed Ali", "mobile": "0501234567", "users_count": 3, "confirm_count": 3 }
  ]
}
```

---

## GET /qr_count/{id}

المدعوين الذين دخلوا بالـ QR في custom event.

**URL Params:** `id` = custom_event_id

**Response:**
```json
{
  "custom_event_users": [
    { "id": 1, "name": "Ahmed Ali", "mobile": "0501234567", "scan": "yes", "scan_count": 3 }
  ]
}
```

---

## GET /confirm_count/{id}

المدعوين الذين أكدوا الحضور في custom event (confirm_count > 0).

**URL Params:** `id` = custom_event_id

**Response:**
```json
{
  "Item": { "id": 10, "title": "..." },
  "user_events": [
    { "id": 1, "name": "Ahmed Ali", "mobile": "0501234567", "confirm_count": 3, "users_count": 3 }
  ]
}
```

---

## GET /apologize_count/{id}

المدعوين الذين اعتذروا في custom event (apologize_count > 0).

**URL Params:** `id` = custom_event_id

**Response:**
```json
{
  "Item": { "id": 10, "title": "..." },
  "user_events": [
    { "id": 2, "name": "Sara Mohamed", "mobile": "0501234568", "apologize_count": 2, "users_count": 2 }
  ]
}
```

---

# Regular Event

## GET /all_invited_users/{id}

كل المدعوين للحدث.

**URL Params:** `id` = event_id

**Response:**
```json
{
  "Item": { "id": 2225, "title": "حفل زفاف" },
  "data": [
    { "id": 1, "name": "Ahmed Ali", "mobile": "0501234567", "users_count": 3, "status": "attend", "is_sent": "yes", "qr_sent": "yes" }
  ],
  "title": "كل المدعوين",
  "type": "all_invited_users"
}
```

---

## GET /event_qr_details/{id}

المدعوين الذين دخلوا بالـ QR (scan=yes).

**URL Params:** `id` = event_id

**Response:**
```json
{
  "Item": { "id": 2225, "title": "..." },
  "data": [ { "id": 1, "name": "Ahmed Ali", "mobile": "0501234567", "scan": "yes", "scan_count": 3 } ],
  "title": "كل المدعوين الذين اكدو الحضور (QR)",
  "is_qr_page": "yes",
  "type": "qr"
}
```

---

## GET /confirmed_event_details/{id}

المدعوين الذين أكدوا الحضور (status=attend).

**URL Params:** `id` = event_id

**Response:**
```json
{
  "Item": { "id": 2225 },
  "data": [ { "id": 1, "name": "Ahmed Ali", "status": "attend", "accept_count": 3 } ],
  "title": "كل المدعوين الذين ينوون الحضور",
  "type": "confirmed_event_details"
}
```

---

## GET /confirmed_users_web_chat/{id}

المدعوين الذين أكدوا من الشات الويب (action=accept_event).

**URL Params:** `id` = event_id

**Response:**
```json
{
  "Item": { "id": 2225 },
  "data": [
    { "id": 1, "event_user_id": 1, "mobile": "0501234567", "action": "accept_event", "users_count": 3, "event_user": { "id": 1, "name": "Ahmed Ali", "scan_count": 0 }, "event": "حفل زفاف", "user_name": "محمد", "user_id": 5 }
  ],
  "title": "كل المدعوين الذين اكدوا الحضور من الشات الويب",
  "type": "confirmed_event_details"
}
```

---

## GET /not_attend_event_details/{id}

المدعوين الذين اعتذروا (status=not-attend).

**URL Params:** `id` = event_id

**Response:**
```json
{
  "Item": { "id": 2225 },
  "data": [ { "id": 2, "name": "Sara Mohamed", "mobile": "0501234568", "status": "not-attend" } ],
  "title": "كل المدعوين الذين اعتذرو"
}
```

---

## GET /hold_event_details/{id}

المدعوين المنتظرين — لم يُرسل لهم بعد.

**URL Params:** `id` = event_id

**Response:**
```json
{
  "Item": { "id": 2225 },
  "data": [ { "id": 3, "name": "Omar Khalid", "status": "hold", "is_sent": null, "is_new_sent": 0 } ],
  "title": "كل المدعوين المنتظرين",
  "type": "hold"
}
```

---

## GET /failed_event_details/{id}

المدعوين الذين أُرسل لهم ولم يردوا.

**URL Params:** `id` = event_id

**Response:**
```json
{
  "Item": { "id": 2225 },
  "data": [ { "id": 4, "name": "Nora Salem", "is_accepted": null, "is_refused": null, "is_sent": "yes" } ],
  "title": "لم يتم تاكيد الحضور",
  "type": "failed"
}
```

---

## GET /non_attendance_event_details/{id}

المدعوين الذين أكدوا لكن لم يحضروا فعلياً.

**URL Params:** `id` = event_id

**Response:**
```json
{
  "Item": { "id": 2225 },
  "data": [ { "id": 1, "name": "Ahmed Ali", "status": "attend", "scan": null, "accept_count": 3, "scan_count": 0 } ],
  "title": "عدم الحضور فعليا",
  "type": "non_attendance"
}
```

---

## GET /qr_sent_event_details/{id}

المدعوين الذين أُرسل لهم QR (qr_sent=yes).

**URL Params:** `id` = event_id

**Response:**
```json
{
  "Item": { "id": 2225 },
  "data": [ { "id": 1, "name": "Ahmed Ali", "qr_sent": "yes", "accept_count": 3 } ],
  "title": "كل الدعوات (Sent QR)"
}
```

---

## Error Responses

**Validation (400):**
```json
{ "errors": { "custom_event_id": ["The custom event id field is required."] } }
```

**Not Found (404):** Resource not found

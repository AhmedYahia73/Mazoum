# Excel Export APIs (Event Users)

Base URL: `{{base_url}}/admin`

**Headers (required for all):**
```
Authorization: Bearer {admin_token}
Accept: application/json
```

**No Body required** — all are GET requests with event ID in URL.

---

## Common Response Structure

كل الـ endpoints بترجع نفس الشكل:

```json
{
  "Item": {
    "id": 1295,
    "title": "حفل زفاف",
    "date": "2026-06-01",
    "address": "الرياض",
    "...": "..."
  },
  "data": [ ...event_users... ],
  "title": "عنوان القائمة",
  "type": "نوع القائمة"
}
```

**event_user object:**
```json
{
  "id": 1,
  "name": "Ahmed Ali",
  "mobile": "0501234567",
  "users_count": 3,
  "accept_count": 3,
  "scan_count": 2,
  "status": "attend",
  "is_sent": "yes",
  "is_delivered": "yes",
  "is_read": "yes",
  "is_accepted": "yes",
  "is_refused": null,
  "qr_sent": "yes",
  "scan": "yes",
  "scan_at": "2026-06-01 21:00:00",
  "confirmed_at": "2026-05-20 10:00:00",
  "message_id": "wamid.xxx",
  "error_title": null,
  "error": null
}
```

---

## GET /excel-all-invited-users/{id}

كل المدعوين بدون فلتر.

**Filter:** كل الـ event_users

```json
{
  "Item": { "id": 2127, "title": "..." },
  "data": [
    { "id": 1, "name": "Ahmed Ali",    "mobile": "0501234567", "users_count": 3, "status": "attend",     "is_sent": "yes", "qr_sent": "yes", "scan": "yes" },
    { "id": 2, "name": "Sara Mohamed", "mobile": "0501234568", "users_count": 2, "status": "not-attend", "is_sent": "yes", "qr_sent": null,  "scan": null  },
    { "id": 3, "name": "Omar Khalid",  "mobile": "0501234569", "users_count": 4, "status": "hold",       "is_sent": null,  "qr_sent": null,  "scan": null  }
  ],
  "title": "كل المدعوين",
  "type": "all_invited_users"
}
```

---

## GET /excel-event-qr-details/{id}

المدعوين الذين دخلوا بالـ QR فعلياً.

**Filter:** `scan = 'yes'`

```json
{
  "Item": { "id": 1295, "title": "..." },
  "data": [
    { "id": 1, "name": "Ahmed Ali",   "mobile": "0501234567", "users_count": 3, "scan_count": 3, "scan": "yes", "scan_at": "2026-06-01 21:00:00" },
    { "id": 3, "name": "Omar Khalid", "mobile": "0501234569", "users_count": 4, "scan_count": 2, "scan": "yes", "scan_at": "2026-06-01 21:30:00" }
  ],
  "title": "كل المدعوين الذين اكدو الحضور (QR)",
  "is_qr_page": "yes",
  "type": "qr"
}
```

---

## GET /excel-not-attend-event-details/{id}

المدعوين الذين اعتذروا.

**Filter:** `status = 'not-attend'`

```json
{
  "Item": { "id": 1295, "title": "..." },
  "data": [
    { "id": 2, "name": "Sara Mohamed", "mobile": "0501234568", "users_count": 2, "status": "not-attend", "is_refused": "yes", "confirmed_at": null }
  ],
  "title": "كل المدعوين الذين اعتذرو"
}
```

---

## GET /excel-hold-event-details/{id}

المدعوين المنتظرين — لم يُرسل لهم بعد.

**Filter:** `status = 'hold'` AND `is_new_sent = 0` AND `is_sent = null`

```json
{
  "Item": { "id": 1295, "title": "..." },
  "data": [
    { "id": 5, "name": "Nora Salem",  "mobile": "0501234570", "users_count": 2, "status": "hold", "is_sent": null, "is_new_sent": 0 },
    { "id": 6, "name": "Fahad Nasser","mobile": "0501234571", "users_count": 1, "status": "hold", "is_sent": null, "is_new_sent": 0 }
  ],
  "title": "كل المدعوين المنتظرين",
  "type": "hold"
}
```

---

## GET /excel-failed-event-details/{id}

المدعوين الذين أُرسل لهم ولم يردوا.

**Filter:** `is_accepted = null` AND `is_refused = null` AND (`is_new_sent = 1` OR `is_sent != null`)

```json
{
  "Item": { "id": 1831, "title": "..." },
  "data": [
    { "id": 7, "name": "Khalid Hassan", "mobile": "0501234572", "users_count": 3, "status": "sent", "is_sent": "yes", "is_accepted": null, "is_refused": null },
    { "id": 8, "name": "Mona Ali",      "mobile": "0501234573", "users_count": 2, "status": "sent", "is_sent": "yes", "is_accepted": null, "is_refused": null }
  ],
  "title": "لم يتم تاكيد الحضور",
  "type": "failed"
}
```

---

## GET /excel-non-attendance-event-details/{id}

المدعوين الذين أكدوا لكن لم يحضروا فعلياً.

**Filter:** `status = 'attend'` AND `scan = null` AND `is_refused = null`

```json
{
  "Item": { "id": 1295, "title": "..." },
  "data": [
    { "id": 9,  "name": "Reem Sultan", "mobile": "0501234574", "users_count": 3, "accept_count": 3, "scan_count": 0, "status": "attend", "scan": null, "confirmed_at": "2026-05-20 10:00:00" },
    { "id": 10, "name": "Tariq Saad",  "mobile": "0501234575", "users_count": 2, "accept_count": 2, "scan_count": 0, "status": "attend", "scan": null, "confirmed_at": "2026-05-21 09:00:00" }
  ],
  "title": "عدم الحضور فعليا",
  "type": "non_attendance"
}
```

---

## GET /excel-confirmed-event-details/{id}

المدعوين الذين أكدوا الحضور.

**Filter:** `status = 'attend'`

```json
{
  "Item": { "id": 1295, "title": "..." },
  "data": [
    { "id": 1,  "name": "Ahmed Ali",   "mobile": "0501234567", "users_count": 3, "accept_count": 3, "status": "attend", "confirmed_at": "2026-05-20 10:00:00", "qr_sent": "yes" },
    { "id": 9,  "name": "Reem Sultan", "mobile": "0501234574", "users_count": 3, "accept_count": 3, "status": "attend", "confirmed_at": "2026-05-20 11:00:00", "qr_sent": "yes" },
    { "id": 10, "name": "Tariq Saad",  "mobile": "0501234575", "users_count": 2, "accept_count": 2, "status": "attend", "confirmed_at": "2026-05-21 09:00:00", "qr_sent": "yes" }
  ],
  "title": "كل المدعوين الذين ينوون الحضور",
  "type": "confirmed_event_details"
}
```

---

## GET /excel-confirmed-users-web-chat/{id}

المدعوين الذين أكدوا من الشات الويب.

**Filter:** من `event_user_actions` حيث `action = 'accept_event'`

```json
{
  "Item": { "id": 1295, "title": "..." },
  "data": [
    {
      "id": 1,
      "event_id": 1295,
      "event_user_id": 1,
      "mobile": "0501234567",
      "action": "accept_event",
      "msg": null,
      "users_count": 3,
      "event_user": {
        "id": 1,
        "name": "Ahmed Ali",
        "users_count": 3,
        "is_read": "yes",
        "scan": "yes",
        "scan_count": 3
      },
      "event": "حفل زفاف",
      "user_name": "محمد علي",
      "user_id": 5
    }
  ],
  "title": "كل المدعوين الذين اكدوا الحضور من الشات الويب",
  "type": "confirmed_event_details"
}
```

---

## GET /excel-qr-sent-event-details/{id}

المدعوين الذين أُرسل لهم QR.

**Filter:** `qr_sent = 'yes'`

```json
{
  "Item": { "id": 1295, "title": "..." },
  "data": [
    { "id": 1, "name": "Ahmed Ali",   "mobile": "0501234567", "users_count": 3, "accept_count": 3, "qr_sent": "yes", "status": "attend" },
    { "id": 9, "name": "Reem Sultan", "mobile": "0501234574", "users_count": 3, "accept_count": 3, "qr_sent": "yes", "status": "attend" }
  ],
  "title": "كل الدعوات (Sent QR)"
}
```

---

## Error Response (404)

```json
{ "message": "No query results for model [App\\Models\\Events] 1295" }
```

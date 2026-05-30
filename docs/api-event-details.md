# GET /api/user/event-details/{id}

تفاصيل الحدث مع كل إحصائيات المدعوين.

**Base URL:** `https://mazoom.online`

---

## Request

**Method:** `GET`

**URL:** `/api/user/event-details/{id}`

**URL Params:**

| Param | Type | Required |
|-------|------|----------|
| id | integer | ✓ (event_id) |

**Headers:**

| Header | Value | Required |
|--------|-------|----------|
| language | `ar` or `en` | ✓ |
| token | user token | ✓ |
| Accept | `application/json` | ✓ |

---

## Response (200)

```json
{
  "status": true,
  "data": {
    "event": {
      "id": 2225,
      "title": "حفل زفاف",
      "image": "https://mazoom.online/images/xxx.jpg",
      "lat": 24.7136,
      "long": 46.6753,
      "address": "الرياض",
      "showing_qr": "yes",
      "first_name": "محمد",
      "last_name": "علي",
      "date": "2026-06-01",
      "have_reminder": 1,
      "can_replay_messages": 1,
      "sent_remember": null,
      "sending_type": "meta"
    },
    "event_details": [
      {
        "title_en": "all_invited_users",
        "title_ar": "",
        "count": 9,
        "users": [
          { "id": 1, "name": "Ahmed Ali",    "mobile": "0501234567", "users_count": 3, "scan_at": null,                   "confirmed_at": null,                  "scan_count": 0, "scan_status": true  },
          { "id": 2, "name": "Sara Mohamed", "mobile": "0501234568", "users_count": 2, "scan_at": null,                   "confirmed_at": "2026-05-01 20:00:00", "scan_count": 0, "scan_status": true  },
          { "id": 3, "name": "Omar Khalid",  "mobile": "0501234569", "users_count": 4, "scan_at": "2026-05-02 21:00:00", "confirmed_at": "2026-05-01 19:00:00", "scan_count": 3, "scan_status": true  }
        ]
      },
      {
        "title_en": "invitations_not_sent_users",
        "title_ar": "",
        "count": 3,
        "users": [
          { "id": 1, "name": "Ahmed Ali",   "mobile": "0501234567", "users_count": 3, "scan_count": 0, "scan_status": true },
          { "id": 4, "name": "Nora Salem",  "mobile": "0501234570", "users_count": 2, "scan_count": 0, "scan_status": true }
        ]
      },
      {
        "title_en": "confirmed_invitatios_users",
        "title_ar": "",
        "count": 6,
        "users": [
          { "id": 2, "name": "Sara Mohamed", "mobile": "0501234568", "users_count": 2, "scan_at": null,                   "confirmed_at": "2026-05-01 20:00:00", "scan_count": 0, "accept_count": 2, "scan_status": true },
          { "id": 3, "name": "Omar Khalid",  "mobile": "0501234569", "users_count": 4, "scan_at": "2026-05-02 21:00:00", "confirmed_at": "2026-05-01 19:00:00", "scan_count": 3, "accept_count": 4, "scan_status": true }
        ]
      },
      {
        "title_en": "scaned_qr_users",
        "title_ar": "",
        "count": 3,
        "users": [
          { "id": 3, "name": "Omar Khalid", "mobile": "0501234569", "users_count": 4, "scan_at": "2026-05-02 21:00:00", "confirmed_at": "2026-05-01 19:00:00", "scan_count": 3, "scan_status": true }
        ]
      },
      {
        "title_en": "apologized_invitatios_users",
        "title_ar": "",
        "count": 2,
        "users": [
          { "id": 5, "name": "Khalid Nasser", "mobile": "0501234571", "users_count": 2, "scan_at": null, "confirmed_at": null, "scan_count": 0, "scan_status": true }
        ]
      },
      {
        "title_en": "failed_invitatios_users",
        "title_ar": "",
        "count": 3,
        "users": [
          { "id": 6, "name": "Mona Hassan", "mobile": "0501234572", "users_count": 3, "scan_at": null, "confirmed_at": null, "scan_count": 0, "scan_status": true }
        ]
      },
      {
        "title_en": "enterd_events",
        "title_ar": "",
        "count": 2,
        "users": [
          { "id": 1, "name": "Fahad Ali",   "mobile": "0501234573", "scan_qr": "yes" },
          { "id": 2, "name": "Reem Sultan", "mobile": "0501234574", "scan_qr": "yes" }
        ]
      },
      {
        "title_en": "non_attendance_users",
        "title_ar": "",
        "count": 1,
        "users": [
          { "id": 3, "name": "Omar Khalid", "mobile": "0501234569", "users_count": 4, "accept_count": 4, "scan_count": 3, "attendance": 1, "available": 1, "scan_status": true }
        ]
      }
    ],
    "event_users": [
      { "id": 1, "name": "Ahmed Ali",    "mobile": "0501234567", "users_count": 3, "scan_at": null,                   "confirmed_at": null,                  "scan_count": 0, "scan_status": true },
      { "id": 2, "name": "Sara Mohamed", "mobile": "0501234568", "users_count": 2, "scan_at": null,                   "confirmed_at": "2026-05-01 20:00:00", "scan_count": 0, "scan_status": true },
      { "id": 3, "name": "Omar Khalid",  "mobile": "0501234569", "users_count": 4, "scan_at": "2026-05-02 21:00:00", "confirmed_at": "2026-05-01 19:00:00", "scan_count": 3, "scan_status": true }
    ],
    "event_messages": [
      { "id": 1, "name": "Ahmed Ali",    "mobile": "0501234567", "message": "هل الحفل في الموعد المحدد؟",     "created_at": "2026-05-01 10:00:00" },
      { "id": 2, "name": "Sara Mohamed", "mobile": "0501234568", "message": "ما هو عنوان القاعة بالتفصيل؟", "created_at": "2026-05-01 11:30:00" }
    ],
    "event_congratulations_messages": [
      { "id": 1, "name": "Omar Khalid",  "mobile": "0501234569", "message": "مبروك وعقبال المسرات",          "created_at": "2026-05-02 22:00:00" },
      { "id": 2, "name": "Nora Salem",   "mobile": "0501234570", "message": "ألف مبروك للعروسين",            "created_at": "2026-05-02 22:30:00" }
    ]
  }
}
```

---

## event_details sections

| title_en | الوصف |
|----------|-------|
| `all_invited_users` | كل المدعوين — count = مجموع users_count |
| `invitations_not_sent_users` | لم يُرسل لهم بعد (status=hold) |
| `confirmed_invitatios_users` | أكدوا الحضور — count = مجموع accept_count |
| `scaned_qr_users` | دخلوا بالـ QR — count = مجموع scan_count |
| `apologized_invitatios_users` | اعتذروا (status=not-attend) |
| `failed_invitatios_users` | أُرسل لهم ولم يردوا (accept_count=0) |
| `enterd_events` | أفراد العائلة الذين دخلوا |
| `non_attendance_users` | أكدوا لكن لم يحضروا فعلياً — available = accept_count - scan_count |

---

## Error Responses

**Missing language header:**
```json
{ "status": false, "errNum": "E300", "msg": "language is required" }
```

**Invalid/missing token:**
```json
{ "status": false, "errNum": "E100", "msg": "المستخدم مطلوب" }
```

**Event not found:**
```json
{ "status": false, "errNum": "404", "msg": "عفوا هذا الحدث غير موجود مسبقا" }
```

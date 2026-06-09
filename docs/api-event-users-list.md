# GET /api/user/event-users/{id}/{type}

قائمة مدعوين الحدث مع pagination وsearch — بديل للـ `event_details` لكل section منفردة.

**Base URL:** `https://mazoom.online`

---

## Request

**Method:** `GET`

**URL:** `/api/user/event-users/{id}/{type}`

**URL Params:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| id | integer | ✓ | event_id |
| type | string | ✓ | نوع القائمة (see table below) |

**Query Params:**

| Param | Type | Required | Default |
|-------|------|----------|---------|
| search | string | ✗ | بيبحث في name و mobile |
| page | integer | ✗ | 1 |
| per_page | integer | ✗ | 15 |

**Headers:**

| Header | Value | Required |
|--------|-------|----------|
| token | user token | ✓ |
| language | `ar` or `en` | ✓ |
| Accept | `application/json` | ✓ |

---

## Types

| type | الوصف | count يحسب على |
|------|-------|----------------|
| `all_invited_users` | كل المدعوين | `sum(users_count)` |
| `invitations_not_sent_users` | لم يُرسل لهم (status=hold) | `sum(users_count)` |
| `confirmed_invitatios_users` | أكدوا الحضور (is_accepted=yes) | `sum(accept_count)` |
| `scaned_qr_users` | دخلوا بالـ QR (scan=yes) | `sum(scan_count)` |
| `apologized_invitatios_users` | اعتذروا (status=not-attend) | `sum(users_count)` |
| `failed_invitatios_users` | أُرسل لهم ولم يردوا | `sum(users_count)` |
| `send_Qr` | أُرسل لهم QR (qr_sent=yes) | `sum(accept_count)` |
| `confirm_web_users` | أكدوا من الويب | `sum(accept_count)` |
| `non_attendance_users` | أكدوا لكن لم يحضروا | `sum(available)` |
| `enterd_events` | أفراد العائلة كلهم | `count` |
| `scan_enterd_events` | أفراد العائلة الذين دخلوا | `count` |
| `not_scan_enterd_events` | أفراد العائلة لم يدخلوا | `count` |

---

## Response (200)

```json
{
  "status": true,
  "data": {
    "title_en": "confirmed_invitatios_users",
    "count": 45,
    "users": {
      "data": [
        {
          "id": 1,
          "name": "Ahmed Ali",
          "mobile": "0501234567",
          "users_count": 3,
          "accept_count": 3,
          "scan_count": 0,
          "scan_at": null,
          "confirmed_at": "2026-05-20 10:00:00",
          "is_sent": "yes",
          "is_accepted": "yes",
          "is_refused": null,
          "is_delivered": "yes",
          "is_read": "yes",
          "qr_sent": "yes",
          "status": "attend"
        },
        {
          "id": 2,
          "name": "Sara Mohamed",
          "mobile": "0501234568",
          "users_count": 2,
          "accept_count": 2,
          "scan_count": 0,
          "scan_at": null,
          "confirmed_at": "2026-05-21 09:00:00",
          "is_sent": "yes",
          "is_accepted": "yes",
          "is_refused": null,
          "is_delivered": "yes",
          "is_read": "yes",
          "qr_sent": "yes",
          "status": "attend"
        }
      ],
      "current_page": 1,
      "last_page": 3,
      "per_page": 15,
      "total": 30
    }
  }
}
```

**enterd_events / scan_enterd_events / not_scan_enterd_events:**
```json
{
  "status": true,
  "data": {
    "title_en": "enterd_events",
    "count": 5,
    "users": {
      "data": [
        { "id": 1, "name": "Fahad Ali",   "mobile": "0501234573", "scan_qr": "yes" },
        { "id": 2, "name": "Reem Sultan", "mobile": "0501234574", "scan_qr": "no"  }
      ],
      "current_page": 1,
      "last_page": 1,
      "per_page": 15,
      "total": 5
    }
  }
}
```

**non_attendance_users:**
```json
{
  "status": true,
  "data": {
    "title_en": "non_attendance_users",
    "count": 3,
    "users": {
      "data": [
        {
          "id": 3,
          "name": "Omar Khalid",
          "mobile": "0501234569",
          "accept_count": 4,
          "scan_count": 1,
          "available": 3,
          "scan_status": true
        }
      ],
      "current_page": 1,
      "last_page": 1,
      "per_page": 15,
      "total": 1
    }
  }
}
```

---

## Error Responses

**Invalid token:**
```json
{ "status": false, "errNum": "E100", "msg": "المستخدم مطلوب" }
```

**Event not found:**
```json
{ "status": false, "errNum": "404", "msg": "عفوا هذا الحدث غير موجود" }
```

**Invalid type:**
```json
{ "status": false, "errNum": "E400", "msg": "نوع غير صحيح" }
```

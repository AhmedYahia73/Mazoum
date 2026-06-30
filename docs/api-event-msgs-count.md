# Event Messages & Count APIs

Base URL: `{{domain}}/api/user`

**Headers (required for all):**

| Header | Value | Required |
|--------|-------|----------|
| token | user token | ✓ |
| language | `ar` or `en` | ✓ |
| Accept | `application/json` | ✓ |

---

## GET /events/apologize_msgs/{id}

رسائل الاعتذار للحدث مع pagination وsearch.

**URL Params:** `id` = event_id

**Query Params:**

| Param | Type | Required |
|-------|------|----------|
| search | string | ✗ بيبحث في name و mobile |
| page | integer | ✗ default 1 |

**Response (200):**
```json
{
  "apologize_msgs": {
    "data": [
      {
        "id": 1,
        "event_id": 573,
        "name": "Ahmed Ali",
        "mobile": "0501234567",
        "message": "آسف لن أتمكن من الحضور",
        "message_id": null,
        "created_at": "2026-05-01 10:00:00",
        "reply": {
          "id": 5,
          "name": "Admin",
          "mobile": null,
          "message": "شكراً لإخباركم",
          "type": "reply",
          "message_id": 1
        }
      },
      {
        "id": 2,
        "event_id": 573,
        "name": "Sara Mohamed",
        "mobile": "0501234568",
        "message": "للأسف لن أستطيع الحضور",
        "message_id": null,
        "created_at": "2026-05-02 11:00:00",
        "reply": null
      }
    ],
    "current_page": 1,
    "last_page": 2,
    "per_page": 15,
    "total": 20
  }
}
```

---

## GET /events/congratulation_msgs/{id}

رسائل التهنئة للحدث مع pagination وsearch.

**URL Params:** `id` = event_id

**Query Params:**

| Param | Type | Required |
|-------|------|----------|
| search | string | ✗ بيبحث في name و mobile |
| page | integer | ✗ default 1 |

**Response (200):**
```json
{
  "congratulation_msgs": {
    "data": [
      {
        "id": 1,
        "event_id": 573,
        "name": "Omar Khalid",
        "mobile": "0501234569",
        "message": "مبروك وعقبال المسرات",
        "message_id": null,
        "created_at": "2026-06-01 22:00:00",
        "reply": {
          "id": 3,
          "name": "Admin",
          "mobile": null,
          "message": "شكراً جزيلاً",
          "type": "reply",
          "message_id": 1
        }
      },
      {
        "id": 2,
        "event_id": 573,
        "name": "Nora Salem",
        "mobile": "0501234570",
        "message": "ألف مبروك للعروسين",
        "message_id": null,
        "created_at": "2026-06-01 22:30:00",
        "reply": null
      }
    ],
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 35
  }
}
```

---

## GET /event_users_count/{id}

إحصائيات شاملة للحدث — أعداد كل الـ sections بدون بيانات تفصيلية.

**URL Params:** `id` = event_id

**Response (200):**
```json
{
  "status": true,
  "data": {
    "Item": {
      "id": 573,
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
      "sending_type": "meta",
      "resend_qr": null
    },
    "all_invited_users": 500,
    "invitations_not_sent_users": 50,
    "confirmed_invitatios_users": 200,
    "scaned_qr_users": 150,
    "apologized_invitatios_users": 30,
    "failed_invitatios_users": 20,
    "send_Qr": 180,
    "confirm_web_users": 15,
    "non_attendance_users": 50,
    "enterd_events": 10,
    "scan_enterd_events": 8,
    "not_scan_enterd_events": 2,
    "congratulation_msgs": 35,
    "apologize_msgs": 20
  }
}
```

**Fields:**

| Field | الوصف |
|-------|-------|
| `all_invited_users` | مجموع كل الـ users_count |
| `invitations_not_sent_users` | مجموع users_count للمنتظرين |
| `confirmed_invitatios_users` | مجموع accept_count للمؤكدين |
| `scaned_qr_users` | مجموع scan_count للداخلين بـ QR |
| `apologized_invitatios_users` | مجموع users_count للمعتذرين |
| `failed_invitatios_users` | مجموع users_count الذين لم يردوا |
| `send_Qr` | مجموع accept_count الذين أُرسل لهم QR |
| `confirm_web_users` | مجموع accept_count المؤكدين من الويب |
| `non_attendance_users` | confirmed - scaned (لم يحضروا فعلياً) |
| `enterd_events` | عدد أفراد العائلة الكلي |
| `scan_enterd_events` | عدد أفراد العائلة الذين دخلوا |
| `not_scan_enterd_events` | عدد أفراد العائلة لم يدخلوا |
| `congratulation_msgs` | عدد رسائل التهنئة |
| `apologize_msgs` | عدد رسائل الاعتذار |

---

## Error Responses

**Missing/invalid token:**
```json
{ "status": false, "errNum": "E100", "msg": "المستخدم مطلوب" }
```

**Event not found:**
```json
{ "status": false, "errNum": "404", "msg": "عفوا هذا الحدث غير موجود" }
```

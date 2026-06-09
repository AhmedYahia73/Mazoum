# Event Family APIs

Base URL: `{{domain}}/api`

**Headers (required for all):**

| Header | Value | Required |
|--------|-------|----------|
| language | `ar` or `en` | ✓ |
| token | user token | ✓ |
| Accept | `application/json` | ✓ |

---

## POST /save_event_family

حفظ أفراد عائلة جدد للحدث.

**Body (JSON):**
```json
{
  "event_id": 1981,
  "event_users": [
    { "name": "Ahmed Ali",    "mobile": "0501234567" },
    { "name": "Sara Mohamed", "mobile": null }
  ]
}
```

| Field | Type | Required |
|-------|------|----------|
| event_id | integer | ✓ exists in events |
| event_users | array | ✓ |
| event_users.*.name | string | ✓ |
| event_users.*.mobile | numeric | ✗ |

**Response (200):**
```json
{ "status": true, "msg": "تم الحفظ بنجاح" }
```

**Error — missing language:**
```json
{ "status": false, "errNum": "E300", "msg": "language is required" }
```

**Error — validation (400):**
```json
{ "status": false, "errNum": "E001", "msg": { "event_id": ["The event id field is required."] } }
```

---

## POST /update_event_family

تحديث بيانات أفراد العائلة الموجودين.

**Body (JSON):**

الـ key بتاع كل object هو الـ `id` بتاع فرد العائلة:

```json
{
  "old_event_users": {
    "83": { "name": "Ahmed Ali Updated", "mobile": "0501234567" },
    "84": { "name": "Sara Mohamed",      "mobile": null }
  }
}
```

| Field | Type | Required |
|-------|------|----------|
| old_event_users | object (key = family_id) | ✓ |
| old_event_users.{id}.name | string | ✓ |
| old_event_users.{id}.mobile | numeric | ✗ |

**Response (200):**
```json
{ "status": true, "msg": "تم التحديث بنجاح" }
```

---

## GET /event_family/destroy/{id}

حذف فرد من قائمة العائلة.

**URL Params:** `id` = event_family_id

**Response (200):**
```json
{ "status": true, "msg": "تم الحذف بنجاح" }
```

**Error — missing language:**
```json
{ "status": false, "errNum": "E300", "msg": "language is required" }
```

---

## GET /open_event_family/{id}

تسجيل دخول فرد العائلة للحفل — بيحدث `scan_qr` لـ `yes`.

**URL Params:** `id` = event_family_id

**Response (200):**
```json
{ "status": true, "msg": "تم دخول الحفل بنجاح" }
```

**Error — not found (500):**
لو الـ id مش موجود بيرمي `ModelNotFoundException`.

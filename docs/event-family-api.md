# Event Family APIs

Base URL: `https://mazoom.online/admin`

All endpoints require admin authentication.

---

## GET /events/{id}/enter-event

بيانات دخول الحفل — بيجيب تفاصيل الحدث مع قائمة أفراد العائلة.

**URL Params:** `id` = event_id

**Response:**
```json
{
  "Item": {
    "id": 1981,
    "title": "...",
    "status": "...",
    "...": "..."
  },
  "event_family": [
    {
      "id": 608,
      "event_id": 1981,
      "name": "Ahmed",
      "mobile": "0501234567",
      "scan_qr": "no"
    }
  ]
}
```

---

## GET /event_family_search

بحث في أفراد العائلة بالاسم أو الموبايل.

**Query Params:**

| Param | Type | Required |
|-------|------|----------|
| event_id | integer | ✓ |
| name | string | ✗ |
| mobile | string | ✗ |

**Response:**
```json
{
  "event_users": [
    {
      "id": 608,
      "event_id": 1981,
      "name": "Ahmed",
      "mobile": "0501234567",
      "scan_qr": "no"
    }
  ],
  "event_id": 1981
}
```

**Error (400):**
```json
{ "errors": { "event_id": ["The event id field is required."] } }
```

---

## POST /save_event_family

حفظ أفراد عائلة جدد للحدث.

**Body (JSON):**
```json
{
  "event_id": 1981,
  "event_users": [
    { "name": "Ahmed Ali", "mobile": "0501234567" },
    { "name": "Sara Mohamed", "mobile": null }
  ]
}
```

| Field | Type | Required |
|-------|------|----------|
| event_id | integer | ✓ |
| event_users | array | ✓ |
| event_users.*.name | string | ✓ |
| event_users.*.mobile | numeric | ✗ |

**Response:**
```json
{ "success": "تم الحفظ بنجاح" }
```

---

## POST /update_event_family

تحديث بيانات أفراد العائلة الموجودين.

**Body (JSON):**
```json
{
  "event_users": [
    { "id": 608, "name": "Ahmed Ali Updated", "mobile": "0501234567" },
    { "id": 609, "name": "Sara Mohamed", "mobile": null }
  ]
}
```

| Field | Type | Required |
|-------|------|----------|
| event_users | array | ✓ |
| event_users.*.id | integer | ✓ |
| event_users.*.name | string | ✓ |
| event_users.*.mobile | numeric | ✗ |

**Response:**
```json
{ "success": "تم التحديث بنجاح" }
```

---

## GET /open_event_family/{id}

تسجيل دخول فرد العائلة للحفل — بيحدث `scan_qr` لـ `yes`.

**URL Params:** `id` = event_family_id

**Response:**
```json
{ "success": "تم دخول الحفل بنجاح" }
```

---

## GET /event_family/destroy/{id}

حذف فرد من قائمة العائلة.

**URL Params:** `id` = event_family_id

**Response:**
```json
{ "success": "تم الحذف بنجاح" }
```

---

## POST /save_custom_event_family

حفظ أفراد عائلة للـ custom event.

**Body (JSON):**
```json
{
  "custom_event_id": 10,
  "event_users": [
    { "id": 1, "name": "Ahmed Ali", "mobile": "0501234567" },
    { "id": 2, "name": "Sara Mohamed", "mobile": null }
  ]
}
```

| Field | Type | Required |
|-------|------|----------|
| custom_event_id | integer | ✓ |
| event_users | array | ✓ |
| event_users.*.id | integer (event_user id) | ✓ |
| event_users.*.name | string | ✓ |
| event_users.*.mobile | numeric | ✗ |

**Response:**
```json
{ "success": "تم الحفظ بنجاح" }
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

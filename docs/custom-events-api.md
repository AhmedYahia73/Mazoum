# Custom Events APIs

Base URL: `{{base_url}}/admin`

**Headers (required for all):**
```
Authorization: Bearer {token}
Accept: application/json
```

---

## GET /custom_events/deleted_custom_events

الأحداث المحذوفة (soft deleted).

**Query Params:**

| Param | Type | Required |
|-------|------|----------|
| page | integer | ✗ |
| search | string | ✗ (بيبحث في title و address) |

**Response:**
```json
{
  "Item": {
    "data": [
      {
        "id": 10,
        "title": "...",
        "address": "...",
        "deleted_at": "2026-01-01 00:00:00"
      }
    ],
    "current_page": 1,
    "last_page": 3,
    "total": 40
  }
}
```

---

## GET /custom_events/{id}/event-report

تقرير إحصائي للحدث.

**URL Params:** `id` = custom_event_id

**Response:**
```json
{
  "Item": { "id": 10, "title": "..." },
  "visitors_count": 150,
  "qr_count": 80,
  "event_host": 3,
  "congratulation_msg": 25,
  "apologize_msg": 10,
  "apologize_count": 15,
  "confirm_count": 120
}
```

---

## DELETE /custom_events/force_destroy/{id}

حذف نهائي لحدث (حتى لو soft deleted).

**URL Params:** `id` = custom_event_id

**Response:**
```json
{ "success": "تم حذف البيانات نهائياً بنجاح" }
```

---

## POST /custom_events/force_multi_delete

حذف نهائي لأكثر من حدث.

**Body (JSON):**
```json
{
  "items": [10, 11, 12]
}
```

| Field | Type | Required |
|-------|------|----------|
| items | array of integers | ✓ |
| items.* | exists in custom_event | ✓ |

**Response:**
```json
{ "success": "تم حذف البيانات المحددة نهائياً بنجاح" }
```

**Error (400):**
```json
{ "errors": { "items": ["The items field is required."] } }
```

---

## GET /custom_events/confirm_count/{id}

قائمة المدعوين الذين أكدوا الحضور.

**URL Params:** `id` = custom_event_id

**Query Params:**

| Param | Type | Required |
|-------|------|----------|
| search | string | ✗ (بيبحث في name و mobile) |
| page | integer | ✗ |

**Response:**
```json
{
  "Item": { "id": 10, "title": "..." },
  "user_events": {
    "data": [
      {
        "id": 1,
        "name": "Ahmed",
        "mobile": "0501234567",
        "confirm_count": 3,
        "users_count": 5
      }
    ],
    "current_page": 1,
    "total": 20
  }
}
```

---

## GET /custom_events/apologize_count/{id}

قائمة المدعوين الذين اعتذروا.

**URL Params:** `id` = custom_event_id

**Query Params:**

| Param | Type | Required |
|-------|------|----------|
| search | string | ✗ |
| page | integer | ✗ |

**Response:**
```json
{
  "Item": { "id": 10, "title": "..." },
  "user_events": {
    "data": [
      {
        "id": 2,
        "name": "Sara",
        "mobile": "0501234568",
        "apologize_count": 2,
        "users_count": 4
      }
    ],
    "current_page": 1,
    "total": 10
  }
}
```

---

## GET /custom_events/congratulation_msg/{id}

رسائل التهنئة للحدث.

**URL Params:** `id` = custom_event_id

**Response:**
```json
{
  "messages": [
    {
      "id": 1,
      "msg": "مبروك",
      "name": "Ahmed Ali",
      "mobile": "0501234567"
    }
  ]
}
```

---

## GET /custom_events/apologize_msg/{id}

رسائل الاعتذار للحدث.

**URL Params:** `id` = custom_event_id

**Response:**
```json
{
  "messages": [
    {
      "id": 2,
      "msg": "آسف لن أتمكن من الحضور",
      "name": "Sara Mohamed",
      "mobile": "0501234568"
    }
  ]
}
```

---

## POST /custom_events/send_message

إرسال رسالة تهنئة أو اعتذار.

**Body (JSON):**
```json
{
  "custom_event_id": 10,
  "custom_user_id": 5,
  "type": "congratulation",
  "msg": "مبروك الحفل"
}
```

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| custom_event_id | integer | ✓ | exists in custom_event |
| custom_user_id | integer | ✓ | exists in custom_event_users |
| type | string | ✓ | `congratulation` أو `apologize` |
| msg | string | ✓ | |

**Response:**
```json
{
  "message": {
    "id": 5,
    "custom_event_id": 10,
    "custom_user_id": 5,
    "msg": "مبروك الحفل",
    "type": "congratulation"
  },
  "success": "You add data success"
}
```

**Error (400):**
```json
{ "errors": { "type": ["The selected type is invalid."] } }
```

---

## PUT /custom_events/status/{id}

تحديث حالة مدعو (تأكيد أو اعتذار).

**URL Params:** `id` = custom_event_user_id

**Body (JSON):**
```json
{
  "status": "confirm",
  "count": 3
}
```

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| status | string | ✓ | `confirm` أو `apologize` |
| count | integer | ✓ | max = `users_count - confirm_count - apologize_count` |

**Response:**
```json
{ "success": "You update data success" }
```

**Error (400):**
```json
{
  "errors": {
    "count": ["The count may not be greater than 5."]
  }
}
```

---

## Error Responses

**Validation Error (400):**
```json
{ "errors": { "field": ["message"] } }
```

**Not Found (404):** Resource not found

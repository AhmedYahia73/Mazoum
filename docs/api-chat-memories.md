# Chat & Memories APIs

Base URL: `{{domain}}/api/user`

---

## Headers

| Header | Value | Required | Notes |
|--------|-------|----------|-------|
| token | user token | ✓ (most) | ✗ for visitor endpoints |
| language | `ar` or `en` | ✓ |  |
| Accept | `application/json` | ✓ |  |

> endpoints marked **[No Auth]** لا تحتاج token

---

# Chat — Custom Event

## GET /chat/custom_users/{id}

قائمة المدعوين في custom event مرتبين بعدد الرسائل غير المقروءة.

**URL Params:** `id` = custom_event_id

**Query Params:**

| Param | Type | Required | Values |
|-------|------|----------|--------|
| type | string | ✓ | `user` or `visitor` |

**Response:**
```json
{
  "users": [
    { "id": 1, "name": "Ahmed Ali",    "mobile": "0501234567", "un_read_msgs_count": 3 },
    { "id": 2, "name": "Sara Mohamed", "mobile": "0501234568", "un_read_msgs_count": 0 }
  ]
}
```

---

## GET /chat/custom_msgs/{id}

محادثة مع مدعو في custom event.

**URL Params:** `id` = custom_event_user_id

**Response:**
```json
{
  "chat": [
    { "id": 1, "msg": "أهلاً", "image": null, "is_read": true, "user_sent": true,  "date": "2026-05-01", "time": "09:00:00 AM" },
    { "id": 2, "msg": "مرحباً", "image": "https://...", "is_read": false, "user_sent": false, "date": "2026-05-01", "time": "09:05:00 AM" }
  ],
  "custom_event": { "id": 10, "title": "..." }
}
```

---

## GET /chat/custom_msg_read/{id}

تعليم رسائل المدعو كمقروءة (User side).

**URL Params:** `id` = custom_event_user_id

**Response:**
```json
{ "success": "You read msg success" }
```

---

## GET /chat/custom_msg_vistor_read/{id} **[No Auth]**

تعليم رسائل الزائر كمقروءة (Visitor side).

**URL Params:** `id` = custom_event_user_id

**Response:**
```json
{ "success": "You read msg success" }
```

---

## POST /chat/user_send_custom_msg

إرسال رسالة من المستخدم (صاحب الحدث) للمدعو.

**Body (multipart/form-data):**

| Field | Type | Required |
|-------|------|----------|
| custom_user_id | integer | ✓ exists in custom_event_users |
| msg | string | ✗ (msg أو image واحد منهم مطلوب) |
| image | file (image) | ✗ |

**Response:**
```json
{ "success": "You send msg success" }
```

**Error:**
```json
{ "errors": "you must enter msg or image" }
```

---

## POST /chat/event_user_send_custom_msg **[No Auth]**

إرسال رسالة من المدعو لصاحب الحدث (حد الصور 2).

**Body (multipart/form-data):**

| Field | Type | Required |
|-------|------|----------|
| custom_user_id | integer | ✓ |
| msg | string | ✗ |
| image | file (image) | ✗ |

**Response:**
```json
{ "success": "You send msg success" }
```

**Error — image limit:**
```json
{ "errors": "you have limit images 2" }
```

---

# Chat — Regular Event

## GET /chat/event_users/{id}

قائمة المدعوين في regular event مرتبين بعدد الرسائل غير المقروءة.

**URL Params:** `id` = event_id

**Query Params:**

| Param | Type | Required | Values |
|-------|------|----------|--------|
| type | string | ✓ | `user` or `visitor` |

**Response:**
```json
{
  "users": [
    { "id": 1, "name": "Ahmed Ali", "mobile": "0501234567", "un_read_msgs_count": 5 }
  ]
}
```

---

## GET /chat/event_msgs/{id}

محادثة مع مدعو في regular event.

**URL Params:** `id` = event_user_id

**Response:**
```json
{
  "chat": [
    { "id": 1, "msg": "مرحبا", "image": null, "is_read": true, "user_sent": true, "date": "2026-05-01", "time": "10:00:00 AM" }
  ],
  "event": { "id": 2225, "title": "..." }
}
```

---

## GET /chat/event_msg_read/{id}

تعليم رسائل المدعو كمقروءة (User side).

**URL Params:** `id` = event_user_id

**Response:**
```json
{ "success": "You read msg success" }
```

---

## GET /chat/event_msg_vistor_read/{id} **[No Auth]**

تعليم رسائل الزائر كمقروءة (Visitor side).

**URL Params:** `id` = event_user_id

**Response:**
```json
{ "success": "You read msg success" }
```

---

## POST /chat/user_send_event_msg

إرسال رسالة من المستخدم (صاحب الحدث) للمدعو.

**Body (multipart/form-data):**

| Field | Type | Required |
|-------|------|----------|
| event_user_id | integer | ✓ exists in event_users |
| msg | string | ✗ |
| image | file (image) | ✗ |

**Response:**
```json
{ "success": "You send msg success" }
```

---

## POST /chat/event_user_send_event_msg **[No Auth]**

إرسال رسالة من المدعو لصاحب الحدث (حد الصور 2).

**Body (multipart/form-data):**

| Field | Type | Required |
|-------|------|----------|
| event_user_id | integer | ✓ |
| msg | string | ✗ |
| image | file (image) | ✗ |

**Response:**
```json
{ "success": "You send msg success" }
```

---

# Memories — Custom Event

## GET /memories/custom_memories/{id} **[No Auth]**

صور الذكريات لمدعو في custom event.

**URL Params:** `id` = custom_event_user_id

**Response:**
```json
{
  "memories": [
    { "id": 1, "image_url": "https://mazoom.online/storage/custom_events/memories/xxx.jpg", "date": "2026-06-01", "time": "09:00:00 PM" }
  ],
  "custom_event": { "id": 10, "title": "..." }
}
```

---

## POST /memories/send_custom_memories **[No Auth]**

رفع صور ذكريات لـ custom event.

**Body (multipart/form-data):**

| Field | Type | Required |
|-------|------|----------|
| custom_user_id | integer | ✓ exists in custom_event_users |
| images | array of files | ✓ |
| images.* | image file | ✓ |

**Response:**
```json
{ "success": "You add data success" }
```

---

# Memories — Regular Event

## GET /memories/memories/{id} **[No Auth]**

صور الذكريات لمدعو في regular event.

**URL Params:** `id` = event_user_id

**Response:**
```json
{
  "memories": [
    { "id": 1, "image_url": "https://mazoom.online/storage/events/memories/xxx.jpg", "date": "2026-06-01", "time": "09:30:00 PM" }
  ],
  "event": { "id": 2225, "title": "..." }
}
```

---

## POST /memories/send_memories **[No Auth]**

رفع صور ذكريات لـ regular event.

**Body (multipart/form-data):**

| Field | Type | Required |
|-------|------|----------|
| event_user_id | integer | ✓ exists in event_users |
| images | array of files | ✓ |
| images.* | image file | ✓ |

**Response:**
```json
{ "success": "You add data success" }
```

---

# Best Memories

## GET /best_memories/{id}

أفضل الذكريات لـ regular event مع pagination.

**URL Params:** `id` = event_id

**Query Params:** `page` (default 1, 10 per page)

**Response:**
```json
{
  "memories": {
    "data": [
      { "id": 1, "image": "https://mazoom.online/storage/events/memories/xxx.jpg", "time": "09:00:00 PM" }
    ],
    "current_page": 1,
    "last_page": 3,
    "per_page": 10,
    "total": 25
  }
}
```

---

## GET /best_custom_memories/{id}

أفضل الذكريات لـ custom event مع pagination.

**URL Params:** `id` = custom_event_id

**Query Params:** `page` (default 1, 10 per page)

**Response:**
```json
{
  "memories": {
    "data": [
      { "id": 1, "image": "https://mazoom.online/storage/custom_events/memories/xxx.jpg", "time": "09:00:00 PM" }
    ],
    "current_page": 1,
    "last_page": 2,
    "per_page": 10,
    "total": 15
  }
}
```

---

## Common Errors

**Validation (400):**
```json
{ "errors": { "field": ["The field is required."] } }
```

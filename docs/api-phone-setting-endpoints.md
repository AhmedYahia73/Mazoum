# APIs with phone_setting_id

Base URL: `{{base_url}}/admin`

**Headers (required for all):**

| Header | Value | Required |
|--------|-------|----------|
| Authorization | Bearer {admin_token} | ✓ |
| Accept | `application/json` | ✓ |

> `phone_setting_id` = ID من جدول `new_settings` — بيحدد الـ WhatsApp phone number اللي هيبعت منه

---

## POST /send_event_users

إرسال الدعوات للمدعوين.

**Body (JSON):**

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| event_id | integer | ✓ | exists in events |
| users | array | ✓ | |
| users.*.id | integer | ✓ | exists in event_users |
| users.*.users_count | numeric | ✓ | عدد الدعوات |
| phone_setting_id | integer | ✓ | exists in new_settings |
| type | string | ✗ | `image` \| `video` \| `pdf` |

```json
{
  "event_id": 573,
  "phone_setting_id": 2,
  "type": "image",
  "users": [
    { "id": 1, "users_count": 3 },
    { "id": 2, "users_count": 2 }
  ]
}
```

**Response:**
```json
{ "success": "تم الأرسال بنجاح" }
```

---

## POST /send-custom-message

إرسال رسالة مخصصة لمدعوين محددين.

**Body (multipart/form-data):**

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| sending_type | string | ✓ | `old_send` \| `new_send` |
| message | string | ✓ | نص الرسالة |
| event_id | integer | ✓ | |
| users | array | ✓ | `[{id}]` |
| users.*.id | integer | ✓ | |
| phone_setting_id | integer | ✓ | |
| type | string | ✗ | `image` \| `pdf` \| `video` |
| file | file | ✗ | صورة/ملف مرفق |

```json
{
  "sending_type": "old_send",
  "message": "نذكركم بموعد الحفل",
  "event_id": 573,
  "phone_setting_id": 2,
  "users": [{ "id": 1 }, { "id": 2 }]
}
```

**Response:**
```json
{ "success": "تم الأرسال بنجاح" }
```

---

## POST /remember-users-to-event

إرسال تذكير للمدعوين بموعد الحفل.

**Body (multipart/form-data):**

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| sending_type2 | string | ✓ | `old_send` \| `new_send` |
| message2 | string | ✓ | نص التذكير |
| event_id | integer | ✓ | |
| users | array | ✓ | `[{id}]` |
| users.*.id | integer | ✓ | |
| date | string | ✓ | تاريخ الحفل |
| time | string | ✓ | وقت الحفل |
| phone_setting_id | integer | ✓ | |
| file2 | file | ✗ | صورة مرفقة |

```json
{
  "sending_type2": "old_send",
  "message2": "حفل الزفاف",
  "event_id": 573,
  "date": "2026-06-01",
  "time": "20:00",
  "phone_setting_id": 2,
  "users": [{ "id": 1 }, { "id": 2 }]
}
```

**Response:**
```json
{ "success": "تم الأرسال بنجاح" }
```

---

## GET /send-qr/{id}

إعادة إرسال QR لمدعو.

**URL Params:** `id` = event_user_id

**Body (JSON):**

| Field | Type | Required |
|-------|------|----------|
| phone_setting_id | integer | ✓ |

```json
{ "phone_setting_id": 2 }
```

**Response:**
```json
{ "status": "success", "message": "تم أرسال QR Scan  بنجاح" }
```

---

## GET /send-new-qr/{id}

إرسال QR جديد لمدعو (بتعمل accept وتولد QR جديد).

**URL Params:** `id` = event_user_id

**Body (JSON):**

| Field | Type | Required |
|-------|------|----------|
| phone_setting_id | integer | ✓ |

```json
{ "phone_setting_id": 2 }
```

**Response:**
```json
{ "status": "success", "message": "تم أرسال QR Scan  بنجاح" }
```

---

## GET /send-event-location/{id}

إرسال موقع الحفل لمدعو.

**URL Params:** `id` = event_user_id

**Body (JSON):**

| Field | Type | Required |
|-------|------|----------|
| phone_setting_id | integer | ✓ |

```json
{ "phone_setting_id": 2 }
```

**Response:**
```json
{ "success": "تم ارسال الموقع بنجاح" }
```

---

## GET /send-congratulations/{id}

إرسال رسالة تهنئة لكل مدعوي حدث.

**URL Params:** `id` = event_id

**Body (JSON):**

| Field | Type | Required |
|-------|------|----------|
| phone_setting_id | integer | ✓ |

```json
{ "phone_setting_id": 2 }
```

**Response:**
```json
{ "success": "تم ارسال التهنئه بنجاح" }
```

**Error:**
```json
{ "errors": "عفوا لم يتم ارسال تهنئه لبعض المستخدمين" }
```

---

## POST /send-congratulation-messages

إرسال رسائل تهنئة لمدعوين محددين.

**Body (JSON):**

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| sending_type | string | ✓ | `old_send` \| `new_send` |
| event_id | integer | ✓ | |
| users | array | ✓ | `[{id}]` |
| users.*.id | integer | ✓ | |
| phone_setting_id | integer | ✓ | |

```json
{
  "sending_type": "old_send",
  "event_id": 573,
  "phone_setting_id": 2,
  "users": [{ "id": 1 }, { "id": 2 }]
}
```

**Response:**
```json
{ "success": "تم الأرسال بنجاح" }
```

---

## Error Responses

**Validation (400):**
```json
{ "errors": { "phone_setting_id": ["The phone setting id field is required."] } }
```

# Events Lists & Phone Settings APIs

Base URL: `{{base_url}}/admin`

**Headers (required for all):**

| Header | Value | Required |
|--------|-------|----------|
| Authorization | Bearer {admin_token} | ✓ |
| Accept | `application/json` | ✓ |

---

## GET /events/phones_lists

قائمة الـ WhatsApp phone numbers المفعلة — بتستخدم لاختيار `phone_setting_id`.

**No params required.**

**Response:**
```json
{
  "data": [
    { "id": 1, "phone_numer_id": "992972750570172", "phone_number": "966593907079", "country_name": "السعودية" },
    { "id": 2, "phone_numer_id": "344115548775193", "phone_number": "96597378181",  "country_name": "الكويت" }
  ]
}
```

---

## GET /events/all_events

كل الأحداث المفتوحة (`is_open = yes`) مع pagination وsearch وفلتر بالدولة.

**Query Params:**

| Param | Type | Required | Notes |
|-------|------|----------|-------|
| page | integer | ✗ | default 1, 20 per page |
| search | string | ✗ | بيبحث في title, address, first_name, last_name, user.name, user.mobile |
| country_id | integer | ✗ | exists in countries |

**Response:**
```json
{
  "Items": {
    "data": [
      {
        "id": 573,
        "title": "حفل زفاف",
        "address": "الرياض",
        "file": "https://mazoom.online/images/xxx.jpg",
        "user_id": 5,
        "first_name": "محمد",
        "last_name": "علي",
        "date": "2026-06-01",
        "time": "20:00",
        "image": "https://mazoom.online/images/xxx.jpg",
        "assistant_id": null,
        "user": { "id": 5, "name": "محمد علي", "mobile": "0501234567" },
        "employee": null
      }
    ],
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}
```

---

## GET /events/all_current_events

الأحداث الجارية حالياً (`is_open = current`).

**Query Params:** نفس `all_events`

**Response:** نفس `all_events`

---

## GET /events/all_closed_events

الأحداث المغلقة (`is_open = no`).

**Query Params:** نفس `all_events`

**Response:** نفس `all_events`

---

## GET /events/all_deleted_events

الأحداث المحذوفة (soft deleted).

**Query Params:** نفس `all_events`

**Response:** نفس `all_events`

---

## Error Response (400)

```json
{ "errors": { "country_id": ["The selected country id is invalid."] } }
```

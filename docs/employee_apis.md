# Employee & Scan Employee APIs

**Base URL:** `https://mazoom.online/admin`

**Authentication:** All endpoints require a Bearer token via `Authorization: Bearer {token}` header (Sanctum).

**Content-Type:** `application/json` unless uploading files (`multipart/form-data`).

---

## Authentication

### Login
`POST https://mazoom.online/admin-login`

**Body:**
```json
{
  "email": "employee@example.com",
  "password": "password123"
}
```

**Response `200`:**
```json
{
  "token": "1|abc123...",
  "user": { "id": 1, "name": "...", "user_type": "employee" }
}
```

---

## Role Permissions

| Route Group | employee | scan_employee |
|---|---|---|
| Events (read/write) | ✅ | ✅ (read only) |
| Event Users (CRUD) | ✅ | ✅ |
| Event Family (CRUD) | ✅ | ✅ |
| Custom Events (CRUD) | ✅ | ✅ |
| Custom Event Users (CRUD) | ✅ | ✅ |
| Custom Event Family (CRUD) | ✅ | ✅ |
| Settings | ❌ | ❌ |
| Login User (QR Scan) | ✅ | ✅ |

---

## Events

### List Events (KW)
`GET /events`

**Query Params (optional):**
| Param | Type | Description |
|---|---|---|
| search | string | Search by title, address, name |

**Response `200`:**
```json
{
  "Items": {
    "data": [
      {
        "id": 1,
        "title": "حفل زفاف",
        "address": "الكويت",
        "date": "2026-05-01",
        "time": "20:00",
        "user_id": 5,
        "assistant_id": 2,
        "image": "https://mazoom.online/images/xxx.jpg",
        "file": "https://mazoom.online/images/xxx.pdf"
      }
    ],
    "current_page": 1,
    "total": 50
  }
}
```

---

### List SA Events
`GET /sa-events`

Same response structure as above filtered by `country_code = sa`.

---

### List Closed Events
`GET /closed-events`

### List Current Events
`GET /current-events`

### List Deleted Events
`GET /deleted-events`

### List SA Closed Events
`GET /sa-closed-events`

### List SA Current Events
`GET /sa-current-events`

### List SA Deleted Events
`GET /sa-deleted-events`

---

### Show Event
`GET /events/{id}`

**Response `200`:**
```json
{
  "Item": { "id": 1, "title": "...", "date": "...", "time": "..." },
  "event_users": [],
  "event_user": null,
  "actions": null
}
```

---

### Close Event
`GET /close-event/{id}`

### Open Event (Current)
`GET /current-event/{id}`

### Un-close Event
`GET /un-close-event/{id}`

---

### Event Visitors
`GET /events/{id}/event-visitors`

**Query Params (optional):** `search`

**Response `200`:**
```json
{
  "items": { "id": 1, "title": "..." },
  "event_users": { "data": [] },
  "codes": []
}
```

---

### Event Send List
`GET /events/{id}/send-events`

**Query Params (optional):** `search`

---

### Event Report
`GET /events/{id}/event-report`

**Response `200`:**
```json
{
  "Item": {},
  "invitees": 100,
  "qr": 40,
  "confirm_attend": 60,
  "apologize": 5,
  "waiting": 10,
  "not_confirm": 25,
  "send_Qr": 60,
  "not_attend": 20,
  "congratulation_msgs": 3,
  "apologize_msgs": 2
}
```

---

### Event Users (Scanned)
`GET /events/{id}/event-users`

**Query Params (optional):** `search`

---

### Event Location
`GET /events/{id}/event-location`

---

### Enter Event (Scanner Page)
`GET /events/{id}/enter-event`

---

### Scanner
`GET /events/{id}/scanner`

---

### Delete Events (Bulk)
`POST /delete_events`

**Body:**
```json
{
  "events": [1, 2, 3]
}
```

---

## Event Users

### Save Event Users
`POST /save_event_users`

**Body:**
```json
{
  "event_id": 1,
  "event_users": [
    { "name": "أحمد محمد", "mobile": "96512345678", "users_count": 2 }
  ]
}
```

---

### Update Event Users
`POST /update_event_users`

**Body:**
```json
{
  "old_event_users": [
    { "id": 10, "name": "أحمد محمد", "mobile": "96512345678", "users_count": 3 }
  ]
}
```

---

### Send Event Users (Invitations)
`POST /send_event_users`

**Body:**
```json
{
  "event_id": 1,
  "users": [
    { "id": 10, "users_count": 2 }
  ]
}
```

---

### New Send Event Invitation
`POST /new-send-event-invitation`

**Body:**
```json
{
  "event_id": 1,
  "file_type": "image",
  "users": [
    { "id": 10, "users_count": 2 }
  ]
}
```

`file_type`: `image` or `video`

---

### Search Event Users
`GET /event_users_search?event_id=1&search=أحمد`

---

### Search Event Messages
`GET /event_messages_search?event_id=1`

---

### Delete Single Event User
`GET /event_users/destroy/{id}`

---

### Delete Selected Event Users
`POST /delete_selected_event_users`

**Body:**
```json
{
  "users": [
    { "id": 10 },
    { "id": 11 }
  ]
}
```

---

### Update User Mobile
`POST /update-user-mobile`

**Body:**
```json
{
  "event_user_id": 10,
  "mobile": "96512345678",
  "users_count": 2,
  "name": "أحمد محمد"
}
```

---

### Event User History
`GET /event-user-history/{id}`

---

### Send QR
`GET /send-qr/{id}`

### Send New QR
`GET /send-new-qr/{id}`

### Accept User Event
`GET /accept-user-event/{id}`

### Refuse User Event
`GET /refuse-user-event/{id}`

### QR Is Sent
`GET /qr-is-send/{id}`

### Is Send Event
`GET /is-send-event/{id}`

---

### Login User (QR Scan Entry)
`GET /login-user/{id}`

> scan_employee ✅

---

### All Invited Users
`GET /all-invited-users/{id}`

### Event QR Details
`GET /event-qr-details/{id}`

### Confirmed Event Details
`GET /confirmed-event-details/{id}`

### Not Attend Event Details
`GET /not-attend-event-details/{id}`

### Hold Event Details
`GET /hold-event-details/{id}`

### Failed Event Details
`GET /failed-event-details/{id}`

### Non Attendance Event Details
`GET /non-attendance-event-details/{id}`

### QR Sent Event Details
`GET /qr-sent-event-details/{id}`

### Congratulations Messages Details
`GET /congratulations-event-messages-details/{id}`

### Confirmed Users Web Chat
`GET /confirmed-users-web-chat/{id}`

---

### Event Messages
`GET /event-messages/{id}`

### Event Chat
`GET /event-chat/{id}`

---

### Send Custom Message
`POST /send-custom-message`

**Body:**
```json
{
  "event_id": 1,
  "sending_type": "new_send",
  "message": "نص الرسالة",
  "users": [
    { "id": 10 }
  ]
}
```

`sending_type`: `old_send` or `new_send`

---

### Send Congratulation Message
`POST /send-congratulation-message`

### Send Congratulation Messages (Bulk)
`POST /send-congratulation-messages`

### Send Apologize Message
`POST /send-apologize-message`

### Remember Users To Event
`POST /remember-users-to-event`

**Body:**
```json
{
  "event_id": 1,
  "sending_type2": "new_send",
  "message2": "نص التذكير",
  "date": "2026-05-01",
  "time": "20:00",
  "users": [{ "id": 10 }]
}
```

---

### Delete Messages
`POST /delete-messages`

**Body:**
```json
{
  "messags_ids": [
    { "id": 1, "type": "congrate" },
    { "id": 2, "type": "event_message" }
  ]
}
```

`type`: `congrate` or `event_message`

---

### Import Event Users (Excel)
`POST /event-user-import`

**Body:** `multipart/form-data`
| Field | Type | Required |
|---|---|---|
| file | file (xlsx/xls/csv) | yes |
| event_id | integer | yes |

---

### Send Event Location
`GET /send-event-location/{id}`

### Event Report PDF
`GET /event-report/{id}`

---

## Event Family

### Save Event Family
`POST /save_event_family`

**Body:**
```json
{
  "event_id": 1,
  "event_users": [
    { "name": "فاطمة", "mobile": "96512345678" }
  ]
}
```

---

### Update Event Family
`POST /update_event_family`

**Body:**
```json
{
  "event_users": [
    { "id": 5, "name": "فاطمة", "mobile": "96512345678" }
  ]
}
```

---

### Search Event Family
`GET /event_family_search?event_id=1&search=فاطمة`

---

### Delete Event Family Member
`GET /event_family/destroy/{id}`

### Open Event Family (Mark Entry)
`GET /open_event_family/{id}`

---

## Custom Events

### List Custom Events
`GET /custom_events`

**Query Params (optional):** `search`

**Response `200`:**
```json
{
  "Item": {
    "data": [{ "id": 1, "title": "حفل خاص", "date": "2026-05-01" }]
  }
}
```

---

### Show Custom Event
`GET /custom_events/{id}`

### Create Custom Event
`POST /custom_events`

**Body:** `multipart/form-data`
| Field | Type | Required |
|---|---|---|
| title | string | yes |
| user_id | integer | yes |
| color | string | yes |
| language | string (`ar`/`en`) | yes |
| address | string | yes |
| date | date (`Y-m-d`) | yes |
| time | string (`H:i`) | yes |
| image | file | yes |
| assistant_id | integer | no |
| scan_assistant_id | integer | no |
| send_type | string | no |
| name_qr | boolean | no |
| number_qr | boolean | no |
| qr_height | integer | no |
| qr_width | integer | no |
| qr_x | integer | no |
| qr_y | integer | no |
| lat | decimal | no |
| lng | decimal | no |
| video | file | no |

---

### Update Custom Event
`POST /custom_events/{id}`

Same fields as create (all optional except required ones).

---

### Delete Custom Event
`GET /custom_events/destroy/{id}`

---

### Custom Event Visitors
`GET /custom_events/{id}/event-visitors`

**Query Params (optional):** `search`

---

### Custom Event Users
`GET /custom_events/{id}/event-users`

**Query Params (optional):** `search`

**Response `200`:**
```json
{
  "Item": {},
  "user_events": { "data": [] },
  "invetations": 100,
  "attendance": 40
}
```

---

### Custom Event Report
`GET /custom_events/{id}/event-report`

**Response `200`:**
```json
{
  "Item": {},
  "visitors_count": 100,
  "qr_count": 40
}
```

---

### Custom Event Enter
`GET /custom_events/{id}/enter-event`

---

## Custom Event Users

### Save Custom Event Users
`POST /save_custom_event_users`

**Body:**
```json
{
  "custom_event_id": 1,
  "event_users": [
    { "name": "محمد علي", "users_count": 2, "mobile": "96512345678" }
  ]
}
```

---

### Update Custom Event Users
`POST /update_custom_event_users`

**Body:**
```json
{
  "event_users": [
    { "id": 5, "name": "محمد علي", "users_count": 3, "mobile": "96512345678" }
  ]
}
```

---

### Search Custom Event Users
`GET /custom_event_users_search?custom_event_id=1&search=محمد`

---

### Delete Single Custom Event User
`GET /custom_event_users/destroy/{id}`

---

### Delete Selected Custom Event Users
`POST /delete_selected_custom_event_users`

**Body:**
```json
{
  "users": [1, 2, 3]
}
```

---

### New Send Custom Event Invitation
`POST /new-send-custom-event-invitation`

**Body:**
```json
{
  "custom_event_id": 1,
  "users": [1, 2, 3]
}
```

---

### Import Custom Event Users (Excel)
`POST /custom-event-user-import`

**Body:** `multipart/form-data`
| Field | Type | Required |
|---|---|---|
| file | file (xlsx/xls/csv) | yes |
| custom_event_id | integer | yes |

---

## Custom Event Family

### Save Custom Event Family
`POST /save_custom_event_family`

**Body:**
```json
{
  "custom_event_id": 1,
  "event_users": [
    { "id": 10, "name": "سارة", "mobile": "96512345678" }
  ]
}
```

---

### Update Custom Event Family
`POST /update_custom_event_family`

**Body:**
```json
{
  "event_users": [
    { "id": 5, "name": "سارة", "mobile": "96512345678" }
  ]
}
```

---

### Search Custom Event Family
`GET /custom_event_family_search?custom_event_id=1&search=سارة`

---

### Delete Custom Event Family Member
`GET /custom_event_family/destroy/{id}`

### Open Custom Event Family (Mark Entry)
`GET /open_custom_event_family/{id}`

---

## Error Responses

### 403 Forbidden
```json
{ "errors": "Unauthorized" }
```

### 400 Validation Error
```json
{
  "errors": {
    "field_name": ["The field is required."]
  }
}
```

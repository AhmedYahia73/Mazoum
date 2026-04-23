# Scan Employee APIs

**Base URL:** `https://mazoom.online/admin`

**Authentication:** `Authorization: Bearer {token}` (Sanctum)

**Content-Type:** `application/json` unless uploading files (`multipart/form-data`)

---

## Events

### List Events (KW)
`GET /events`

**Query Params (optional):** `search`

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
        "image": "https://mazoom.online/images/xxx.jpg"
      }
    ],
    "current_page": 1,
    "total": 50
  }
}
```

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

### List SA Events
`GET /sa-events`

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

### Close Event
`GET /close-event/{id}`

**Response `200`:**
```json
{ "success": "You close event success" }
```

---

### Open Event (Current)
`GET /current-event/{id}`

**Response `200`:**
```json
{ "success": "You open event success" }
```

---

### Un-close Event
`GET /un-close-event/{id}`

**Response `200`:**
```json
{ "success": "You un close event success" }
```

---

### Delete Events (Bulk)
`POST /delete_events`

**Body:**
```json
{
  "events": [1, 2, 3]
}
```

**Response `200`:**
```json
{ "success": "You delete data success" }
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

**Response `200`:**
```json
{ "success": "تم الحفظ بنجاح" }
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

**Response `200`:**
```json
{ "success": "تم التحديث بنجاح" }
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

**Response `200`:**
```json
{
  "event_users": { "data": [] }
}
```

---

### Search Event Messages
`GET /event_messages_search?event_id=1`

---

### Delete Single Event User
`GET /event_users/destroy/{id}`

**Response `200`:**
```json
{ "success": "تم الحذف بنجاح" }
```

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

**Response `200`:**
```json
{ "success": "تم حذف العناصر المختاره" }
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

**Response `200`:**
```json
{ "success": "تم التحديث بنجاح" }
```

---

### Event User History
`GET /event-user-history/{id}`

---

### Send QR
`GET /send-qr/{id}`

**Response `200`:**
```json
{ "success": "تم أرسال QR Scan بنجاح" }
```

---

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

**Response `200`:**
```json
{ "success": "تم عمل QR Scan بنجاح" }
```

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

### Event Report
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

---

### Show Custom Event
`GET /custom_events/{id}`

---

### Custom Event Visitors
`GET /custom_events/{id}/event-visitors`

### Custom Event Users
`GET /custom_events/{id}/event-users`

### Custom Event Report
`GET /custom_events/{id}/event-report`

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

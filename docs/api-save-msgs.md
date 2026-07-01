# Save Message APIs

Base URL: `{{domain}}/api`

> هذه الـ endpoints **لا تحتاج token** — بتشتغل بالـ mobile number مباشرة.

---

## POST /save-congratulation-msg

حفظ رسالة تهنئة من ضيف للحدث.

**Headers:**

| Header | Value | Required |
|--------|-------|----------|
| Accept | `application/json` | ✓ |

**Body (JSON):**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| phone | string | ✓ | رقم موبايل الضيف (بـ + أو بدونه) |
| msg | string | ✓ | نص رسالة التهنئة |

**Example:**
```json
{
  "phone": "+966501234567",
  "msg": "مبروك وعقبال المسرات"
}
```

**Response (200):**
```json
null
```
> الـ endpoint مش بيرجع response body — بيحفظ الرسالة وبيبعت notification لصاحب الحدث.

**ما بيحصل:**
- بيدور على الـ event_user بالـ mobile في الأحداث المفتوحة (`is_open = yes/current`)
- بيحفظ رسالة التهنئة في `congratulation_messages`
- بيبعت notification لصاحب الحدث

---

## POST /save-apology-msg

حفظ رسالة اعتذار من ضيف للحدث.

**Headers:**

| Header | Value | Required |
|--------|-------|----------|
| Accept | `application/json` | ✓ |

**Body (JSON):**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| phone | string | ✓ | رقم موبايل الضيف (بـ + أو بدونه) |
| msg | string | ✓ | نص رسالة الاعتذار |

**Example:**
```json
{
  "phone": "+966501234567",
  "msg": "آسف لن أتمكن من الحضور بسبب ظروف طارئة"
}
```

**Response (200):**
```json
null
```

**ما بيحصل:**
- بيدور على الـ event_user بالـ mobile في الأحداث المفتوحة (`is_open = yes/current`)
- بيحفظ رسالة الاعتذار في `event_messages`
- بيبعت notification لصاحب الحدث

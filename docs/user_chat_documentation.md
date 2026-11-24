# دليل تكامل شات المستخدم والـ Realtime
# User Chat API & Realtime Integration Guide

دليل توثيقي شامل لمطوري واجهات المستخدم (تطبيق الموبايل Flutter / React Native والويب) للربط مع نظام المحادثات اللحظية المباشرة بين العميل وإدارة المنصة / الدعم الفني، مع دعم إرسال النصوص، الصور، والتسجيلات الصوتية (`Voice Notes`).

---

## 📌 معلومات الاتصال الأساسية (Base Information)

- **Base URL:** `https://mazoom.online`
- **User Chat Base API:** `https://mazoom.online/api/user/user_chat` (أو الرابط العام `https://mazoom.online/api/user_chat`)
- **Authentication:** مطلوب لجميع الـ Endpoints (Token / Sanctum Bearer)

---

## 📌 الهيدرز المشتركة (Common Headers)

تتطلب جميع طلبات الـ API تمرير الهيدرز التالية:

| Header | النوع | مطلوب | الوصف | مثال |
|---|---|---|---|---|
| `token` | String | **نعم** (أو Bearer) | توكن المستخدم المسجل في التطبيق | `a1b2c3d4e5f6...` |
| `Authorization` | String | بديل | توكن Sanctum في حال استخدامه | `Bearer 45\|xyz...` |
| `Accept` | String | **نعم** | صيغة الرد المطلوبة | `application/json` |
| `Accept-Language` | String | اختياري | لغة الردود (`ar` أو `en`) - الافتراضي: `ar` | `ar` |

> 💡 **ملاحظة:** يدعم النظام استخراج هوية المستخدم تلقائياً سواء قمت بتمرير الهيدر `token` أو هيدر `Authorization: Bearer <token>`.

---

## 🚀 قائمة الـ APIs الخاصة بالمستخدم

### 1. إرسال رسالة إلى الإدارة (Send Message)

تُستخدم لإرسال رسالة جديدة من المستخدم إلى الإدارة / الدعم. تدعم الرسالة النص أو الصورة أو التسجيل الصوتي، أو مزيجاً منها.

- **Endpoint:** `/api/user/user_chat/send_message`
- **Method:** `POST`
- **Content-Type:** `multipart/form-data`

#### معلمات الطلب (Body Form-Data):

| الحقل (Key) | النوع | إلزامي | الوصف | القيود |
|---|---|---|---|---|
| `msg` | Text | اختياري* | نص الرسالة | أقصى حد: نص مفتوح |
| `image` | File | اختياري* | ملف صورة مرفقة | `jpeg, png, jpg, gif, webp` (بحد أقصى 20MB) |
| `voice` | File | اختياري* | ملف تسجيل صوتي (`Voice File`) | `mp3, m4a, wav, ogg, aac, webm, 3gp` (بحد أقصى 50MB) |

> ⚠️ **تنبيه:** يجب إرسال حقل واحد على الأقل من الثلاثة (`msg` أو `image` أو `voice`).

#### مثال على استجابة النجاح (200 OK):
```json
{
  "status": true,
  "message": "تم إرسال الرسالة بنجاح",
  "data": {
    "id": 105,
    "user_id": 42,
    "admin_id": null,
    "is_admin": false,
    "msg": "مرحباً، لدي استفسار بخصوص مناسبتي القادمة",
    "is_admin_read": false,
    "is_user_read": true,
    "image": null,
    "voice": "user_chats/voices/kL89N2...m4a",
    "image_url": null,
    "voice_url": "https://mazoom.online/storage/user_chats/voices/kL89N2...m4a",
    "date": "2026-11-23",
    "time": "07:15:30 AM",
    "created_at": "2026-11-23T05:15:30.000000Z",
    "updated_at": "2026-11-23T05:15:30.000000Z"
  }
}
```

#### استجابات الأخطاء المحتملة:
- `400 Bad Request`: لم يتم إرسال أي نص أو صورة أو صوت.
  ```json
  {
    "status": false,
    "errors": "يجب إرسال نص أو صورة أو ملف صوتي (You must provide msg, image, or voice file)."
  }
  ```
- `401 Unauthorized`: التوكن غير صالح أو غير موجود.
- `422 Unprocessable Entity`: صيغة الملف المرفوع غير مدعومة أو الحجم تجاوز الحد الأقصى.

---

### 2. جلب سجل رسائل المحادثة (Get Chat Messages)

تُستخدم لتحميل سجل الرسائل السابقة بين المستخدم المسجل حالياً وإدارة التطبيق مع الترقيم الصفحي (`Pagination`).

- **Endpoint:** `/api/user/user_chat/messages`
- **Method:** `GET`

#### معلمات الاستعلام (Query Parameters):

| المعلمة | النوع | مطلوب | القيمة الافتراضية | الوصف |
|---|---|---|---|---|
| `page` | Integer | اختياري | `1` | رقم الصفحة المطلوب جلبها |
| `per_page` | Integer | اختياري | `50` | عدد الرسائل في كل صفحة |

#### مثال على استجابة النجاح (200 OK):
```json
{
  "status": true,
  "data": {
    "user": {
      "id": 42,
      "name": "أحمد الشمري",
      "mobile": "966500000000",
      "email": "ahmed@example.com"
    },
    "messages": {
      "current_page": 1,
      "data": [
        {
          "id": 101,
          "user_id": 42,
          "admin_id": null,
          "is_admin": false,
          "msg": "السلام عليكم",
          "is_admin_read": true,
          "is_user_read": true,
          "image": null,
          "voice": null,
          "image_url": null,
          "voice_url": null,
          "date": "2026-11-23",
          "time": "06:40:00 AM"
        },
        {
          "id": 102,
          "user_id": 42,
          "admin_id": 1,
          "is_admin": true,
          "msg": "وعليكم السلام، كيف يمكننا مساعدتك؟",
          "is_admin_read": true,
          "is_user_read": true,
          "image": null,
          "voice": null,
          "image_url": null,
          "voice_url": null,
          "date": "2026-11-23",
          "time": "06:42:15 AM"
        }
      ],
      "first_page_url": "https://mazoom.online/api/user/user_chat/messages?page=1",
      "from": 1,
      "last_page": 1,
      "next_page_url": null,
      "path": "https://mazoom.online/api/user/user_chat/messages",
      "per_page": 50,
      "prev_page_url": null,
      "to": 2,
      "total": 2
    }
  }
}
```

---

### 3. تعليم جميع رسائل المحادثة كمقروءة (Mark All Conversation As Read)

تُستدعى فور فتح شاشة الشات من قبل المستخدم ليتم تعليم كافة رسائل الأدمن التي لم تُقرأ بعد كرسائل مقروءة (`is_user_read = true`).

- **Endpoint:** `/api/user/user_chat/read_all`
- **Method:** `POST` (يدعم أيضاً `GET`)

#### مثال على استجابة النجاح (200 OK):
```json
{
  "status": true,
  "user_id": 42,
  "reader": "user",
  "updated_count": 3,
  "message": "تم تحديث حالة القراءة بنجاح / Conversation marked as read successfully"
}
```

> ⚡ **تأثير لحظي (Realtime Trigger):** هذا الاستدعاء يُطلق فورياً حدث `user_chat_read_event` إلى لوحة تحكم الأدمن حتى تظهر علامة المقروء في شاشته دون الحاجة لعمل Refresh.

---

## ⚡ التكامل اللحظي (Real-Time WebSockets Integration)

يعتمد النظام على **Pusher Channels / Laravel Echo** لإرسال واستقبال الرسائل وتحديث حالة القراءة في أجزاء من الثانية.

### 1. إعدادات الاتصال (Connection Credentials)

- **Broadcaster:** `pusher`
- **App Key:** `239duDE3289768hggdorkxsmFS234521` (أو من متغير `PUSHER_APP_KEY`)
- **Cluster:** `mt1`
- **wsHost:** `mazoom.online`
- **wsPort / wssPort:** `443` (`forceTLS: true`)
- **Auth Endpoint (للقناة الخاصة):** `POST https://mazoom.online/api/broadcasting/auth`
  - يقبل الهيدر `token: <user_token>` أو `Authorization: Bearer <token>`

---

### 2. القنوات والأحداث (Channels & Events)

#### القناة المخصصة للمستخدم (Channel Name):
اسم القناة الخاصة بالمستخدم (Pusher SDK) وتعتمد على `user_id` الخاص به:
```
private-user-chat.{user_id}
```
*مثال:* إذا كان معرّف المستخدم هو `42`، تكون القناة الخاصة:
```
private-user-chat.42
```
*(في `Laravel Echo` استخدم `Echo.private('user-chat.42')` — ويُتاح أيضاً الاشتراك في القناة العامة `user-chat.42`).*

---

#### أ) حدث استلام رسالة جديدة (`user_chat_event`)
يتم إطلاقه تلقائياً عند وصول أي رسالة جديدة (سواء أرسلها الأدمن أو المستخدم من جهاز آخر).

- **اسم الحدث (Event Name):** `user_chat_event` (أو `.user_chat_event` حسب الـ SDK)

**شكل الـ Payload اللحظي:**
```json
{
  "id": 106,
  "user_id": 42,
  "admin_id": 3,
  "msg": "تم تأكيد طلبك بنجاح، استمع للتسجيل المرفق لمزيد من التفاصيل",
  "image": "https://mazoom.online/storage/user_chats/images/sample.jpg",
  "voice": "https://mazoom.online/storage/user_chats/voices/note.m4a",
  "is_admin": true,
  "is_admin_read": true,
  "is_user_read": false,
  "date": "2026-11-23",
  "time": "07:20:00 AM",
  "created_at": "2026-11-23T05:20:00.000000Z"
}
```

**منطق الفرونت إند (Frontend Handling):**
1. إذا كانت `is_admin == true`: الرسالة واردة من الإدارة -> قم بعرضها في الجانب المقابل وتشغيل صوت استلام رسالة.
2. إذا كانت شاشة المحادثة مفتوحة حالياً أمام المستخدم، استدعِ فوراً `POST /api/user/user_chat/read_all` لتأكيد القراءة.
3. إذا كانت تحتوي على `voice` (رابط غير فارغ)، اعرض المشغل الصوتي (Audio Player) مع إمكانية التشغيل والإيقاف.
4. إذا كانت تحتوي على `image`، اعرض الصورة المصغرة مع إمكانية النقر للتكبير.

---

#### ب) حدث قراءة الأدمن لرسائل المستخدم (`user_chat_read_event`)
يتم إطلاقه عندما يفتح الأدمن محادثة المستخدم ويقرأ رسائله.

- **اسم الحدث (Event Name):** `user_chat_read_event`

**شكل الـ Payload اللحظي:**
```json
{
  "user_id": 42,
  "reader": "admin",
  "updated_count": 2,
  "date": "2026-11-23",
  "time": "07:22:10 AM"
}
```

**منطق الفرونت إند (Frontend Handling):**
- عند استلام هذا الحدث والـ `reader == "admin"`:
  قم بتحديث حالة الرسائل المرسلة من المستخدم (`is_admin == false`) واجعل علامة القراءة مزدوجة/زرقاء (Seen Checkmarks `is_admin_read = true`).

---

## 💻 نماذج برمجية جاهزة (Frontend Code Snippets)

### أ) كود فلاتر (Flutter / Dart - Pusher Channels)

```dart
import 'package:pusher_channels_flutter/pusher_channels_flutter.dart';
import 'dart:convert';

class UserChatRealtimeService {
  PusherChannelsFlutter pusher = PusherChannelsFlutter.getInstance();

  Future<void> initPusher(int currentUserId, Function(Map<String, dynamic>) onNewMessage, Function(Map<String, dynamic>) onMessagesRead) async {
    try {
      await pusher.init(
        apiKey: "YOUR_PUSHER_APP_KEY",
        cluster: "mt1",
        onEvent: (event) {
          if (event.channelName == "user-chat.$currentUserId") {
            final data = jsonDecode(event.data);
            
            if (event.eventName == "user_chat_event") {
              onNewMessage(Map<String, dynamic>.from(data));
            } else if (event.eventName == "user_chat_read_event") {
              onMessagesRead(Map<String, dynamic>.from(data));
            }
          }
        },
      );

      // الاشتراك في القناة الخاصة بالمستخدم
      await pusher.subscribe(channelName: "user-chat.$currentUserId");
      await pusher.connect();
    } catch (e) {
      print("Pusher Init Error: $e");
    }
  }

  Future<void> disconnect(int currentUserId) async {
    await pusher.unsubscribe(channelName: "user-chat.$currentUserId");
    await pusher.disconnect();
  }
}
```

---

### ب) كود جافاسكريبت (JavaScript / Vue / React with Laravel Echo)

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const echo = new Echo({
    broadcaster: 'pusher',
    key: 'YOUR_PUSHER_APP_KEY',
    cluster: 'mt1',
    forceTLS: true
});

const currentUserId = 42; // رقم المستخدم الحالي

// الاشتراك في قناة المستخدم
echo.channel(`user-chat.${currentUserId}`)
    .listen('.user_chat_event', (messageData) => {
        console.log("رسالة جديدة لحظياً:", messageData);
        // أضف الرسالة إلى قائمة الرسائل في الـ UI
        messagesList.push(messageData);
        
        // إذا كان الشات مفتوحاً، علّم الرسائل كمقروءة
        markAllAsReadApi();
    })
    .listen('.user_chat_read_event', (readData) => {
        console.log("قام الأدمن بقراءة الرسائل:", readData);
        // تحديث أيقونة الصح المزدوج
        messagesList.forEach(msg => {
            if (!msg.is_admin) msg.is_admin_read = true;
        });
    });
```

---

## 📞 خاصية المكالمات الصوتية والمرئية (Agora.io Voice & Video Calling)

تتيح هذه الخاصية للمستخدم إجراء مكالمة صوتية أو فيديو مباشرة مع الدعم/الأدمن، أو استقبال المكالمات الواردة منهم عبر خدمة **Agora.io** ذات الـ 10,000 دقيقة مجانية شهرياً.

### 1. بدء المكالمة من جانب العميل (Start Call)
- **Endpoint:** `POST /api/user/user_chat/call/start`
- **Body:**
  - `call_type`: `"voice"` أو `"video"` (اختياري - الافتراضي: `"voice"`).
- **الرد (200 OK):**
  ```json
  {
    "status": true,
    "channel_name": "call_u42_1700000000_123",
    "token": "006970ca35de60c4464a8038a14ec10882dIAB...",
    "app_id": "YOUR_AGORA_APP_ID",
    "uid": 42,
    "call_type": "voice"
  }
  ```
  *يقوم التطبيق بالانضمام لقناة Agora فوراً باستخدام `channel_name` و `token` و `uid`، ويتم إطلاق إشعار رنين لحظي للأدمن.*

### 2. قبول مكالمة واردة وتوليد التوكن (Accept Incoming Call)
- **Endpoint:** `POST /api/user/user_chat/call/token`
- **Body:**
  - `channel_name`: اسم القناة المستلمة في إشعار الرنين اللحظي.
- **الرد (200 OK):**
  ```json
  {
    "status": true,
    "token": "006970ca35de60c4464a8038a14ec10882dIAB...",
    "app_id": "YOUR_AGORA_APP_ID",
    "channel_name": "call_u42_1700000000_123",
    "uid": 42
  }
  ```
  *ينضم العميل للقناة مباشرة وتتحول المكالمة إلى جارية.*

### 3. إنهاء أو رفض المكالمة (End / Reject Call)
- **Endpoint:** `POST /api/user/user_chat/call/end`
- **Body:**
  - `channel_name`: اسم القناة.
  - `reason`: `"ended"` أو `"rejected"` أو `"missed"` أو `"busy"` أو `"cancelled"`.

### 4. أحداث المكالمات اللحظية عبر Pusher (`user_call_event`)
يستمع العميل لنفس القناة `user-chat.{user_id}` للحدث `user_call_event`:
- **عند وجود مكالمة واردة من الأدمن (`action: "incoming"`):**
  ```json
  {
    "action": "incoming",
    "user_id": 42,
    "channel_name": "call_u42_1700000000_123",
    "call_type": "voice",
    "caller_id": 1,
    "caller_name": "الدعم الفني",
    "caller_role": "admin"
  }
  ```
  *يعرض التطبيق شاشة الرنين فورا مع زر قبول ورفض.*
- **عند قبول المكالمة (`action: "accepted"`):** يتم فتح الاتصال.
- **عند انتهاء/رفض المكالمة (`action: "ended"` / `"rejected"`):** يتم إغلاق شاشة المكالمة والخروج من Agora.


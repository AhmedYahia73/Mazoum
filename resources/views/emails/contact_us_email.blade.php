@component('mail::message')


  <h2 style="text-align: right">عزيزي المدير</h2>
       
  <span style="text-align: right;display:block"> تفاصيل الرساله </span>
     
     اسم المستخدم: {{ $details['name'] }}
           
      البريد الالكتروني للمستخدم: {{ $details['email'] }}

      رقم الموبيل: {{ $details['mobile'] }}
     
      الرساله: {{ $details['message'] }} 
      
      تاريخ انشاء الرساله: {{ $details['created_at'] }} 
      


{{ 'https://www.superpower.ae/' }}

@endcomponent

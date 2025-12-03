<!DOCTYPE html>
<html class=''>

   <head>

    <meta charset='UTF-8'>
    <meta name="robots" content="noindex">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF Token -->

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.2/css/font-awesome.min.css'>

    <style class="cp-pen-styles">

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: #E6EAEA;
            font-family: "proxima-nova", "Source Sans Pro", sans-serif;
            font-size: 1em;
            letter-spacing: 0.1px;
            color: #32465a;
            text-rendering: optimizeLegibility;
            text-shadow: -1px 1px 1px rgba(0, 0, 0, 0.004);
            -webkit-font-smoothing: antialiased;
            background-image:url({{ asset('event-background-v2.jpg') }}?{{ rand() }});
      		background-size: cover;
    		background-repeat: no-repeat;
        }


      #frame .content {
         background-image:url({{ asset('event-background-v5.jpg') }}?{{ rand() }});
      		background-size: contain;
      }



      	html, body {
            box-sizing: border-box;
        }
        *, *:before, *:after {
            box-sizing: inherit;
        }

        #frame {
            width: 95%;
            min-width: 320px;
            max-width: 1000px;
            height: 92vh;
            min-height: 300px;
            max-height: 720px;
            background: #E6EAEA;
        }

        #frame #sidepanel {
            float: right;
            min-width: 280px;
            max-width: 340px;
            width: 40%;
            height: 100%;
            background: #2c3e50;
            color: #f5f5f5;
            overflow: hidden;
            position: relative;
        }


        #frame #sidepanel #profile {
            width: 80%;
            margin: 25px auto;
        }

        #frame #sidepanel #profile.expanded .wrap {
            height: 210px;
            line-height: initial;
        }

        #frame #sidepanel #profile.expanded .wrap p {
            margin-top: 20px;
        }

        #frame #sidepanel #profile.expanded .wrap i.expand-button {
            -moz-transform: scaleY(-1);
            -o-transform: scaleY(-1);
            -webkit-transform: scaleY(-1);
            transform: scaleY(-1);
            filter: FlipH;
            -ms-filter: "FlipH";
        }

        #frame #sidepanel #profile .wrap {
            height: 60px;
            line-height: 60px;
            overflow: hidden;
            -moz-transition: 0.3s height ease;
            -o-transition: 0.3s height ease;
            -webkit-transition: 0.3s height ease;
            transition: 0.3s height ease;
        }


        #frame #sidepanel #profile .wrap img {
            width: 50px;
            border-radius: 50%;
            padding: 3px;
            border: 2px solid #e74c3c;
            height: auto;
            float: right;
            cursor: pointer;
            -moz-transition: 0.3s border ease;
            -o-transition: 0.3s border ease;
            -webkit-transition: 0.3s border ease;
            transition: 0.3s border ease;
        }

        #frame #sidepanel #profile .wrap img.online {
            border: 2px solid #2ecc71;
        }

        #frame #sidepanel #profile .wrap img.away {
            border: 2px solid #f1c40f;
        }

        #frame #sidepanel #profile .wrap img.busy {
            border: 2px solid #e74c3c;
        }

        #frame #sidepanel #profile .wrap img.offline {
            border: 2px solid #95a5a6;
        }

        #frame #sidepanel #profile .wrap p {
            float: right;
            margin-right: 15px;
        }

        #frame #sidepanel #profile .wrap i.expand-button {
            float: left;
            margin-top: 23px;
            font-size: 0.8em;
            cursor: pointer;
            color: #435f7a;
        }

        #frame #sidepanel #profile .wrap #status-options {
            position: absolute;
            opacity: 0;
            visibility: hidden;
            width: 150px;
            margin: 70px 0 0 0;
            border-radius: 6px;
            z-index: 99;
            line-height: initial;
            background: #435f7a;
            -moz-transition: 0.3s all ease;
            -o-transition: 0.3s all ease;
            -webkit-transition: 0.3s all ease;
            transition: 0.3s all ease;
        }


        #frame #sidepanel #profile .wrap #status-options.active {
            opacity: 1;
            visibility: visible;
            margin: 75px 0 0 0;
        }


        #frame #sidepanel #profile .wrap #status-options:before {
            content: '';
            position: absolute;
            width: 0;
            height: 0;
            border-right: 6px solid transparent;
            border-left: 6px solid transparent;
            border-bottom: 8px solid #435f7a;
            margin: -8px 24px 0 0;
        }

        #frame #sidepanel #profile .wrap #status-options ul {
            overflow: hidden;
            border-radius: 6px;
        }


        #frame #sidepanel #profile .wrap #status-options ul li {
            padding: 15px 18px 30px 0;
            display: block;
            cursor: pointer;
        }

        #frame #sidepanel #profile .wrap #status-options ul li:hover {
            background: #496886;
        }

        #frame #sidepanel #profile .wrap #status-options ul li span.status-circle {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin: 5px 0 0 0;
        }

        #frame #sidepanel #profile .wrap #status-options ul li span.status-circle:before {
            content: '';
            position: absolute;
            width: 14px;
            height: 14px;
            margin: -3px -3px 0 0;
            background: transparent;
            border-radius: 50%;
            z-index: 0;
        }

        #frame #sidepanel #profile .wrap #status-options ul li p {
            padding-right: 12px;
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-online span.status-circle {
            background: #2ecc71;
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-online.active span.status-circle:before {
            border: 1px solid #2ecc71;
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-away span.status-circle {
            background: #f1c40f;
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-away.active span.status-circle:before {
            border: 1px solid #f1c40f;
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-busy span.status-circle {
            background: #e74c3c;
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-busy.active span.status-circle:before {
            border: 1px solid #e74c3c;
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-offline span.status-circle {
            background: #95a5a6;
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-offline.active span.status-circle:before {
            border: 1px solid #95a5a6;
        }

        #frame #sidepanel #profile .wrap #expanded {
            padding: 100px 0 0 0;
            display: block;
            line-height: initial !important;
        }

        #frame #sidepanel #profile .wrap #expanded label {
            float: right;
            clear: both;
            margin: 0 0 5px 8px;
            padding: 5px 0;
        }

        #frame #sidepanel #profile .wrap #expanded input {
            border: none;
            margin-bottom: 6px;
            background: #32465a;
            border-radius: 3px;
            color: #f5f5f5;
            padding: 7px;
            width: calc(100% - 43px);
        }

        #frame #sidepanel #profile .wrap #expanded input:focus {
            outline: none;
            background: #435f7a;
        }

        #frame #sidepanel #search {
            border-top: 1px solid #32465a;
            border-bottom: 1px solid #32465a;
            font-weight: 300;
        }

        #frame #sidepanel #search label {
            position: absolute;
            margin: 10px 20px 0 0;
        }

        #frame #sidepanel #search input {
            font-family: "proxima-nova", "Source Sans Pro", sans-serif;
            padding: 10px 46px 10px 0;
            width: calc(100% - 25px);
            border: none;
            background: #32465a;
            color: #f5f5f5;
        }

        #frame #sidepanel #search input:focus {
            outline: none;
            background: #435f7a;
        }

        #frame #sidepanel #search input::-webkit-input-placeholder {
            color: #f5f5f5;
        }

        #frame #sidepanel #search input::-moz-placeholder {
            color: #f5f5f5;
        }

        #frame #sidepanel #search input:-ms-input-placeholder {
            color: #f5f5f5;
        }

        #frame #sidepanel #search input:-moz-placeholder {
            color: #f5f5f5;
        }

        #frame #sidepanel #contacts {
            height: calc(100% - 177px);
            overflow-y: scroll;
            overflow-x: hidden;
        }

        #frame #sidepanel #contacts.expanded {
            height: calc(100% - 334px);
        }

        #frame #sidepanel #contacts::-webkit-scrollbar {
            width: 8px;
            background: #2c3e50;
        }

        #frame #sidepanel #contacts::-webkit-scrollbar-thumb {
            background-color: #243140;
        }

        #frame #sidepanel #contacts ul li.contact {
            position: relative;
            padding: 10px 0 15px 0;
            font-size: 0.9em;
            cursor: pointer;
        }

        #frame #sidepanel #contacts ul li.contact:hover {
            background: #32465a;
        }

        #frame #sidepanel #contacts ul li.contact.active {
            background: #32465a;
            border-left: 5px solid #435f7a;
        }

        #frame #sidepanel #contacts ul li.contact.active span.contact-status {
            border: 2px solid #32465a !important;
        }

        #frame #sidepanel #contacts ul li.contact .wrap {
            width: 88%;
            margin: 0 auto;
            position: relative;
        }

        #frame #sidepanel #contacts ul li.contact .wrap span {
            position: absolute;
            right: 0;
            margin: -2px -2px 0 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid #2c3e50;
            background: #95a5a6;
        }

        #frame #sidepanel #contacts ul li.contact .wrap span.online {
            background: #2ecc71;
        }

        #frame #sidepanel #contacts ul li.contact .wrap span.away {
            background: #f1c40f;
        }

        #frame #sidepanel #contacts ul li.contact .wrap span.busy {
            background: #e74c3c;
        }

        #frame #sidepanel #contacts ul li.contact .wrap img {
            width: 40px;
            border-radius: 50%;
            float: right;
            margin-left: 10px;
        }

        #frame #sidepanel #contacts ul li.contact .wrap .meta {
            padding: 5px 0 0 0;
        }

        #frame #sidepanel #contacts ul li.contact .wrap .meta .name {
            font-weight: 600;
        }

        #frame #sidepanel #contacts ul li.contact .wrap .meta .preview {
            margin: 5px 0 0 0;
            padding: 0 0 1px;
            font-weight: 400;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            -moz-transition: 1s all ease;
            -o-transition: 1s all ease;
            -webkit-transition: 1s all ease;
            transition: 1s all ease;
        }

        #frame #sidepanel #contacts ul li.contact .wrap .meta .preview span {
            position: initial;
            border-radius: initial;
            background: none;
            border: none;
            padding: 0 0 0 2px;
            margin: 0 1px 0 0;
            opacity: .5;
        }

        #frame #sidepanel #bottom-bar {
            position: absolute;
            width: 100%;
            bottom: 0;
        }

        #frame #sidepanel #bottom-bar button {
            float: right;
            border: none;
            width: 50%;
            padding: 10px 0;
            background: #32465a;
            color: #f5f5f5;
            cursor: pointer;
            font-size: 0.85em;
            font-family: "proxima-nova", "Source Sans Pro", sans-serif;
        }


        #frame #sidepanel #bottom-bar button:focus {
            outline: none;
        }

        #frame #sidepanel #bottom-bar button:nth-child(1) {
            border-left: 1px solid #2c3e50;
        }

        #frame #sidepanel #bottom-bar button:hover {
            background: #435f7a;
        }

        #frame #sidepanel #bottom-bar button i {
            margin-left: 3px;
            font-size: 1em;
        }


        #frame .content {
            float: left;
            width: 100%;
            height: 100%;
            overflow: hidden;
            position: relative;
        }

        #frame .content .contact-profile {
            width: 100%;
            height: 60px;
            line-height: 60px;
            background: #f5f5f5;
        }

        #frame .content .contact-profile img {
            width: 40px;
            border-radius: 50%;
            float: right;
            margin: 9px 9px 0 12px;
        }

        #frame .content .contact-profile p {
            float: right;
        }

        #frame .content .contact-profile .social-media {
            float: left;
        }

        #frame .content .contact-profile .social-media i {
            margin-right: 14px;
            cursor: pointer;
        }

        #frame .content .contact-profile .social-media i:nth-last-child(1) {
            margin-left: 20px;
        }

        #frame .content .contact-profile .social-media i:hover {
            color: #435f7a;
        }

        #frame .content .messages {
            height: auto;
            min-height: calc(100% - 128px);
            max-height: calc(100% - 128px);
            overflow-y: scroll;
            overflow-x: hidden;
            width: 100%;
        }

        #frame .content .messages::-webkit-scrollbar {
            width: 8px;
            background: transparent;
        }

        #frame .content .messages::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.3);
        }

        #frame .content .messages ul li {
            display: inline-block;
            clear: both;
            float: right;
            margin: 15px 15px 5px 15px;
            width: calc(100% - 25px);
            font-size: 0.9em;
        }

        #frame .content .messages ul li:nth-last-child(1) {
            margin-bottom: 20px;
        }

        #frame .content .messages ul li.sent .msg_img {
            margin: 6px 0 0 8px;
        }

        #frame .content .messages ul li.sent .msg_content {
            background: #435f7a;
            color: #f5f5f5;
        }

        #frame .content .messages ul li.replies .msg_img {
            float: left;
            margin: 6px 8px 0 0;
        }

        #frame .content .messages ul li.replies .msg_content {
            background: #f5f5f5;
            float: left;
        }

        #frame .content .messages ul li .msg_img {
            width: 22px;
            border-radius: 50%;
            float: right;
        }

        #frame .content .messages ul li .msg_content {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 20px;
            max-width: 205px;
            line-height: 130%;
        }

        #frame .content .message-input {
            position: absolute;
            bottom: 0;
            width: 100%;
            z-index: 99;
        }

        #frame .content .message-input .wrap {
            position: relative;
        }

        #frame .content .message-input .wrap input {
            font-family: "proxima-nova", "Source Sans Pro", sans-serif;
            float: right;
            border: none;
            width: calc(100% - 50px);
            padding: 11px 8px 10px 32px;
            font-size: 0.8em;
            color: #32465a;
            height: 56px;
        }

        #frame .content .message-input .wrap input:focus {
            outline: none;
        }

        #frame .content .message-input .wrap .attachment {
            position: absolute;
            left: 60px;
            z-index: 4;
            margin-top: 10px;
            font-size: 1.1em;
            color: #435f7a;
            opacity: .5;
            cursor: pointer;
        }

        #frame .content .message-input .wrap .attachment:hover {
            opacity: 1;
        }

        #frame .content .message-input .wrap button {
            float: left;
            border: none;
            width: 50px;
            padding: 12px 0;
            cursor: pointer;
            background: #32465a;
            color: #f5f5f5;
            height: 56px;
        }

        #frame .content .message-input .wrap button:hover {
            background: #435f7a;
        }

        #frame .content .message-input .wrap button:focus {
            outline: none;
        }

        #frame .content {
			border: 1px solid #CCC;
        }

        html,body {
            direction: rtl;
            text-align: right;
        }



        .card-img-top {
		    border-top-left-radius: 20px;
    		border-top-right-radius: 20px;
        }

        .card {
          border-radius: 20px;
        }

        @media screen and (min-width: 900px) {
            #frame .content {
                width: 100%;
            }
        }

        @media screen and (min-width: 735px) {
            #frame .content .messages ul li .msg_content {
                max-width: 300px;
            }
        }

        @media screen and (max-width: 735px) {

            #frame #sidepanel {
                width: 58px;
                min-width: 58px;
            }

            #frame #sidepanel #profile {
                width: 100%;
                margin: 0 auto;
                padding: 5px 0 0 0;
                background: #32465a;
            }

            #frame #sidepanel #profile .wrap {
                height: 55px;
            }

            #frame #sidepanel #profile .wrap img {
                width: 40px;
                margin-right: 4px;
            }

            #frame #sidepanel #profile .wrap p {
                display: none;
            }

            #frame #sidepanel #profile .wrap i.expand-button {
                display: none;
            }

            #frame #sidepanel #profile .wrap #status-options ul li span.status-circle {
                width: 14px;
                height: 14px;
            }

            #frame #sidepanel #profile .wrap #status-options ul li span.status-circle:before {
                height: 18px;
                width: 18px;
            }

            #frame #sidepanel #profile .wrap #status-options ul li p {
                display: none;
            }

            #frame #sidepanel #profile .wrap #status-options {
                width: 58px;
                margin-top: 57px;
            }

            #frame #sidepanel #profile .wrap #status-options.active {
                margin-top: 62px;
            }

            #frame #sidepanel #profile .wrap #status-options:before {
                margin-right: 23px;
            }

            #frame #sidepanel #profile .wrap #status-options ul li {
                padding: 15px 22px 35px 0;
            }

            #frame #sidepanel #search {
                display: none;
            }

            #frame #sidepanel #contacts {
                height: calc(100% - 149px);
                overflow-y: scroll;
                overflow-x: hidden;
            }

            #frame #sidepanel #contacts::-webkit-scrollbar {
                display: none;
            }

            #frame #sidepanel #contacts ul li.contact {
                padding: 6px 8px 46px 0;
            }

            #frame #sidepanel #contacts ul li.contact .wrap {
                width: 100%;
            }

            #frame #sidepanel #contacts ul li.contact .wrap img {
                margin-left: 0px;
            }

            #frame #sidepanel #contacts ul li.contact .wrap .meta {
                display: none;
            }

            #frame #sidepanel #bottom-bar button {
                float: none;
                width: 100%;
                padding: 15px 0;
            }

            #frame #sidepanel #bottom-bar button:nth-child(1) {
                border-left: none;
                border-bottom: 1px solid #2c3e50;
            }

            #frame #sidepanel #bottom-bar button i {
                font-size: 1.3em;
            }

            #frame #sidepanel #bottom-bar button span {
                display: none;
            }

            #frame .content {
                width: 100%;
                min-width: 300px !important;
                padding-bottom: 50px !important;
            }

            #frame .content .messages {
                height: auto;
              /* @if($actions != null && $actions->count() > 0)
              height: auto;
              @else
              height: calc(100vh - 200px);
              @endif */
            }

            #frame .content .message-input .wrap input {
                padding: 15px 8px 16px 32px;
            }

            #frame .content .message-input .wrap .attachment {
                margin-top: 17px;
                left: 65px;
            }

            #frame .content .message-input .wrap button {
                padding: 16px 0;
            }

            #frame .content .messages ul li .msg_content {
              max-width: 300px;
            }

          	#frame {
                width: 100%;
                max-width: 100%;
                height: 100%;
            }

            html body {
                min-height: 100% !important;
                margin-top: 0px !important;
                margin-bottom: 0px !important;
            }
        }


        @media (max-width: 375px) {

            #frame .content .messages ul li {
                margin: 0;
                width: 100%;
            }

            #messages_container ul {
                display: block;
                overflow: hidden;
                clear: both;
                width: 100%;
                height: 100%;
                padding: 15px
            }

            #frame .content .messages ul li {
                margin-bottom: 20px !important
            }

            #frame {
                width: 100%;
                max-width: 100%;
                height: 100%;
            }

            html body {
                min-height: 100% !important;
                margin-top: 0px !important;
                margin-bottom: 0px !important;
            }

        }

        @media (max-device-width: 768px) {
            html body {
                min-height: 100% !important;
                margin-top: 0px !important;
                margin-bottom: 0px !important;
            }
        }

        @media screen and (max-width: 360px) {
            #frame {
                width: 100%;
                height: 100vh;
            }
        }


    </style>

   </head>



   <body>

      <div id="frame">
         <div class="content">


            <div class="contact-profile">
               <img src="{{ asset('user-profile.png') }}" style="width: 40px;height:40px" alt="" />
               <p> {{ $event_user->name }} </p>
            </div>


            <div class="messages" id="messages_container">
               <ul>

                  <li class="sent">
                     <img class="msg_img" src="{{ asset('user-profile.png') }}" style="width: 22px;height:22px" alt="" />
                     <div class="msg_content" style="padding: 1px;">
                        <div class="card">

                          	@if($event_user->event && $event_user->event->file)
                            	<img class="card-img-top" src="{{ $event_user->event->file }}" alt="Card image cap">
                          	@endif

                            <div class="card-body">
                              <p class="card-text" style="color: #000;">
                                {{ $event_user->name }}
                              </p>
                              <p class="card-text" style="color: #000;">
                                {{ @$event_user->event->title }}
                              </p>
                              <ul style="margin: 0;padding: 0;">
                                <li style="color: #000;padding: 0;margin: 0;">
                                    - عند وصول الدعوة  يمكنكـم تأكيد الحضـــور أو الإعتذار من خلال الضغـط علـى( قبـول الدعـــوة ) أو ( إعتذار  الدعوة ) ليصلكم الكود الخــاص بدخــول المناسبـة 🌹
                                </li>
                                <li style="color: #000;padding: 0;margin: 0;">
                                    - الإرسال عن طريـق شركة Mazoom لتنظيم المناسبات الخاصـة .🌹
                                </li>
                              </ul>
                              <div style="display: block;text-align: center;">
                                <button type="button" data-action="accept_event" class="btn btn-primary @if(get_action('accept_event',$event_id,$event_user_id,$mobile) == null) event_btn @endif" @if(get_action('accept_event',$event_id,$event_user_id,$mobile) != null) disabled="disabled" @endif style="margin-top: 10px;margin-bottom: 10px;">
                                    قبول الدعــوة
                                </button>
                                <button type="button" data-action="refuse_event" class="btn btn-primary @if(get_action('refuse_event',$event_id,$event_user_id,$mobile) == null) event_btn @endif" @if(get_action('refuse_event',$event_id,$event_user_id,$mobile) != null) disabled="disabled" @endif style="margin-top: 10px;margin-bottom: 10px;">
                                    إعتذار الدعــوة
                                </button>
                                <button type="button" data-action="location_event" class="btn btn-primary  @if(get_action('location_event',$event_id,$event_user_id,$mobile) == null) event_btn @endif" @if(get_action('location_event',$event_id,$event_user_id,$mobile) != null) disabled="disabled" @endif>
                                    موقع المناسبـة
                                </button>
                              </div>
                            </div>
                          </div>
                     </div>
                  </li>

                 @if($actions != null && $actions->count() > 0)
                   @foreach($actions as $action)

                 		@if($action->action == 'accept_event')

                 			<li class="sent">
                               <img class="msg_img" src="{{ asset('user-profile.png') }}" alt="" />
                               <div class="msg_content">
                                  <p>
                                      قبول الدعــوة
                                  </p>
                               </div>
                            </li>


                            <li class="replies">
                                <img class="msg_img" src="http://emilcarlsson.se/assets/harveyspecter.png" style="width: 22px;height:22px" alt="" />
                                <div class="msg_content">
                                    <p>
                                        شكراً , تم تأكيد حضوركم بنحاح👍
                                    </p>
                                </div>
                            </li>


                            <li class="replies">
                                <img class="msg_img" src="http://emilcarlsson.se/assets/harveyspecter.png" style="width: 22px;height:22px" alt="" />
                                <div class="msg_content" style="padding: 1px;">
                                <div class="card">

                                    @php
                                        $qr_row = App\Models\Qr_Code::where('event_id',$event_user->event_id)->where('event_user_id',$event_user->id)->latest()->first();
                                    @endphp

                                    @if($qr_row && $qr_row->qr)
                                        <img class="card-img-top" src="{{ asset('qr_code/'.$qr_row->qr) }}" alt="Card image cap">
                                    @endif

                                    <div class="card-body">
                                        <p class="card-text" style="color: #000;">
                                            {{ $event_user->name }}
                                        </p>
                                        <ul style="margin: 0;padding: 0;">
                                            <li style="color: #000;padding: 0;margin: 0;">
                                                - شكرا  لقبـــــولك الدعــوة
                                            </li>
                                        <li style="color: #000;padding: 0;margin: 0;">
                                            - كـــود الدخــــول الخـــاص بالمنـاسبــة .
                                        </li>
                                        <li style="color: #000;padding: 0;margin: 0;">
                                            - الكود للأستعمال مرة واحدة فقط وخــاص بالشـركـــة .
                                        </li>
                                        </ul>
                                    </div>
                                    </div>
                                </div>
                            </li>


                            <li class="replies">
                                <img class="msg_img" src="http://emilcarlsson.se/assets/harveyspecter.png" style="width: 22px;height:22px" alt="" />
                                <div class="msg_content">
                                    <div class="card">
                                        <div class="card-body">
                                        <p class="card-text" style="color: #000;font-weight: bold;">
                                            مرحبا
                                        </p>
                                        <p class="card-text" style="color: #000;">
                                            للتأكيد هل تم إســتلام كود QR الدخول الخاص بكم من الشركة
                                        </p>
                                        <div style="display: block;text-align: center;">
                                            <button type="button" data-action="not_received_qr" class="btn btn-primary @if(get_action('not_received_qr',$event_id,$event_user_id,$mobile) == null) event_btn @endif" @if(get_action('not_received_qr',$event_id,$event_user_id,$mobile) != null) disabled="disabled" @endif style="margin-top: 10px;margin-bottom: 10px;">
                                                لا لم استلم كود الدخول
                                            </button>
                                            <button type="button" data-action="yes_received_qr" class="btn btn-primary @if(get_action('yes_received_qr',$event_id,$event_user_id,$mobile) == null) event_btn @endif" @if(get_action('yes_received_qr',$event_id,$event_user_id,$mobile) != null) disabled="disabled" @endif style="margin-top: 10px;margin-bottom: 10px;">
                                                نعم استلمت كود الدخول
                                            </button>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li class="replies">
                                <img class="msg_img" src="http://emilcarlsson.se/assets/harveyspecter.png" style="width: 22px;height:22px" alt="" />
                                <div class="msg_content">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="card-text" style="color: #000;">
                                            هل تريد إرسال تهنئة لصاحب المناسبة💐
                                        </p>
                                        <div style="display: block;text-align: center;">
                                        <button type="button" data-action="yes_receive_congratulation" class="btn btn-primary @if(get_action('yes_receive_congratulation',$event_id,$event_user_id,$mobile) == null) event_btn @endif" @if(get_action('yes_receive_congratulation',$event_id,$event_user_id,$mobile) != null) disabled="disabled" @endif style="margin-top: 10px;margin-bottom: 10px;">
                                            نعم
                                        </button>
                                        <button type="button" data-action="no_receive_congratulation" class="btn btn-primary @if(get_action('no_receive_congratulation',$event_id,$event_user_id,$mobile) == null) event_btn @endif" @if(get_action('no_receive_congratulation',$event_id,$event_user_id,$mobile) != null) disabled="disabled" @endif style="margin-top: 10px;margin-bottom: 10px;">
                                            لا
                                        </button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </li>

                 		@elseif($action->action == 'yes_received_qr')

                            <li class="sent">
                                <img class="msg_img" src="{{ asset('user-profile.png') }}" alt="" />
                                <div class="msg_content">
                                <p>
                                    نعم استلمت كود الدخول
                                </p>
                                </div>
                            </li>

                            <li class="replies">
                                <img class="msg_img" src="http://emilcarlsson.se/assets/harveyspecter.png" style="width: 22px;height:22px" alt="" />
                                <div class="msg_content">
                                <div class="card" style="background: transparent;border: 0;color: #FFF;">
                                    <div class="card-body">
                                        <p class="card-text" style="color: #000;">
                                            .شكراً لك نراكم في الحفل
                                        </p>
                                    </div>
                                    </div>
                                </div>
                            </li>

                        @elseif($action->action == 'not_received_qr')

                            <li class="sent">
                                <img class="msg_img" src="{{ asset('user-profile.png') }}" alt="" />
                                <div class="msg_content">
                                <p>
                                    لا لم استلم كود الدخول
                                </p>
                                </div>
                            </li>

                        @elseif($action->action == 'yes_receive_congratulation'&& $action->msg == null)

                            <li class="sent">
                                <img class="msg_img" src="{{ asset('user-profile.png') }}" alt="" />
                                <div class="msg_content">
                                <p>
                                    نعم
                                </p>
                                </div>
                            </li>

                            <li class="replies">
                                <img class="msg_img" src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
                                <div class="msg_content">
                                <p>
                                    أكتب رسالتك الأن يسمح بإرسال نص فقط 📨
                                </p>
                                </div>
                            </li>

                        @elseif($action->action == 'yes_receive_congratulation' && $action->msg != null)

                            <li class="sent">
                                <img class="msg_img" src="{{ asset('user-profile.png') }}" alt="" />
                                <div class="msg_content">
                                <p>
                                    {{ $action->msg }}
                                </p>
                                </div>
                            </li>

                            <li class="replies">
                                <img class="msg_img" src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
                                <div class="msg_content">
                                <p>
                                    شكرا لتواصـلك تم إرسال رسالتك لصاحب المناسبـة.
                                </p>
                                </div>
                            </li>

                        @elseif($action->action == 'no_receive_congratulation')

                            <li class="sent">
                                <img class="msg_img" src="{{ asset('user-profile.png') }}" alt="" />
                                <div class="msg_content">
                                <p>
                                    لا
                                </p>
                                </div>
                            </li>

                            <li class="replies">
                                <img class="msg_img" src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
                                <div class="msg_content">
                                <p>
                                    شكراً لك يسعـدنـا أن نراكـم من جـديـد.
                                </p>
                                </div>
                            </li>

                        @elseif($action->action == 'resend_qr')

                            <li class="replies">
                                <img class="msg_img" src="http://emilcarlsson.se/assets/harveyspecter.png" style="width: 22px;height:22px" alt="" />
                                <div class="msg_content"  style="padding: 1px;">
                                <div class="card">

                                    @php
                                        $qr_row = App\Models\Qr_Code::where('event_id',$event_user->event_id)->where('event_user_id',$event_user->id)->latest()->first();
                                    @endphp

                                    @if($qr_row && $qr_row->qr)
                                        <img class="card-img-top" src="{{ asset('qr_code/'.$qr_row->qr) }}" alt="Card image cap">
                                    @endif

                                    <div class="card-body">
                                        <p class="card-text" style="color: #000;">
                                            {{ $event_user->name }}
                                        </p>
                                        <ul style="margin: 0;padding: 0;">
                                            <li style="color: #000;padding: 0;margin: 0;">
                                                - شكرا  لقبـــــولك الدعــوة
                                            </li>
                                        <li style="color: #000;padding: 0;margin: 0;">
                                            - كـــود الدخــــول الخـــاص بالمنـاسبــة .
                                        </li>
                                        <li style="color: #000;padding: 0;margin: 0;">
                                            - الكود للأستعمال مرة واحدة فقط وخــاص بالشـركـــة .
                                        </li>
                                        </ul>
                                    </div>
                                    </div>
                                </div>
                            </li>

                        @elseif($action->action == 'refuse_event')

                            <li class="sent">
                                <img class="msg_img" src="{{ asset('user-profile.png') }}" style="width: 22px;height:22px" alt="" />
                                <div class="msg_content">
                                <div class="card" style="background: transparent;border: 0;color: #FFF;">
                                    <div class="card-body">
                                        <p class="card-text" style="color: #fff;">
                                            إعتذار الدعــوة
                                        </p>
                                    </div>
                                    </div>
                                </div>
                            </li>

                            <li class="replies">
                                <img class="msg_img" src="http://emilcarlsson.se/assets/harveyspecter.png" style="width: 22px;height:22px" alt="" />
                                <div class="msg_content">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="card-text" style="color: #000;">
                                            - يؤسفنـا ذلك , نراكم في مناسبات أخرى 👍
                                        </p>
                                        <p class="card-text" style="color: #000;">
                                            - هل ترغب بإرسال أعتذار إلى صاحب المناسبـة ؟
                                        </p>
                                        <div style="display: block;text-align: center;">
                                            <button type="button" data-action="yes_receive_apology" class="btn btn-primary @if(get_action('yes_receive_apology',$event_id,$event_user_id,$mobile) == null) event_btn @endif" @if(get_action('yes_receive_apology',$event_id,$event_user_id,$mobile) != null) disabled="disabled" @endif style="margin-top: 10px;margin-bottom: 10px;">
                                                نعم أريد
                                            </button>
                                            <button type="button" data-action="no_receive_apology" class="btn btn-primary @if(get_action('no_receive_apology',$event_id,$event_user_id,$mobile) == null) event_btn @endif" @if(get_action('no_receive_apology',$event_id,$event_user_id,$mobile) != null) disabled="disabled" @endif style="margin-top: 10px;margin-bottom: 10px;">
                                                لا أريد
                                            </button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </li>

                        @elseif($action->action == 'yes_receive_apology'&& $action->msg == null)

                            <li class="sent">
                                <img class="msg_img" src="{{ asset('user-profile.png') }}" style="width: 22px;height:22px" alt="" />
                                <div class="msg_content">
                                <div class="card" style="background: transparent;border: 0;color: #FFF;">
                                    <div class="card-body">
                                        <p class="card-text" style="color: #fff;">
                                            نعم أريد
                                        </p>
                                    </div>
                                    </div>
                                </div>
                            </li>


                            <li class="replies">
                                <img class="msg_img" src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
                                <div class="msg_content">
                                <p>
                                    أكتب رسالتك الأن يسمح بإرسال نص فقط 📨
                                </p>
                                </div>
                            </li>

                        @elseif($action->action == 'yes_receive_apology' && $action->msg != null)

                            <li class="sent">
                                <img class="msg_img" src="{{ asset('user-profile.png') }}" style="width: 22px;height:22px" alt="" />
                                <div class="msg_content">
                                <div class="card" style="background: transparent;border: 0;color: #FFF;">
                                    <div class="card-body">
                                        <p class="card-text" style="color: #fff;">
                                            {{ $action->msg }}
                                        </p>
                                    </div>
                                    </div>
                                </div>
                            </li>


                            <li class="replies">
                                <img class="msg_img" src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
                                <div class="msg_content">
                                <p>
                                    شكرا لتواصـلك تم إرسال رسالتك لصاحب المناسبـة.
                                </p>
                                </div>
                            </li>

                        @elseif($action->action == 'no_receive_apology')

                            <li class="sent">
                                <img class="msg_img" src="{{ asset('user-profile.png') }}" alt="" />
                                <div class="msg_content">
                                <p>
                                    لا أريد
                                </p>
                                </div>
                            </li>

                            <li class="replies">
                                <img class="msg_img" src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
                                <div class="msg_content">
                                <p>
                                    شكراً لك يسعـدنـا أن نراكـم من جـديـد.
                                </p>
                                </div>
                            </li>

                        @elseif($action->action == 'location_event')

                            @php
                                $event = App\Models\Events::find($event_id);
                                $location = $event != null ? 'https://www.google.com/maps?q=' . $event->lat . ',' . $event->long : null;
                            @endphp

                            <li class="sent">
                                <img class="msg_img" src="{{ asset('user-profile.png') }}" style="width: 22px;height:22px" alt="" />
                                <div class="msg_content">
                                <div class="card" style="background: transparent;border: 0;color: #FFF;">
                                    <div class="card-body">
                                        <p class="card-text" style="color: #fff;">
                                            موقع المناسبـة
                                        </p>
                                    </div>
                                    </div>
                                </div>
                            </li>

                            <li class="replies">
                                <img class="msg_img" src="http://emilcarlsson.se/assets/harveyspecter.png" style="width: 22px;height:22px" alt="" />
                                <div class="msg_content">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="card-text" style="color: #000;">
                                            - {{ $event_user->name }}
                                        </p>
                                        <p class="card-text" style="color: #000;">
                                            - شكرا لك على قبـول دعوتنــا لحضور المناسبة .
                                        </p>
                                        <div style="display: block;text-align: center;">
                                            <a href="{{ $location }}" style="margin-top: 10px;margin-bottom: 10px;">
                                                موقع المناسبة
                                            </a>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </li>

                 		@endif

                   @endforeach
                 @endif



               </ul>
            </div>


            {{--  @include('event.send_msg')  --}}


         </div>
      </div>


      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>



        <script>
            $(document).ready(function() {

               	//$("#messages_container").animate({ scrollTop: $(body).height() }, "fast");

              	var chatContainer = $('#messages_container');
                chatContainer.animate({ scrollTop: chatContainer.prop("scrollHeight") }, 1000); // 1000ms for animation


                // Set CSRF token for AJAX requests
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Handle form submission
                $('.event_btn').click(function(e) {

                    var ele = $(this);
                    var msg = null;

                    ele.removeClass('event_btn');
                    ele.attr('disabled','disabled');

                    const data = {
                        event_user_id: '{{ $event_user_id }}',
                        action: ele.attr('data-action'),
                        msg: msg
                    };

                    $.ajax({
                        url: '{{ route("website.chat.save_event_action") }}', // Laravel route
                        method: 'POST',
                        data: data, // Send key-value pairs
                        // data: $(this).serialize(),
                        success: function(response) {

                            // alert('Success: ' + response.message);

                            setTimeout(function() {
                                location.reload(); // Reload the page
                            }, 1500); // 2000 milliseconds = 2 seconds
                        },
                        error: function(xhr) {
                            alert('Error: ' + xhr.responseJSON.message);
                        }
                    });

                });



            });
        </script>


   </body>
</html>

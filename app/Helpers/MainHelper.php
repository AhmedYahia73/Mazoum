<?php

use GuzzleHttp\Client;
use App\Models\EventUserActions;
use App\Models\EventUsers;
use App\Models\Setting;


if (! function_exists('generateUniqueCode')) {

    function generateUniqueCode()
    {
       do {
            // Generate a random 6-character alphanumeric code
            $code = strtolower(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
        } while (EventUsers::where('code', $code)->exists());

        return $code;

    }

}


if (! function_exists('get_whats_setting')) {

    function get_whats_setting($event)
    {

        $setting = Setting::first();

      	//dd($event->country_code);

        if($event->country_code == 'kw') {
            $token     = $setting->access_token;
            $sender_id = $setting->sender_id;
        } else {
          	//dd('ok');
            $token     = $setting->sa_access_token;
            $sender_id = $setting->sa_sender_id;
        }

        $arr['token'] = $token;
        $arr['sender_id'] = $sender_id;

        return $arr;

    }

}


if (! function_exists('get_action')) {

    function get_action($action,$event_id,$event_user_id,$mobile)
    {
       $action_row = EventUserActions::where('event_id',$event_id)->where('event_user_id',$event_user_id)->where('mobile',$mobile)->where('action',$action)->first();

        return $action_row;

    }

}


if (! function_exists('SendTemplateV1')) {

    function SendTemplateV1($to,$template_name,$language,$image_url,$user_name,$event_title,$phone_numer_id,$token)
    {

        $arr = [
          'messaging_product' => 'whatsapp',
          'recipient_type' => 'individual',
          'to' => $to,
          'type' => 'template',
          'template' => [
                'name' => $template_name,
                'language' => [
                    'code' => $language
                ],
                'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'image',
                                'image' => [
                                    'link' => $image_url,
                                ],
                            ]
                        ],
                    ],
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $user_name
                            ],
                            [
                                'type' => 'text',
                                'text' => $event_title
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '0',
                        'parameters' => [
                            [
                                'type' => 'PAYLOAD',
                                'payload' => 'attend'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '1',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => 'not-attend'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '2',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => 'location'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '3',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => 'date'
                            ]
                        ],
                    ],
                ]
           ],
        ];

        $fullUrl = 'https://graph.facebook.com/v18.0/'.$phone_numer_id.'/messages';

        $client = new Client();

        $response = $client->post($fullUrl, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
            'json' => $arr,
        ]);

        return $response;

    }
}





if (! function_exists('SendTemplateV2')) {

    function SendTemplateV2($to,$template_name,$language,$image_url,$user_name,$phone_numer_id,$token)
    {

        $arr = [
          'messaging_product' => 'whatsapp',
          'recipient_type' => 'individual',
          'to' => $to,
          'type' => 'template',
          'template' => [
                'name' => $template_name,
                'language' => [
                    'code' => $language
                ],
                'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'image',
                                'image' => [
                                    'link' => $image_url,
                                ],
                            ]
                        ],
                    ],
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $user_name
                            ]
                        ]
                    ]
                ]
           ],
        ];

        $fullUrl = 'https://graph.facebook.com/v18.0/'.$phone_numer_id.'/messages';

        $client = new Client();

        $response = $client->post($fullUrl, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'key_id' => 123,
            ],
            'json' => $arr,
        ]);

        return $response;

    }
}




if (! function_exists('SendTemplateV3')) {

    function SendTemplateV3($to,$template_name,$language,$user_name,$location,$phone_numer_id,$token)
    {

        $arr = [
          'messaging_product' => 'whatsapp',
          'recipient_type' => 'individual',
          'to' => $to,
          'type' => 'template',
          'template' => [
                'name' => $template_name,
                'language' => [
                    'code' => $language
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $user_name
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'url',
                        'index' => '0',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $location
                            ]
                        ],
                    ]
                ]
           ],
        ];

        $fullUrl = 'https://graph.facebook.com/v18.0/'.$phone_numer_id.'/messages';

        $client = new Client();

        $response = $client->post($fullUrl, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
            'json' => $arr,
        ]);

        return $response;

    }
}




if (! function_exists('SendTemplateV4')) {

    function SendTemplateV4($to,$template_name,$language,$phone_numer_id,$token)
    {

        $arr = [
          'messaging_product' => 'whatsapp',
          'recipient_type' => 'individual',
          'to' => $to,
          'type' => 'template',
          'template' => [
                'name' => $template_name,
                'language' => [
                    'code' => $language
                ],
                'components' => [
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '0',
                        'parameters' => [
                            [
                                'type' => 'PAYLOAD',
                                'payload' => 'yes'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '1',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => 'no'
                            ]
                        ],
                    ]
                ]
           ],
        ];

        $fullUrl = 'https://graph.facebook.com/v18.0/'.$phone_numer_id.'/messages';

        $client = new Client();

        $response = $client->post($fullUrl, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
            'json' => $arr,
        ]);

        return $response;

    }
}





if (! function_exists('SendTemplateV5')) {

    function SendTemplateV5($to,$template_name,$language,$whatsapp,$phone_numer_id,$token)
    {

        $arr = [
          'messaging_product' => 'whatsapp',
          'recipient_type' => 'individual',
          'to' => $to,
          'type' => 'template',
          'template' => [
                'name' => $template_name,
                'language' => [
                    'code' => $language
                ]
           ],
        ];

        $fullUrl = 'https://graph.facebook.com/v18.0/'.$phone_numer_id.'/messages';

        $client = new Client();

        $response = $client->post($fullUrl, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
            'json' => $arr,
        ]);

        return $response;

    }
}





if (! function_exists('SendTemplateV6')) {

    function SendTemplateV6($to,$template_name,$language,$image_url,$user_name,$event_title,$phone_numer_id,$token)
    {

        $arr = [
          'messaging_product' => 'whatsapp',
          'recipient_type' => 'individual',
          'to' => $to,
          'type' => 'template',
          'template' => [
                "namespace" => 20200,
                'name' => $template_name,
                'language' => [
                    'code' => $language
                ],
                'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'image',
                                'image' => [
                                    'link' => $image_url,
                                ],
                            ]
                        ],
                    ],
                ]
           ],
        ];

        $fullUrl = 'https://graph.facebook.com/v18.0/'.$phone_numer_id.'/messages';

        $client = new Client();

        $response = $client->post($fullUrl, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
            'json' => $arr,
        ]);

        return $response;

    }
}




if (! function_exists('SendTemplateV7')) {

    function SendTemplateV7($to,$template_name,$language,$date,$phone_numer_id,$token)
    {

        $arr = [
          'messaging_product' => 'whatsapp',
          'recipient_type' => 'individual',
          'to' => $to,
          'type' => 'template',
          'template' => [
                'name' => $template_name,
                'language' => [
                    'code' => $language
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $date
                            ]
                        ],
                    ]
                ]
           ],
        ];

        $fullUrl = 'https://graph.facebook.com/v18.0/'.$phone_numer_id.'/messages';

        $client = new Client();

        $response = $client->post($fullUrl, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
            'json' => $arr,
        ]);

        return $response;

    }
}




if (! function_exists('SendTemplateV8')) {

    function SendTemplateV8($to,$template_name,$language,$phone_numer_id,$token)
    {

        $arr = [
          'messaging_product' => 'whatsapp',
          'recipient_type' => 'individual',
          'to' => $to,
          'type' => 'template',
          'template' => [
                'name' => $template_name,
                'language' => [
                    'code' => $language
                ]
           ],
        ];

        $fullUrl = 'https://graph.facebook.com/v18.0/'.$phone_numer_id.'/messages';

        $client = new Client();

        $response = $client->post($fullUrl, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
            'json' => $arr,
        ]);

        return $response;

    }
}





if (! function_exists('SendCarTemplateV1')) {

    function SendCarTemplateV1($to,$template_name,$language,$phone_numer_id,$token)
    {

        $arr = [
          'messaging_product' => 'whatsapp',
          'recipient_type' => 'individual',
          'to' => $to,
          'type' => 'template',
          'template' => [
                'name' => $template_name,
                'language' => [
                    'code' => $language
                ],
                'components' => [
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '0',
                        'parameters' => [
                            [
                                'type' => 'PAYLOAD',
                                'payload' => 'send-car'
                            ]
                        ],
                    ],
                ]
           ],
        ];

        $fullUrl = 'https://graph.facebook.com/v18.0/'.$phone_numer_id.'/messages';

        $client = new Client();

        $response = $client->post($fullUrl, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
            'json' => $arr,
        ]);

        return $response;

    }
}



if (! function_exists('SendCarTemplateV2')) {

    function SendCarTemplateV2($to,$template_name,$language,$phone_numer_id,$token)
    {

        $arr = [
          'messaging_product' => 'whatsapp',
          'recipient_type' => 'individual',
          'to' => $to,
          'type' => 'template',
          'template' => [
                'name' => $template_name,
                'language' => [
                    'code' => $language
                ]
           ],
        ];

        $fullUrl = 'https://graph.facebook.com/v18.0/'.$phone_numer_id.'/messages';

        $client = new Client();

        $response = $client->post($fullUrl, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
            'json' => $arr,
        ]);

        return $response;

    }
}




if (! function_exists('SendTemplateV9')) {

    function SendTemplateV9($to,$template_name,$language,$phone_numer_id,$token)
    {

        $arr = [
          'messaging_product' => 'whatsapp',
          'recipient_type' => 'individual',
          'to' => $to,
          'type' => 'template',
          'template' => [
                'name' => $template_name,
                'language' => [
                    'code' => $language
                ],
                'components' => [
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '0',
                        'parameters' => [
                            [
                                'type' => 'PAYLOAD',
                                'payload' => 'yes-congrato'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '1',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => 'no-congrato'
                            ]
                        ],
                    ]
                ]
           ],
        ];

        $fullUrl = 'https://graph.facebook.com/v18.0/'.$phone_numer_id.'/messages';

        $client = new Client();

        $response = $client->post($fullUrl, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
            'json' => $arr,
        ]);

        return $response;

    }
}



if (! function_exists('SendTemplateV10')) {

    function SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token)
    {

        $arr = [
          'messaging_product' => 'whatsapp',
          'recipient_type' => 'individual',
          'to' => $to,
          'type' => 'template',
          'template' => [
                'name' => $template_name,
                'language' => [
                    'code' => $language
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $message
                            ]
                        ],
                    ],
                ]
           ],
        ];

        $fullUrl = 'https://graph.facebook.com/v18.0/'.$phone_numer_id.'/messages';

        $client = new Client();

        $response = $client->post($fullUrl, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
            'json' => $arr,
        ]);

        return $response;

    }
}


if (! function_exists('SendNewTemplateCodeV1')) {

    function SendNewTemplateCodeV1($url)
    {

        $client = new Client();

        $response = $client->request('POST', $url);

        return $response;

    }
}




if (! function_exists('GetEditedLocation')) {

    function GetEditedLocation($url) {

        $url = str_replace("https://www.google.com/maps/place","",$url);
        $url = str_replace("http://www.google.com/maps/place","",$url);

        $url = str_replace("https://google.com/maps/place","",$url);
        $url = str_replace("http://google.com/maps/place","",$url);

        return $url;
    }
}







if (! function_exists('Gender')) {

    function Gender() {

        $type = [
            '0' => 'ولد',
            '1' => 'بنت'

        ];

        return $type;
    }
}


if (! function_exists('Status')) {

    function Status() {

        $status = [
            '0' => 'غير مفعل',
            '1' => 'مفعل'

        ];

        return $status;
    }
}

if (! function_exists('YesOrNo')) {

    function YesOrNo() {

        $status = [
            '1' => 'نعم',
            '2' => 'لا'

        ];

        return $status;
    }
}



if (! function_exists('add3dots')) {

    function add3dots($string, $repl, $limit)
    {
      if(strlen($string) > $limit)
      {
        return substr($string, 0, $limit) . $repl;
      }
      else
      {
        return $string;
      }
    }

}







if (! function_exists('Get_Main_Prefix')) {

    function Get_Main_Prefix($lang = 'ar')
    {

        $prefix = '';

        if($lang == 'ar') {
            $prefix = '/';
        } else {
            $prefix = 'en/';
        }

        return $prefix;

    }

}


if (! function_exists('Get_Prefix')) {

    function Get_Prefix($lang = 'ar')
    {

        $arr = [];
        if($lang == 'ar') {
            $prefix = 'en/';
            $attr = 'en';
        } else {
            $prefix = '';
            $attr = 'ar';
        }

        $arr['prefix'] = $prefix;
        $arr['attr'] = $attr;

        return $arr;

    }

}


if (! function_exists('Get_Dir')) {

    function Get_Dir($lang = 'ar')
    {

        $arr = [];

        $dir = 'right';
        $dir2 = 'left';

        if($lang == 'ar') {
            $dir = 'right';
            $dir2 = 'left';
        } else {
            $dir = 'left';
            $dir2 = 'right';
        }

        $arr['dir'] = $dir;
        $arr['dir2'] = $dir2;

        return $arr;

    }

}


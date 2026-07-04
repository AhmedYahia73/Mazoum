<?php

use GuzzleHttp\Client;
use App\Models\EventUserActions;
use App\Models\EventUsers;
use App\Models\Setting;



if (! function_exists('SendCarMsgTemplate')) {

    function SendCarMsgTemplate($to,$template_name,$language,$image_url,$param1,$param2,$param3,$phone_numer_id,$token)
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
                                'text' => $param1
                            ],
                            [
                                'type' => 'text',
                                'text' => $param2
                            ],
                            [
                                'type' => 'text',
                                'text' => $param3
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



if (! function_exists('SendCustomMessageTemplate')) {

    function SendCustomMessageTemplate($to,$template_name,$language,$param1,$param2,$number,$phone_numer_id,$token)
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
                                'text' => $param1
                            ],
                            [
                                'type' => 'text',
                                'text' => $param2
                            ]
                        ],
                    ],
                  	/*
                    [
                        'type' => 'button',
                        'sub_type' => 'phone_number',
                        'index' => '0',

                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $number
                            ]
                        ],

                    ]
            		*/
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



if (! function_exists('SendWeddingDataV1ArTemplate')) {

    function SendWeddingDataV1ArTemplate($to,$template_name,$language,$param_1,$param_2,$param_3,$param_4,$param_5,$param_6,$image_url,$phone_numer_id,$token, $header_type = "image")
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
                                'type' => $header_type,
                                $header_type => [
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
                                'text' => $param_1
                            ],
                            [
                                'type' => 'text',
                                'text' => $param_2
                            ],
                            [
                                'type' => 'text',
                                'text' => $param_3
                            ],
                            [
                                'type' => 'text',
                                'text' => $param_4
                            ],
                            [
                                'type' => 'text',
                                'text' => $param_5
                            ],
                            [
                                'type' => 'text',
                                'text' => $param_6
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


if (! function_exists('SendWeddingUtilityV1ArTemplate')) {

    function SendWeddingUtilityV1ArTemplate($to,$template_name,$language,$param_1,$param_2,$image_url,$phone_numer_id,$token, $header_type = "image")
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
                                'type' => $header_type,
                                $header_type => [
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
                                'text' => $param_1
                            ],
                            [
                                'type' => 'text',
                                'text' => $param_2
                            ],
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



if (! function_exists('SendWeddingDataV2ArTemplate')) {

    function SendWeddingDataV2ArTemplate($to,$template_name,$language,$user_name,$image_url,$phone_numer_id,$token)
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



if (! function_exists('SendWeddingDataAr10Template')) {

    function SendWeddingDataAr10Template($to,$template_name,$language,$image_url,$phone_numer_id,$token)
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



if (! function_exists('SendWeddingDataV15Template')) {

    function SendWeddingDataV15Template($to,$template_name,$language,$location,$phone_numer_id,$token)
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



if (! function_exists('SendMessageTemplate')) {

    function SendMessageTemplate($to,$template_name,$language,$phone_numer_id,$token)
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



if (! function_exists('SendConfirmationTemplate')) {

    function SendConfirmationTemplate($to,$template_name,$language,$phone_numer_id,$token)
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



if (! function_exists('SendCongratulationArNewTemplate')) {

    function SendCongratulationArNewTemplate($to,$template_name,$language,$phone_numer_id,$token)
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



if (! function_exists('SendApologizedTemplate')) {

    function SendApologizedTemplate($to,$template_name,$language,$phone_numer_id,$token)
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
                                'payload' => 'yes-apologize'
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
                                'payload' => 'no-apologize'
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






if (! function_exists('SendWeddingDataV7ATemplate')) {

    function SendWeddingDataV7ATemplate($to,$template_name,$language,$user_name,$location,$phone_numer_id,$token)
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


if (! function_exists('SendWeddingDataV9ArTemplate')) {

    function SendWeddingDataV9ArTemplate($to,$template_name,$language,$date,$phone_numer_id,$token)
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



if (! function_exists('SendCustomMessageV2ArTemplate')) {

    function SendCustomMessageV2ArTemplate($to,$template_name,$language,$param1,$param2,$image_url,$phone_numer_id,$token, $type = "image")
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
                                'type' => $type,
                                $type => [
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
                                'text' => $param1
                            ],
                            [
                                'type' => 'text',
                                'text' => $param2
                            ]
                        ],
                    ],
                    // [
                    //     'type' => ody',
                    //     'parameters' => [
                    //         [
                    //             'type' => 'text',
                    //             'text' => $param1
                    //         ]
                    //     ]
                    // ],
                    // [
                    //     'type' => 'body',
                    //     'parameters' => [
                    //         [
                    //             'type' => 'text',
                    //             'text' => $param2
                    //         ]
                    //     ]
                    // ]
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



if (! function_exists('SendArFlowV1Template')) {

    function SendArFlowV1Template($to,$template_name,$language,$phone_numer_id,$token)
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
                                'payload' => '1'
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


if (! function_exists('SendArFlowV2Template')) {

    function SendArFlowV2Template($to,$template_name,$language,$phone_numer_id,$token)
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
                                'payload' => '1'
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
                                'payload' => '2'
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


if (! function_exists('SendArFlowV3Template')) {

    function SendArFlowV3Template($to,$template_name,$language,$phone_numer_id,$token)
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
                                'payload' => '1'
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
                                'payload' => '2'
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
                                'payload' => '3'
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


if (! function_exists('SendArFlowV4Template')) {

    function SendArFlowV4Template($to,$template_name,$language,$phone_numer_id,$token)
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
                                'payload' => '1'
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
                                'payload' => '2'
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
                                'payload' => '3'
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
                                'payload' => '4'
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


if (! function_exists('SendArFlowV5Template')) {

    function SendArFlowV5Template($to,$template_name,$language,$phone_numer_id,$token)
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
                                'payload' => '1'
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
                                'payload' => '2'
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
                                'payload' => '3'
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
                                'payload' => '4'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '4',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '5'
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


if (! function_exists('SendArFlowV6Template')) {

    function SendArFlowV6Template($to,$template_name,$language,$phone_numer_id,$token)
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
                                'payload' => '1'
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
                                'payload' => '2'
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
                                'payload' => '3'
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
                                'payload' => '4'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '4',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '5'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '5',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '6'
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


if (! function_exists('SendArFlowV7Template')) {

    function SendArFlowV7Template($to,$template_name,$language,$phone_numer_id,$token)
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
                                'payload' => '1'
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
                                'payload' => '2'
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
                                'payload' => '3'
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
                                'payload' => '4'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '4',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '5'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '5',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '6'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '6',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '7'
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


if (! function_exists('SendArFlowV8Template')) {

    function SendArFlowV8Template($to,$template_name,$language,$phone_numer_id,$token)
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
                                'payload' => '1'
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
                                'payload' => '2'
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
                                'payload' => '3'
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
                                'payload' => '4'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '4',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '5'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '5',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '6'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '6',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '7'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '7',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '8'
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


if (! function_exists('SendArFlowV9Template')) {

    function SendArFlowV9Template($to,$template_name,$language,$phone_numer_id,$token)
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
                                'payload' => '1'
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
                                'payload' => '2'
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
                                'payload' => '3'
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
                                'payload' => '4'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '4',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '5'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '5',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '6'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '6',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '7'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '7',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '8'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '8',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '9'
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

if (! function_exists('SendArFlowV10Template')) {

    function SendArFlowV10Template($to,$template_name,$language,$phone_numer_id,$token)
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
                                'payload' => '1'
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
                                'payload' => '2'
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
                                'payload' => '3'
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
                                'payload' => '4'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '4',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '5'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '5',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '6'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '6',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '7'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '7',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '8'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '8',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '9'
                            ]
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'quick_reply',
                        'index' => '9',
                        'parameters' => [
                            [
                                'type' => 'payload',
                                'payload' => '10'
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


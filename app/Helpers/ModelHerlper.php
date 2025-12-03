<?php


use App\Models\Admin;
use App\Models\Currency;
use App\Models\MobileCodes;
use App\Models\User;
use App\Models\Assistant;

if (! function_exists('All_Admin')) {

    function All_Admin()
    {
        return Admin::pluck('name', 'id');
    }
}

if (! function_exists('Assistants')) {

    function Assistants()
    {
        return User::where('user_type','employee')->pluck('name', 'id');
    }
}


if (! function_exists('Users')) {

    function Users()
    {
        return User::pluck('name', 'id');
    }
}

if (! function_exists('Currencies')) {

    function Currencies()
    {
        return Currency::pluck('ar_name', 'id');
    }
}


if (! function_exists('MobileCodes')) {

    function MobileCodes()
    {
        return MobileCodes::pluck('ar_country_name', 'id');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;


class ParkingLogin extends Controller
{
    public function login()
    {

        $lang = app()->getLocale();

        session()->put('locale', $lang);
        app()->setLocale($lang);

        if(Auth::guard('parking')->check()) {

            return redirect('parking_panel');

        } else {
            return view('parking.layouts.login');
        }
    }

    public function login_post(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->has('remember') ? true : false;

        if (Auth::guard('parking')->attempt([ 'email' => request('email') , 'password' => request('password')], $remember)) {

            return redirect('parking_panel');

        } else {
            return redirect()->back()->with('error', 'Please Check Your User Name and Password again');
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('parking')->logout();
        return redirect('parking_panel/login');
    }
  
  
  	public function test_send() {
    
      
      $fullUrl = 'https://api.karzoun.app/CloudApi.php';
      
      $token = 'EAAMsejfnW3YBO8Dyx2A33sCNgtAcJ4TNlOBrQ3LWP32viFq2ZB2UCR7w1NF5iCOxCsn5DNZAoK9qwCISeDDA7C91bjdfVzLau1pze5huMjn0d9FFW8ri1k33o8kQelh32ayQZBvDwJtcJNJiebk7BAGT34kUUe3E0YutgnHPbafP3IZCx14OQqNp4TAfCR6o';

      $arr = [
        'token' => 'EAAMsejfnW3YBO8Dyx2A33sCNgtAcJ4TNlOBrQ3LWP32viFq2ZB2UCR7w1NF5iCOxCsn5DNZAoK9qwCISeDDA7C91bjdfVzLau1pze5huMjn0d9FFW8ri1k33o8kQelh32ayQZBvDwJtcJNJiebk7BAGT34kUUe3E0YutgnHPbafP3IZCx14OQqNp4TAfCR6o',
        'sender_id' => '344115548775193',
        'phone' => '201008478014',
        'template' => 'wedding_data_v3_ar',
      ];

      $client = new Client();

      $response = $client->post($fullUrl,[
        'headers' => [
          'User-Agent' => 'Apidog/1.0.0 (https://apidog.com)',
        ],
        'json' => $arr,
      ]);

      
      if($response != null && $response->getStatusCode() == 200) {

        // Parse the response object, e.g. read the headers, body, etc.
        $body = $response->getBody();
        
       // dd($response,$body,json_decode($body, true));

        //$data = json_decode($body);
        $data = json_decode($body, true);

        
        info('ok');
        
        info($data);
        
      
        dd('ok',$data);
        
      } else {
      
        dd('not-ok');
      }
      
      
      
      /*
      $curl = curl_init();

      curl_setopt_array($curl, array(
         CURLOPT_URL => 'https://api.karzoun.app/CloudApi.php?token=EAAMsejfnW3YBO8Dyx2A33sCNgtAcJ4TNlOBrQ3LWP32viFq2ZB2UCR7w1NF5iCOxCsn5DNZAoK9qwCISeDDA7C91bjdfVzLau1pze5huMjn0d9FFW8ri1k33o8kQelh32ayQZBvDwJtcJNJiebk7BAGT34kUUe3E0YutgnHPbafP3IZCx14OQqNp4TAfCR6o&sender_id=344115548775193&phone=201008478014&template=wedding_data_v3_ar',
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 0,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_HTTPHEADER => array(
            'User-Agent: Apidog/1.0.0 (https://apidog.com)'
         ),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      dd($response,json_decode($response, true));
	  */
      
    
    }


}

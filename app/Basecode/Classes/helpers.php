<?php

function generateOTP(){
    
    $permitted_chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $str = substr(str_shuffle($permitted_chars), 0, 2);

    return $str.mt_rand(111, 999);
    
}


function executeApi($url, $data = [], $method = "GET", $port = 8283) {
    try {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_PORT, $port);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return json_decode($result);
    } catch(Exception $e) {

    }

}

function sendOtp($mobile_no, $message = '') {

    $otp = generateOTP();

    if(!$message) $message = 'Please enter OTP '.$otp.' for completing your order delivery. Team Rodafleet';

    sendMessage($mobile_no, $message);

    return $otp;
    
    $sender = 'Rodafleet';
    $route = '4';
    $country = '91';
    $mobiles = $mobile_no;
    $authkey = '184727ALq3xUBNj7u05a13f749';

    try{
        $api = "http://api.msg91.com/api/sendhttp.php?sender=". $sender;

        $api .= '&route='. $route;
        $api .= '&country='. $country;
        $api .= '&message='. $message;
        $api .= '&mobiles='. $mobiles;
        $api .= '&authkey='. $authkey;

        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $api);

        if( $res->getStatusCode() != 200 ) return [];

        $res = (array)json_decode($res->getBody());

    } catch ( Exception $e ) {

    }

    return $otp;
}

function sendPasswordBySms($mobile_no, $password, $message = '') {

    if(!$message) $message = "Your password is ".$password;

    sendMessage($mobile_no, $message);
}

function getImageUrl($image, $type = null) {

    if((!$image) && $type == 'user' ) return asset('uploads/user-not-found.svg');

    if(!$image) return asset('uploads/not-found.png');

    // return 'https://s3.ap-south-1.amazonaws.com/diagno-labs/'.$image;

    return asset($image);

}

function sendMessage( $mobileNo, $message ) {

    // $api = 'interconnectsms.com/sendurlcomma.aspx?user=20078677&pwd=sanjay_1973&senderid=ZIIPPY&mobileno='.$mobileNo.'&msgtext='.$message.'&smstype=0';
    $api = 'http://nimbusit.co.in/api/swsendSingle.asp?username=t1pixelbypixel&password=100829102&sender=Dosweb&sendto='.$mobileNo.'&message='.$message;
    try {

        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $api);

    } catch ( \Exception $e ) {

    }
}



/**
 * WE ARE USING THIS FUNCTION TO INCLUDE
 * SCRIPT AND CSS FILES AND OTHER MEDIA
 * FILES IN ADMIN APPLICATION
 */
if (! function_exists('getMediaUrl')) {

    function getMediaUrl($filePath) {
        return asset($filePath);
    }

}

function currencySign() {
    return '₹';
}

function distanceSign() {
    return 'Km';
}

function redirectToDashboard() {
    return redirect()->route('admin.dashboard')->withErrors('Something went wrong');
}

if (! function_exists('sendPushNotification')) {

    function sendPushNotification( $userIds, $data, $debug = false ) {


        if(!is_array($userIds)) $userIds = [$userIds];
        // notification android
        //$key = 'AAAAFVAHYlc:APA91bGG4gRFS8PoLYnhe-nDwybv3p_s_YBjuov_k9WLADPIfiG7jR4209jIiMM5emPYVo1-dzBNzVHv0bIqRVU4UPVcSuDRTCIWEO62P5fNAxrtR-RSgFRCEY8w6DvmyUbL-w14Ucu_';
        $key = Appconfig::get_config_value('pushnotification', 'android_firebase_api_user1');
        
        $deviceTokens = \App\DeviceToken::whereIn('user_id', $userIds)->where('device_type', 'android')->pluck('device_token')->toArray();

        if( $deviceTokens ) {
            sendAndroidPushNotification($deviceTokens, $data, $key);
        }

        $key = Appconfig::get_config_value('pushnotification', 'ios_firebase_api_user1');

        // ios notification
        $deviceTokens = \App\DeviceToken::whereIn('user_id', $userIds)->where('device_type', 'ios')->pluck('device_token')->toArray();

        if( $deviceTokens) {
            sendIOSPushNotification($deviceTokens, $data, $key);
        }

        // $mode = Appconfig::get_config_value('pushnotification', 'develop_mode_user1');
        // $pem = Appconfig::get_config_value('pushnotification', 'sandbox_pem_user1');
        // if ( $mode == 'production' ) $pem = Appconfig::get_config_value('pushnotification', 'production_pem_user1');

        // foreach ( $deviceTokens as $token ) {
        //     try {
        //         sendIOSPushNotification($token, $data, $key);
        //     } catch (\Exception $e) {
        //         echo $e->getMessage(); die;
        //     }
        // }
        die;

    }

}

if (! function_exists('sendAndroidPushNotification')) {

    function sendAndroidPushNotification($registrationIDs, $data, $fire_base_api_key, $debug = false) {

        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = array(
            'registration_ids' => $registrationIDs,
            'data' => $data,
        );

        $headers = array(
            'Authorization: key=' . $fire_base_api_key,
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the URL, number of POST vars, POST data
        curl_setopt( $ch, CURLOPT_URL, $url);
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields));
        $result = curl_exec($ch);
        curl_close($ch);

        // if($debug) {
            echo '<pre>';
            print_r($result);
        // }

    }

}

if (! function_exists('sendIOSPushNotification')) {

    function sendIOSPushNotification($registrationIDs, $data, $fire_base_api_key, $debug = false) {

        // Set POST variables
        // $url = 'https://android.googleapis.com/gcm/send';
        $url = 'https://fcm.googleapis.com/fcm/send';
        

        $fields = array(
            'registration_ids' => $registrationIDs,
            'data' => $data,
        );

        $headers = array(
            'Authorization: key=' . $fire_base_api_key,
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the URL, number of POST vars, POST data
        curl_setopt( $ch, CURLOPT_URL, $url);
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields));
        $result = curl_exec($ch);
        curl_close($ch);

        // if($debug) {
            echo '<pre>';
            print_r($result);
            die;
        // }
        

    }

}

define('DATE_FORMAT', 'd-m-Y');

define('DATETIME_FORMAT', 'd-m-Y g:i A');

define('TIME_FORMAT', 'g:i A');

if (! function_exists('getDateTimeValue')) {

    function getDateTimeValue($date) {
        if( in_array($date, ['0000-00-00', '0000-00-00 00:00:00']) || (!$date)) return '';
        return date(DATETIME_FORMAT, strtotime($date));
    }

}

if (! function_exists('getDateValue')) {

    function getDateValue($date, $format = DATE_FORMAT ) {
        if( in_array($date, ['0000-00-00', '0000-00-00 00:00:00', '1970-01-01']) || (!$date)) return '';
        if( request('is_app') ) return date('Y-m-d', strtotime($date));
        return date($format, strtotime($date));
    }

}

if (! function_exists('getTimeValue')) {

    function getTimeValue($date) {
        if( in_array($date, ['0000-00-00', '0000-00-00 00:00:00', '00:00:00']) || (!$date)) return '';
        return date(TIME_FORMAT, strtotime($date));
    }

}

/**
 * WE ARE USING THIS FUNCTION TO REMOVE
 * VALIDATION RULES KEY FROM RULES IF THEY ARE
 * BEING UPDATE USING SAME MODEL.
 */
if (! function_exists('getValidationRules')) {

    function getValidationRules( $rules = [], $model = null ) {
        if(!$rules) return [];

        foreach( $rules as $key => $rule ) {
            if( $model && ( $model->$key == request($key) ) ) unset($rules[$key]);
        }
        return $rules;
    }
}

if (! function_exists('decodeId')) {

    function decodeId($value) {
        try{
            return decrypt($value);
        } catch (\Exception $e) {
            return '';
        }
    }

}

function getAddressFromGoogle( $address = '', $lat = '', $lng = '' ){

    $data = [
        'lat'                   => '',
        'lng'                   => '',
        'locality'              => '',
        'street_number'         => '',
        'route'                 => '',
        'sublocality_level_1'   => '',
        'postal_code'           => '',
        'city'                  => '',
        'short_city'            => '',
        'state'                 => '',
        'short_state'           => '',
        'country'               => '',
        'short_country'         => '',
    ];

    try {
        if( $address ) {
            $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key=AIzaSyAKrYvcyFVz3Ao4R60OzZzj0zttjzsQRwE&address='.urlencode($address).'&sensor=false&region=India');
        } else {
            $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key=AIzaSyAKrYvcyFVz3Ao4R60OzZzj0zttjzsQRwE&latlng='.$lat.','.$lng.'&sensor=false&region=India');
        }

        $output = json_decode($geocode);

        // echo '<pre>'; print_r($output); die;

        if(! isset( $output->results ) ) return $data;
        if(! isset($output->results[0]) ) return $data;

        if( isset($output->results[0]->geometry) && isset( $output->results[0]->geometry->location ) && $output->results[0]->geometry->location->lat ) $data['lat'] = $output->results[0]->geometry->location->lat;
        if( isset($output->results[0]->geometry) && isset( $output->results[0]->geometry->location ) && $output->results[0]->geometry->location->lng ) $data['lng'] = $output->results[0]->geometry->location->lng;

        if( isset($output->results[0]->address_components) ) {

            foreach( $output->results[0]->address_components as $res ) {

                if( isset($res->types[0]) && $res->types[0] == 'administrative_area_level_1' ) {
                    $data['state'] = $res->long_name;
                    $data['short_state'] = $res->short_name;
                } elseif( isset($res->types[0]) && $res->types[0] == 'administrative_area_level_2' ) {
                    $data['city'] = $res->long_name;
                    $data['short_city'] = $res->short_name;
                } elseif( isset($res->types[0]) && $res->types[0] == 'country' ) {
                    $data['country'] = $res->long_name;
                    $data['short_country'] = $res->short_name;
                } elseif( isset($res->types[0]) && $res->types[0] == 'locality' ) {
                    $data['locality'] = $res->long_name;
                }elseif( isset($res->types[2]) && $res->types[2] == 'sublocality_level_1' ) {
                    $data['sublocality_level_1'] = $res->long_name;
                } elseif( isset($res->types[0]) && $res->types[0] == 'postal_code' ) {
                    $data['postal_code'] = $res->long_name;
                } elseif( isset($res->types[0]) && $res->types[0] == 'street_number' ) {
                    $data['street_number'] = $res->long_name;
                } elseif( isset($res->types[0]) && $res->types[0] == 'route' ) {
                    $data['route'] = $res->long_name;
                }

            }

        }

        if( isset($output->results[0]->formatted_address) ) $data['address'] = $output->results[0]->formatted_address;

    } catch ( \Exception $e ) {
        return $data;
    }

    return $data;

}

if (! function_exists('isAuth')) {

    function isAuth($class, $method) {

        try{
            $className = '\App\Basecode\Classes\Permissions\\'.$class.'Permission';
            $permission = new $className();
            return $permission->$method();
        } catch (\Exception $e) {
            return false;
        }

    }

};

if (! function_exists('isSuperAdmin')) {

    function isSuperAdmin() {
        if(!auth()->check()) return false;
        if(auth()->user()->type == \App\User::SUPERADMIN) return true;
        return false;
    }

}

if (! function_exists('userHasChildUserCondition')) {

    function userHasChildUserCondition() {
        // return true;
        // if(!auth()->check()) return false;
        if( in_array(auth()->user()->type, [ \App\User::ADMIN, \App\User::SUPERADMIN ]) ) return false;
        return true;
    }
}


function cvalidate( $rules, $attrs = [] ) {

    if(! count($attrs) ) $attrs = request()->all();
    $validation = Validator::make( $attrs, $rules );
    if(! $validation->fails() ) return false;
    return $validation->messages();

}

function appError($message = '', $messages = []) {

    try {

        $response = ['error' => true, 'data' => $messages, 'message' => $message];

        \DB::table('api_logs')->insert([
            'url'       => request()->url(),
            'json'      => serialize( $_POST ),
            'req'       => serialize( $_POST ),
            'res'       => serialize($response),
            'created_at'=> date('Y-m-d H:i:s')
        ]);

    } catch ( \Exception $e) {
        //echo $e->getMessage();die;
    }

    if((!$messages) && is_array($messages)) return response()->json(['error' => true, 'data' => new \stdClass, 'message' => $message]);
    if(!$messages) return response()->json( ['error' => true, 'data' => new \stdClass, 'message' => $message] );
    return response()->json(['error' => true, 'data' => $messages, 'message' => $message]);
}

function appData($res, $message = '', $results_name = 'results') {


    try {

        if(!$res){
            $response = [
                'error' => false,
                'data' => [ $results_name => [] ],
                'message' => $message
            ];
        } else {
            $response = [
                'error' => false,
                'data' => [ $results_name => $res ],
                'message' => $message
            ];
        }

        \DB::table('api_logs')->insert([
            'url'       => request()->url(),
            'json'      => serialize( $_POST ),
            'req'       => serialize( $_POST ),
            'res'       => serialize($response),
            'created_at'=> date('Y-m-d H:i:s')
        ]);

    } catch ( \Exception $e) {
        //echo $e->getMessage();die;
    }

    if(!$res) return response()->json ([
        'error' => false,
        'data' => [ $results_name => [] ],
        'message' => $message
    ]);

    return response()->json (['error' => false, 'data' => [ $results_name => $res ], 'message' => $message ]);
}

// this will return single model data
function appModelData($res, $message = '', $results_name = 'results', $images = []) {

    try {

        if(!$res){
            $response = [
                'error' => false,
                'data' => [ $results_name => new \stdClass() ],
                'message' => $message
            ];
        } else {
            $response = [
                'error' => false,
                'data' => [ $results_name => $res ],
                'message' => $message
            ];
        }

        \DB::table('api_logs')->insert([
            'url'       => request()->url(),
            'json'      => serialize( $_POST ),
            'req'       => serialize( array_merge($_POST, $images)  ),
            'res'       => serialize($response),
            'created_at'=> date('Y-m-d H:i:s')
        ]);

    } catch ( \Exception $e) {
        //echo $e->getMessage();die;
    }


    if(!$res) return response()->json ([
        'error' => false,
        'data' => [ $results_name => new \stdClass() ],
        'message' => $message
    ]);

    return response()->json (['error' => false, 'data' => [ $results_name => $res ], 'message' => $message ]);
}

function noAuth(){
    return response()->json(['message' => 'Not Authorised'], 401);
}

function appLogout( $userId = null ) {
    if($userId) {
        try{
            \DB::table('oauth_access_tokens')->where('user_id', $userId)->delete();
        } catch (\Exception $e) {

        }
    } elseif (auth()->check()) {
        try{
            \DB::table('oauth_access_tokens')->where('user_id', auth()->user()->id)->delete();
        } catch (\Exception $e) {

        }
    }
}


function exportCsv($headerFields = [], $dataFiles = [], $fileName = 'export') {

    ini_set('max_execution_time', 30000);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$fileName.'.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $fp = fopen('php://output', 'w');

    fputcsv($fp, $headerFields);

    foreach( $dataFiles as $row ) {
        fputcsv($fp, $row);
    }

    die;
}

function csvToArray($file) {

    $csv = array_map('str_getcsv', file($file));
    
    foreach( $csv as &$row ) {
        foreach ( $row as &$field ) {
            $field = trim($field);
        }
    }
    array_walk($csv, function(&$a) use ($csv) {
        $a = array_combine($csv[0], $a);
    });
    array_shift($csv);
    return $csv;
}

function delhiICNO() {
    $configModel = \App\AppConfig::where('module', 'member')->where('field', 'delhi_activity_code')->first();
    $configModel->value += 1;
    $configModel->save();
    return $configModel->value;
}

function outsideDelhiICNO() {
    $configModel = \App\AppConfig::where('module', 'member')->where('field', 'outside_delhi_activity_code')->first();
    $configModel->value += 1;
    $configModel->save();
    return $configModel->value;
}

function internationalICNO() {
    $configModel = \App\AppConfig::where('module', 'member')->where('field', 'not_indian_activity_code')->first();
    $configModel->value += 1;
    $configModel->save();

    return str_pad($configModel->value, 3, "0", STR_PAD_LEFT);
}
function unpackSlug( $str, $sep = '_' ) {
    $str = str_replace($sep, ' ', $str);
    return ucwords($str);
}

function getMasterLabel( $masterTag, $optionTag, $field = 'title' ) {

    $model = \App\AppMasterOption::
    join('app_masters', 'app_masters.id', '=', 'app_master_options.app_master_id')
        ->where('app_masters.tag', $masterTag)
        ->where('app_master_options.tag', $optionTag)
        ->select('app_master_options.*')
        ->first();

    if(! $model ) return unpackSlug($optionTag);

    return $model->$field;

}


function Pdecrypt($encryptedText,$key) {

    $key = Phextobin(md5($key));
    $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
    $encryptedText = Phextobin($encryptedText);
    $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
    return $decryptedText;


}

function Phextobin($hexString) {
    $length = strlen($hexString);
    $binString="";
    $count=0;
    while($count<$length)
    {
        $subString =substr($hexString,$count,2);
        $packedString = pack("H*",$subString);
        if ($count==0)
        {
            $binString=$packedString;
        }

        else
        {
            $binString.=$packedString;
        }

        $count+=2;
    }
    return $binString;
}
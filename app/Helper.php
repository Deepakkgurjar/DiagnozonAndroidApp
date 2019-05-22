<?php

function sendMessages($message,$number){
    return ;

    return true;
    $fields = array( "sender_id" => "FSTSMS", "message" => "$message", "language" => "english", "route" => "p", "numbers" => "$number", );
    $curl = curl_init();
    curl_setopt_array($curl, array( CURLOPT_URL => "https://www.fast2sms.com/dev/bulk", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => json_encode($fields), CURLOPT_HTTPHEADER => array( "authorization: GZRfsEDlxu70Wn6ki4o8vaUg2TdMLNK5yrwS3PAp9z1VtQeOmjjCwKvy6A1D5SEFPk0tMY2qfbXUlh9B", "accept: */*", "cache-control: no-cache", "content-type: application/json" ), ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
    }
    else {
//        echo $response;
    }

}

?>
<?php
    $sms_number = '995598508035';

    /*----CURL SEND SMS---*/
    $data = array(
        "number" => $sms_number,
        "text" => "this is 1",
        "key" => "aPQKQjQ0VBi6a3ue"
    );

    $url = "https://sms.ar.com.ge/api/integration/sms";
    #$url = "https://sms.ar.com.ge/api/integration/sms-latin";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



    $response = curl_exec($ch);

    if (!$response) {
        die(curl_error($ch) . " - Code: " . curl_errno($ch));
    }

    curl_close($ch);
    var_dump($response);
?>

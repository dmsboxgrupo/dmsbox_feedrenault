<?php

function push_notification( $devices, $text ) {
	
	$payload = array();
	
	foreach($devices as &$device_uid) {
		
		$payload[] = array(
			'to' => $device_uid,
			'sound' => 'default',
			'body' => $text
		);
		
	}

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://exp.host/--/api/v2/push/send",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => json_encode($payload),
		CURLOPT_HTTPHEADER => array(
			"Accept: application/json",
			"Accept-Encoding: gzip, deflate",
			"Content-Type: application/json",
			"cache-control: no-cache",
			"host: exp.host"
		)
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		
		return array( 'error' => $err );
		
	} else {
		
		return json_decode( $response, true );
		
	}
	
}

/*
function post_without_wait($url, $params)
{
    foreach ($params as $key => &$val) {
      if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
    }
    $post_string = implode('&', $post_params);

    $parts=parse_url($url);

    $fp = fsockopen($parts['host'],
        isset($parts['port'])?$parts['port']:80,
        $errno, $errstr, 30);

    $out = "POST ".$parts['path']." HTTP/1.1\r\n";
    $out.= "Host: ".$parts['host']."\r\n";
    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out.= "Content-Length: ".strlen($post_string)."\r\n";
    $out.= "Connection: Close\r\n\r\n";
    if (isset($post_string)) $out.= $post_string;

    fwrite($fp, $out);
    fclose($fp);
}
*/

?>
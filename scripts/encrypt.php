<?php
	function encrypt_decrypt($action, $string) {
		$output = false;
	
		$encrypt_method = "AES-256-CBC";
		$secret_key = "C248D-93B3B-FD28A-53D5C";
		$secret_iv = "54D14-48698-BBD7A-1B953";
	
		// hash
		$key = hash("sha256", $secret_key);
		
		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash("sha256", $secret_iv), 0, 16);
		if( $action == "encrypt" ) {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		}
		else if( $action == "decrypt" ){
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
		return $output;
	}
?>
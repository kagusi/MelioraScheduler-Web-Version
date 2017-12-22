<?php

class RSA{

	
	public function __construct(){
			
	}
	
	//encrypt data
	public function encrypt($data, $salt){

		// Compress the data
		$fulldata = $data . $salt;		
		$plaintext = gzcompress($fulldata);
		
		// Get the public Key
		$publicKey = openssl_pkey_get_public(file_get_contents(dirname(__FILE__) .'\public.key'));
		$a_key = openssl_pkey_get_details($publicKey);
		 
		// Encrypt the data in small chunks 
		$chunkSize = ceil($a_key['bits'] / 8) - 11;
		$output = '';

		while ($plaintext)
		{
			$chunk = substr($plaintext, 0, $chunkSize);
			$plaintext = substr($plaintext, $chunkSize);
			$encrypted = '';
			if (!openssl_public_encrypt($chunk, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING))
			{
				return FAILED_TO_ENCRYPT;
				//die('Failed to encrypt data');
			}
			$output .= $encrypted;
		}
		openssl_free_key($publicKey);
		 
		// This is the final encrypted data to be store in database
		$encrypted = $output;
		
		return $encrypted;
		
	}
	
	//decrypt data
	public function decrypt($data){
		if (!$privateKey = openssl_pkey_get_private(file_get_contents(dirname(__FILE__) .'\private.key')))
		{
			die('Private Key failed');
		}
		$a_key = openssl_pkey_get_details($privateKey);
		 
		// Decrypt the data in the small chunks
		$chunkSize = ceil($a_key['bits'] / 8);
		$output = '';
		$encrypted = $data;
		 
		while ($encrypted)
		{
			$chunk = substr($encrypted, 0, $chunkSize);
			$encrypted = substr($encrypted, $chunkSize);
			$decrypted = '';
			if (!openssl_private_decrypt($chunk, $decrypted, $privateKey, OPENSSL_PKCS1_PADDING))
			{
				return FAILED_TO_DECRYPT;
				//die('Failed to decrypt data');
			}
			$output .= $decrypted;
		}
		openssl_free_key($privateKey);
		 
		// Uncompress the unencrypted data.
		$output = gzuncompress($output);
		
		return $output;
		
	}	
	
}


?>
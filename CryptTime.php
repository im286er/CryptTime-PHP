<?php
/*
 * CryptTime
 * Author: Kittinan
 * 
 * AES128 + PKCS7
 * 
 * 
 */
namespace Kittinan\CryptTime;

class CryptTime {
  
  
  private $IV = '0000000000000000';
  private $KEY = '00000000';
  private $SEPERATOR = 'XXXXX_XXXXX';
  
  
  function __construct() {
    
  }
  
  public function setIV($iv) {
    $this->IV = $iv;
  }
  
  public function setKey($key) {
    $this->KEY = $key;
  }
  
  public function encrypt($plainText, $availableTime = 86400) {
    $endTime = time() + $availableTime;
    $str = rand(0x112, 0xDEADC0DE).$this->SEPERATOR.$endTime.$this->SEPERATOR.$plainText;
    return $this->base64url_encode($this->_encryptAES128($this->IV, $this->KEY, $str));
  }
  
  public function decrypt($cipherText) {
    $str = $this->_decryptAES128($this->IV, $this->KEY, $this->base64url_decode($cipherText));
    
    $list = explode($this->SEPERATOR, $str);
    
    if (count($list) < 3) {
      return false;
    }
    
    list($randNum, $endTime, $plainText) = $list;
    $now = time();
    
    if ($now > $endTime) {
      //Overdue
      return false;
    }
    
    return $plainText;
  }
  
  private function _encryptAES128($iv, $key, $plain_text) {
    $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
    $blocksize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $plain_text = $this->pkcs7_pad($plain_text, $blocksize);
    mcrypt_generic_init($cipher, $key, $iv);
    $cipher_text = mcrypt_generic($cipher, $plain_text);
    mcrypt_generic_deinit($cipher);
    mcrypt_module_close($cipher);
    return $cipher_text;
  }

  private function _decryptAES128($iv, $key, $cipher_text) {
    $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
    mcrypt_generic_init($cipher, $key, $iv);
    $plain_text = mdecrypt_generic($cipher, $cipher_text);
    $plain_text = $this->pkcs7_unpad($plain_text);
    mcrypt_generic_deinit($cipher);
    mcrypt_module_close($cipher);
    return $plain_text;
  }

  private function pkcs7_pad($text, $blocksize) {
    $pad = $blocksize - (strlen($text) % $blocksize);
    return $text . str_repeat(chr($pad), $pad);
  }

  private function pkcs7_unpad($text) {
    $pad = ord($text{strlen($text) - 1});
    if ($pad > strlen($text))
      return false;
    if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
      return false;
    return substr($text, 0, -1 * $pad);
  }
  
  private function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }

  private function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
  }
  
}

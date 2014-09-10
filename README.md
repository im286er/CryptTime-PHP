CryptTime-PHP
=========

CryptTime-PHP is a simple class to encrypt string with timeout. the encryption use AES128/PKCS7.

Easy to use.
```php

$plainText = ''Hello World';

$cryptTime = \KITTINAN\CryptTime\CryptTime::getInstance();

$cipherText = $cryptTime->encrypt($plainText);  //Default timeout is 86400 seconds (1 day)

$decryptText = $cryptTime->decrypt($cipherText);

```

if you want to encrypt string with 10 minutes timeout.
```php

$plainText = ''Hello World';

$cryptTime = \KITTINAN\CryptTime\CryptTime::getInstance();

$cipherText = $cryptTime->encrypt($plainText, 600);  //10 minutes = 600 seconds

$decryptText = $cryptTime->decrypt($cipherText);
```

you can set IV and Key
```php

$cryptTime = \KITTINAN\CryptTime\CryptTime::getInstance();
$cryptTime->setIV('MyNewInitialValue');
$cryptTime->setKey('MyNewKeyMyNewKeyMyNewKey');
```




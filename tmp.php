<?php

$publicKey = "-----BEGIN PUBLIC KEY-----
MIIEIjANBgkqhkiG9w0BAQEFAAOCBA8AMIIECgKCBAEAm6glpTALuc82R+9Mqb5f
HVRC5dScc7USgWoIsYN1tF8YE0d7rhSIFvayVabiyabVmWscjAIlmYf6InSlLsDx
avfmapg32ECd92H49ZbsvXQpLqasyOkN7z6FUcuQ6pEMqfPBmBXKGngHazPp420o
Iki9hLc7IE9EMlDHfozckuJI8mB+bsd2oqua6SSTBYx5HYuSCbootf9GliSd7PVk
H6uir88j49/NFfvmrReicFBiMba959uOdBIhWl9AveZL+iI2NdS5SPw6eWltaMot
PSk7/5Z4Vn5Od7sQA0yUqmCj5XNV5EzRlP1jhP7SDv0D6Mpdf3HuKWdCBqepJBe2
rCHPQ9KrChQau4eEbJb5LIE3gFDpLxTHk9FEp+50evkpFONj0aAjSb3P4wsEGiOk
95Tm56gDRirnbbw/6SzhE7pEvXRUfMl1KO6maYK1z7KNMgEH99C2zCjZhpaXL5Io
rywCw009zoMT2qdKPMGOyQ4KPlCLCJYSF0y/rE07WNgl4BupVZR41B5MbXL5L7+X
OD0jBpbWI7v2ChP9rn5u6Lqpq6ewvc2RJO8lrAyZtzrNJNZNYmUKHrm7qAHJeJZX
Zh7OEf9U/T9JBpzf8l0MzyywlQGUBzb/niG0iZILt3XIpD+Xeyrr6hr+nabeiKXV
jHyUcG84zzLjv7sREzWEGoLBrdztMy69rbfd3d0DpjS90xceKZYBDd3vwjn6h0TN
KssqUb7BMH+zkCe/LQg6EGdXB13+xUSUjFKLLeBKu1VxMPfd/WmV1QumOodidvee
rQAv6yMevq2hVFkiFo7CUpaRv6dvQnQaqX2rHFKZY6zEIzbJXTznl6ZMtCcmcZMk
CYcoWZIAUR5tFP221XzIfJmymVRfJGiKTvt+g/SUUFJt6mq8ettu11XS4KSIxtaA
l8q2SSxpRQa80NUuaBpQc/3eP293wgcf/EOfzhCjxDLjsHSKV1AkSMyjvCzSsCdG
mMEIuT/D7PB7N8vlfhn5qsyt1Sm81/1EZ3u8UqToELhe8j7G26GVl/8ptSxofvZE
X0goYwW18PPhtZvkR8CXpZ7qwjqDcL5cQzcCldufjtqJ5GAwN6SrcmnYjQoo2cu9
XlWo0InPE8BpjR7vJpKLbppQzwUs9GQYx2bMSTbsrduc8zDXlPT5aOfgkJui/NQa
uxttvsXqXd3nNJhbO0BN+wCDT0j4LNRvMlJloWEGrBkY4SA5I1MX8XBL34Csy6Bu
bHWxXNBAGYMchcJKly7XN2hA61V4QCCiFz/MP9l1llw/Mk4D5IUTxcfcEDHx7LO0
To+pc5kuXS6Aps6lKJdwv6h0Bi9SWtBpFi2RtpQpAc+dVPQ9lwq3VTJV5GZz3AgV
KQIDAQAB
-----END PUBLIC KEY-----
";

$payload['device_id']  = 'f9ed7a5e-7572-5f87-2259-a2c00a04fd45';
$payload['project_id'] = '28f27590923d962388f0da125553c5';
$payload['version'] = 1;
$payload = json_encode($payload, JSON_UNESCAPED_UNICODE);

openssl_public_encrypt(
    $payload,
    $encrypted,
    $publicKey,
    OPENSSL_PKCS1_OAEP_PADDING
);

$postData = [
    'encrypted' => base64_encode($encrypted)
];

$ch = curl_init('https://account.urmic.org/encoder/update.php');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $postData,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
]);

$response = curl_exec($ch);
print_r($response);

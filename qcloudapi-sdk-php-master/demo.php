<?php
require_once './src/QcloudApi/QcloudApi.php';

$config = array('SecretId'       => getenv("QCLOUD_SECRET_ID"), //'你的secretId',需要在环境变量中设置
                'SecretKey'      => getenv("QCLOUD_SECRET_KEY"), //'你的secretKey',需要在环境变量中设置
                'RequestMethod'  => 'GET',
                'DefaultRegion'  => 'gz');

$cvm = QcloudApi::load(QcloudApi::MODULE_CVM, $config);

$package = array('offset' => 0, 'limit' => 3, 'SignatureMethod' =>'HmacSHA256');

$a = $cvm->DescribeInstances($package);
// $a = $cvm->generateUrl('DescribeInstances', $package);

if ($a === false) {
    $error = $cvm->getError();
    echo "Error code:" . $error->getCode() . ".\n";
    echo "message:" . $error->getMessage() . ".\n";
    echo "ext:" . var_export($error->getExt(), true) . ".\n";
}

echo "Request: " . $cvm->getLastRequest();
echo "\nResponse: " . $cvm->getLastResponse();
echo "\n";

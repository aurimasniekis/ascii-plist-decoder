<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

spl_autoload_register(function ($className) {
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    require __DIR__ . '/src/' . $fileName;
});

$s = '
{
    "key-1" = "value 1";
    "key-2" = "value 2";
    "ary" = (
        "1", "2", "13"
    );
    "pysch" = "\'lol\"bugoga\\\\";
}
';

$out = \AsciiPlist\Decoder::decode($s);

echo $s.PHP_EOL;
echo var_export($out, true).PHP_EOL;

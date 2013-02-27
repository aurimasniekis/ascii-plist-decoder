# AsciiPlist\Decoder

Decoding to php-array tool of (Old-style, NextStep) Ascii Plist

## Example

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


See test.php for full example.

## License

[WTFPL](http://sam.zoy.org/wtfpl/).

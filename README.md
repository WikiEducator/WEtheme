# WEtheme

## STATUS

This software is VERY NEW.  There are bugs and missing features.

## USAGE

Please see the
[extension wiki page](http://wikieducator.org/Extension:WEtheme).

## INSTALLATION

1. Rename this directory to extensions/WEtheme inside your MediaWiki directory.
2. Add this line to the end of your LocalSettings.php:

    ```require_once('extensions/WEtheme/WEtheme.php');```

3. Create a global map from partner name to CSS/JS prefix.

    ```
    $wgWEthemePartners = array(
        'WikiEducator' => 'http://s3.amazonaws.com/WikiEducator/',
        'OERF' => '/extensions/WEtheme/t/OERf/',
    );
    ```

## CONTACT

* Jim Tittsler
* jimt@OER.me

## LICENSE

MIT


# WEtheme

## STATUS

This software is not widely used.  There are bugs and missing features.

## USAGE

Please see the
[extension wiki page](https://wikieducator.org/Extension:WEtheme).

## INSTALLATION

Requires MediaWiki 1.35 or later.

1. Rename this directory to extensions/WEtheme inside your MediaWiki directory.
2. Add this line to the end of your LocalSettings.php:

    ```wfLoadExtension( 'WEtheme' );```

3. Create a global map from partner name to CSS/JS prefix.
   Partner theme files live in `t/` (not kept in this repository).

    ```
    $wgWEthemePartners = array(
        'OERf' => '/extensions/WEtheme/t/OERf/',
    );
    ```

## CONTACT

* Jim Tittsler
* jimt@OER.me

## LICENSE

MIT


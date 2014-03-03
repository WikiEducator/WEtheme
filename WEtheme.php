<?php

/**
* @package MediaWiki
* @ingroup Extensions
* @subpackage WEtheme
* @version 0.1
* @author Jim Tittsler <jim@OERfoundation.org>
* @licence MIT
*/

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( -1 );
}

$wgExtensionCredits['parserhook'][] = array(
	'name'           => 'WEtheme',
	'version'        => '0.2.0',
	'url'            => 'http://WikiEducator.org/Extension:WEtheme',
	'author'         => '[http://WikiEducator.org/User:JimTittsler Jim Tittsler]',
        'description'    => 'Allow theming pages',
);

$GLOBALS['weTheme'] = array();
$wgHooks['ParserFirstCallInit'][] = 'weThemeInit';
$wgHooks['ParserAfterTidy'][] = 'weThemeHeaders';

function weThemeInit( Parser $parser ) {
	$parser->setHook( 'wetheme', 'weThemeTag' );
	return true;
}

function weThemeTag( $input, array $args, Parser $parser ) {
	global $weTheme;
	$partners = array('WikiEducator', 'OERf', 'UCan', 'OP');
	//error_log("wetheme " . getcwd() . " input=$input args=". print_r($args, true) . "\n", 3, '/tmp/html5video.log');
	if (array_key_exists('t', $args)) {
		// check it is a partner, and the required file(s) exist
		$p = $args['t'];
		//error_log("partner $p\n", 3, '/tmp/html5video.log');
		if (in_array($p, $partners)) {
			//error_log("partner theme $p\n", 3, '/tmp/html5video.log');
			if (is_readable(dirname(__FILE__) . "/$p/$p.css")) {
				//error_log("  adding css\n", 3, '/tmp/html5video.log');
				$weTheme[] = "/extensions/WEtheme/$p/$p.css";
			}
			if (is_readable(dirname(__FILE__) . "/$p/$p.js")) {
				//error_log("  adding js\n", 3, '/tmp/html5video.log');
				$weTheme[] = "/extensions/WEtheme/$p/$p.js";
			}
		}
		if (array_key_exists('s', $args)) {
			// use s for a subtheme name
			$s = preg_replace('/[^-._a-zA-Z0-9]/', '', $args['s']);
			//error_log("partner $p\n", 3, '/tmp/html5video.log');
			//error_log("partner theme $p\n", 3, '/tmp/html5video.log');
			if (is_readable(dirname(__FILE__) . "/$p/$s.css")) {
				//error_log("  adding css\n", 3, '/tmp/html5video.log');
				$weTheme[] = "/extensions/WEtheme/$p/$s.css";
			}
			if (is_readable(dirname(__FILE__) . "/$p/$s.js")) {
				//error_log("  adding js\n", 3, '/tmp/html5video.log');
				$weTheme[] = "/extensions/WEtheme/$p/$s.js";
			}
		}
	}
	return "";
}

function endsWith($haystack, $needle) {
	$length = strlen($needle);
	if ($length == 0) {
		return true;
	}

	return (substr($haystack, -$length) === $needle);
}

function weThemeHeaders( Parser $parser, $text ){
	global $weTheme;
	global $wgOut;

	//error_log("checking if we need to add WEtheme headers\n", 3, '/tmp/html5video.log');
	if ( count($weTheme) ) {
		//error_log("  adding headers\n", 3, '/tmp/html5video.log');
		foreach ($weTheme as $f) {
			if (endsWith($f, '.css')) {
				$wgOut->addScript('<link href="' . $f . '" rel="stylesheet" type="text/css">' . "\n");
			} elseif (endswith($f, '.js')) {
				$wgOut->addScript('<script type="text/javascript" src="' . $f . '"></script>' . "\n");
			}
		}
	}
	return true;
}

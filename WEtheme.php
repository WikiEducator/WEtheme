<?php

/**
* @package MediaWiki
* @ingroup Extensions
* @subpackage WEtheme
* @version 0.2
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

function weThemeLog( $msg ) {
	error_log( date('c') . ' ' . $msg . "\n", 3, '/tmp/WEtheme.log' );
}

function weThemeTag( $input, array $args, Parser $parser ) {
	global $weTheme;
	$partners = array('WikiEducator', 'OERf', 'UCan', 'OP');
	//weThemeLog("wetheme " . getcwd() . " input=$input args=". print_r($args, true));
	if (array_key_exists('t', $args)) {
		// check it is a partner, and the required file(s) exist
		$p = $args['t'];
		//weThemeLog("partner $p");
		if (in_array($p, $partners)) {
			//weThemeLog("partner theme $p");
			if (is_readable(dirname(__FILE__) . "/$p/$p.css")) {
				//weThemeLog("  adding css");
				$weTheme[] = "/extensions/WEtheme/$p/$p.css";
			}
			if (is_readable(dirname(__FILE__) . "/$p/$p.js")) {
				//weThemeLog("  adding js");
				$weTheme[] = "/extensions/WEtheme/$p/$p.js";
			}
		}
		if (array_key_exists('s', $args)) {
			// use s for a subtheme name
			$s = preg_replace('/[^-._a-zA-Z0-9]/', '', $args['s']);
			//weThemeLog("partner $p");
			//weThemeLog("partner subtheme $s");
			if (is_readable(dirname(__FILE__) . "/$p/$s.css")) {
				//weThemeLog("  adding css");
				$weTheme[] = "/extensions/WEtheme/$p/$s.css";
			}
			if (is_readable(dirname(__FILE__) . "/$p/$s.js")) {
				//weThemeLog("  adding js");
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

	//weThemeLog("checking if we need to add WEtheme headers, count: " . count($weTheme));
	if ( count($weTheme) ) {
		//weThemeLog("  adding headers");
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

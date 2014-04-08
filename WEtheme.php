<?php

/**
* @package MediaWiki
* @ingroup Extensions
* @subpackage WEtheme
* @version 0.4
* @author Jim Tittsler <jim@OERfoundation.org>
* @licence MIT
*/

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( -1 );
}

$wgExtensionCredits['parserhook'][] = array(
	'name'           => 'WEtheme',
	'version'        => '0.4.0',
	'url'            => 'http://WikiEducator.org/Extension:WEtheme',
	'author'         => '[http://WikiEducator.org/User:JimTittsler Jim Tittsler]',
	'description'    => 'Allow theming pages',
);

$GLOBALS['weTheme'] = array();
$wgHooks['ParserFirstCallInit'][] = 'weThemeInit';
$wgHooks['OutputPageBeforeHTML'][] = 'weThemeHeaders';

function weThemeInit( Parser $parser ) {
	$parser->setHook( 'wetheme', 'weThemeTag' );
	return true;
}

function weThemeLog( $msg ) {
	error_log( date('c') . ' ' . $msg . "\n", 3, '/tmp/WEtheme.log' );
}

function weThemeTag( $input, array $args, Parser $parser ) {
	global $wgWEthemePartners;
	// FIXME add author, keywords
	$weThemeArgs = array( 'p', 'css', 'js', 'viewport', 'ie', 'header' );
	$r = array();
	if ( array_key_exists('p', $args) ) {
		$p = $args['p'];
	       	if ( array_key_exists($args['p'], $wgWEthemePartners) ) {
			//embed marker in wikitext
			foreach ( $weThemeArgs as $a ) {
				if ( array_key_exists( $a, $args ) ) {
					$r[$a] = $args[$a];
				}
			}
			return '<!-- WEtheme ' . base64_encode( json_encode( $r ) ) . ' -->';
		}
	}
	return '';
}

function weThemeHeaders( OutputPage &$out, &$text ){
	global $wgOut, $IP;
	global $wgWEthemePartners, $wgWEthemePrefix;

	// look for our magic comment, and add headers when detected
	//   perform simple sanity check on validity
	//weThemeLog("checking if we need to add WEtheme headers");
	if ( preg_match_all(
		'|<!-- WEtheme ([=+/a-zA-Z0-9]+)|',
		$text, $matches) == false ) {
			return true;
	}
	foreach ( $matches[1] as $m ) {
		$r = json_decode( @base64_decode( $m ) );

		if ( array_key_exists('p', $r)
		&& array_key_exists( $r->p, $wgWEthemePartners ) ) {
			$p = $wgWEthemePartners[$r->p];
			if ( array_key_exists( 'ie', $r ) ) {
				$wgOut->addScript('<!--['
					. preg_replace( '/[^- a-zA-Z0-9]/', '', $r->ie ). ']>');
			}
			if ( array_key_exists( 'css', $r ) ) {
				$f = preg_replace( '|[^-_:./a-zA-Z0-9]|', '',
					$r->css );
				$wgOut->addScript('<link href="' . $p . $f . '" rel="stylesheet" type="text/css">' . "\n");
			}
			if ( array_key_exists( 'js', $r ) ) {
				$f = preg_replace( '|[^-_:./a-zA-Z0-9]|', '',
					$r->js );
				$wgOut->addScript('<script type="text/javascript" src="' . $p . $f . '"></script>' . "\n");
			}
			if ( array_key_exists( 'viewport', $r ) ) {
				//FIXME do a decent job of sanitizing viewport
				$f = preg_replace( '/[^-=., a-zA-Z0-9]/',
					'', $r->viewport);
				$wgOut->addScript('<meta name="viewport" content="' . $f . ' />' . "\n");
			}
			if ( array_key_exists( 'header', $r ) ) {
				$f = preg_replace( '/[^-=., a-zA-Z0-9]/',
					'', $r->header);
				$f = preg_replace( '/\.{2,}/', '.', $f);
				$pathname = "$IP/$p$f";
				if ( is_readable( $pathname ) ) {
					$h = fopen( $pathname, 'r' );
					$o = fread( $h, filesize( $pathname ) );
					fclose( $h );
					$wgOut->addScript ( $o ) ;
				}
			}
			if ( array_key_exists( 'ie', $r ) ) {
				$wgOut->addScript('<![endif]-->');
			}
		}
	}
	return true;
}

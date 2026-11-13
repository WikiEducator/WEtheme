<?php
/**
 * @package MediaWiki
 * @ingroup Extensions
 * @subpackage WEtheme
 * @version 0.6
 * @author Jim Tittsler <jimt@OER.me>
 * @license MIT
 */

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( -1 );
}

class WEthemeHooks {

	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->setHook( 'wetheme', 'WEthemeHooks::weThemeTag' );
		return true;
	}

	public static function weThemeTag( $input, array $args, Parser $parser ) {
		global $wgWEthemePartners;
		// FIXME add author, keywords
		$weThemeArgs = array( 'p', 'css', 'js', 'viewport', 'header' );
		$r = array();
		if ( array_key_exists('p', $args) ) {
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

	public static function onOutputPageBeforeHTML( OutputPage $out, &$text ) {
		global $IP;
		global $wgWEthemePartners;

		// look for our magic comment, and add headers when detected
		//   perform simple sanity check on validity
		if ( preg_match_all(
			'|<!-- WEtheme ([=+/a-zA-Z0-9]+)|',
			$text, $matches) == false ) {
				return true;
		}
		foreach ( $matches[1] as $m ) {
			$r = json_decode( base64_decode( $m ), true );
			if ( !is_array( $r ) ) {
				continue;
			}

			if ( isset( $r['p'] )
			&& array_key_exists( $r['p'], $wgWEthemePartners ) ) {
				$p = $wgWEthemePartners[$r['p']];
				// css= and js= must stay inside the partner's prefix
				if ( isset( $r['css'] ) ) {
					$f = preg_replace( '|[^-_:./a-zA-Z0-9]|', '',
						$r['css'] );
					if ( strpos( $f, '..' ) === false ) {
						$out->addScript('<link href="' . $p . $f . '" rel="stylesheet" type="text/css">' . "\n");
					}
				}
				if ( isset( $r['js'] ) ) {
					$f = preg_replace( '|[^-_:./a-zA-Z0-9]|', '',
						$r['js'] );
					if ( strpos( $f, '..' ) === false ) {
						$out->addScript('<script type="text/javascript" src="' . $p . $f . '"></script>' . "\n");
					}
				}
				if ( isset( $r['viewport'] ) ) {
					//FIXME do a decent job of sanitizing viewport
					$f = preg_replace( '/[^-=., a-zA-Z0-9]/',
						'', $r['viewport']);
					$out->addScript('<meta name="viewport" content="' . $f . '" />' . "\n");
				}
				if ( isset( $r['header'] ) ) {
					$f = preg_replace( '/[^-=., a-zA-Z0-9]/',
						'', $r['header']);
					$f = preg_replace( '/\.{2,}/', '.', $f);
					$pathname = "$IP/$p$f";
					$pathname = preg_replace( '|\/{2,}|', '/',
						$pathname );
					if ( is_file( $pathname ) && is_readable( $pathname ) ) {
						$o = file_get_contents( $pathname );
						if ( $o !== false && $o !== '' ) {
							$out->addScript( $o );
						}
					}
				}
			}
		}
		return true;
	}
}

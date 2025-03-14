<?php

namespace Onoi\Tesa;

/**
 * @license GPL-2.0-or-later
 * @since 0.1
 *
 * @author mwjames
 */
class Normalizer {

	/**
	 * @since 0.1
	 *
	 * @param string $text
	 *
	 * @param int $flag
	 */
	public static function applyTransliteration( $text, $flag = Transliterator::DIACRITICS ) {
		return Transliterator::transliterate( $text, $flag );
	}

	/**
	 * @see Localizer::convertDoubleWidth
	 *
	 * @since 0.1
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public static function convertDoubleWidth( $text ) {
		static $full = null;
		static $half = null;

		// ,。／？《》〈〉；：“”＂〃＇｀［］｛｝＼｜～！－＝＿＋）（()＊…—─％￥＃
		//,./?«»();:“”

		if ( $full === null ) {
			$fullWidth = "０１２３４５６７８９ＡＢＣＤＥＦＧＨＩＪＫＬＭＮＯＰＱＲＳＴＵＶＷＸＹＺａｂｃｄｅｆｇｈｉｊｋｌｍｎｏｐｑｒｓｔｕｖｗｘｙｚ";
			$halfWidth = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

			// http://php.net/manual/en/function.str-split.php, mb_str_split
			$length = mb_strlen( $fullWidth, 'UTF-8' );
			$full = [];

			for ( $i = 0; $i < $length; $i += 1 ) {
				$full[] = mb_substr( $fullWidth, $i, 1, 'UTF-8' );
			}

			$half = str_split( $halfWidth );
		}

		return str_replace( $full, $half, trim( $text ) );
	}

	/**
	 * @since 0.1
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public static function toLowercase( $text ) {
		$encoding = mb_detect_encoding( $text ) ?: 'UTF-8';
		return mb_strtolower( $text, $encoding );
	}

	/**
	 * @since 0.1
	 *
	 * @param string $text
	 * @param int|null $length
	 *
	 * @return string
	 */
	public static function reduceLengthTo( $text, $length = null ) {
		if ( $length === null || mb_strlen( $text ) <= $length ) {
			return $text;
		}

		$encoding = mb_detect_encoding( $text ) ?: 'UTF-8';
		$lastWholeWordPosition = $length;

		if ( strpos( $text, ' ' ) !== false ) {
			$lastWholeWordPosition = strrpos( mb_substr( $text, 0, $length, $encoding ), ' ' ); // last whole word
		}

		if ( $lastWholeWordPosition > 0 ) {
			$length = $lastWholeWordPosition;
		}

		return mb_substr( $text, 0, $length, $encoding );
	}

}

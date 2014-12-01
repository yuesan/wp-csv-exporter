<?php
/**
 * esc_htmlの配列対応版
 */
function esc_htmls( $str ) {
	if ( is_array( $str ) ) {
		return array_map( "esc_html", $str );
	}else {
		return esc_html( $str );
	}
}

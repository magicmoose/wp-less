<?php
/**
 * Enables the use LESS in WordPress
 *
 * See README.md for usage information
 */


if ( ! class_exists( 'lessc' ) ) {

	// load LESS parser
	require_once( 'lessc/lessc.inc.php' );

}

if ( ! class_exists( 'wp_less' ) ) {

	class wp_less {

		function __construct() {

			// every CSS file URL gets passed through this filter
			add_filter( 'style_loader_src', array( &$this, 'parse_stylesheet' ), 100000, 2 );

		}

		/**
		 * Lessify the stylesheet and return the href of the compiled file
		 *
		 * @return String    URL of the compiled stylesheet
		 */
		function parse_stylesheet( $src, $handle ) {

			// we only want to handle .less files
			if ( ! strstr( $src, '.less' ) )
				return $src;

			// get file path from $src
			preg_match( "/^(.*?\/wp-content\/)([^\?]+)(.*)$/", $src, $src_bits );
			$src_path = WP_CONTENT_DIR . '/' . $src_bits[ 2 ];

			// cache file name
			$cache_path = str_replace(".less",".css",$src_path);

			// ccompile automatically regenerates files if source's modified time has changed
			try {
				//lessc::ccompile( $src_path, $cache_path );
				$this->auto_compile_less( $src_path, $cache_path );
			} catch ( exception $ex ) {
				wp_die( $ex->getMessage() );
			}

			// return the compiled stylesheet with the query string it had if any
			return get_stylesheet_directory_uri(). "/$handle.css" . ( isset( $src_bits[ 3 ] ) ? $src_bits[ 3 ] : '' );

		}

		/**
		 * Get (and create if unavailable) the compiled CSS cache directory
		 */
		function get_cache_dir( $path = true ) {

			// get path and url info
			$upload_dir = wp_upload_dir();

			if ( $path ) {
				$dir = str_replace( $upload_dir[ 'subdir' ], '', $upload_dir[ 'path' ] ) . '/wp-less-cache';
				// create folder if it doesn't exist yet
				if ( ! file_exists( $dir ) )
					wp_mkdir_p( $dir );
			} else {
				$dir = str_replace( $upload_dir[ 'subdir' ], '', $upload_dir[ 'url' ] ) . '/wp-less-cache';
			}

			return $dir;
		}
		
		function auto_compile_less($less_fname, $css_fname) {
		  // load the cache
		  $cache_fname = $less_fname.".cache";
		  if (file_exists($cache_fname)) {
		    $cache = unserialize(file_get_contents($cache_fname));
		  } else {
		    $cache = $less_fname;
		  }
		
		  $new_cache = lessc::cexecute($cache);
		  if (!is_array($cache) || $new_cache['updated'] > $cache['updated']) {
		    file_put_contents($cache_fname, serialize($new_cache));
		    file_put_contents($css_fname, $new_cache['compiled']);
		  }
		}

	}

	// initialise
	$wp_less = new wp_less();

}

?>

<?php

// phpcs:disable

class WPCF_Loader
{

    /**
     * Settings
     * @var array
     */
    private static $__settings = array();

    public static function init( $settings = array() ) {
        self::$__settings = (array) $settings;
        self::registerScripts();
        self::registerStyles();
        add_action( 'admin_print_scripts', array('WPCF_Loader', 'renderJsSettings'), 5 );
		add_filter( 'the_posts', array('WPCF_Loader', 'wpcf_cache_complete_postmeta') );
		add_filter( 'wpcf_fields_postmeta_value_save', array( 'WPCF_Loader', 'wpcf_sanitize_postmeta_values_on_save' ) );
		add_filter( 'wpcf_fields_usermeta_value_save', array( 'WPCF_Loader', 'wpcf_sanitize_usermeta_values_on_save' ) );
    }

	/**
	* Sanitize fields values on save
	*
	*/

	public static function wpcf_sanitize_postmeta_values_on_save( $value ) {
		if (
			current_user_can( 'unfiltered_html' )
			&& wpcf_get_settings('postmeta_unfiltered_html') != 'off'
		) {
			return $value;
		}
		if ( is_array( $value ) ) {
			// Recursion
			$value = array_map( array( 'WPCF_Loader', 'wpcf_sanitize_postmeta_values_on_save' ), $value );
		} else {
			$value = wp_filter_post_kses( $value );
		}
		return $value;
	}

	public static function wpcf_sanitize_usermeta_values_on_save( $value ) {
		if (
			current_user_can( 'unfiltered_html' )
			&& wpcf_get_settings('usermeta_unfiltered_html') != 'off'
		) {
			return $value;
		}
		if ( is_array( $value ) ) {
			// Recursion
			$value = array_map( array( 'WPCF_Loader', 'wpcf_sanitize_usermeta_values_on_save' ), $value );
		} else {
			$value = wp_filter_post_kses( $value );
		}
		return $value;
	}

    /**
     * Cache the postmeta for posts returned by a WP_Query
     *
     * @global object $wpdb
     *
     */

    public static function wpcf_cache_complete_postmeta( $posts ) {
		global $wpdb;
		if ( !$posts )
			return $posts;
		$post_ids = array();
		$cache_group_ids = 'types_cache_ids';
		$cache_group = 'types_cache';
		foreach ( $posts as $post ) {
			$cache_key_looped_post = md5( 'post::_is_cached' . $post->ID );
			$cached_object = wp_cache_get( $cache_key_looped_post, $cache_group_ids );
			if ( false === $cached_object ) {
				$post_ids[] = intval( $post->ID );
				wp_cache_add( $cache_key_looped_post, $post->ID, $cache_group_ids );
			}
		}
		if ( count( $post_ids ) > 0 ) {
			$id_list = join( ',', $post_ids );
			$all_postmeta = $wpdb->get_results( "SELECT * FROM {$wpdb->postmeta} WHERE post_id IN ($id_list)", OBJECT );
			if ( !empty( $all_postmeta ) ) {
				$cache_key_keys = array();
				foreach ( $all_postmeta as $metarow ) {
					$mpid = intval($metarow->post_id);
					$mkey = $metarow->meta_key;
					$cache_key_keys[$mpid . $mkey][] = $metarow;
				}
				foreach ( $cache_key_keys as $single_meta_keys => $single_meta_values ) {
					$cache_key_looped_new = md5( 'field::_get_meta' . $single_meta_keys );
					wp_cache_add( $cache_key_looped_new, $single_meta_values, $cache_group );// WordPress cache
				}
			}
		}
		return $posts;
    }

    /**
     * Register scripts.
     */
    private static function registerScripts() {
        $min = '';//WPCF_DEBUG ? '-min' : '';
        wp_register_script( 'types',
	        WPCF_EMBEDDED_RES_RELPATH . '/js/basic.js',
	        array('jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-tabs', 'toolset_select2'),
	        WPCF_VERSION, true );
        if ( !wp_script_is( 'toolset-colorbox', 'registered' ) ) {
            wp_register_script( 'toolset-colorbox',
                    WPCF_EMBEDDED_RES_RELPATH . '/js/jquery.colorbox.js',
                    array('jquery'), WPCF_VERSION, true );
        }
        wp_register_script( 'types-utils',
                WPCF_EMBEDDED_RES_RELPATH . "/js/utils{$min}.js", array('jquery'),
                WPCF_VERSION, true );
        global $pagenow;
        // Exclude on post edit screen
        if ( defined( 'WPTOOLSET_FORMS_ABSPATH' )
                && !in_array( $pagenow, array('edit.php', 'post.php', 'post-new.php') ) ) {
        wp_register_script( 'types-conditional',
                WPCF_EMBEDDED_RES_RELPATH . '/js/conditional.js',
                array('types-utils'), WPCF_VERSION, true );
        wp_register_script( 'types-validation',
                WPCF_EMBEDDED_RES_RELPATH . "/js/validation{$min}.js",
                array('jquery'), WPCF_VERSION, true );
        }
//        wp_register_script( 'types-jquery-validation',
//                WPCF_EMBEDDED_RES_RELPATH . '/js/jquery-form-validation/jquery.validate-1.11.1.min.js',
//                array('jquery'), WPCF_VERSION, true );
//        wp_register_script( 'types-jquery-validation-additional',
//                WPCF_EMBEDDED_RES_RELPATH . '/js/jquery-form-validation/additional-methods-1.11.1.min.js',
//                array('types-jquery-validation'), WPCF_VERSION, true );
//        wp_register_script( 'types-js-validation',
//                WPCF_EMBEDDED_RES_RELPATH . '/js/jquery-form-validation/types.js',
//                array('types-jquery-validation-additional'), WPCF_VERSION, true );
		wp_register_script(
			'types-export-import',
			WPCF_EMBEDDED_RES_RELPATH . '/js/export-import.js',
			[ 'jquery', Types_Asset_Manager::SCRIPT_POINTER ],
			WPCF_VERSION,
			true
		);
		wp_register_script( 'types-settings', WPCF_EMBEDDED_RES_RELPATH . '/js/settings.js',
                array( 'jquery', 'underscore' ), WPCF_VERSION, true );
		$settings_script_texts = array(
			'wpcf_settings_nonce'	=> wp_create_nonce( 'wpcf_settings_nonce' )
		);
		wp_localize_script( 'types-settings', 'wpcf_settings_i18n', $settings_script_texts );
    }

    /**
     * Register styles.
     */
    private static function registerStyles() {
        wp_register_style( 'types',
                WPCF_EMBEDDED_RES_RELPATH . '/css/basic.css', array(),
                WPCF_VERSION );
        if ( !wp_style_is( 'toolset-colorbox', 'registered' ) ) {
            wp_register_style( 'toolset-colorbox',
                    WPCF_EMBEDDED_RES_RELPATH . '/css/colorbox.css', array(),
                    WPCF_VERSION );
        }
    }

    /**
     * Returns HTML formatted output.
     *
     * @param string $view
     * @param mixed $data
     * @return string
     */
    public static function view( $view, $data = array() ) {
        $file = WPCF_EMBEDDED_ABSPATH . '/views/'
                . strtolower( strval( $view ) ) . '.php';
        if ( !file_exists( $file ) ) {
            return '<code>missing_view</code>';
        }
        ob_start();
        include $file;
        $output = ob_get_contents();
        ob_get_clean();

        return apply_filters( 'wpcf_get_view', $output, $view, $data );
    }

    /**
     * Returns HTML formatted output.
     *
     * @param string $view
     * @param mixed $data
     * @return string
     */
    public static function loadView( $view ) {
        $file = WPCF_EMBEDDED_ABSPATH . '/views/'
                . strtolower( strval( $view ) ) . '.php';
        if ( !file_exists( $file ) ) {
            return new WP_Error( 'types_loader', 'missing view ' . $view );
        }
        require_once $file;
    }

    /**
     * Returns HTML formatted output.
     *
     * @param string $template
     * @param mixed $data
     * @return string
     */
    public static function template( $template, $data = array() ) {
        $file = WPCF_EMBEDDED_ABSPATH . '/views/templates/'
                . strtolower( strval( $template ) ) . '.tpl.php';
        if ( !file_exists( $file ) ) {
            return '<code>missing_template</code>';
        }
        ob_start();
        include $file;
        $output = ob_get_contents();
        ob_get_clean();

        return apply_filters( 'wpcf_get_template', $output, $template, $data );
    }

    /**
     * Loads model.
     *
     * @param string $template
     * @param mixed $data
     * @return string
     */
    public static function loadModel( $model ) {
        $file = WPCF_EMBEDDED_ABSPATH . '/models/'
                . strtolower( strval( $model ) ) . '.php';
        if ( !file_exists( $file ) ) {
            return new WP_Error( 'types_loader', 'missing model ' . $model );
        }
        require_once $file;
    }

    /**
     * Loads class.
     *
     * @param string $template
     * @param mixed $data
     * @return string
     */
    public static function loadClass( $class ) {
        $file = WPCF_EMBEDDED_ABSPATH . '/classes/'
                . strtolower( strval( $class ) ) . '.php';
        if ( !file_exists( $file ) ) {
            return new WP_Error( 'types_loader', 'missing class ' . $class );
        }
        require_once $file;
    }

    /**
     * Loads include.
     *
     * @param string $template
     * @param mixed $data
     * @return string
     */
    public static function loadInclude( $name, $mode = 'embedded' ) {
        $path = $mode == 'plugin' ? WPCF_ABSPATH : WPCF_EMBEDDED_ABSPATH;
        $file = $path . '/includes/' . strtolower( strval( $name ) ) . '.php';
        if ( !file_exists( $file ) ) {
            return new WP_Error( 'types_loader', 'missing include ' . $name );
        }
        require_once $file;
    }

    /**
     * Adds JS settings.
     *
     * @staticvar array $settings
     * @param type $id
     * @param type $setting
     */
    public static function addJsSetting( $id, $setting = '' ) {
        self::$__settings[$id] = $setting;
    }

    /**
     * Renders JS settings.
     */
    public static function renderJsSettings() {
        $settings = (array) self::$__settings;
        $settings['wpnonce'] = wp_create_nonce( '_typesnonce' );
        $settings['cookiedomain'] = COOKIE_DOMAIN;
        $settings['cookiepath'] = COOKIEPATH;
        $settings['validation'] = array();
        echo '
        <script type="text/javascript">
            //<![CDATA[
            var types = ' . json_encode( $settings ) . ';
            //]]>
        </script>';
    }

}

<?php
/*
Plugin Name: Code Debugget
Plugin URI: http://www.andrespina.com/
Description: Enable debug code
Version: 1.0
Author: neoslink
Author URI: http://www.andrespina.com
License: GPL
*/

// If this file is called directly, abort.
defined('ABSPATH') or die();

class CodeDebugger {

	/**
	 * Variable for singleton
	 * @var CodeDebugger
	 */
	private static $instance;

	function __construct() {

		define( 'CDEBUG_URL', plugin_dir_url( __FILE__ ) );
		define( 'CDEBUG_PATH', plugin_dir_path( __FILE__ ) );
		define( 'CDEBUG_URL_INCLUDE', CDEBUG_URL . 'includes/' );

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
     * Initialize the singleton
     */
    public static function instance() {

        if ( ! isset( self::$instance ) ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * This method add the admin_menu
     */
    public function admin_menu() {
    	add_menu_page( 'Code Debugger', 'Code Debugger', 'manage_options', 'code-debugger', array( $this, 'page' ), 'dashicons-editor-code' );
	}

	/**
	 * This methos render page
	 */
    public function page() {

    	$this->register_style_and_javascript();

    	$content = '';

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$content = $this->clear_content( $_POST['textarea-debug'] );
		}

		echo '<div class="wrap">';
			echo '<form action="#" method="POST">';
				echo '<h1>Code Debugger</h1>';
				echo '<div class="postbox">';
					echo '<div class="inside">';
						echo '<textarea id="textarea-debug" name="textarea-debug" class="large-text code" rows="10" cols="20">' . $content . '</textarea>';
					echo '</div>';
				echo '</div>';
				echo '<input type="submit" value="Execute code" class="button button-primary">';
			echo '</form>';
		echo '</div>';
		
		$this->execute();
    }

    /**
     * This method execute de code
     */
    public function execute() {

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {

			$title = 'Result execute';

			try {

				$content = $this->clear_content($_POST['textarea-debug']);

				print '<div class="wrap">';
					print '<h2>' . $title . '</h2>';
					print '<div class="postbox">';
						print '<div class="inside">';
							eval($content);
						print '</div>';
					print '</div>';
				print '</div>';
			}
			catch(Exception $e) {

				echo '<div class="wrap">';
					echo '<h2>' . $title . '</h2>';
						echo '<div class="postbox">';
							echo '<div class="inside">';
								echo '<div class="error">';
									echo $e->getMessage();
								echo '</div>';
							echo '</div>';
						echo '</div>';
				echo '</div>';
			}
		}
	}

	/**
	 * This method clear the content text debugger
	 * 
	 * @param  [string] $text [text code]
	 * @return [string]       [clear text code]
	 */
    public function clear_content($text) {

    	$clear = array(
    		'find' => array(
    			"\'",
    			'\"'
    		),
    		'replace' => array(
    			"'",
    			'"'
    		)
    	);

		$clear = apply_filters( 'cdebug_items_for_replace' ,$clear ); 

		return str_replace( $clear['find'], $clear['replace'], $text );
	}

	/**
	 * This method add the style and javascript
	 */
	public function register_style_and_javascript() {

		$items = array(
			'cdebug_codemirror_style' => array(
				'type' => 'style',
				'file' => CDEBUG_URL_INCLUDE . 'codemirror/lib/codemirror.css'
			),
			'cdebug_codemirror_js' => array(
				'type' => 'script',
				'file' => CDEBUG_URL_INCLUDE . 'codemirror/lib/codemirror.js'
			),
			'cdebug_codemirror_addon_matchbrackets' => array(
				'type' => 'script',
				'file' => CDEBUG_URL_INCLUDE . 'codemirror/addon/edit/matchbrackets.js'
			),
			'cdebug_codemirror_mode_clike' => array(
				'type' => 'script',
				'file' => CDEBUG_URL_INCLUDE . 'codemirror/mode/clike/clike.js'
			),
			'cdebug_codemirror_mode_php' => array(
				'type' => 'script',
				'file' => CDEBUG_URL_INCLUDE . 'codemirror/mode/php/php.js'
			),
			'cdebug_own_js' => array(
				'type' => 'script',
				'file' => CDEBUG_URL_INCLUDE . 'own/code_debugger.js'
			),
			'cdebug_own_css' => array(
				'type' => 'style',
				'file' => CDEBUG_URL_INCLUDE . 'own/code_debugger.css'
			),
		);

		foreach ($items as $key => $item) {

			$funcname = array( 
				'register' => 'wp_register_' . $item['type'],
				'enqueue' => 'wp_enqueue_' . $item['type']
				);

			$funcname['register']($key, $item['file'] );
			$funcname['enqueue']($key);
		}
	}
}

/**
 * Function code debugger
 * 
 * @param  [Any] $var    [varible to debug]
 * @param  string $title [optional title for the current debug]
 */
function cdebug( $var, $title = '' ) {

	include_once CDEBUG_PATH . 'includes/krumo/class.krumo.php';

	echo '<div class="cdebug-result">';
		if ( $title != '' ) {
			echo '<div class="cdebug-result-head">' . $title . '</div>';
		}

		krumo($var);
	echo  '</div>';
}

/**
 * Invoke plugin
 */
function GetCodeDebugger() {
    return CodeDebugger::instance();
}

GetCodeDebugger();
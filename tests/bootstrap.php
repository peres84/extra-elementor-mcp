<?php
/**
 * PHPUnit bootstrap for Extra Elementor MCP.
 *
 * Initialises WP_Mock so that WordPress functions and hooks are available
 * in the unit test suite without requiring a full WordPress installation.
 * Integration tests that extend WP_UnitTestCase require the WordPress test
 * library to be installed via bin/install-wp-tests.sh.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

// Ensure Composer autoloader is available.
$autoload = dirname( __DIR__ ) . '/vendor/autoload.php';
if ( ! file_exists( $autoload ) ) {
    die( 'Run `composer install` before executing the test suite.' . PHP_EOL );
}
require_once $autoload;

// Boot WP_Mock for the unit test suite.
WP_Mock::bootstrap();

// Define ABSPATH so plugin files that guard against direct access can load.
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

// Pre-define the plugin constants so that when extra-elementor-mcp.php is
// loaded below the define() calls there silently no-op instead of triggering
// "constant already defined" notices.  The constants must match the values
// that the real plugin would compute.
if ( ! defined( 'EXTRA_ELEMENTOR_MCP_VERSION' ) ) {
    define( 'EXTRA_ELEMENTOR_MCP_VERSION', '1.0.0' );
}
if ( ! defined( 'EXTRA_ELEMENTOR_MCP_DIR' ) ) {
    define( 'EXTRA_ELEMENTOR_MCP_DIR', dirname( __DIR__ ) . DIRECTORY_SEPARATOR );
}
if ( ! defined( 'EXTRA_ELEMENTOR_MCP_URL' ) ) {
    define( 'EXTRA_ELEMENTOR_MCP_URL', 'http://example.com/wp-content/plugins/extra-elementor-mcp/' );
}
if ( ! defined( 'EXTRA_ELEMENTOR_MCP_BASENAME' ) ) {
    define( 'EXTRA_ELEMENTOR_MCP_BASENAME', 'extra-elementor-mcp/extra-elementor-mcp.php' );
}

// Stub wp_register_ability() so unit tests can load the helper function from
// the main plugin file without a real WordPress Abilities API present.
if ( ! function_exists( 'wp_register_ability' ) ) {
    /**
     * Stub: WordPress Abilities API.
     *
     * @param string $name Ability name.
     * @param array  $args Ability arguments.
     * @return true
     */
    function wp_register_ability( string $name, array $args ): bool {
        return true;
    }
}

if ( ! function_exists( 'wp_register_ability_category' ) ) {
    /**
     * Stub: WordPress Abilities API — category registration.
     *
     * @param string $name Category name.
     * @param array  $args Category arguments.
     * @return true
     */
    function wp_register_ability_category( string $name, array $args ): bool {
        return true;
    }
}

// Provide a minimal WP_Error stub for unit tests that run without a real
// WordPress installation.  The real class is available in integration tests.
if ( ! class_exists( 'WP_Error' ) ) {
	/**
	 * Minimal WP_Error stub for unit testing.
	 */
	class WP_Error {
		/** @var string */
		private $code;
		/** @var string */
		private $message;
		/** @var mixed */
		private $data;

		/**
		 * Constructor.
		 *
		 * @param string $code    Error code.
		 * @param string $message Error message.
		 * @param mixed  $data    Optional additional data.
		 */
		public function __construct( string $code = '', string $message = '', $data = '' ) {
			$this->code    = $code;
			$this->message = $message;
			$this->data    = $data;
		}

		/**
		 * Returns the error code.
		 *
		 * @return string
		 */
		public function get_error_code(): string {
			return $this->code;
		}

		/**
		 * Returns the error message.
		 *
		 * @return string
		 */
		public function get_error_message(): string {
			return $this->message;
		}
	}
}

// Provide a minimal is_wp_error() stub.
if ( ! function_exists( 'is_wp_error' ) ) {
	/**
	 * Stub: checks whether a value is a WP_Error.
	 *
	 * @param mixed $thing Value to check.
	 * @return bool
	 */
	function is_wp_error( $thing ): bool {
		return $thing instanceof WP_Error;
	}
}

// Provide lightweight stubs for WordPress functions called at file-load time
// (i.e. during require_once, outside of any test method).  WP_Mock only stubs
// functions inside a test run; these stubs cover the bootstrap phase.

if ( ! function_exists( 'plugin_dir_path' ) ) {
	/**
	 * Stub: plugin_dir_path().
	 *
	 * @param string $file Absolute path to the plugin file.
	 * @return string
	 */
	function plugin_dir_path( string $file ): string {
		return trailingslashit( dirname( $file ) );
	}
}

if ( ! function_exists( 'trailingslashit' ) ) {
	/**
	 * Stub: trailingslashit().
	 *
	 * @param string $string Input string.
	 * @return string
	 */
	function trailingslashit( string $string ): string {
		return rtrim( $string, '/\\' ) . '/';
	}
}

if ( ! function_exists( 'plugin_dir_url' ) ) {
	/**
	 * Stub: plugin_dir_url().
	 *
	 * @param string $file Absolute path to the plugin file.
	 * @return string
	 */
	function plugin_dir_url( string $file ): string {
		return 'http://example.com/wp-content/plugins/' . basename( dirname( $file ) ) . '/';
	}
}

if ( ! function_exists( 'plugin_basename' ) ) {
	/**
	 * Stub: plugin_basename().
	 *
	 * @param string $file Absolute path to the plugin file.
	 * @return string
	 */
	function plugin_basename( string $file ): string {
		return basename( dirname( $file ) ) . '/' . basename( $file );
	}
}

if ( ! function_exists( 'add_action' ) ) {
	/**
	 * Stub: add_action().
	 *
	 * @param string   $hook_name  Action hook name.
	 * @param callable $callback   Callback.
	 * @param int      $priority   Priority.
	 * @param int      $accepted_args Number of accepted args.
	 * @return true
	 */
	function add_action( string $hook_name, $callback, int $priority = 10, int $accepted_args = 1 ): bool {
		return true;
	}
}

if ( ! function_exists( '__' ) ) {
	/**
	 * Stub: __() translation function.
	 *
	 * @param string $text   Text to translate.
	 * @param string $domain Text domain.
	 * @return string
	 */
	function __( string $text, string $domain = 'default' ): string {
		return $text;
	}
}

if ( ! function_exists( 'absint' ) ) {
	/**
	 * Stub: absint().
	 *
	 * @param mixed $maybeint Value to convert.
	 * @return int
	 */
	function absint( $maybeint ): int {
		return abs( (int) $maybeint );
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	/**
	 * Stub: sanitize_text_field().
	 *
	 * @param string $str Input string.
	 * @return string
	 */
	function sanitize_text_field( string $str ): string {
		return strip_tags( trim( $str ) );
	}
}

if ( ! function_exists( 'sanitize_title' ) ) {
	/**
	 * Stub: sanitize_title().
	 *
	 * @param string $title Input title.
	 * @param string $fallback Fallback title.
	 * @param string $context Context.
	 * @return string
	 */
	function sanitize_title( string $title, string $fallback = '', string $context = 'save' ): string {
		return strtolower( preg_replace( '/[^a-z0-9-]/', '-', strtolower( $title ) ) );
	}
}

if ( ! function_exists( 'sanitize_textarea_field' ) ) {
	/**
	 * Stub: sanitize_textarea_field().
	 *
	 * @param string $str Input string.
	 * @return string
	 */
	function sanitize_textarea_field( string $str ): string {
		return strip_tags( $str );
	}
}

if ( ! function_exists( 'wp_parse_args' ) ) {
	/**
	 * Stub: wp_parse_args().
	 *
	 * @param array|string $args     Arguments to parse.
	 * @param array        $defaults Default values.
	 * @return array
	 */
	function wp_parse_args( $args, array $defaults = array() ): array {
		if ( is_string( $args ) ) {
			parse_str( $args, $parsed );
			$args = $parsed;
		}
		return array_merge( $defaults, (array) $args );
	}
}

// Provide the two plugin-level helpers that ability classes depend on.
// We intentionally do NOT require_once the main plugin file here because
// that file calls define() unconditionally for the constants we already set
// above, which would generate "constant already defined" notices.

if ( ! function_exists( 'extra_elementor_mcp_sanitize_schema' ) ) {
	/**
	 * Recursively removes empty strings from enum arrays in a JSON Schema.
	 *
	 * @param array $schema A JSON Schema array.
	 * @return array
	 */
	function extra_elementor_mcp_sanitize_schema( array $schema ): array {
		if ( isset( $schema['enum'] ) && is_array( $schema['enum'] ) ) {
			$schema['enum'] = array_values(
				array_filter(
					$schema['enum'],
					function ( $value ) {
						return '' !== $value;
					}
				)
			);
			if ( empty( $schema['enum'] ) ) {
				unset( $schema['enum'] );
			}
		}

		if ( isset( $schema['properties'] ) && is_array( $schema['properties'] ) ) {
			if ( empty( $schema['properties'] ) ) {
				$schema['properties'] = new \stdClass();
			} else {
				foreach ( $schema['properties'] as $key => $prop ) {
					if ( is_array( $prop ) ) {
						$schema['properties'][ $key ] = extra_elementor_mcp_sanitize_schema( $prop );
					}
				}
			}
		}

		if ( isset( $schema['items'] ) && is_array( $schema['items'] ) ) {
			$schema['items'] = extra_elementor_mcp_sanitize_schema( $schema['items'] );
		}

		foreach ( array( 'allOf', 'oneOf', 'anyOf' ) as $keyword ) {
			if ( isset( $schema[ $keyword ] ) && is_array( $schema[ $keyword ] ) ) {
				foreach ( $schema[ $keyword ] as $i => $sub ) {
					if ( is_array( $sub ) ) {
						$schema[ $keyword ][ $i ] = extra_elementor_mcp_sanitize_schema( $sub );
					}
				}
			}
		}

		return $schema;
	}
}

if ( ! function_exists( 'extra_elementor_mcp_register_ability' ) ) {
	/**
	 * Wrapper around wp_register_ability() that sanitizes schemas.
	 *
	 * @param string $name The ability name.
	 * @param array  $args The ability arguments.
	 * @return mixed
	 */
	function extra_elementor_mcp_register_ability( string $name, array $args ) {
		if ( isset( $args['input_schema'] ) && is_array( $args['input_schema'] ) ) {
			$args['input_schema'] = extra_elementor_mcp_sanitize_schema( $args['input_schema'] );
		}
		if ( isset( $args['output_schema'] ) && is_array( $args['output_schema'] ) ) {
			$args['output_schema'] = extra_elementor_mcp_sanitize_schema( $args['output_schema'] );
		}
		return wp_register_ability( $name, $args );
	}
}

/**
 * Extra MCP Tools for Elementor — Admin Settings Scripts
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

(function () {
	'use strict';

	/**
	 * Populate a code block and its hidden copy source.
	 *
	 * @param {string} codeId The ID of the <code> element.
	 * @param {string} copyId The ID of the <textarea> copy source.
	 * @param {string} json   The string to display.
	 */
	function setConfigBlock( codeId, copyId, json ) {
		var codeEl = document.getElementById( codeId );
		var copyEl = document.getElementById( copyId );
		if ( codeEl ) {
			codeEl.textContent = json;
		}
		if ( copyEl ) {
			copyEl.value = json;
		}
	}

	/**
	 * Connection tab — Generate credentials and populate all config blocks.
	 */
	function initBase64Generator() {
		var generateBtn = document.getElementById( 'extra-mcp-generate-b64' );
		if ( ! generateBtn ) {
			return;
		}

		generateBtn.addEventListener( 'click', function () {
			var username = document.getElementById( 'extra-mcp-b64-username' );
			var appPassword = document.getElementById( 'extra-mcp-b64-app-password' );

			if ( ! username || ! appPassword || ! username.value.trim() || ! appPassword.value.trim() ) {
				alert( 'Please enter both username and application password.' );
				return;
			}

			var credentials = username.value.trim() + ':' + appPassword.value.trim();
			var base64 = btoa( credentials );
			var headerValue = 'Basic ' + base64;

			// Show the result row.
			var resultRow = document.getElementById( 'extra-mcp-b64-result-row' );
			var resultCode = document.getElementById( 'extra-mcp-b64-result' );
			var resultCopy = document.getElementById( 'extra-mcp-b64-result-copy' );

			if ( resultRow && resultCode && resultCopy ) {
				resultRow.style.display = '';
				resultCode.textContent = headerValue;
				resultCopy.value = headerValue;
			}

			if ( typeof extraMcpAdmin === 'undefined' || ! extraMcpAdmin.mcpEndpoint ) {
				return;
			}

			var endpoint = extraMcpAdmin.mcpEndpoint;

			// Show the HTTP config blocks container.
			var configsDiv = document.getElementById( 'extra-mcp-http-configs' );
			if ( configsDiv ) {
				configsDiv.style.display = '';
			}

			// Claude Code (.mcp.json)
			var claudeCodeConfig = {
				mcpServers: {
					'extra-elementor-mcp': {
						type: 'http',
						url: endpoint,
						headers: {
							Authorization: headerValue
						}
					}
				}
			};
			setConfigBlock(
				'extra-mcp-claude-code-http-code',
				'claude-code-http',
				JSON.stringify( claudeCodeConfig, null, 4 )
			);

			// Claude Desktop
			setConfigBlock(
				'extra-mcp-claude-desktop-http-code',
				'claude-desktop-http',
				JSON.stringify( claudeCodeConfig, null, 4 )
			);

			// Cursor — uses url field, no type.
			var cursorConfig = {
				mcpServers: {
					'extra-elementor-mcp': {
						url: endpoint,
						headers: {
							Authorization: headerValue
						}
					}
				}
			};
			setConfigBlock(
				'extra-mcp-cursor-code',
				'cursor-config',
				JSON.stringify( cursorConfig, null, 4 )
			);

			// Windsurf — uses serverUrl.
			var windsurfConfig = {
				mcpServers: {
					'extra-elementor-mcp': {
						serverUrl: endpoint,
						headers: {
							Authorization: headerValue
						}
					}
				}
			};
			setConfigBlock(
				'extra-mcp-windsurf-code',
				'windsurf-config',
				JSON.stringify( windsurfConfig, null, 4 )
			);

			// Codex — TOML format.
			var codexConfig = '[mcp_servers.extra-elementor-mcp]\n' +
				'url = "' + endpoint + '"\n\n' +
				'[mcp_servers.extra-elementor-mcp.http_headers]\n' +
				'"Authorization" = "' + headerValue + '"';
			setConfigBlock(
				'extra-mcp-codex-code',
				'codex-config',
				codexConfig
			);

			// npx mcp-remote
			var mcpRemoteConfig = {
				mcpServers: {
					'extra-elementor-mcp': {
						command: 'npx',
						args: [
							'-y',
							'mcp-remote',
							endpoint,
							'--header',
							'Authorization: ' + headerValue
						]
					}
				}
			};
			setConfigBlock(
				'extra-mcp-mcp-remote-code',
				'mcp-remote-config',
				JSON.stringify( mcpRemoteConfig, null, 4 )
			);
		} );
	}

	/**
	 * Copy text to clipboard with fallback for non-HTTPS contexts.
	 *
	 * @param {string} text The text to copy.
	 * @returns {Promise} Resolves when copied.
	 */
	function copyToClipboard( text ) {
		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			return navigator.clipboard.writeText( text );
		}

		return new Promise( function ( resolve ) {
			var textarea = document.createElement( 'textarea' );
			textarea.value = text;
			textarea.style.position = 'fixed';
			textarea.style.opacity = '0';
			document.body.appendChild( textarea );
			textarea.select();
			document.execCommand( 'copy' );
			document.body.removeChild( textarea );
			resolve();
		} );
	}

	/**
	 * Copy to clipboard buttons.
	 */
	function initCopyButtons() {
		document.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( '.extra-mcp-copy-btn' );
			if ( ! btn ) {
				return;
			}

			var targetId = btn.getAttribute( 'data-target' );
			var source = document.getElementById( targetId );
			if ( ! source ) {
				return;
			}

			var copiedText = ( typeof extraMcpAdmin !== 'undefined' && extraMcpAdmin.copied ) ? extraMcpAdmin.copied : 'Copied!';

			copyToClipboard( source.value ).then( function () {
				var original = btn.textContent;
				btn.textContent = copiedText;
				setTimeout( function () {
					btn.textContent = original;
				}, 2000 );
			} );
		} );
	}

	// Initialize on DOM ready.
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			initBase64Generator();
			initCopyButtons();
		} );
	} else {
		initBase64Generator();
		initCopyButtons();
	}
})();

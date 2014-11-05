<?php
	/*
	 * Plugin Name: Studiorum Print Me
	 * Description: A plugin which adds the ability to visit a permalink to view a print stylesheet for that post type (or a 'custom endpoint')
	 * Version:     0.1
	 * Plugin URI:  #
	 * Author:      UBC, CTLT, Richard Tape
	 * Author URI:  http://ubc.ca/
	 * Text Domain: studiorum-print-me
	 * License:     GPL v2 or later
	 * Domain Path: languages
	 *
	 * studiorum-print-me is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation, either version 2 of the License, or
	 * any later version.
	 *
	 * studiorum-print-me is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with studiorum-print-me. If not, see <http://www.gnu.org/licenses/>.
	 *
	 * @package studiorum-print-me
	 * @author Richard Tape
	 * @version 0.1.0
	 */

	if( !defined( 'ABSPATH' ) ){
		die( '-1' );
	}

	if( !defined( 'STUDIORUM_PRINT_PLUGIN_DIR' ) ){
		define( 'STUDIORUM_PRINT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	}

	// Plugin Folder URL
	if( !defined( 'STUDIORUM_PRINT_PLUGIN_URL' ) ){
		define( 'STUDIORUM_PRINT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}


	if( !class_exists( 'Studiorum_Print_Me' ) ) :

		class Studiorum_Print_Me
		{


			/**
			 * Actions and filters
			 *
			 * @author Richard Tape <@richardtape>
			 * @since 1.0
			 * @param null
			 * @return null
			 */
			
			public function __construct()
			{

				// Custom 'print' endpoint
				add_action( 'init', array( $this, 'ini__addPrintEndpoint' ) );

				// Add 'print' to the query vars
				add_filter( 'query_vars', array( $this, 'query_vars__addPrintQueryVar' ) );

				// Append to the 'this is one of your submissions' message above a user's submission
				add_filter( 'studiorum_lectio_author_note_above_submission', array( $this, 'studiorum_lectio_author_note_above_submission__addPrintMeMessage' ), 99 );

			}/* __construct() */


			/**
			 * Add the custom 'print' endpoint
			 *
			 * @author Richard Tape <@richardtape>
			 * @since 1.0
			 * @param null
			 * @return null
			 */
			
			public function ini__addPrintEndpoint()
			{

				add_rewrite_endpoint( 'print', EP_PERMALINK );

			}/* ini__addPrintEndpoint() */


			/**
			 * Add the print query var
			 *
			 * @author Richard Tape <@richardtape>
			 * @since 1.0
			 * @param (array) $query_vars - currently registered $query_vars
			 * @return (array) $query_vars - modified $query_vars
			 */
			
			public function query_vars__addPrintQueryVar( $query_vars )
			{

				$query_vars[] = 'print';

				return $query_vars;

			}/* query_vars__addPrintQueryVar() */
			
			
			/**
			 * Append a link to print this particular submission
			 *
			 * @author Richard Tape <@richardtape>
			 * @since 1.0
			 * @param (string) $message - the original message
			 * @return (string) $message - updated message with an additional link to print this page
			 */
			
			public function studiorum_lectio_author_note_above_submission__addPrintMeMessage( $message )
			{

				global $post_id;

				// Form the permalink - which is the current link with '/print' added to the end
				$additionalMessage = static::_generateLinkMessage( $post_id );

				return $message . ' ' . $additionalMessage;

			}/* studiorum_lectio_author_note_above_submission__addPrintMeMessage() */


			/**
			 * A utility method to help generate a message with a link to print the submission
			 *
			 * @author Richard Tape <@richardtape>
			 * @since 1.0
			 * @param (int) $postID - the ID for which to generate the print me link
			 * @return (string) $message - A message with a link to print the submission
			 */
			
			public static function _generateLinkMessage( $postID = false )
			{

				if( !$postID ){
					$postID = get_the_ID();
				}

				$permalink = get_permalink( $postID );

				$messageLink = untrailingslashit( $permalink ) . '/print';

				$messageText = __( 'Print this submission', 'studiorum-print-me' );
				$message = '<a class="studiorum-print-me-link" href="' . $messageLink . '" title="">' . $messageText . '</a>';

				return $message;

			}/* _generateLinkMessage() */
			

		}/* class Studiorum_Print_Me */

	endif;

	// Register us
	add_action( 'studiorum_after_includes', 'studiorum_after_includes__loadPrintEndpoint' );

	function studiorum_after_includes__loadPrintEndpoint()
	{

		$Studiorum_Print_Me = new Studiorum_Print_Me;

	}/* studiorum_after_includes__loadPrintEndpoint() */
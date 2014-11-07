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

				// Register ourself as an addon
				add_filter( 'studiorum_modules', array( $this, 'studiorum_modules__registerAsModule' ) );

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
			 * Register ourself as a studiorum addon, so it's available in the main studiorum page
			 *
			 * @since 0.1
			 *
			 * @param array $modules Currently registered modules
			 * @return array $modules modified list of modules
			 */

			public function studiorum_modules__registerAsModule( $modules )
			{

				if( !$modules || !is_array( $modules ) ){
					$modules = array();
				}

				$modules['studiorum-print-me'] = array(
					'id' 				=> 'studiorum_print_me',
					'plugin_slug'		=> 'studiorum-print-me',
					'title' 			=> __( 'Submissions Print Stylesheet', 'studiorum' ),
					'requires'			=> 'lectio',
					'icon' 				=> 'desktop', // dashicons-#
					'excerpt' 			=> __( 'Add a print stylesheet and /print/ url for lectio submissions. Includes side comments.', 'studiorum' ),
					'image' 			=> 'http://dummyimage.com/310/162',
					'link' 				=> 'http://code.ubc.ca/studiorum/studiorum-print-me',
					'content' 			=> __( '<p>Combining Lectio and Side Comments, you have a powerful tool from which your students can submit and self/pier-assess. But, if your student prefers to print out and have a hard copy of their work and the associated comments, due to the nature of the inline comments, this isn\'t easy.</p><p>This module enables your students to simply append /print/ to the url of their submissions to see a beautiful, clean, easy-to-read page with their submission with all the inline comments directly below the paragraph to which they are related as well as having the linear comments at the bottom.</p>', 'studiorum' ),
					'content_sidebar' 	=> 'http://dummyimage.com/300x150',
					'date'				=> '2014-09-01'
				);

				return $modules;

			}/* studiorum_modules__registerAsModule() */


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
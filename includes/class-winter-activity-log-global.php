<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
/**
 * Global Shared Class for the plugin
 *
 * @package    Winter_Activity_Log
 * @subpackage Winter_Activity_Log/includes
 * @author     Sandi Winter
 */
if ( ! class_exists( 'Winter_Activity_Log_Global' ) ) {

	class Winter_Activity_Log_Global {


		protected static $template_loader;


		public static function template_loader() {

			if ( empty( self::$template_loader ) ) {
				self::set_template_loader();
			}


			return self::$template_loader;

		}


		public static function set_template_loader() {

			//require_once ROCKET_BOOKS_BASE_DIR . 'public/class-rocket-books-template-loader.php';

			//self::$template_loader = new Winter_Activity_Log_Template_Loader();

		}


	}
}
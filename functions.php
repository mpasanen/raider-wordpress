<?php
/**
 * Raider Production theme bootstrap.
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

define( 'RAIDER_VERSION', '1.1.0' );
define( 'RAIDER_DIR', get_template_directory() );
define( 'RAIDER_URI', get_template_directory_uri() );

require_once RAIDER_DIR . '/inc/setup.php';
require_once RAIDER_DIR . '/inc/cleanup.php';
require_once RAIDER_DIR . '/inc/enqueue.php';
require_once RAIDER_DIR . '/inc/template-tags.php';
require_once RAIDER_DIR . '/inc/pages.php';
require_once RAIDER_DIR . '/inc/contact-form.php';
require_once RAIDER_DIR . '/inc/page-fields.php';
require_once RAIDER_DIR . '/inc/client-logos.php';
require_once RAIDER_DIR . '/inc/pricing-cards.php';
require_once RAIDER_DIR . '/inc/analytics.php';

<?php
/*
HFA-SPXP is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
HFA-SPXP is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with HFA-SPXP. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

/**
 * Plugin Name:       HFA SPXP Support
 * Plugin URI:        https://heyfolks.app/wordpress-hfa-spxp
 * Description:       Exposes your blog via the Social Profile Exchange
 *                    Protocol so your friends can follow your updates
 *                    using any SPXP client of their choice, like the
 *                    HeyFolks app.
 * Version:           1.0
 * Requires at least: 4.7
 * Requires PHP:      7.2
 * Author:            HeyFolks.app
 * Author URI:        https://heyfolks.app/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

include dirname( __FILE__ ) . '/hfa-spxp-class.php';
$hfaspxp_plugin = new HeyFolksApp_SPXP_Plugin();
add_action( 'plugins_loaded', [ $hfaspxp_plugin, 'add_hooks' ] );
register_activation_hook(  __FILE__, [ $hfaspxp_plugin, 'activation' ] );
register_deactivation_hook(  __FILE__, [ $hfaspxp_plugin, 'deactivation' ] );

if ( is_admin() ) {
    include dirname( __FILE__ ) . '/hfa-spxp-settings-class.php';
    add_action( 'plugins_loaded', [ new HeyFolksApp_SPXP_Settings(), 'add_hooks' ] );
}

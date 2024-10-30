<?php

/**
 * Idea Publisher
 *
 * @package   IdeaPublisher
 * @author    CodingNagger
 * @copyright 2022 CodingNagger
 * @license   GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Idea Publisher
 * Plugin Url: http://wordpress.org/plugins/idea-publisher
 * Description: This plugin allows you to share posts to Minds when they get published
 * Version: 1.0.9
 * Author: CodingNagger
 * Author URI: https://www.codingnagger.com
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

/*
Idea Publisher is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation version 2 of the License.

Idea Publisher is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Idea Publisher. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

require_once 'bootstrap.php';

\IdeaPublisher\WordPress\WordPressAdminFacade::createWithPlatforms(
    new \IdeaPublisher\Social\Platforms\Minds\MindsFacade()
);

function ideapublisher_sidebar_plugin_register()
{
    wp_register_script(
        'ideapublisher-sidebar-js',
        plugins_url('block/ideapublisher-sidebar.js', __FILE__),
        array(
        'wp-plugins',
        'wp-edit-post',
        'wp-element',
        'wp-components',
        ),
        filemtime(plugin_dir_path(__FILE__) . '/block/ideapublisher-sidebar.js'),
        true,
    );
}
add_action('init', 'ideapublisher_sidebar_plugin_register');

function ideapublisher_sidebar_plugin_script_enqueue()
{
    wp_enqueue_script('ideapublisher-sidebar-js');
}
add_action('enqueue_block_editor_assets', 'ideapublisher_sidebar_plugin_script_enqueue');

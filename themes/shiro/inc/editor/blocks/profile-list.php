<?php
/**
 * Server-side registration for the shiro/profile block.
 */

namespace WMF\Editor\Blocks\ProfileList;

use \WMF\Editor\Blocks\Profile;

const BLOCK_NAME = 'shiro/profile-list';

/**
 * Bootstrap this block functionality.
 */
function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\\register_block' );
}

/**
 * Register the block here.
 */
function register_block() {
	register_block_type(
		BLOCK_NAME,
		[
			'apiVersion'      => 2,
			'render_callback' => __NAMESPACE__ . '\\render_block',
			'attributes'      => [
				'profile_ids' => [
					'type' => 'array',
				],
			],
		]
	);
}

/**
 * Render this block, given its attributes.
 *
 * @param [] $attributes Block attributes.
 *
 * @return string HTML markup.
 */
function render_block( $attributes ) {
	$profile_ids = count($attributes['profile_ids'] ?? []) > 0 ? $attributes['profile_ids'] : false;

	if (!$profile_ids) {
		return '';
	}

	ob_start();

	echo '<div class="profile-list">';

	foreach ($profile_ids as $id) {
		echo Profile\render_block(['profile_id' => $id]);
	}

	echo '</div>';

	return ob_get_clean();
}

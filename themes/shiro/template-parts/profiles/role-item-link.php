<?php
/**
 * Adds a link inside of a list item
 *
 * @package shiro
 */

$post_data = $args;

if ( empty( $post_data ) ) {
	return;
}

$term_id = ! empty( $post_data['term_id'] ) ? $post_data['term_id'] : 0;
$name    = ! empty( $post_data['name'] ) ? $post_data['name'] : '';

if ( empty( $name ) ) {
	return;
}
?>
<li class="toc__item">
	<a class="toc__link niceScroll" href="#section-<?php echo absint( $term_id ); ?>">
		<span>
			<?php echo esc_html( $name ); ?>
		</span>
	</a>
</li>

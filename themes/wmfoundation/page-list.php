<?php
/**
 * Template Name: List Page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package wmfoundation
 */

get_header();
while ( have_posts() ) :
	the_post();

	// Page Header.
	$parent_page   = wp_get_post_parent_id( get_the_ID() );
	$template_args = array(
		'h4_link'  => ! empty( $parent_page ) ? get_the_permalink( $parent_page ) : '',
		'h4_title' => ! empty( $parent_page ) ? get_the_title( $parent_page ) : '',
		'h2_title' => get_the_title(),
		'h1_title' => get_post_meta( get_the_ID(), 'sub_title', true ),
	);

	if ( has_post_thumbnail() ) {
		$template_args['image'] = get_the_post_thumbnail_url( get_the_ID(), 'large' );
		wmf_get_template_part( 'template-parts/header/page-image', $template_args );
	} else {
		wmf_get_template_part( 'template-parts/header/page-noimage', $template_args );
	}
?>
<div class="mw-1360 mod-margin-bottom flex flex-medium">
	<div class="w-68p">
		<?php if ( get_the_content() ) : ?>
		<div class="mod-margin-bottom wysiwyg h3 color-gray">
			<?php the_content(); ?>
		</div>
		<?php endif; ?>

		<?php get_template_part( 'template-parts/page/page', 'list' ); ?>
	</div>

	<div class="module-mu wysiwyg w-32p">
		<?php get_sidebar( 'list' ); ?>
	</div>
</div>
<?php

$modules = array(
	'cta',
	'related',
	'support',
	'connect',
);

foreach ( $modules as $module ) {
	get_template_part( 'template-parts/page/page', $module );
}
endwhile;

get_footer();

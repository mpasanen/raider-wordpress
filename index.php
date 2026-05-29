<?php
/**
 * Fallback template for any query that doesn't have a more specific template.
 *
 * @package Raider
 */

get_header();
?>
<section class="section">
	<div class="container">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class(); ?>>
					<h1><?php the_title(); ?></h1>
					<div class="prose"><?php the_content(); ?></div>
				</article>
				<?php
			endwhile;
		else :
			?>
			<h1><?php esc_html_e( 'Ei sisältöä', 'raider' ); ?></h1>
			<?php
		endif;
		?>
	</div>
</section>
<?php
get_footer();

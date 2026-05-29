<?php
/**
 * Generic page fallback (used for any page slug other than the four specialized ones).
 *
 * @package Raider
 */

get_header();
?>
<section class="section">
	<div class="container">
		<?php while ( have_posts() ) : the_post(); ?>
			<h1><?php the_title(); ?></h1>
			<div class="prose"><?php the_content(); ?></div>
		<?php endwhile; ?>
	</div>
</section>
<?php
get_footer();

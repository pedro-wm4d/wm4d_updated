<?php
/**
 * Template Name: Front Page Template
 *
 * Description: A page template that provides a key component of WordPress as a CMS
 * by meeting the need for a carefully crafted introductory page. The front page template
 * in Twenty Twelve consists of a page content area for adding text, images, video --
 * anything you'd like -- followed by front-page-only widgets in one or two columns.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>
	<div id="primary" class="site-content">
		<div id="content" class="giz_storeLocator show-sidebar" role="main">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="post-entry">
						<?php
							global $post;
							$page_data = get_page( $post->ID );
							$theOrginallContent = do_shortcode($page_data->post_content);
							echo str_replace(array("&#038;","&amp;"), "&", $theOrginallContent);
						?>
						</div><!-- end of .post-entry -->
					</div><!-- end of #post-<?php the_ID(); ?> -->
				<?php endwhile;
			endif; ?>

		</div><!-- #content -->
	</div><!-- #primary -->
<?php get_footer(); ?>
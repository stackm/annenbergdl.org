<?php
/*
 * Template Name: Staff Page
 */

/**
 * The template for displaying the staff page.
 *
 * @package Digital Lounge
 */

wp_enqueue_script( 'digitallounge-staff', get_template_directory_uri() . '/js/staff.js', array( 'jquery', 'wp-util' ), '20150511', true );

get_header(); ?>

	<div id="primary" class="content-area staff-page paper-front">
		<main id="main" class="site-main" role="main">
			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'template-parts/content', 'page' ); ?>

			<?php endwhile; // end of the loop.

			$contributor_ids = get_users( array(
				'fields'  => 'ID',
				'who'     => '',
			) );
			$active_contributor_ids = array();
			foreach ( $contributor_ids as $contributor_id ) {
				// Skip former staff members.
				if ( ! get_the_author_meta( 'active_staff', $contributor_id ) ) {
					continue;
				}

				$active_contributor_ids[] = $contributor_id;
			}

			$types = array(
				'faculty' => 'Faculty',
				'staff' => 'Staff',
				'trainer' => 'Trainers',
				'student' => 'Crew',
			);
			foreach ( $types as $type => $label ) {
				// extra loop to check for any... could probably be improved...
				$count = false;
				foreach ( $active_contributor_ids as $contributor_id ) {
					if ( $type === get_the_author_meta( 'staff_type', $contributor_id ) ) {
						$count = true;
						break;
					}
				}
				if ( ! $count ) {
					continue;
				}
				echo '<h2>' . $label . '</h2><div class="staff-collection">';
				foreach ( $active_contributor_ids as $contributor_id ) {
					if ( $type === get_the_author_meta( 'staff_type', $contributor_id ) ) {
					?>
						<div class="staff-summary" tabindex="0" data-staff-id="<?php echo $contributor_id; ?>">
							<div class="staff-avatar"><?php echo get_avatar( $contributor_id, 384 ); ?></div>
							<div class="staff-name"><?php the_author_meta( 'display_name', $contributor_id ); ?></div>
						</div>
					<?php
					}
				}
				echo '</div>';
			}
			?>
			
			<div class="staff-members">
				<button type="button" class="back-to-index">X</button>
			</div>
			<script type="text/html" id="tmpl-single-staff-view">
				<article class="staff-member" id="staff-member-{{ data.id }}">
					<div class="staff-background" style="background-image: url('{{ data.background_image }}');"></div>
					{{{ data.avatar }}}
					<div class="staff-content">
						<button type="button" class="previous"><</button>
						<h2 class="staff-name">{{ data.name }}</h2>
						<h5 class="staff-info">{{{ data.info }}}</h5>
						<aside class="staff-recent-content">
							<# if ( data.hasPosts ) { #>
								<h4>Recent News</h4>
								<# for ( var i = 0; i < data.posts.length; i++ ) { #> 
									<h5><a href="{{ data.posts[i].url }}">{{ data.posts[i].title }}</a></h5>
									<p>{{ data.posts[i].date }}</p>
								<# } #>
							<# } if ( data.hasTutorials ) { #>
								<h4>Latest Tutorials</h4>
								<# for ( var i = 0; i < data.tutorials.length; i++ ) { #> 
									<h5><a href="{{ data.tutorials[i].url }}">{{ data.tutorials[i].title }}</a></h5>
									<p>{{{ data.tutorials[i].tags }}}</p>
								<# } #>
							<# } #>
						</aside>
						<div class="staff-bio">
							{{{ data.description }}}
						</div>
						<button type="button" class="next">></button>
					</div>
				</article>
			</script>
			<script type="text/javascript">
				var _anndlAllStaff = <?php echo wp_json_encode( $active_contributor_ids ); ?>;
			</script>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>

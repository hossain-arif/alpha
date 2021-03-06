<?php get_header();

$alpha_layout_class = 'col-md-8';
$alpha_text_class   = '';

if (!is_active_sidebar("sidebar-1")) {
	$alpha_layout_class = 'col-md-10 offset-md-1';
	$alpha_text_class   = 'text-center';
}

?>
<body <?php body_class(array("newClass")); ?>>
<?php get_template_part("/template-parts/common/hero"); ?>
    <div class="container">
        <div class="row">
            <div class="<?php echo $alpha_layout_class; ?>">
                <div class="posts">
					<?php
					while (have_posts()) :
					the_post();
					?>
                    <div <?php post_class(); ?>>
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <h2 class="post-title <?php echo $alpha_text_class; ?>">
										<?php the_title(); ?>
                                    </h2>
                                    <p class="<?php echo $alpha_text_class; ?>">
                                        <em><?php the_author_posts_link(); ?></em><br/>
										<?php echo get_the_date(); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="slider">
										<?php
										if (class_exists('Attachments')) {
											$attachments = new Attachments('slider');
											if ($attachments->exist()) {
												while ($attachment = $attachments->get()) {
													?>
                                                    <div>
														<?php echo $attachments->image("large"); ?>
                                                    </div>
												<?php }
											}
										}
										?>
                                    </div>
                                    <p>
										<?php
										if (class_exists('Attachments')) {
											if (has_post_thumbnail()) {
//											    $thumbnail_url = get_the_post_thumbnail_url(null,"large");
//											    printf("<a href='#' data-featherlight='%s'>",$thumbnail_url);
												echo "<a class='popup' href='#' data-featherlight='#'>";
												the_post_thumbnail("large", array("class" => "img-fluid"));
												echo '</a>';
											}
										}

										the_content();

										if (get_post_format() == 'image' && function_exists("the_field")):

										$alpha_camera_model = get_field('camera_model');
										$alpha_location     = get_field('location');
										$alpha_date         = get_field('date');


										?>
                                        <div class="metainfo">
                                            <strong>Camera model:</strong><br>
											<?php echo esc_html($alpha_camera_model); ?>
                                            <br>
                                            <strong>Location:</strong><br>
											<?php echo esc_html($alpha_location); ?>
                                            <br>
                                            <strong>Date:</strong><br>
											<?php echo esc_html($alpha_date); ?>
                                            <br>

                                    <p>
										<?php $file = get_field("attachment");
										if ($file) {
											$file_url   = wp_get_attachment_url($file);
											$file_thumb = get_field("thumbnail", $file);
											if ($file_thumb) {
												$file_thumb_details = wp_get_attachment_image_src($file_thumb);
												echo "<a target='_blank' href='{$file_url}'><img src='" . esc_url($file_thumb_details[0]) . "' alt=''></a>";
											} else {
												echo "<a target='_blank' href='{$file_url}'>{$file_url}</a>";
											}
										}
										?>
                                    </p>
                                </div>

								<?php
								endif;
								?>

								<?php if (get_post_format() == 'image' && class_exists('CMB2')):
									$alpha_model = get_post_meta(get_the_ID(), "_alpha_camera_model", true);
									$alpha_location = get_post_meta(get_the_ID(), "_alpha_location", true);
									$alpha_date = get_post_meta(get_the_ID(), "_alpha_date", true);
									$alpha_licensed = get_post_meta(get_the_ID(), "_alpha_licensed", true);
									$alpha_license_information = get_post_meta(get_the_ID(), "_alpha_license_information", true);

									?>

                                    <div class="metainfo">
                                        <p>Camera: <?php echo esc_html($alpha_model) ?></p>
                                        <p>Location: <?php echo esc_html($alpha_location) ?></p>
                                        <p>Date: <?php echo esc_html($alpha_date) ?></p>
										<?php if ($alpha_licensed): ?>
                                            <p>
                                                Info: <?php echo apply_filters("the_content", $alpha_license_information) ?></p>
										<?php endif; ?>
                                        <?php
                                        $alpha_image = get_post_meta(get_the_ID(),"_alpha_image_id",true);
                                        $alpha_image_details = wp_get_attachment_image_src($alpha_image,"medium");
										echo "<img src='" . esc_url($alpha_image_details[0]) . "' alt=''>";
										$alpha_resume = get_post_meta(get_the_ID(),"_alpha_resume",true);
										echo $alpha_resume;
										?>
                                    </div>


								<?php endif; ?>

								<?php
								wp_link_pages();

								next_post_link();
								echo "<br/>";
								previous_post_link();

								?>
                                </p>
                            </div>
                            <div class="authorsection">
                                <div class="row">
                                    <div class="col-md-2">
										<?php echo get_avatar(get_the_author_meta("id")); ?>
                                    </div>
                                    <div class="col-md-10">
                                        <h2><?php echo get_the_author_meta("display_name"); ?></h2>
                                        <p>
											<?php echo get_the_author_meta("description"); ?>
                                        </p>
										<?php if (function_exists("the_field")): ?>
                                            <p>
                                                Facebook: <?php the_field("facebook", "user_" . get_the_author_meta("id")) ?>
                                            </p>
                                            <p>
                                                Twitter: <?php the_field("twitter", "user_" . get_the_author_meta("id")) ?>
                                            </p>

										<?php endif; ?>

										<?php if (function_exists("the_field")): ?>
                                            <div>
                                                <h1><?php _e("Related posts", "alpha") ?></h1>
												<?php
												$related_posts = get_field("related_posts");
												$alpha_rp      = new WP_Query(array(
													'post__in' => $related_posts,
													'orderby' => 'post__in',
												));

												while ($alpha_rp->have_posts()) {
													$alpha_rp->the_post();
													?>
                                                    <h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a>
                                                    </h3>
												<?php }
												wp_reset_query(); ?>

                                            </div>
										<?php endif; ?>
                                    </div>
                                </div>
                            </div>
							<?php if (!post_password_required()): ?>
                                <div class="col-md-12">
									<?php
									comments_template();
									?>
                                </div>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
				<?php
				endwhile;
				?>

                <div class="container post-pagination">
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-8">
							<?php
							the_posts_pagination(array(
								"screen_reader_text" => ' ',
								"prev_text" => "New Posts",
								"next_text" => "Old Posts"
							));
							?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php if (is_active_sidebar("sidebar-1")): ?>
            <div class="col-md-4">
				<?php
				if (is_active_sidebar("sidebar-1")) {
					dynamic_sidebar("sidebar-1");
				}
				?>
            </div>
		<?php endif; ?>
    </div>
    </div>
<?php get_footer(); ?>
<?php
/**
 * The template for displaying all single posts.
 *
 * @package understrap
 */

get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<header class="header-image" style="
<?php if (has_post_thumbnail()){?> background-image: url('<?php echo the_post_thumbnail_url(); ?>'); <?php } else {} ?> 
background-position-y: top;
">
    <div class="headline">
        <div class="container">
            <?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>
        </div>
    </div>
</header>

<div class="wrapper" id="single-wrapper">
    
    <div  id="content" class="container">

        <div class="row">
        
            <div id="primary" class="col-md-10 col-md-offset-1 content-area">
                
                <main id="main" class="site-main" role="main">

                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                             
                            <h2>By <?php $tags = wp_get_post_tags($post->ID); echo $tags[0]->name; ?></h2>
                            <div class="entry-content">

                                <?php echo do_shortcode('[gocha_video url="'.get_field('video_link').'"]'); ?> 
                                <p class="bg-info"><i>To add a comment click on the start time to set a comment start time, and the end to set an end time.</i></p> 

                            </div><!-- .entry-content -->

                            <footer class="entry-footer">
                                <h4>In categories: 
                                <?php $cats = wp_get_post_categories($post->ID); 
                                    foreach($cats as $c){
                                        echo '<a class="btn btn-default" href="'.get_category_link($c).'">';
                                        echo get_category($c)->name;
                                        echo '</a>';
                                    };
                                 ?>
                                </h4>
                            </footer><!-- .entry-footer -->

                        </article><!-- #post-## -->


                        <?php
                        // If comments are open or we have at least one comment, load up the comment template
                        // if ( comments_open() || get_comments_number() ) :
                        //     comments_template();
                        // endif;
                        ?>
                        
                    <?php endwhile; // end of the loop. ?>

                </main><!-- #main -->
                
            </div><!-- #primary -->
        

        </div><!-- .row -->
        
    </div><!-- Container end -->
    
</div><!-- Wrapper end -->

<?php get_footer(); ?>

<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package understrap
 */

get_header(); ?>

<div class="wrapper" id="page-wrapper">
    
    <div  id="content" class="container">

        <div class="row">
        
    	   <div id="primary" class="col-md-12 content-area">
           
                 <main id="main" class="site-main" role="main">

                    <?php while ( have_posts() ) : the_post(); ?>

                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>


                                <?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>


                             <?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?> 
                            
                            <div class="entry-content">

                                <?php the_content(); ?>

                                <?php
                                    wp_link_pages( array(
                                        'before' => '<div class="page-links">' . __( 'Pages:', 'understrap' ),
                                        'after'  => '</div>',
                                    ) );
                                ?>

                            </div><!-- .entry-content -->

                            <footer class="entry-footer">

                                <?php edit_post_link( __( 'Edit', 'understrap' ), '<span class="edit-link">', '</span>' ); ?>

                            </footer><!-- .entry-footer -->

                        </article><!-- #post-## -->


                        <?php
                            // If comments are open or we have at least one comment, load up the comment template
                            if ( comments_open() || get_comments_number() ) :
                                comments_template();
                            endif;
                        ?>

                    <?php endwhile; // end of the loop. ?>

                </main><!-- #main -->
               
    	    </div><!-- #primary -->
            
        </div><!-- .row -->
        
    </div><!-- Container end -->
    
</div><!-- Wrapper end -->

<?php get_footer(); ?>

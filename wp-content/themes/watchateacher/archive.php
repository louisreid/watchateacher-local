<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package understrap
 */

get_header(); ?>

<div class="wrapper" id="archive-wrapper">
    
       <section class="jumbotron text-center">
          <div class="container">
            <h1 class="jumbotron-heading">
              <?php if ( have_posts() ) : ?>

                    <?php
                        the_archive_title( '<h1 class="page-title">', '</h1>' );
                        the_archive_description( '<div class="taxonomy-description">', '</div>' );
                    ?>
            </h1>
          </div>
        </section>




    <div id="content" class="container">

        <div class="row">
        

                        <?php /* Start the Loop */ ?>
                        <?php while ( have_posts() ) : the_post(); ?>
                            <div class="col-md-4">

                                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        
            
                                    <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

                                    

                                   <?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?> 

                                    <div class="entry-content">


                                    </div><!-- .entry-content -->


                                </article><!-- #post-## -->
                            </div>
                        <?php endwhile; ?>

                             <?php the_posts_navigation(); ?>

                        <?php else : ?>

                            <?php get_template_part( 'loop-templates/content', 'none' ); ?>

                        <?php endif; ?>



    </div> <!-- .row -->
        
    </div><!-- Container end -->
    
</div><!-- Wrapper end -->

<?php get_footer(); ?>

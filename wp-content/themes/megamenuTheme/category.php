<?php get_header(); ?>

<main class="category-posts">
    <div class="container">
        <h1><?php single_cat_title(); ?></h1>
        
        <?php if (have_posts()) : ?>
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="post-item">
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <div class="post-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <div class="pagination">
                <?php the_posts_pagination(); ?>
            </div>
        <?php else : ?>
            <p>No posts found in this category.</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <p class="copyright">
                <?php
                    printf(
                        esc_html__('© %1$s %2$s. All rights reserved.', 'your-theme'),
                        date('Y'),
                        get_bloginfo('name')
                    );
                ?>
            </p>
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const mobileMenu = document.getElementById('mobile-menu');
    const servicesMenu = document.getElementById('services-menu');
    
    mobileMenu.addEventListener('click', function() {
        servicesMenu.classList.toggle('active');
    });
});

</script>
<?php wp_footer(); ?>
</body>
</html>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header>
        
    <nav class="services-menu">
        <?php wp_nav_menu([
            'theme_location' => 'services-menu',
            'menu_class'     => 'services-nav',
            'container'      => false,
            'depth'          => 3,
            'walker'         => new Mega_Menu_Walker()
        ]); ?>
    </nav>
    </header>
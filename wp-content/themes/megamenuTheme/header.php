<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header>
        <div class="menu-toggle" id="mobile-menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
        <nav class="services-menu" id="services-menu">
            <?php wp_nav_menu([
                'theme_location' => 'services-menu',
                'menu_class'     => 'services-nav',
                'container'      => false,
                'depth'          => 3,
                'walker'         => new Mega_Menu_Walker()
            ]); ?>
        </nav>
    </header>
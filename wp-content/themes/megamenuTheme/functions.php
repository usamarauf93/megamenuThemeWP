<?php
// Register theme features
function theme_setup() {
    // Essential supports
    add_theme_support('menus');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    
    // Register menu location
    register_nav_menus([
        'services-menu' => __('Services Menu', 'megamenuTheme')
    ]);
}
add_action('after_setup_theme', 'theme_setup');

// Auto-create/update menu with categories
function create_default_menu() {
    $menu_name = 'Services Menu';
    $menu = wp_get_nav_menu_object($menu_name);
    
    // Create menu if it doesn't exist
    if (!$menu) {
        $menu_id = wp_create_nav_menu($menu_name);
    } else {
        $menu_id = $menu->term_id;
    }

    // Get existing menu items
    $existing_items = wp_get_nav_menu_items($menu_id);
    
    // Find or create the main "Services" parent item
    $services_parent_id = null;
    foreach ($existing_items as $item) {
        if ($item->title === 'Services') {
            $services_parent_id = $item->ID;
            break;
        }
    }
    
    if (!$services_parent_id) {
        $services_parent_id = wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title' => 'Services',
            'menu-item-url' => '#',
            'menu-item-status' => 'publish',
            'menu-item-type' => 'custom',
        ]);
    }

    // Get categories (excluding uncategorized)
    $uncategorized = get_term_by('slug', 'uncategorized', 'category');
    $parent_categories = get_terms([
        'taxonomy' => 'category',
        'parent' => 0,
        'hide_empty' => false,
        'exclude' => $uncategorized ? [$uncategorized->term_id] : []
    ]);

    // Process parent categories
    foreach ($parent_categories as $parent_category) {
        $parent_exists = false;
        $parent_item_id = null;

        // Check if parent category already exists in menu
        foreach ($existing_items as $item) {
            if ($item->object_id == $parent_category->term_id && 
                $item->menu_item_parent == $services_parent_id) {
                $parent_exists = true;
                $parent_item_id = $item->ID;
                break;
            }
        }

        // Add new parent category if not exists
        if (!$parent_exists) {
            $parent_item_id = wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title' => $parent_category->name,
                'menu-item-url' => '#',
                'menu-item-status' => 'publish',
                'menu-item-type' => 'taxonomy',
                'menu-item-object' => 'category',
                'menu-item-object-id' => $parent_category->term_id,
                'menu-item-parent-id' => $services_parent_id,
            ]);
        }

        // Process child categories
        $child_categories = get_terms([
            'taxonomy' => 'category',
            'parent' => $parent_category->term_id,
            'hide_empty' => false,
        ]);

        foreach ($child_categories as $child_category) {
            $child_exists = false;
            
            // Check if child exists
            foreach ($existing_items as $item) {
                if ($item->object_id == $child_category->term_id && 
                    $item->menu_item_parent == $parent_item_id) {
                    $child_exists = true;
                    break;
                }
            }
            
            // Add new child category
            if (!$child_exists) {
                wp_update_nav_menu_item($menu_id, 0, [
                    'menu-item-title' => $child_category->name,
                    'menu-item-url' => get_term_link($child_category),
                    'menu-item-status' => 'publish',
                    'menu-item-type' => 'taxonomy',
                    'menu-item-parent-id' => $parent_item_id,
                    'menu-item-object' => 'category',
                    'menu-item-object-id' => $child_category->term_id,
                ]);
            }
        }
    }

    // Set theme location
    $locations = get_theme_mod('nav_menu_locations', []);
    $locations['services-menu'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);
}

// Hook into category creation and theme setup
add_action('after_setup_theme', 'create_default_menu');
add_action('created_category', 'create_default_menu');
add_action('edited_category', 'create_default_menu');

class Mega_Menu_Walker extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = null) {
        if ($depth == 0) {
            $output .= '<ul class="sub-menu mega-menu">';
        } else {
            $output .= '<ul class="sub-sub-menu">';
        }
    }
    
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
        
        if ($depth == 1) {
            $output .= '<li class="mega-menu-column">';
        } else {
            $output .= '<li class="' . esc_attr($class_names) . '">';
        }
        
        // Menu item attributes
        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        
        // Menu item output
        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

function enqueue_theme_styles() {
    // Enqueue main stylesheet
    wp_enqueue_style(
        'theme-style',                       // Handle
        get_stylesheet_uri(),                // Stylesheet URL
        array(),                             // Dependencies
        filemtime(get_stylesheet_directory() . '/style.css'), // Version number (file modification time)
        'all'                                // Media
    );
    
    // Optional: Enqueue additional stylesheets
    /*
    wp_enqueue_style(
        'theme-mobile',
        get_template_directory_uri() . '/mobile.css',
        array('theme-style'),
        filemtime(get_stylesheet_directory() . '/mobile.css'),
        'screen and (max-width: 768px)'
    );
    */
}
add_action('wp_enqueue_scripts', 'enqueue_theme_styles');
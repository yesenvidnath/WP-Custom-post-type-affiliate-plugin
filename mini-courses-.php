<?php
/*
Plugin Name: Affiliate
Plugin URI: www.webstronomy.com/plugins
Description: This is for Affiliate Links for the Website
Version: 1.0.0
Author: Webstronomy (Pvt) Ltd
Author URI: www.webstronomy.com
Text Domain: Mc
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Enqueue styles and scripts
function affiliate_enqueue_scripts() {
    wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css', array(), '4.5.2');
    wp_enqueue_style('affiliate-css', plugin_dir_url(__FILE__) . 'assets/css/affiliate.css', array('bootstrap'), time(), 'all');
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '4.5.2', true);
    // wp_enqueue_script('affiliate-js', plugin_dir_url(__FILE__) . 'assets/js/affiliate.js', array('bootstrap'), time(), true);
}
add_action('wp_enqueue_scripts', 'affiliate_enqueue_scripts');

// Custom post type for affiliates
function register_affiliate_post_type() {
    $labels = array(
        'name'               => 'Affiliates',
        'singular_name'      => 'Affiliate',
        'menu_name'          => 'Affiliates',
        'all_items'          => 'All Affiliates',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Affiliate',
        'edit_item'          => 'Edit Affiliate',
        'new_item'           => 'New Affiliate',
        'view_item'          => 'View Affiliate',
        'search_items'       => 'Search Affiliates',
        'not_found'          => 'No affiliates found',
        'not_found_in_trash' => 'No affiliates found in Trash',
    );

    $args = array(
        'public'       => true,
        'label'        => 'Affiliates',
        'labels'       => $labels,
        'supports'     => array('title', 'editor', 'thumbnail'),
        'menu_icon'    => 'dashicons-money-alt',
        'has_archive'  => true,
        'rewrite'      => array('slug' => 'affiliates'),
    );

    register_post_type('affiliate', $args);
}
add_action('init', 'register_affiliate_post_type');

// Add Meta Boxes for Affiliate Details
function add_affiliate_meta_boxes() {
    add_meta_box('affiliate_details', 'Affiliate Details', 'render_affiliate_details_meta_box', 'affiliate', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_affiliate_meta_boxes');

function render_affiliate_details_meta_box($post) {
    // Retrieve existing values for the fields
    $affiliate_name = get_post_meta($post->ID, '_affiliate_name', true);
    $affiliate_description = get_post_meta($post->ID, '_affiliate_description', true);
    $affiliate_info = get_post_meta($post->ID, '_affiliate_info', true);
    $affiliate_btn_link = get_post_meta($post->ID, '_affiliate_btn_link', true);
    $affiliate_image_url = get_post_meta($post->ID, '_affiliate_image_url', true);

    // Output fields with Bootstrap styling
    ?>
    <div class="form-group-affli">
        <label for="affiliate_name">Affiliate Name:</label>
        <input type="text" class="form-control" name="affiliate_name" value="<?php echo esc_attr($affiliate_name); ?>" />
    </div>

    <div class="form-group-affli">
        <label for="affiliate_description">Affiliate Description:</label>
        <textarea class="form-control" name="affiliate_description"><?php echo esc_textarea($affiliate_description); ?></textarea>
    </div>

    <div class="form-group-affli">
        <label for="affiliate_info">Affiliate Information:</label>
        <?php
        // Use the WordPress editor for affiliate information
        $settings = array(
            'textarea_name' => 'affiliate_info',
            'media_buttons' => false,
            'editor_height' => 200,
        );
        wp_editor($affiliate_info, 'affiliate_info_editor', $settings);
        ?>
    </div>

    <div class="form-group-affli">
        <label for="affiliate_btn_link">Affiliate Button Link:</label>
        <input type="text" class="form-control" name="affiliate_btn_link" value="<?php echo esc_url($affiliate_btn_link); ?>" />
    </div>

    <div class="form-group-affli">
        <label for="affiliate_image_url">Affiliate Image URL:</label>
        <input type="text" name="affiliate_image_url" value="<?php echo esc_url($affiliate_image_url); ?>" />
    </div>
    <?php
}

function save_affiliate_details($post_id) {
    // Save affiliate details
    if (isset($_POST['affiliate_name'])) {
        update_post_meta($post_id, '_affiliate_name', sanitize_text_field($_POST['affiliate_name']));
    }

    if (isset($_POST['affiliate_description'])) {
        update_post_meta($post_id, '_affiliate_description', sanitize_textarea_field($_POST['affiliate_description']));
    }

    if (isset($_POST['affiliate_info'])) {
        update_post_meta($post_id, '_affiliate_info', wp_kses_post($_POST['affiliate_info']));
    }

    if (isset($_POST['affiliate_btn_link'])) {
        update_post_meta($post_id, '_affiliate_btn_link', esc_url($_POST['affiliate_btn_link']));
    }

    if (isset($_POST['affiliate_image_url'])) {
        update_post_meta($post_id, '_affiliate_image_url', esc_url($_POST['affiliate_image_url']));
    }
}

add_action('save_post', 'save_affiliate_details');

// Shortcode for Displaying Affiliates
function display_affiliates_shortcode($atts) {
    ob_start();

    // Query to retrieve affiliates
    $query = new WP_Query(array(
        'post_type'      => 'affiliate',
        'posts_per_page' => -1,
    ));

    if ($query->have_posts()) {
        ?>
        <div class="row">
            <?php $index = 1; ?>
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <div class="col-md-4 mb-2">
                    <?php
                    $affiliate_btn_link = get_post_meta(get_the_ID(), '_affiliate_btn_link', true);

                    // Check if the affiliate URL is available
                    if ($affiliate_btn_link) {
                        // Open a link tag with the affiliate URL
                        echo '<a href="' . esc_url($affiliate_btn_link) . '" class="card-link" target="_blank">';
                    }
                    ?>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php the_title(); ?></h5>

                            <?php if (has_post_thumbnail()) : ?>
                                <?php
                                $affiliate_image_url = get_post_meta(get_the_ID(), '_affiliate_image_url', true);

                                // Check if the value is empty
                                if (empty($affiliate_image_url)) {
                                    // Set default image URL
                                    $default_image_url = 'https://example.com/default-image.png'; // Replace with your default image URL
                                    $affiliate_image_url = esc_url($default_image_url);
                                }
                                ?>

                                <img src="<?php echo $affiliate_image_url; ?>" class="card-img-affli lazyload" alt="<?php the_title(); ?>">

                            <?php endif; ?>

                            <p class="card-text">
                                <?php echo esc_html(get_post_meta(get_the_ID(), '_affiliate_description', true)); ?>
                                
                            </p>

                            <?php if ($affiliate_btn_link = get_post_meta(get_the_ID(), '_affiliate_btn_link', true)) : ?>
                                <!-- Load the Modal into the mainframe -->
                                <?php $modal_id = 'affiliateModal' . $index; ?>

                                <div class="row">
                                    <div class="col-6">
                                        <a href="<?php echo esc_url($affiliate_btn_link); ?>" target="_blank" class="btn btn-primary"><span class="dashicons dashicons-cart"></span> Buy Now</a>
                                    </div>

                                    <div class="col-6">
                                        <button type="button" class="btn btn-info affiliate-more-info-poup-btn-main" data-toggle="modal" data-target="#<?php echo $modal_id; ?>" data-affiliate-info="<?php echo esc_attr(get_post_meta(get_the_ID(), '_affiliate_info', true)); ?>">More Info</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php
                    if ($affiliate_btn_link) {
                        echo '</a>';
                    }
                    ?>

                    <!-- Modal -->
                    <div class="modal fade" id="<?php echo $modal_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel"><?php the_title(); ?> - Affiliate Information</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p><?php echo wp_kses_post(get_post_meta(get_the_ID(), '_affiliate_info', true)); // Affiliate Information ?></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $index++; ?>
            <?php endwhile; ?>
        </div>

        <?php
    } else {
        echo '<p>No affiliates found</p>';
    }

    wp_reset_postdata();

    ?>

    <?php
    return ob_get_clean();
}

add_shortcode('display_affiliates', 'display_affiliates_shortcode');

// Add a custom class to the body when inside your plugin settings page
function add_custom_affiliate_class($classes) {
    global $post;

    if (is_admin() && isset($post->ID) && get_post_type($post->ID) === 'affiliate') {
        $classes .= ' affiliate-plugin';
    }

    return $classes;
}
add_filter('admin_body_class', 'add_custom_affiliate_class');

// Add styles specific to your plugin
function add_custom_affiliate_styles() {
    ?>
    <style>
        body.affiliate-plugin div#postdivrich {
            display: none !important;
        }

        body.affiliate-plugin div#postimagediv {
            /* display: none !important; */
        }

        body.affiliate-plugin div#astra_settings_meta_box {
            display: none !important;
        }

        body.affiliate-plugin div#litespeed_meta_boxes {
            display: none !important;
        }

        .form-group-affli {
            display: flex;
            width: 90%;
            padding: 1em 20px;
        }

        label {
            width: 50%;
        }

        input.form-control {
            width: 50%;
        }

        .frm-img-group-selection {
            display: none;
        }

        button.btn.affiliate-more-info-poup-btn-main {
            position: relative;
            bottom: -19px !important;
            padding: 13px 40px !important;
        }


        h5#exampleModalLabel {
            color: black;
            font-size: 20px;
            text-transform: capitalize;
        }

        .modal-header .close{
            opacity: 1;
            
        }

        .modal-header .close span {
            font-size: 25px;
        }
        
        .modal-body {
            max-height: 700px;
            overflow-y: scroll;
        }
    </style>
    <?php
}
add_action('admin_head', 'add_custom_affiliate_styles');
?>

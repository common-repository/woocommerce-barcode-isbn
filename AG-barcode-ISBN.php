<?php
/*
Plugin Name: WooCommerce Barcode & ISBN
Plugin URI: https://www.weareag.co.uk/add-barcode-meta-box-woocommerce/
Description: A plugin to add a barcode & ISBN fields to WooCommerce
Author: We are AG
Version: 1.2.4
Author URI: https://www.weareag.co.uk
*/

// Add barcode & ISBN to product edit screen

// Display Fields
add_action( 'woocommerce_product_options_general_product_data', 'woo_add_barcode' );


function woo_add_barcode() {

    global $woocommerce, $post;
    // Text Field
    woocommerce_wp_text_input(
        array(
            'id' => 'barcode',
            'label' => __( 'Barcode', 'woocommerce' ),
            'placeholder' => 'barcode here',
            'desc_tip' => 'true',
            'description' => __( 'Product barcode.', 'woocommerce' )
        )
    );
    woocommerce_wp_text_input(
        array(
            'id' => 'ISBN',
            'label' => __( 'ISBN', 'woocommerce' ),
            'placeholder' => 'ISBN here',
            'desc_tip' => 'true',
            'description' => __( 'Product ISBN.', 'woocommerce' )
        )
    );
}
function woo_add_barcode_save( $post_id ){

    // Saving Barcode
    $barcode = $_POST['barcode'];
    if( !empty( $barcode ) ) {
        update_post_meta( $post_id, 'barcode', esc_attr( $barcode ) );
    } else {
        update_post_meta( $post_id, 'barcode', esc_attr( $barcode ) );
    }
    // Saving ISBN
    $ISBN = $_POST['ISBN'];
    if( !empty( $ISBN ) ) {
        update_post_meta( $post_id, 'ISBN', esc_attr( $ISBN ) );
    } else {
        update_post_meta( $post_id, 'ISBN', esc_attr( $ISBN ) );
    }
}

// Save Fields
add_action( 'woocommerce_process_product_meta', 'woo_add_barcode_save' );
// End of adding barcode & ISBN to product edit screen


// Add to front end
function add_barcode_under_sku() {

    if (get_post_meta( get_the_ID(), 'barcode', true )) { ?>

<span class="barcode_wrapper"><?php _e( 'Barcode:', 'woocommerce' ); ?> <span class="barcode" itemprop="barcode"><?php echo get_post_meta( get_the_ID(), 'barcode', true ); ?></span>.</span>

<?php if (get_post_meta( get_the_ID(), 'barcode', true ) && get_post_meta( get_the_ID(), 'ISBN', true )) { ?>
    <br>
<?php } else { } ?>


<?php } else { }
    if (get_post_meta( get_the_ID(), 'ISBN', true )) { ?>
<span class="isbn_wrapper"><?php _e( 'ISBN:', 'woocommerce' ); ?> <span class="ISBN" itemprop="ISBN"><?php echo get_post_meta( get_the_ID(), 'ISBN', true ); ?></span>.</span>
   <?php } else { }
}
add_action( 'woocommerce_product_meta_end', 'add_barcode_under_sku', 21 );
// End


// Display AG Feed

function AG_dashboard_widget_function() {
    $rss = fetch_feed( "https://weareag.co.uk/feed/" );

    if ( is_wp_error($rss) ) {
        if ( is_admin() || current_user_can('manage_options') ) {
            echo '<p>';
            printf(__('<strong>RSS Error</strong>: %s'), $rss->get_error_message());
            echo '</p>';
        }
        return;
    }

    if ( !$rss->get_item_quantity() ) {
        echo '<p>Apparently, there are no updates to show!</p>';
        $rss->__destruct();
        unset($rss);
        return;
    }

    echo "<ul>\n";

    if ( !isset($items) )
        $items = 5;

    foreach ( $rss->get_items(0, $items) as $item ) {
        $publisher = '';
        $site_link = '';
        $link = '';
        $content = '';
        $date = '';
        $link = esc_url( strip_tags( $item->get_link() ) );
        $title = esc_html( $item->get_title() );
        $content = $item->get_content();
        $content = wp_html_excerpt($content, 250) . ' ...';

        echo "<li><a class='rsswidget' href='$link'>$title</a>\n<div class='rssSummary'>$content</div>\n";
    }

    echo "</ul>\n";
    $rss->__destruct();
    unset($rss);
}

function add_dashboard_widget() {
    wp_add_dashboard_widget('lawyerist_dashboard_widget', 'Recent Posts from We are AG', 'AG_dashboard_widget_function');
}

add_action('wp_dashboard_setup', 'add_dashboard_widget');
//END

add_action('admin_notices', 'admin_notice');

function admin_notice() {
    global $current_user ;
    $user_id = $current_user->ID;
    /* Check that the user hasn't already clicked to ignore the message */
    if ( ! get_user_meta($user_id, 'ignore_notice') ) { ?>
<div class="notice error licence-notice" style="position: relative;">
    <p>You are using <strong>FREE Version</strong> of WooCommerce Barcode & ISBN plugin without additional features. Why not <strong>upgrade</strong> and get the Pro version which works with product variations and some other features.</p>
    <a href="http://www.siteground.com" onClick="this.href='https://www.siteground.com/web-hosting.htm?afbannercode=36e0bf9c2f4043a7bcbfb8029ddfa60e'" ><img src="https://ua.siteground.com/img/banners/general/comfort/468x60.gif" alt="Web Hosting" width="468" height="60" border="0"></a><br />
    <a class="button-primary" target="_blank" href="https://www.weareag.co.uk/product/woocommerce-barcodeisbn-amazon-asin-pro/" style="margin-bottom: 10px;">Download the PRO version to get premium features</a>
    <?php printf(__('<a href="%1$s"><button type="button" class="notice-dismiss"><span class="screen-reader-text">Hide Notice</span></button></a>'), '?nag_ignore=0'); ?>
</div>
<?php
                                                      }
}

add_action('admin_init', 'nag_ignore');

function nag_ignore() {
    global $current_user;
    $user_id = $current_user->ID;
    /* If user clicks to ignore the notice, add that to their user meta */
    if ( isset($_GET['nag_ignore']) && '0' == $_GET['nag_ignore'] ) {
        add_user_meta($user_id, 'ignore_notice', 'true', true);
    }
}


add_action('admin_notices', 'discount_notice');

function discount_notice() {
    global $current_user ;
    $user_id = $current_user->ID;
    /* Check that the user hasn't already clicked to ignore the message */
    if ( ! get_user_meta($user_id, 'discount_notice') ) { ?>
<div class="notice error licence-notice" style="position: relative;">
    <p>Upgrade to the PRO version of WooCommerce Barcode & ISBN and use it with product variations.<br /></p>
    <br />
    <a class="button-primary" target="_blank" href="https://www.weareag.co.uk/product/woocommerce-barcodeisbn-amazon-asin-pro/" style="margin-bottom: 10px;">Download the PRO version to get premium features</a>
    <?php printf(__('<a href="%1$s"><button type="button" class="notice-dismiss"><span class="screen-reader-text">Hide Notice</span></button></a>'), '?ignore=0'); ?>
</div>
<?php
                                                      }
}

add_action('admin_init', 'ignore');

function ignore() {
    global $current_user;
    $user_id = $current_user->ID;
    /* If user clicks to ignore the notice, add that to their user meta */
    if ( isset($_GET['ignore']) && '0' == $_GET['ignore'] ) {
        add_user_meta($user_id, 'discount_notice', 'true', true);
    }
}

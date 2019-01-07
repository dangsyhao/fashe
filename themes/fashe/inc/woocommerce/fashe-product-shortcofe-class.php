<?php
/**
 * Products shortcode
 *
 * @package  WooCommerce/Shortcodes
 * @version  3.2.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Products shortcode class.
 */
class fashe_product_shortcode_class
{

    /**
     * Shortcode type.
     *
     * @since 3.2.0
     * @var   string
     */
    protected $type = 'products';

    /**
     * Attributes.
     *
     * @since 3.2.0
     * @var   array
     */
    protected $attributes = array();

    /**
     * Query args.
     *
     * @since 3.2.0
     * @var   array
     */
    protected $query_args = array();

    /**
     * Set custom visibility.
     *
     * @since 3.2.0
     * @var   bool
     */
    protected $custom_visibility = false;

    /**
     * Initialize shortcode.
     *
     * @since 3.2.0
     * @param array $attributes Shortcode attributes.
     * @param string $type Shortcode type.
     */
    public function __construct($attributes = array(), $type = 'products')
    {
        $this->type = $type;
        $this->attributes = $this->parse_attributes($attributes);
        $this->query_args = $this->parse_query_args();
    }

    /**
     * Get shortcode attributes.
     *
     * @since  3.2.0
     * @return array
     */
    public function get_attributes()
    {
        return $this->attributes;
    }

    /**
     * Get query args.
     *
     * @since  3.2.0
     * @return array
     */
    public function get_query_args()
    {
        return $this->query_args;
    }

    /**
     * Get shortcode type.
     *
     * @since  3.2.0
     * @return array
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * Get shortcode content.
     *
     * @since  3.2.0
     * @return string
     */
    public function fashe_get_content()
    {
        return $this->product_loop();
    }

    /**
     * Get .
     *
     * @since  3.2.0
     * @return string
     */
    public function fashe_get_query_results()
    {
        return $this->get_query_results();
    }


    /**
     * Parse attributes.
     *
     * @since  3.2.0
     * @param  array $attributes Shortcode attributes.
     * @return array
     */
    protected function parse_attributes($attributes)
    {
        $attributes = $this->parse_legacy_attributes($attributes);

        $attributes = shortcode_atts(
            array(
                'limit' => '-1',      // Results limit.
                'columns' => '',        // Number of columns.
                'rows' => '',        // Number of rows. If defined, limit will be ignored.
                'orderby' => 'title',   // menu_order, title, date, rand, price, popularity, rating, or id.
                'order' => 'ASC',     // ASC or DESC.
                'ids' => '',        // Comma separated IDs.
                'skus' => '',        // Comma separated SKUs.
                'category' => '',        // Comma separated category slugs or ids.
                'cat_operator' => 'IN',      // Operator to compare categories. Possible values are 'IN', 'NOT IN', 'AND'.
                'attribute' => '',        // Single attribute slug.
                'terms' => '',        // Comma separated term slugs or ids.
                'terms_operator' => 'IN',      // Operator to compare terms. Possible values are 'IN', 'NOT IN', 'AND'.
                'tag' => '',        // Comma separated tag slugs.
                'visibility' => 'visible', // Possible values are 'visible', 'catalog', 'search', 'hidden'.
                'class' => '',        // HTML class.
                'page' => 1,         // Page for pagination.
                'paginate' => false,     // Should results be paginated.
                'cache' => true,      // Should shortcode output be cached.
            ), $attributes, $this->type
        );

        if (!absint($attributes['columns'])) {
            $attributes['columns'] = wc_get_default_products_per_row();
        }

        return $attributes;
    }

    /**
     * Parse legacy attributes.
     *
     * @since  3.2.0
     * @param  array $attributes Attributes.
     * @return array
     */
    protected function parse_legacy_attributes($attributes)
    {
        $mapping = array(
            'per_page' => 'limit',
            'operator' => 'cat_operator',
            'filter' => 'terms',
        );

        foreach ($mapping as $old => $new) {
            if (isset($attributes[$old])) {
                $attributes[$new] = $attributes[$old];
                unset($attributes[$old]);
            }
        }

        return $attributes;
    }

    /**
     * Parse query args.
     *
     * @since  3.2.0
     * @return array
     */
    protected function parse_query_args()
    {
        $query_args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts' => true,
            'no_found_rows' => false === wc_string_to_bool($this->attributes['paginate']),
            'orderby' => empty($_GET['orderby']) ? $this->attributes['orderby'] : wc_clean(wp_unslash($_GET['orderby'])),
        );

        $orderby_value = explode('-', $query_args['orderby']);
        $orderby = esc_attr($orderby_value[0]);
        $order = !empty($orderby_value[1]) ? $orderby_value[1] : strtoupper($this->attributes['order']);
        $query_args['orderby'] = $orderby;
        $query_args['order'] = $order;

        //
        if (wc_string_to_bool($this->attributes['paginate'])) {
            $this->attributes['page'] = absint(empty($_GET['product-page']) ? 1 : $_GET['product-page']); // WPCS: input var ok, CSRF ok.
        }

        if (!empty($this->attributes['rows'])) {
            $this->attributes['limit'] = $this->attributes['columns'] * $this->attributes['rows'];
        }

        // @codingStandardsIgnoreStart
        $ordering_args = WC()->query->get_catalog_ordering_args($query_args['orderby'], $query_args['order']);
        $query_args['orderby'] = $ordering_args['orderby'];
        $query_args['order'] = $ordering_args['order'];
        if ($ordering_args['meta_key']) {
            $query_args['meta_key'] = $ordering_args['meta_key'];
        }
        $query_args['posts_per_page'] = intval($this->attributes['limit']);
        if (1 < $this->attributes['page']) {
            $query_args['paged'] = absint($this->attributes['page']);
        }
        $query_args['meta_query'] = WC()->query->get_meta_query();
        $query_args['tax_query'] = array();
        // @codingStandardsIgnoreEnd

        // Visibility.
        $this->set_visibility_query_args($query_args);

        // SKUs.
        $this->set_skus_query_args($query_args);
        // PRICE./Custom by Hao
        $this->set_price_query_args($query_args);

        // IDs.
        $this->set_ids_query_args($query_args);

        // Set specific types query args.
        if (method_exists($this, "set_{$this->type}_query_args")) {
            $this->{"set_{$this->type}_query_args"}($query_args);
        }

        // Attributes.
        $this->set_attributes_query_args($query_args);

        // Categories.
        $this->set_categories_query_args($query_args);

        // Tags.
        $this->set_tags_query_args($query_args);

        $query_args = apply_filters('woocommerce_shortcode_products_query', $query_args, $this->attributes, $this->type);

        // Always query only IDs.
        $query_args['fields'] = 'ids';

        return $query_args;
    }

    /**
     * Set skus query args.
     *
     * @since 3.2.0
     * @param array $query_args Query args.
     */
    protected function set_skus_query_args(&$query_args)
    {
        if (!empty($this->attributes['skus'])) {
            $skus = array_map('trim', explode(',', $this->attributes['skus']));
            $query_args['meta_query'][] = array(
                'key' => '_sku',
                'value' => 1 === count($skus) ? $skus[0] : $skus,
                'compare' => 1 === count($skus) ? '=' : 'IN',
            );
        }
    }

     /**
     * Set Price query args./Custom by Hao
     *
     * @since 3.2.0
     * @param array $query_args Query sargs.
     */
    protected function set_price_query_args(&$query_args)
    {
        $price=isset($_GET['price']) ?intval(wc_clean(wp_unslash($_GET['price']))):false;
        if (isset($price)) {
            $query_args['meta_query'][] = array(
                                            'key' => '_price',
                                            'value' => array($price,$price+50),
                                            'compare' => 'BETWEEN',
                                            'type'  =>'NUMERIC'                   
            );
        }
    }


    /**
     * Set ids query args.
     *
     * @since 3.2.0
     * @param array $query_args Query args.
     */
    protected function set_ids_query_args(&$query_args)
    {
        if (!empty($this->attributes['ids'])) {
            $ids = array_map('trim', explode(',', $this->attributes['ids']));

            if (1 === count($ids)) {
                $query_args['p'] = $ids[0];
            } else {
                $query_args['post__in'] = $ids;
            }
        }
    }

    /**
     * Set attributes query args.
     *
     * @since 3.2.0
     * @param array $query_args Query args.
     */
    protected function set_attributes_query_args(&$query_args)
    {
        if (!empty($this->attributes['attribute']) || !empty($this->attributes['terms'])) {
            $taxonomy = strstr($this->attributes['attribute'], 'pa_') ? sanitize_title($this->attributes['attribute']) : 'pa_' . sanitize_title($this->attributes['attribute']);
            $terms = $this->attributes['terms'] ? array_map('sanitize_title', explode(',', $this->attributes['terms'])) : array();
            $field = 'slug';

            if ($terms && is_numeric($terms[0])) {
                $field = 'term_id';
                $terms = array_map('absint', $terms);
                // Check numeric slugs.
                foreach ($terms as $term) {
                    $the_term = get_term_by('slug', $term, $taxonomy);
                    if (false !== $the_term) {
                        $terms[] = $the_term->term_id;
                    }
                }
            }

            // If no terms were specified get all products that are in the attribute taxonomy.
            if (!$terms) {
                $terms = get_terms(
                    array(
                        'taxonomy' => $taxonomy,
                        'fields' => 'ids',
                    )
                );
                $field = 'term_id';
            }

            // We always need to search based on the slug as well, this is to accommodate numeric slugs.
            $query_args['tax_query'][] = array(
                'taxonomy' => $taxonomy,
                'terms' => $terms,
                'field' => $field,
                'operator' => $this->attributes['terms_operator'],
            );
        }
    }

    /**
     * Set categories query args.
     *
     * @since 3.2.0
     * @param array $query_args Query args.
     */
    protected function set_categories_query_args(&$query_args)
    {
        if (!empty($this->attributes['category'])) {
            $categories = array_map('sanitize_title', explode(',', $this->attributes['category']));
            $field = 'slug';

            if (is_numeric($categories[0])) {
                $field = 'term_id';
                $categories = array_map('absint', $categories);
                // Check numeric slugs.
                foreach ($categories as $cat) {
                    $the_cat = get_term_by('slug', $cat, 'product_cat');
                    if (false !== $the_cat) {
                        $categories[] = $the_cat->term_id;
                    }
                }
            }

            $query_args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'terms' => $categories,
                'field' => $field,
                'operator' => $this->attributes['cat_operator'],

                /*
                 * When cat_operator is AND, the children categories should be excluded,
                 * as only products belonging to all the children categories would be selected.
                 */
                'include_children' => 'AND' === $this->attributes['cat_operator'] ? false : true,
            );
        }
    }

    /**
     * Set tags query args.
     *
     * @since 3.3.0
     * @param array $query_args Query args.
     */
    protected function set_tags_query_args(&$query_args)
    {
        if (!empty($this->attributes['tag'])) {
            $query_args['tax_query'][] = array(
                'taxonomy' => 'product_tag',
                'terms' => array_map('sanitize_title', explode(',', $this->attributes['tag'])),
                'field' => 'slug',
                'operator' => 'IN',
            );
        }
    }

    /**
     * Set sale products query args.
     *
     * @since 3.2.0
     * @param array $query_args Query args.
     */
    protected function set_sale_products_query_args(&$query_args)
    {
        $query_args['post__in'] = array_merge(array(0), wc_get_product_ids_on_sale());
    }

    /**
     * Set best selling products query args.
     *
     * @since 3.2.0
     * @param array $query_args Query args.
     */
    protected function set_best_selling_products_query_args(&$query_args)
    {
        $query_args['meta_key'] = 'total_sales'; // @codingStandardsIgnoreLine
        $query_args['order'] = 'DESC';
        $query_args['orderby'] = 'meta_value_num';
    }

    /**
     * Set visibility as hidden.
     *
     * @since 3.2.0
     * @param array $query_args Query args.
     */
    protected function set_visibility_hidden_query_args(&$query_args)
    {
        $this->custom_visibility = true;
        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_visibility',
            'terms' => array('exclude-from-catalog', 'exclude-from-search'),
            'field' => 'name',
            'operator' => 'AND',
            'include_children' => false,
        );
    }

    /**
     * Set visibility as catalog.
     *
     * @since 3.2.0
     * @param array $query_args Query args.
     */
    protected function set_visibility_catalog_query_args(&$query_args)
    {
        $this->custom_visibility = true;
        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_visibility',
            'terms' => 'exclude-from-search',
            'field' => 'name',
            'operator' => 'IN',
            'include_children' => false,
        );
        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_visibility',
            'terms' => 'exclude-from-catalog',
            'field' => 'name',
            'operator' => 'NOT IN',
            'include_children' => false,
        );
    }

    /**
     * Set visibility as search.
     *
     * @since 3.2.0
     * @param array $query_args Query args.
     */
    protected function set_visibility_search_query_args(&$query_args)
    {
        $this->custom_visibility = true;
        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_visibility',
            'terms' => 'exclude-from-catalog',
            'field' => 'name',
            'operator' => 'IN',
            'include_children' => false,
        );
        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_visibility',
            'terms' => 'exclude-from-search',
            'field' => 'name',
            'operator' => 'NOT IN',
            'include_children' => false,
        );
    }

    /**
     * Set visibility as featured.
     *
     * @since 3.2.0
     * @param array $query_args Query args.
     */
    protected function set_visibility_featured_query_args(&$query_args)
    {
        // @codingStandardsIgnoreStart
        $query_args['tax_query'] = array_merge($query_args['tax_query'], WC()->query->get_tax_query());
        // @codingStandardsIgnoreEnd

        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_visibility',
            'terms' => 'featured',
            'field' => 'name',
            'operator' => 'IN',
            'include_children' => false,
        );
    }

    /**
     * Set visibility query args.
     *
     * @since 3.2.0
     * @param array $query_args Query args.
     */
    protected function set_visibility_query_args(&$query_args)
    {
        if (method_exists($this, 'set_visibility_' . $this->attributes['visibility'] . '_query_args')) {
            $this->{'set_visibility_' . $this->attributes['visibility'] . '_query_args'}($query_args);
        } else {
            // @codingStandardsIgnoreStart
            $query_args['tax_query'] = array_merge($query_args['tax_query'], WC()->query->get_tax_query());
            // @codingStandardsIgnoreEnd
        }
    }

    /**
     * Set product as visible when quering for hidden products.
     *
     * @since  3.2.0
     * @param  bool $visibility Product visibility.
     * @return bool
     */
    public function set_product_as_visible($visibility)
    {
        return $this->custom_visibility ? true : $visibility;
    }

    /**
     * Get wrapper classes.
     *
     * @since  3.2.0
     * @param  array $columns Number of columns.
     * @return array
     */
    protected function get_wrapper_classes($columns)
    {
        $classes = array('woocommerce');

        if ('product' !== $this->type) {
            $classes[] = 'columns-' . $columns;
        }

        $classes[] = $this->attributes['class'];

        return $classes;
    }

    /**
     * Generate and return the transient name for this shortcode based on the query args.
     *
     * @since 3.3.0
     * @return string
     */
    protected function get_transient_name()
    {
        $transient_name = 'wc_product_loop' . substr(md5(wp_json_encode($this->query_args) . $this->type), 28);

        if ('rand' === $this->query_args['orderby']) {
            // When using rand, we'll cache a number of random queries and pull those to avoid querying rand on each page load.
            $rand_index = rand(0, max(1, absint(apply_filters('woocommerce_product_query_max_rand_cache_count', 5))));
            $transient_name .= $rand_index;
        }

        $transient_name .= WC_Cache_Helper::get_transient_version('product_query');

        return $transient_name;
    }

    /**
     * Run the query and return an array of data, including queried ids and pagination information.
     *
     * @since  3.3.0
     * @return object Object with the following props; ids, per_page, found_posts, max_num_pages, current_page
     */
    protected function get_query_results()
    {
        $transient_name = $this->get_transient_name();
        $cache = wc_string_to_bool($this->attributes['cache']) === true;
        $results = $cache ? get_transient($transient_name) : false;

        if (false === $results) {
            if ('top_rated_products' === $this->type) {
                add_filter('posts_clauses', array(__CLASS__, 'order_by_rating_post_clauses'));
                $query = new WP_Query($this->query_args);
                remove_filter('posts_clauses', array(__CLASS__, 'order_by_rating_post_clauses'));
            } else {
                $query = new WP_Query($this->query_args);
            }

            $paginated = !$query->get('no_found_rows');

            $results = (object)array(
                'ids' => wp_parse_id_list($query->posts),
                'total' => $paginated ? (int)$query->found_posts : count($query->posts),
                'total_pages' => $paginated ? (int)$query->max_num_pages : 1,
                'per_page' => (int)$query->get('posts_per_page'),
                'current_page' => $paginated ? (int)max(1, $query->get('paged', 1)) : 1,
            );

            if ($cache) {
                set_transient($transient_name, $results, DAY_IN_SECONDS * 30);
            }
        }
        // Remove ordering query arguments which may have been added by get_catalog_ordering_args.
        WC()->query->remove_ordering_args();
        return $results;
    }

    /**
     * Loop over found products.
     *
     * @since  3.2.0
     * @return string
     */
    protected function product_loop()
    {
        $columns = absint($this->attributes['columns']);
        $classes = $this->get_wrapper_classes($columns);
        $products = $this->get_query_results();
        ob_start();

        if ($products && $products->ids) {
            // Prime caches to reduce future queries.
            if (is_callable('_prime_post_caches')) {
                _prime_post_caches($products->ids);
            }

            // Setup the loop.
            wc_setup_loop(
                array(
                    'columns' => $columns,
                    'name' => $this->type,
                    'is_shortcode' => true,
                    'is_search' => false,
                    'is_paginated' => wc_string_to_bool($this->attributes['paginate']),
                    'total' => $products->total,
                    'total_pages' => $products->total_pages,
                    'per_page' => $products->per_page,
                    'current_page' => $products->current_page,
                )
            );

            $original_post = $GLOBALS['post'];

            do_action("woocommerce_shortcode_before_{$this->type}_loop", $this->attributes);

            ob_start();
            woocommerce_product_loop_start();
            if (wc_get_loop_prop('total')) {
                foreach ($products->ids as $product_id) {
                    $GLOBALS['post'] = get_post($product_id); // WPCS: override ok.
                    setup_postdata($GLOBALS['post']);

                    // Set custom product visibility when quering hidden products.
                    add_action('woocommerce_product_is_visible', array($this, 'set_product_as_visible'));

                    // Render product template.
                    wc_get_template_part('content', 'product');

                    // Restore product visibility.
                    remove_action('woocommerce_product_is_visible', array($this, 'set_product_as_visible'));
                }
            }

            $GLOBALS['post'] = $original_post; // WPCS: override ok.
            woocommerce_product_loop_end();

            $results=ob_get_contents();

            ob_end_clean();

            do_action("woocommerce_shortcode_after_{$this->type}_loop", $this->attributes);


        } else {
            do_action("woocommerce_shortcode_{$this->type}_loop_no_results", $this->attributes);
        }


        if(is_page('shop') || is_shop()){

            do_action('fashe_woocommerce_orderby');
            
            echo "<div class='row'>".$results."</div>";

            do_action( 'fashe_woocommerce_pagination' );
            
        }
        else{

            echo $results;

        }

        //reset
        wp_reset_postdata();
        wc_reset_loop();
    }

    /**
     * Order by rating.
     *
     * @since  3.2.0
     * @param  array $args Query args.
     * @return array
     */
    public static function order_by_rating_post_clauses($args)
    {
        global $wpdb;

        $args['where'] .= " AND $wpdb->commentmeta.meta_key = 'rating' ";
        $args['join'] .= "LEFT JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID) LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)";
        $args['orderby'] = "$wpdb->commentmeta.meta_value DESC";
        $args['groupby'] = "$wpdb->posts.ID";

        return $args;
    }

    public function fashe_pagination()
    {
        $products = $this->get_query_results();
        return $products;
    }

}

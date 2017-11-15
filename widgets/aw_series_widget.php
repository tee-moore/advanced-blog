<?php
/**
 * Add new widget.
 */
class AW_Series_Widget extends WP_Widget {

    function __construct() {

        parent::__construct(
            'aw_series_widget',
            'Series of articles',
            array( 'description' => __('This is a widget, displays a list of articles (pages) that come in one cycle along with the current article (page). For this to work, on the article (page) editing page, add the name of the series to which it belongs.', 'advanced-widget'), 'classname' => 'aw_series_widget', )
        );

        // if widget is active add style & script
        if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
            add_action('wp_footer', array( $this, 'add_aw_series_widget_scripts' ));
            add_action('wp_head', array( $this, 'add_aw_series_widget_style' ) );
        }
    }

    /**
     * Output widget on Front end
     *
     * @param array $args
     * @param array $instance from save options
     */
    function widget( $args, $instance ) {
        global $post_types_support;

        if( is_singular( $post_types_support ) ){
            global $post;

            $active_post_id = $post->ID;
            $title = apply_filters( 'aw_series_widget_title', $instance['title'] );
            $orderby = apply_filters( 'aw_series_widget_orderby', $instance['orderby'] );
            $terms = get_the_terms( $post->ID, 'series' );
            $term = array_shift( $terms );


            echo $args['before_widget'];

            if ( ! empty( $title ) ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }

            $query = new WP_Query( array(
                'post_type' => $post_types_support,
                'post_status' => 'publish',
                'orderby' => $orderby,
                'order' => 'ASC',
                'fields' => 'ids',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'series',
                        'field'    => 'term_id',
                        'terms'    => $term->term_id
                    )
                )
            ) );

            while ( $query->have_posts() ) {
                $query->the_post();

                if( get_the_ID() == $active_post_id ): ?>
                    <span class="active"><?php the_title(); ?></span><br>
                <?php else: ?>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a><br>
                <?php endif;
            }

            echo $args['after_widget'];
        }
    }

    /**
     * Output widget on Back end
     *
     * @param array $instance from save options
     */
    function form( $instance ) {

        $title = @ $instance['title'] ?: __('Read more from this series:', 'advanced-widget');
        $orderby = @ $instance['orderby'] ? $instance['orderby'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Sort by:' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
                <option value="title"<?php echo $orderby == 'title' ? ' selected="selected"' : '';?>>Post title</option>
                <option value="date"<?php echo $orderby == 'date' ? ' selected="selected"' : '';?>>Post date</option>
                <option value="modified"<?php echo $orderby == 'modified' ? ' selected="selected"' : '';?>>Post modified</option>
            </select>
        </p>
        <?php
    }

    /**
     * save options to db
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance new options
     * @param array $old_instance old options
     *
     * @return array options to save
     */
    function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['orderby'] = ( ! empty( $new_instance['orderby'] ) ) ? strip_tags( $new_instance['orderby'] ) : '';

        return $instance;
    }

    function add_aw_series_widget_scripts() {
        //filter so that you can turn off styles
        if( ! apply_filters( 'show_aw_series_widget_script', true, $this->id_base ) )
            return;
        ?>
        <script>
            jQuery(document).ready(function( $ ) {

            });
        </script>
        <?php
    }

    function add_aw_series_widget_style() {
        //filter so that you can turn off styles
        if( ! apply_filters( 'show_aw_series_widget_style', true, $this->id_base ) )
            return;
        ?>
        <style type="text/css">
            
        </style>
        <?php
    }

}


//register widgets
function register_aw_widgets() {
    register_widget( 'AW_Series_Widget' );
}
add_action( 'widgets_init', 'register_aw_widgets' );
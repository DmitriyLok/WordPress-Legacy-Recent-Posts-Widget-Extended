<?php
/**
 * Adds Recent_Posts_Extended widget.
 */
class Recent_Posts_Extended extends WP_Widget {

    /**
     * Register widget with WordPress
     */
    public function __construct() {
        $args = array(
            'classname' => 'rcp-extended',
            'description' => __('Your site&#8217;s most recent Posts.'),
            'customize_selective_refresh' => true,
            'show_instance_in_rest' => true,
        );

        parent::__construct(
            'recent-posts-extended',
            'Recent Posts Extended',
            array('description' => __('Recent Posts Extended', 'text_domain'), $args )
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        extract( $args );

        $default_title = __( 'Recent Posts Extended', 'text_domain' );
        $title         = ( ! empty( $instance['title'] ) ) ? $instance['title'] : $default_title;
        $title          = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        $posts_per_page = ( ! empty( $instance['posts_per_page'] ) ) ? absint( $instance['posts_per_page'] ) : 5;
        if ( ! $posts_per_page ) {
            $posts_per_page = 5;
        }

        $title_level = ( ! empty( $instance['title_level'] ) ) ? absint( $instance['title_level'] ) : 3;
        if ( ! $title_level ) {
            $title_level = 3;
        }

        $show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

        $show_excerpt = isset( $instance['show_excerpt'] ) ? $instance['show_excerpt'] : false;

        $show_thumbnail = isset( $instance['show_thumbnail'] ) ? $instance['show_thumbnail'] : false;

        $show_author = isset( $instance['show_author'] ) ? $instance['show_author'] : false;


        $query_args = new WP_Query(
            array(
                'posts_per_page'      => $instance['posts_per_page'],
                'post_status'         => 'publish',
            )
        );

        if ( ! $query_args->have_posts() ) {
            return;
        }

        echo $before_widget;

        if ( $title ) {
            echo $before_title . $title . $after_title;
        }

        $format = current_theme_supports( 'html5', 'navigation-widgets' ) ? 'html5' : 'xhtml';

        $format = apply_filters( 'navigation_widgets_format', $format );

        if ( 'html5' === $format ) {
            $title      = trim( strip_tags( $title ) );
            $aria_label = $title ? $title : $default_title;
            echo '<nav role="navigation" aria-label="' . esc_attr( $aria_label ) . '">';
        }

        ?>
        <ul class="wp-block-recent-posts-extended__list wp-block-recent-posts-extended">
            <?php
            foreach ( $query_args->posts as $query_arg ) {
                $id         = $query_arg->ID;
                $title      = ( ! empty( get_the_title( $id ) ) ) ? get_the_title( $id ) : __( '(no title)' );
                $permalink  = get_permalink( $id );
                $thumbnail  = '<a href="' . $permalink . '" title="' . $title . '">' . get_the_post_thumbnail( $id ) . '</a>';
                $excerpt    = '<p>' . get_the_excerpt( $id ) . '</p>';
                ?>
                <li>
                    <h<?php echo $title_level; ?>><a href="<?php echo $permalink; ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a></h<?php echo $title_level; ?>>

                    <?php
                    if( $show_thumbnail ) {
                        if( has_post_thumbnail( $id ) ) {
                            echo $thumbnail;
                        }
                    }

                    if( $show_date || $show_author ) {
                        if( $show_date && !$show_author ) :
                            echo '<p>' . get_the_date() . '</p>';
                        endif;

                        if( $show_author && !$show_date ) :
                            echo '<p>' . get_the_author() . '</p>';
                        endif;

                        if( $show_date && $show_author ) :
                            echo '<p><span>' . get_the_author() . ', </span>' . get_the_date() . '</p>';
                        endif;
                    }

                    if( $show_excerpt ) {
                        echo $excerpt;
                    }
                    ?>
                </li>
                <?php
            }
            ?>
        </ul>
    <?php }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $title          = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $posts_per_page = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $title_level    = isset( $instance['title_level'] ) ? absint( $instance['title_level'] ) : 3;
        $show_date      = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
        $show_excerpt   = isset( $instance['show_excerpt'] ) ? (bool) $instance['show_excerpt'] : false;
        $show_thumbnail = isset( $instance['show_thumbnail'] ) ? (bool) $instance['show_thumbnail'] : false;
        $show_author    = isset( $instance['show_author'] ) ? (bool) $instance['show_author'] : false;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="number" step="1" min="1" value="<?php echo $posts_per_page; ?>" size="3" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'title_level' ); ?>"><?php _e( 'Choose heading level:' ); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id( 'title_level' ); ?>" name="<?php echo $this->get_field_name( 'title_level' ); ?>" type="number" step="1" min="2" max="6" value="<?php echo $title_level; ?>" size="3" />
        </p>

        <p>
            <input class="checkbox" type="checkbox"<?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox"<?php checked( $show_excerpt ); ?> id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>"><?php _e( 'Display post short content?' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox"<?php checked( $show_author ); ?> id="<?php echo $this->get_field_id( 'show_author' ); ?>" name="<?php echo $this->get_field_name( 'show_author' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_author' ); ?>"><?php _e( 'Display post author?' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox"<?php checked( $show_thumbnail ); ?> id="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'show_thumbnail' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>"><?php _e( 'Display post thumbnail?' ); ?></label>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance                     = $old_instance;
        $instance['title']            = sanitize_text_field( $new_instance['title'] );
        $instance['posts_per_page']   = (int) $new_instance['number'];
        $instance['title_level']      = (int) $new_instance['title_level'];
        $instance['show_date']        = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
        $instance['show_excerpt']     = isset( $new_instance['show_excerpt'] ) ? (bool) $new_instance['show_excerpt'] : false;
        $instance['show_thumbnail']   = isset( $new_instance['show_thumbnail'] ) ? (bool) $new_instance['show_thumbnail'] : false;
        $instance['show_author']      = isset( $new_instance['show_author'] ) ? (bool) $new_instance['show_author'] : false;

        return $instance;
    }
}

function legacy_recent_posts_register_widget() {
    register_widget( 'Recent_Posts_Extended' );
}

add_action( 'widgets_init', 'legacy_recent_posts_register_widget' );

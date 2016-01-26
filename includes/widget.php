<?php

// Start class soup_widget //

class soup_widget extends WP_Widget {

	// Constructor //

    function soup_widget() {
    	load_plugin_textdomain('soup', false, dirname(plugin_basename(__FILE__)) . '/languages/');
        parent::__construct(false, $name = __('Upcoming Posts', 'soup'), array('description' => __('Displays your upcoming posts to entice your readers', 'soup')) );
    }

	// Extract Args //

	function widget($args, $instance) {
		extract( $args );
		$title			= apply_filters('widget_title', $instance['title']); // the widget title
		$soupnumber		= $instance['soup_number']; // the number of posts to show
		$showdate		= $instance['show_date']; // whether or not to show the scheduled post date
		$showrss		= $instance['show_rss']; // whether or not to show the RSS feed link
		$soup_cat		= isset( $instance['soup_cat'] ) ? $instance['soup_cat'] : ''; // exclude posts from these categories
		$poststatus		= isset( $instance['post_status'] ) ? $instance['post_status'] : 'future'; // the statuses of posts to show
		$posttypes		= isset( $instance['post_types'] ) ? $instance['post_types'] : 'post'; // the type of posts to show
		$posttypesarray	= explode(',', $posttypes); // array of post types
		$postorder		= $instance['post_order']; // Display newest first or random order
		$shownews		= isset($instance['show_newsletter']) ? $instance['show_newsletter'] : false ; // whether or not to show the newsletter link
		$newsletterurl	= $instance['newsletter_url']; // URL of newsletter signup
		$noresults		= $instance['no_results']; // Message for when there are no posts to display

	// Before widget //

		echo $before_widget;

	// Title of widget //

		if ( $title ) { echo $before_title . $title . $after_title; }

	// Widget output //

        $args = array(
            'posts_per_page' => $soupnumber,
            'no_paging' => '1',
            'post_status' => $poststatus,
            'order' => 'ASC',
            'orderby' => $postorder,
            'ignore_sticky_posts' => '1',
            'cat' => $soup_cat,
            'post_type' => $posttypesarray
        );

        $soup_query = new WP_Query( apply_filters( 'soup_query', $args ) );

        if( $soup_query->have_posts() ) { ?>
            <ul class="no-bullets">
            <?php while( $soup_query->have_posts() ) {
                $soup_query->the_post(); ?>
				<li>
					<?php the_title();
					if($showdate) {
						echo ' (' . get_the_time( get_option( 'date_format' ) ) . ')';
					} ?>
				</li>
			<?php } ?>
            </ul>
            <?php wp_reset_postdata();
        } else {
			echo $noresults;
		} ?>

		<?php if ($showrss) { ?>
		<p>
			<a href="<?php bloginfo('rss2_url') ?>" title="<?php _e('Subscribe to ', 'soup'); bloginfo('name'); ?>">
				<img class="soup-rss-icon" src="<?php echo SOUP_PLUGIN_URL . 'images/rss.png'; ?>" width="16px" height="16px" alt="<?php _e('Subscribe to ', 'soup'); bloginfo('name'); ?>" />
			</a>
			<?php _e('Don\'t miss it', 'soup'); ?> - <a href="<?php bloginfo('rss2_url') ?>" title="<?php _e('Subscribe to ', 'soup'); bloginfo('name'); ?>"><?php _e('Subscribe by RSS', 'soup'); ?>.</a>
		</p>
		<?php } ?>

		<?php if ($shownews) { ?>
		<p>
			<?php _e('Or, just', 'soup'); ?> <a href="<?php echo $newsletterurl; ?>" title="<?php _e('Subscribe to ', 'soup'); bloginfo ('name'); _e(' newsletter', 'soup'); ?>"><?php _e('subscribe to the newsletter', 'soup'); ?></a>!
		</p>
		<?php }

	// After widget //

		echo $after_widget;
	}

	// Update Settings //

	function update($new_instance, $old_instance) {
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['soup_number'] = strip_tags($new_instance['soup_number']);
		$instance['show_date'] = strip_tags($new_instance['show_date']);
		$instance['show_rss'] = strip_tags($new_instance['show_rss']);
		$instance['post_order'] = strip_tags($new_instance['post_order']);
		$instance['show_newsletter'] = strip_tags($new_instance['show_newsletter']);
		$instance['newsletter_url'] = strip_tags($new_instance['newsletter_url'],'<a>');
		$instance['no_results'] = strip_tags($new_instance['no_results']);
		return $instance;
	}

	// Widget Control Panel //

	function form($instance) {

		$defaults = array(
			'title' => 'Upcoming Posts',
			'soup_number' => 3,
			'show_date' => 'off',
			'show_rss' => 'off',
			'post_order' => 'date',
			'show_newsletter' => 'off',
			'newsletter_url' => '',
			'no_results' => 'Sorry - nothing planned yet!',
		);

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'soup'); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>'" type="text" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('soup_number'); ?>"><?php _e('Number of upcoming posts to display', 'soup'); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('soup_number'); ?>" name="<?php echo $this->get_field_name('soup_number'); ?>" type="text" value="<?php echo $instance['soup_number']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Show post date', 'soup'); ?>?</label>
			<input <?php checked( $instance['show_date'], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" type="checkbox" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('show_rss'); ?>"><?php _e('Show RSS link', 'soup'); ?>?</label>
			<input <?php checked( $instance['show_rss'], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_rss' ); ?>" name="<?php echo $this->get_field_name( 'show_rss' ); ?>" type="checkbox" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('post_order'); ?>"><?php _e('Sort order', 'soup'); ?>:</label>
			<select id="<?php echo $this->get_field_id('post_order'); ?>" name="<?php echo $this->get_field_name('post_order'); ?>" class="widefat" style="width:100%;">
				<option value="date" <?php selected('date', $instance['post_order']); ?>><?php _e('Next post first', 'soup'); ?></option>
				<option value="rand" <?php selected('rand', $instance['post_order']); ?>><?php _e('Random order', 'soup'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('no_results'); ?>"><?php _e('Message to display for no results', 'soup'); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('no_results'); ?>" name="<?php echo $this->get_field_name('no_results'); ?>" type="text" value="<?php echo $instance['no_results']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('show_newsletter'); ?>"><?php _e('Show Newsletter', 'soup'); ?>?</label>
			<input <?php checked( $instance['show_newsletter'], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_newsletter' ); ?>" name="<?php echo $this->get_field_name( 'show_newsletter' ); ?>" type="checkbox" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('newsletter_url'); ?>"><?php _e('Newsletter URL', 'soup'); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('newsletter_url'); ?>" name="<?php echo $this->get_field_name('newsletter_url'); ?>" type="text" value="<?php echo $instance['newsletter_url']; ?>" />
		</p>
    <?php }

}

// End class soup_widget

add_action('widgets_init', create_function('', 'return register_widget("soup_widget");'));

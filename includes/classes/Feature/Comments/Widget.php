<?php
/**
 * Search comments widget
 *
 * @since  3.6
 * @package  elasticpress
 */

namespace ElasticPress\Feature\Comments;

use \WP_Widget as WP_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Search comment widget class
 */
class Widget extends WP_Widget {

	/**
	 * Initialize the widget
	 *
	 * @since 3.6
	 */
	public function __construct() {
		$options = array( 'description' => esc_html__( 'A search form for comments.', 'elasticpress' ) );
		parent::__construct( 'ep-comments', esc_html__( 'ElasticPress - Comments', 'elasticpress' ), $options );
	}

	/**
	 * Display widget
	 *
	 * @param array $args Widget arguments
	 * @param array $instance Widget instance variables
	 * @since  3.6
	 */
	public function widget( $args, $instance ) {

		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'] );
		}
		?>

		<div class="ep-widget-search-comments"></div>

		<?php
		// Enqueue Script & Styles
		wp_enqueue_script(
			'elasticpress-comments',
			EP_URL . 'dist/js/comments-script.min.js',
			[],
			EP_VERSION,
			true
		);

		wp_enqueue_style(
			'elasticpress-comments',
			EP_URL . 'dist/css/comments-styles.min.css',
			[],
			EP_VERSION
		);

		$default_script_data = [
			'noResultsFoundText'    => esc_html__( 'We could not find any results', 'elasticpress' ),
			'minimumLengthToSearch' => 2,
		];

		/**
		 * Filter the l10n data attached to the Widget Search Comments script
		 *
		 * @since  3.6
		 * @hook ep_widget_search_comments_l10n_data_script
		 * @param  {array} $default_script_data Default data attached to the script
		 * @return  {array} New l10n data to be attached
		 */
		$script_data = apply_filters( 'ep_widget_search_comments_l10n_data_script', $default_script_data );

		$script_data['restApiEndpoint'] = get_rest_url( null, 'elasticpress/v1/comments' );

		wp_localize_script(
			'elasticpress-comments',
			'epc',
			$script_data
		);

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Display widget settings form
	 *
	 * @param array $instance Widget instance variables
	 * @since  3.6
	 */
	public function form( $instance ) {
		$title = ( isset( $instance['title'] ) ) ? $instance['title'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'elasticpress' ); ?>
			</label>

			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

	/**
	 * Update widget settings
	 *
	 * @param  array $new_instance New instance settings
	 * @param  array $old_instance Old instance settings
	 * @since  3.6.0
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance          = [];
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}
}

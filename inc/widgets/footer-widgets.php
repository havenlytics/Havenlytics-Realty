<?php
/**
 * Reusable footer widgets — dynamic Property Locations and Contact Information.
 *
 * Both widgets are WordPress-standard and can be placed in any sidebar. They are
 * also seeded into the footer columns on a fresh install (empty-only). The
 * Property Locations widget hides itself gracefully when the Havenlytics plugin
 * is inactive or there are no location terms.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Footer widget: dynamic property locations.
 */
class HVN_Realty_Footer_Locations_Widget extends WP_Widget {

	/**
	 * Register widget.
	 */
	public function __construct() {
		parent::__construct(
			'hvn_realty_footer_locations',
			__( 'Havenlytics: Property Locations', 'havenlytics-realty' ),
			array(
				'description' => __( 'Lists popular property locations. Hidden automatically when the Havenlytics plugin is inactive.', 'havenlytics-realty' ),
				'classname'   => 'hvn-theme-widget-locations',
			)
		);
	}

	/**
	 * Front-end display.
	 *
	 * @param array<string, mixed> $args     Sidebar args.
	 * @param array<string, mixed> $instance Saved values.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( ! function_exists( 'hvn_realty_get_property_locations' ) ) {
			return;
		}

		$limit = isset( $instance['limit'] ) ? absint( $instance['limit'] ) : 6;
		if ( $limit < 1 ) {
			$limit = 6;
		}

		$terms = hvn_realty_get_property_locations( $limit );
		if ( empty( $terms ) || ! is_array( $terms ) ) {
			return;
		}

		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Property Locations', 'havenlytics-realty' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( '' !== trim( (string) $title ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<ul class="hvn-theme-footer-link-list hvn-theme-footer-locations-list">';
		foreach ( $terms as $term ) {
			if ( ! isset( $term->name ) ) {
				continue;
			}

			$link = get_term_link( $term );
			if ( is_wp_error( $link ) ) {
				continue;
			}

			printf(
				'<li><a href="%1$s">%2$s</a></li>',
				esc_url( $link ),
				esc_html( $term->name )
			);
		}
		echo '</ul>';

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Back-end form.
	 *
	 * @param array<string, mixed> $instance Saved values.
	 * @return void
	 */
	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Property Locations', 'havenlytics-realty' );
		$limit = isset( $instance['limit'] ) ? absint( $instance['limit'] ) : 6;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'havenlytics-realty' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Number of locations:', 'havenlytics-realty' ); ?></label>
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="number" min="1" max="20" value="<?php echo esc_attr( (string) $limit ); ?>">
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values.
	 *
	 * @param array<string, mixed> $new_instance New values.
	 * @param array<string, mixed> $old_instance Old values.
	 * @return array<string, mixed>
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['limit'] = isset( $new_instance['limit'] ) ? absint( $new_instance['limit'] ) : 6;

		return $instance;
	}
}

/**
 * Footer widget: contact information.
 */
class HVN_Realty_Footer_Contact_Widget extends WP_Widget {

	/**
	 * Register widget.
	 */
	public function __construct() {
		parent::__construct(
			'hvn_realty_footer_contact',
			__( 'Havenlytics: Contact Information', 'havenlytics-realty' ),
			array(
				'description' => __( 'Displays address, phone, email and business hours. Falls back to Customizer footer contact values when fields are empty.', 'havenlytics-realty' ),
				'classname'   => 'hvn-theme-widget-contact',
			)
		);
	}

	/**
	 * Resolve a field value, falling back to the Customizer footer contact mods.
	 *
	 * @param array<string, mixed> $instance Saved values.
	 * @param string               $key      Field key.
	 * @return string
	 */
	protected function resolve_field( $instance, $key ) {
		if ( isset( $instance[ $key ] ) && '' !== trim( (string) $instance[ $key ] ) ) {
			return (string) $instance[ $key ];
		}

		return (string) get_theme_mod( 'hvn_realty_footer_contact_' . $key, '' );
	}

	/**
	 * Front-end display.
	 *
	 * @param array<string, mixed> $args     Sidebar args.
	 * @param array<string, mixed> $instance Saved values.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$address = $this->resolve_field( $instance, 'address' );
		$phone   = $this->resolve_field( $instance, 'phone' );
		$email   = $this->resolve_field( $instance, 'email' );
		$hours   = $this->resolve_field( $instance, 'hours' );

		if ( '' === $address && '' === $phone && '' === $email && '' === $hours ) {
			return;
		}

		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Contact', 'havenlytics-realty' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( '' !== trim( (string) $title ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<ul class="hvn-theme-footer-contact-list">';

		if ( '' !== $address ) {
			echo '<li class="hvn-theme-footer-contact-list__item hvn-theme-footer-contact-list__item--address">' . wp_kses_post( wpautop( $address ) ) . '</li>';
		}

		if ( '' !== $phone ) {
			printf(
				'<li class="hvn-theme-footer-contact-list__item hvn-theme-footer-contact-list__item--phone"><a href="%1$s">%2$s</a></li>',
				esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ),
				esc_html( $phone )
			);
		}

		if ( '' !== $email ) {
			printf(
				'<li class="hvn-theme-footer-contact-list__item hvn-theme-footer-contact-list__item--email"><a href="%1$s">%2$s</a></li>',
				esc_url( 'mailto:' . $email ),
				esc_html( $email )
			);
		}

		if ( '' !== $hours ) {
			echo '<li class="hvn-theme-footer-contact-list__item hvn-theme-footer-contact-list__item--hours">' . wp_kses_post( wpautop( $hours ) ) . '</li>';
		}

		echo '</ul>';

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Back-end form.
	 *
	 * @param array<string, mixed> $instance Saved values.
	 * @return void
	 */
	public function form( $instance ) {
		$title   = isset( $instance['title'] ) ? $instance['title'] : __( 'Contact', 'havenlytics-realty' );
		$address = isset( $instance['address'] ) ? $instance['address'] : '';
		$phone   = isset( $instance['phone'] ) ? $instance['phone'] : '';
		$email   = isset( $instance['email'] ) ? $instance['email'] : '';
		$hours   = isset( $instance['hours'] ) ? $instance['hours'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'havenlytics-realty' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'address' ) ); ?>"><?php esc_html_e( 'Address:', 'havenlytics-realty' ); ?></label>
			<textarea class="widefat" rows="2" id="<?php echo esc_attr( $this->get_field_id( 'address' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'address' ) ); ?>"><?php echo esc_textarea( $address ); ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'phone' ) ); ?>"><?php esc_html_e( 'Phone:', 'havenlytics-realty' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'phone' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'phone' ) ); ?>" type="text" value="<?php echo esc_attr( $phone ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>"><?php esc_html_e( 'Email:', 'havenlytics-realty' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email' ) ); ?>" type="text" value="<?php echo esc_attr( $email ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'hours' ) ); ?>"><?php esc_html_e( 'Business Hours:', 'havenlytics-realty' ); ?></label>
			<textarea class="widefat" rows="2" id="<?php echo esc_attr( $this->get_field_id( 'hours' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hours' ) ); ?>"><?php echo esc_textarea( $hours ); ?></textarea>
		</p>
		<p class="description"><?php esc_html_e( 'Leave a field empty to use the Customizer footer contact value.', 'havenlytics-realty' ); ?></p>
		<?php
	}

	/**
	 * Sanitize widget form values.
	 *
	 * @param array<string, mixed> $new_instance New values.
	 * @param array<string, mixed> $old_instance Old values.
	 * @return array<string, mixed>
	 */
	public function update( $new_instance, $old_instance ) {
		$instance            = $old_instance;
		$instance['title']   = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['address'] = isset( $new_instance['address'] ) ? wp_kses_post( $new_instance['address'] ) : '';
		$instance['phone']   = isset( $new_instance['phone'] ) ? sanitize_text_field( $new_instance['phone'] ) : '';
		$instance['email']   = isset( $new_instance['email'] ) ? sanitize_text_field( $new_instance['email'] ) : '';
		$instance['hours']   = isset( $new_instance['hours'] ) ? wp_kses_post( $new_instance['hours'] ) : '';

		return $instance;
	}
}

/**
 * Register the footer widgets.
 *
 * @return void
 */
function hvn_realty_register_footer_widgets() {
	register_widget( 'HVN_Realty_Footer_Locations_Widget' );
	register_widget( 'HVN_Realty_Footer_Contact_Widget' );
}
add_action( 'widgets_init', 'hvn_realty_register_footer_widgets' );

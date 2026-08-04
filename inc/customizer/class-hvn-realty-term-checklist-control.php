<?php
/**
 * Customizer multi-checkbox control for taxonomy terms.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

/**
 * Checklist control that stores a JSON array of term IDs.
 */
class HVN_Realty_Customize_Term_Checklist_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'hvn_realty_term_checklist';

	/**
	 * Taxonomy slug.
	 *
	 * @var string
	 */
	public $taxonomy = 'hvnly_prop_depts';

	/**
	 * Enqueue control scripts.
	 *
	 * @return void
	 */
	public function enqueue() {
		$handle = 'hvn-realty-customizer-term-checklist';
		$src    = get_template_directory_uri() . '/assets/js/customizer-term-checklist-control.js';
		$path   = get_template_directory() . '/assets/js/customizer-term-checklist-control.js';

		if ( ! file_exists( $path ) ) {
			return;
		}

		wp_enqueue_script(
			$handle,
			$src,
			array( 'customize-controls', 'jquery' ),
			(string) filemtime( $path ),
			true
		);
	}

	/**
	 * Render the control.
	 *
	 * @return void
	 */
	public function render_content() {
		$taxonomy = sanitize_key( (string) $this->taxonomy );
		$terms    = array();

		if ( taxonomy_exists( $taxonomy ) ) {
			$fetched = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
					'orderby'    => 'name',
					'order'      => 'ASC',
				)
			);
			if ( ! is_wp_error( $fetched ) && is_array( $fetched ) ) {
				$terms = $fetched;
			}
		}

		$selected = array();
		$decoded  = json_decode( (string) $this->value(), true );
		if ( is_array( $decoded ) ) {
			$selected = array_map( 'absint', $decoded );
		}
		?>
		<?php if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif; ?>
		<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>

		<input
			type="hidden"
			<?php $this->link(); ?>
			value="<?php echo esc_attr( (string) $this->value() ); ?>"
			class="hvn-realty-term-checklist__value"
			data-hvn-realty-term-checklist="1"
		/>

		<?php if ( empty( $terms ) ) : ?>
			<p class="description"><?php esc_html_e( 'No departments found. Add Property Department terms in Havenlytics.', 'havenlytics-realty' ); ?></p>
			<?php
			return;
		endif;
		?>

		<ul class="hvn-realty-term-checklist" style="margin:8px 0 0;padding:0;list-style:none;max-height:220px;overflow:auto;border:1px solid #dcdcde;border-radius:4px;padding:8px 10px;">
			<?php foreach ( $terms as $term ) : ?>
				<?php
				if ( ! $term instanceof WP_Term ) {
					continue;
				}
				$checked = in_array( (int) $term->term_id, $selected, true );
				$input_id = $this->id . '-term-' . (int) $term->term_id;
				?>
				<li style="margin:0 0 6px;">
					<label for="<?php echo esc_attr( $input_id ); ?>">
						<input
							type="checkbox"
							id="<?php echo esc_attr( $input_id ); ?>"
							class="hvn-realty-term-checklist__box"
							value="<?php echo esc_attr( (string) (int) $term->term_id ); ?>"
							<?php checked( $checked ); ?>
						/>
						<?php echo esc_html( $term->name ); ?>
					</label>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}

<?php

class LavaPostMetaBox {
	function __construct($posttype, LavaOption $option){
		$this->options[] = $option;
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		// add_action( 'save_post', array( $this, 'save' );
	}
	function add_meta_box( $post_type ){
		$post_types = array( 'post', 'page' );
		if ( in_array( $post_type , $post_types ) ){
			add_meta_box(
				'some_meta_box_name',
				'Some Meta Box Headline',
				array( $this, 'render_meta_box_content' ),
				$post_type,
				'advanced',
				'high'
			);
		}
	}
	function save_metabox( $post_id ){
		// Check if our nonce is set.
		if ( ! isset( $_POST['myplugin_inner_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['myplugin_inner_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'myplugin_inner_custom_box' ) )
			return $post_id;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}
	}
	public function render_meta_box_content( $post ){
		wp_nonce_field( 'customMetaNonce', 'customMetaNonce_nonce' );
		$value = get_post_meta( $post->ID, '_my_meta_value_key', true );
		foreach( $this->options as $o ){
			echo $o->get_option_field_html();
		}
	}
}
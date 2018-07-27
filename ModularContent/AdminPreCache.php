<?php


namespace ModularContent;


/**
 * A container for information that will be rendered in
 * the admin cache on page load to reduce the number
 * of ajax requests
 */
class AdminPreCache implements \JsonSerializable {
	private $posts  = [ ];
	private $terms  = [ ];
	private $data   = [ ];
	private $files  = [ ];

	public function add_post( $post_id ) {
		$post = self::get_post_array( $post_id );
		if ( $post ) {
			$this->posts[ $post_id ] = $post;
		}
		$thumbnail_id = get_post_thumbnail_id( $post_id );
		if ( $thumbnail_id ) {
			$this->add_file( $thumbnail_id );
		}
	}

	public function add_term( $term_id, $taxonomy ) {
		$term = get_term( (int)$term_id, $taxonomy );
		if ( $term ) {
			$this->terms[ $term_id ] = get_object_vars( $term );
		}
	}

	public function add_data( $key, $data ) {
		$this->data[ $key ] = $data;
	}

	public function add_file( $file_id = 0, $size = 'thumbnail' ) {
		if ( empty( $file_id ) ) {
			return;
		}

		$data = wp_prepare_attachment_for_js( $file_id );

		if ( empty( $data ) ) {
			return;
		}

		if ( ! empty( $data[ 'sizes' ] ) && ! empty( $data[ 'sizes' ][ $size ] ) ) {
			$this->files[ $file_id ][ 'url' ] = $data[ 'sizes' ][ $size ][ 'url' ];
		} else {
			$this->files[ $file_id ][ 'url' ] = $data[ 'url' ];
		}

		$this->files[ $file_id ][ 'id' ]      = $data[ 'id' ];
		$this->files[ $file_id ][ 'name' ]    = $data[ 'name' ];
		$this->files[ $file_id ][ 'mime' ]    = $data[ 'mime' ];
		$this->files[ $file_id ][ 'subtype' ] = $data[ 'subtype' ];
		$this->files[ $file_id ][ 'type' ]    = $data[ 'type' ];
	}

	public function get_cache() {
		return [
			'posts'  => (object)$this->posts,
			'terms'  => (object)$this->terms,
			'data'   => (object)$this->data,
			'files'  => (object)$this->files,
		];
	}

	/**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Specify data which should be serialized to JSON
	 *
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 */
	public function jsonSerialize() {
		return $this->get_cache();
	}

	/**
	 * @param int $post_id
	 *
	 * @return array
	 */
	public static function get_post_array( $post_id ) {
		global $post;
		$original_post = $post;
		$post = get_post($post_id);
		$post_data = array();
		if ( $post ) {
			setup_postdata( $post );
			$excerpt = $post->post_excerpt;
			if ( empty( $excerpt ) ) {
				$excerpt = $post->post_content;
			}
			$title = html_entity_decode( get_the_title($post), ENT_QUOTES | ENT_HTML401 );
			$excerpt = wp_trim_words( $excerpt, 40, '&hellip;' );
			$excerpt = apply_filters( 'get_the_excerpt', $excerpt );
			$thumbnail_html = get_the_post_thumbnail( $post->ID, array( 150, 150 ) );

			$post_data = get_object_vars( $post );

			$post_data[ 'post_title' ] = $title;
			$post_data[ 'post_excerpt' ] = $excerpt;
			$post_data[ 'thumbnail_html' ] = $thumbnail_html;

			$post_type_object = get_post_type_object( $post->post_type );
			$post_data[ 'post_type_label' ] = $post_type_object->labels->singular_name;
			$post_data[ 'permalink' ] = get_permalink( $post->ID );
		} else {
			$post_data = [
				'post_title'      => __( 'Error: Post not found', 'modular-content' ),
				'post_excerpt'    => '',
				'thumbnail_html'  => '',
				'post_type_label' => '',
				'permalink'       => '',
			];
		}

		$post = $original_post;
		if ( $original_post ) {
			wp_reset_postdata();
		}
		return $post_data;
	}


}
<?php

/*---------------------------------------------------------*/
/* Settings Functions                                      */
/*---------------------------------------------------------*/

function mbt_load_settings() {
	global $mbt_settings;
	$mbt_settings = apply_filters("mbt_settings", get_option("mbt_settings"));
	if(empty($mbt_settings)) { mbt_reset_settings(); }
}

function mbt_reset_settings() {
	global $mbt_settings;
	$mbt_settings = array(
		'version' => MBT_VERSION,
		'api_key' => '',
		'api_key_status' => 0,
		'api_key_message' => '',
		'upgrade_active' => false,
		'installed' => '',
		'installed_examples' => false,
		'booktable_page' => 0,
		'compatibility_mode' => true,
		'style_pack' => mbt_get_default_style_pack(),
		'image_size' => 'medium',
		'reviews_type' => 'none',
		'enable_socialmedia_single_book' => false,
		'enable_socialmedia_book_excerpt' => false,
		'enable_seo' => true,
		'buybutton_shadowbox' => 'none',
		'enable_breadcrumbs' => true,
		'show_series' => true,
		'show_find_bookstore' => true,
		'show_find_bookstore_buybuttons_shadowbox' => true,
		'show_about_author' => true,
		'book_button_size' => 'medium',
		'listing_button_size' => 'medium',
		'widget_button_size' => 'medium',
		'posts_per_page' => 12,
		'enable_default_affiliates' => false,
		'google_api_key' => '',
		'product_name' => __('Books', 'mybooktable'),
		'product_slug' => _x('books', 'URL slug', 'mybooktable'),
		'hide_domc_notice' => false,
		'domc_notice_text' => __('Disclosure of Material Connection: Some of the links in the page above are "affiliate links." This means if you click on the link and purchase the item, I will receive an affiliate commission. I am disclosing this in accordance with the Federal Trade Commission\'s <a href="http://www.access.gpo.gov/nara/cfr/waisidx_03/16cfr255_03.html" target="_blank">16 CFR, Part 255</a>: "Guides Concerning the Use of Endorsements and Testimonials in Advertising."', 'mybooktable'),
	);
	$mbt_settings = apply_filters("mbt_default_settings", $mbt_settings);
	update_option("mbt_settings", apply_filters("mbt_update_settings", $mbt_settings));
}

function mbt_get_setting($name) {
	global $mbt_settings;
	return isset($mbt_settings[$name]) ? $mbt_settings[$name] : NULL;
}

function mbt_update_setting($name, $value) {
	global $mbt_settings;
	$mbt_settings[$name] = $value;
	update_option("mbt_settings", apply_filters("mbt_update_settings", $mbt_settings));
}



/*---------------------------------------------------------*/
/* General                                                 */
/*---------------------------------------------------------*/

function mbt_save_taxonomy_image($taxonomy, $term, $url) {
	$taxonomy_images = get_option($taxonomy."_meta");
	if(empty($taxonomy_images)) { $taxonomy_images = array(); }
	$taxonomy_images[$term] = $url;
	update_option($taxonomy."_meta", $taxonomy_images);
}

function mbt_get_taxonomy_image($taxonomy, $term) {
	$taxonomy_images = get_option($taxonomy."_meta");
	if(empty($taxonomy_images)) { $taxonomy_images = array(); }
	return isset($taxonomy_images[$term]) ? $taxonomy_images[$term] : '';
}

function mbt_save_author_priority($author_id, $priority) {
	$author_priorities = mbt_get_setting("author_priorities");
	if(empty($author_priorities)) { $author_priorities = array(); }
	$author_priorities[$author_id] = $priority;
	mbt_update_setting("author_priorities", $author_priorities);
}

function mbt_get_author_priority($author_id) {
	$author_priorities = mbt_get_setting("author_priorities");
	if(empty($author_priorities)) { $author_priorities = array(); }
	return isset($author_priorities[$author_id]) ? $author_priorities[$author_id] : 50;
}

function mbt_get_posts_per_page() {
	$posts_per_page = mbt_get_setting('posts_per_page');
	return empty($posts_per_page) ? get_option('posts_per_page') : $posts_per_page;
}

function mbt_is_mbt_page() {
	return (is_post_type_archive('mbt_book') or is_tax('mbt_author') or is_tax('mbt_genre') or is_tax('mbt_publisher') or is_tax('mbt_series') or is_tax('mbt_tag') or is_singular('mbt_book') or mbt_is_booktable_page() or mbt_is_archive_query());
}

function mbt_is_mbt_admin_page() {
	global $pagenow;
	$screen = get_current_screen();
	return is_admin() and (
		($pagenow == 'edit.php' and $screen->post_type == 'mbt_book') or
		($pagenow == 'post.php' and $screen->post_type == 'mbt_book') or
		($pagenow == 'post-new.php' and $screen->post_type == 'mbt_book') or
		(($pagenow == 'edit-tags.php' or $pagenow == 'term.php') and $screen->taxonomy == 'mbt_author') or
		(($pagenow == 'edit-tags.php' or $pagenow == 'term.php') and $screen->taxonomy == 'mbt_genre') or
		(($pagenow == 'edit-tags.php' or $pagenow == 'term.php') and $screen->taxonomy == 'mbt_publisher') or
		(($pagenow == 'edit-tags.php' or $pagenow == 'term.php') and $screen->taxonomy == 'mbt_series') or
		(($pagenow == 'edit-tags.php' or $pagenow == 'term.php') and $screen->taxonomy == 'mbt_tag') or
		($pagenow == 'admin.php' and $screen->id == 'mybooktable_page_mbt_import') or
		($pagenow == 'admin.php' and $screen->id == 'mybooktable_page_mbt_sort_books') or
		($pagenow == 'admin.php' and $screen->id == 'toplevel_page_mbt_dashboard') or
		($pagenow == 'admin.php' and $screen->id == 'mybooktable_page_mbt_settings') or
		($pagenow == 'admin.php' and $screen->id == 'mybooktable_page_mbt_help')
	);
}

function mbt_is_booktable_page() {
	global $mbt_is_booktable_page;
	return !empty($mbt_is_booktable_page);
}

function mbt_get_booktable_url() {
	if(mbt_get_setting('booktable_page') <= 0 or !get_page(mbt_get_setting('booktable_page'))) {
		$url = get_post_type_archive_link('mbt_book');
	} else {
		$url = get_permalink(mbt_get_setting('booktable_page'));
	}
	return $url;
}

function mbt_get_product_name() {
	$name = mbt_get_setting('product_name');
	return apply_filters('mbt_product_name', empty($name) ? __('Books', 'mybooktable') : $name);
}

function mbt_get_product_slug() {
	$slug = mbt_get_setting('product_slug');
	return apply_filters('mbt_product_slug', empty($slug) ? _x('books', 'URL slug', 'mybooktable') : $slug);
}

function mbt_get_reviews_types() {
	// The 'mbt_reviews_boxes' filter is deprecated, but still supported
	return apply_filters('mbt_reviews_boxes', apply_filters('mbt_reviews_types', array()));
}

function mbt_add_disabled_reviews_types($reviews) {
	$reviews['amazon'] = array(
		'name' => __('Amazon Reviews'),
		'disabled' => mbt_get_upgrade_message(),
	);
	return $reviews;
}
add_filter('mbt_reviews_types', 'mbt_add_disabled_reviews_types', 9);

function mbt_get_wp_filesystem($nonce_url) {
	require_once(ABSPATH.'wp-admin/includes/file.php');

	ob_start();
	$creds = request_filesystem_credentials($nonce_url, '', false, false, null);
	$output = ob_get_contents();
	ob_end_clean();
	if($creds === false) { return $output; }

	if(!WP_Filesystem($creds)) {
		ob_start();
		request_filesystem_credentials($nonce_url, '', true, false, null);
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	return '';
}

function mbt_download_and_insert_attachment($url) {
	$raw_response = wp_remote_get($url, array('timeout' => 3));
	if(is_wp_error($raw_response) or wp_remote_retrieve_response_code($raw_response) != 200) { return 0; }
	$file_data = wp_remote_retrieve_body($raw_response);

	$nonce_url = wp_nonce_url('admin.php', 'mbt_download_and_insert_attachment');
	$output = mbt_get_wp_filesystem($nonce_url);
	if(!empty($output)) { return 0;	}
	global $wp_filesystem, $wpdb;

	$url_parts = parse_url($url);
	$filename = basename($url_parts['path']);
	$filename = preg_replace('/[^A-Za-z0-9_.]/', '', $filename);
	$upload_dir = wp_upload_dir();
	$filepath = $upload_dir['path'].'/'.$filename;
	$fileurl = $upload_dir['url'].'/'.$filename;

	if($wp_filesystem->exists($filepath)) {
		$existing_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid=%s", $fileurl));
		if(!empty($existing_id)) { return $existing_id; } else { return 0; }
	}

	if(!$wp_filesystem->put_contents($filepath, $file_data, FS_CHMOD_FILE)) { return 0; }

	$filetype = wp_check_filetype(basename($filepath), null);
	$attachment = array(
		'guid'           => $fileurl,
		'post_mime_type' => $filetype['type'],
		'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
		'post_content'   => '',
		'post_status'    => 'inherit'
	);

	$attach_id = wp_insert_attachment($attachment, $filepath);

	require_once(ABSPATH.'wp-admin/includes/image.php');
	$attach_data = wp_generate_attachment_metadata($attach_id, $filepath);
	wp_update_attachment_metadata($attach_id, $attach_data);

	return $attach_id;
}

function mbt_copy_and_insert_attachment($path) {
	$nonce_url = wp_nonce_url('admin.php', 'mbt_copy_and_insert_attachment');
	$output = mbt_get_wp_filesystem($nonce_url);
	if(!empty($output)) { return 0;	}
	global $wp_filesystem, $wpdb;

	$filename = basename($path);
	$filename = preg_replace('/[^A-Za-z0-9_.]/', '', $filename);
	$upload_dir = wp_upload_dir();
	$filepath = $upload_dir['path'].'/'.$filename;
	$fileurl = $upload_dir['url'].'/'.$filename;

	if($wp_filesystem->exists($filepath)) {
		$existing_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid=%s", $fileurl));
		if(!empty($existing_id)) { return $existing_id; } else { return 0; }
	}

	if(!$wp_filesystem->copy($path, $filepath, false, FS_CHMOD_FILE)) { return 0; }

	$filetype = wp_check_filetype(basename($filepath), null);
	$attachment = array(
		'guid'           => $fileurl,
		'post_mime_type' => $filetype['type'],
		'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
		'post_content'   => '',
		'post_status'    => 'inherit'
	);

	$attach_id = wp_insert_attachment($attachment, $filepath);

	require_once(ABSPATH.'wp-admin/includes/image.php');
	$attach_data = wp_generate_attachment_metadata($attach_id, $filepath);
	wp_update_attachment_metadata($attach_id, $attach_data);

	return $attach_id;
}

function mbt_add_book_pages_to_front_page_options($pages, $args) {
	if(!is_array($args) or !isset($args['name']) or $args['name'] !== 'page_on_front') { return $pages; }

	$books_query = new WP_Query(array('post_type' => 'mbt_book', 'posts_per_page' => -1));
	$books = $books_query->posts;
	foreach($books as $book) {
		$book->post_title = __('Book Page', 'mybooktable').': '.$book->post_title;
	}

	return array_merge($pages, $books);
}
add_filter('get_pages', 'mbt_add_book_pages_to_front_page_options', 10, 2);



/*---------------------------------------------------------*/
/* Display Modes                                           */
/*---------------------------------------------------------*/

function mbt_get_book_display_modes() {
	$modes = apply_filters('mbt_display_modes', array());
	foreach($modes as $slug => $mode) {
		if(empty($modes[$slug]['name'])) { $modes[$slug]['name'] = __('Unnamed', 'mybooktable'); }
		if(empty($modes[$slug]['supports'])) { $modes[$slug]['supports'] = array(); }
	}
	return $modes;
}

function mbt_add_default_book_display_modes($modes) {
	$modes['storefront'] = array('name' => __('Storefront', 'mybooktable'), 'supports' => array('embedding', 'sale_price'));
	$modes['singlecolumn'] = array('name' => __('Beautiful Page', 'mybooktable'), 'supports' => array('embedding', 'teaser', 'sortable_sections'));
	return $modes;
}
add_filter('mbt_display_modes', 'mbt_add_default_book_display_modes');

function mbt_get_default_book_display_mode() {
	return apply_filters('mbt_default_book_display_mode', 'singlecolumn');
}

function mbt_get_book_display_mode($post_id) {
	$display_mode = get_post_meta($post_id, 'mbt_display_mode', true);
	$display_modes = mbt_get_book_display_modes();
	if(empty($display_modes[$display_mode])) { $display_mode = mbt_get_default_book_display_mode(); }
	return $display_mode;
}

function mbt_book_display_mode_supports($display_mode, $supports) {
	$display_modes = mbt_get_book_display_modes();
	if(empty($display_modes[$display_mode])) { $display_mode = mbt_get_default_book_display_mode(); }
	return array_search($supports, $display_modes[$display_mode]['supports']) !== FALSE;
}

add_action('admin_footer-edit.php', 'mbt_book_bulk_change_display_mode_admin_footer');
function mbt_book_bulk_change_display_mode_admin_footer() {
	$display_modes = mbt_get_book_display_modes();
	global $post_type;
	if($post_type == 'mbt_book') {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				<?php foreach($display_modes as $slug => $mode) { ?>
				jQuery('<option>').val('mbt-switch-mode-<?php echo($slug); ?>').text(mbt_admin_pages_i18n.swich_to_mode.replace('%s', '<?php htmlspecialchars($mode['name'], ENT_QUOTES); ?>')).appendTo('#bulk-action-selector-top');
				jQuery('<option>').val('mbt-switch-mode-<?php echo($slug); ?>').text(mbt_admin_pages_i18n.swich_to_mode.replace('%s', '<?php htmlspecialchars($mode['name'], ENT_QUOTES); ?>')).appendTo('#bulk-action-selector-bottom');
				<?php } ?>
			});
		</script>
	<?php
	}
}

add_action('load-edit.php', 'mbt_book_bulk_change_display_mode_action');
function mbt_book_bulk_change_display_mode_action() {
	$wp_list_table = _get_list_table('WP_Posts_List_Table');
	$action = $wp_list_table->current_action();

	if(substr($action, 0, 16) == 'mbt-switch-mode-') {
		check_admin_referer('bulk-posts');

		$selected_mode = substr($action, 16);

		$display_modes = mbt_get_book_display_modes();
		if(empty($display_modes[$selected_mode])) { return; }

		$post_ids = array_map('intval', isset($_REQUEST['post']) ? $_REQUEST['post'] : array());
		if(empty($post_ids)) { return; }

		$books_updated = 0;
		foreach($post_ids as $post_id) {
			update_post_meta($post_id, 'mbt_display_mode', $selected_mode);

			$books_updated++;
		}

		wp_redirect(add_query_arg(array('paged' => $wp_list_table->get_pagenum(), 'mbt-books-updated' => $books_updated), admin_url('edit.php?post_type=mbt_book')));
		exit();
	}
}

add_action('admin_notices', 'mbt_book_bulk_change_display_mode_admin_notices');
function mbt_book_bulk_change_display_mode_admin_notices() {
	global $post_type, $pagenow;
	if($pagenow == 'edit.php' && $post_type == 'mbt_book' && isset($_REQUEST['mbt-books-updated']) && (int)$_REQUEST['mbt-books-updated']) {
		$message = sprintf(_n('Book updated.', '%s books updated.', $_REQUEST['mbt-books-updated']), number_format_i18n($_REQUEST['mbt-books-updated']));
		echo('<div class="updated"><p>'.$message.'</p></div>');
	}
}

function mbt_get_sorted_content_sections($display_mode) {
	$sections = apply_filters('mbt_get_'.$display_mode.'_content_sections', array());
	$prioritized_sections = array();
	foreach($sections as $id => $section) { $prioritized_sections[] = array_merge($section, array('id' => $id)); }
	$prioritize = function($a, $b) { return ($a['priority'] == $b['priority']) ? 0 : (($a['priority'] < $b['priority']) ? -1 : 1); };
	usort($prioritized_sections, $prioritize);
	$sections_order = mbt_get_setting('book_section_order_'.$display_mode);
	if(empty($sections_order)) { $sections_order = array(); }
	$sorted_sections = array();
	$order_index = 0;
	foreach($prioritized_sections as $section) {
		$index = array_search($section['id'], $sections_order);
		if($index === false) {
			$sorted_sections[] = $section;
		} else {
			while(empty($sections[$sections_order[$order_index]])) { $order_index += 1; }
			$order_section = $sections[$sections_order[$order_index]];
			$sorted_sections[] = array_merge($order_section, array('id' => $sections_order[$order_index], 'priority' => $section['priority']));
			$order_index += 1;
		}
	}
	return $sorted_sections;
}

function mbt_render_book_section($post_id, $section_id, $content, $title='') {
	$output = '';
	$title = apply_filters('mbt_book_section_title', $title, $post_id, $section_id);
	$content = apply_filters('mbt_book_section_content', $content, $post_id, $section_id);
	if(!empty($content)) {
		$output .= '<a class="mbt-book-anchor" id="mbt-book-'.$section_id.'-anchor" name="mbt-book-'.$section_id.'-anchor"></a>';
		$output .= '<div class="mbt-book-section mbt-book-'.$section_id.'-section">';
		if($title) { $output .= '<div class="mbt-book-section-title">'.$title.'</div>'; }
		$output .= '<div class="mbt-book-section-content">'.$content.'</div>';
		$output .= '</div>';
	}
	return apply_filters('mbt_render_book_section', $output, $post_id, $section_id, $content, $title);
}



/*---------------------------------------------------------*/
/* Importers                                               */
/*---------------------------------------------------------*/

function mbt_get_importers() {
	return apply_filters('mbt_importers', array());
}

function mbt_add_disabled_importers($importers) {
	$importers['amazon'] = array(
		'name' => __('Amazon Bulk Book Importer', 'mybooktable'),
		'desc' => __('Import your books in bulk from Amazon with a list of ISBNs.', 'mybooktable'),
		'disabled' => mbt_get_upgrade_message(),
	);
	$importers['uiee'] = array(
		'name' => __('UIEE File', 'mybooktable'),
		'desc' => __('Import your books from a UIEE (Universal Information Exchange Environment) File.', 'mybooktable'),
		'disabled' => mbt_get_upgrade_message(),
	);
	return $importers;
}
add_filter('mbt_importers', 'mbt_add_disabled_importers', 9);

function mbt_import_book($book) {
	$defaults = array(
		'post_status' => 'publish',
		'source_id' => null,
		'title' => '',
		'content' => '',
		'excerpt' => '',
		'authors' => array(),
		'series' => array(),
		'genres' => array(),
		'publisher' => array(),
		'tags' => array(),
		'price' => '',
		'unique_id_isbn' => '',
		'unique_id_asin' => '',
		'buybuttons' => '',
		'publisher_name'  => '',
		'publisher_url'  => '',
		'publication_year' => '',
		'image_id' => '',
		'imported_book_id' => '',
		'sample_url' => '',
		'book_length' => '',
		'sale_price' => '',
		'show_instant_preview' => '',
		'series_order' => '',
		'display_mode' => '',
	);
	$book = array_merge($defaults, $book);
	if(!empty($book['unique_id'])) { $book['unique_id_isbn'] = $book['unique_id']; }

	if(!empty($book['imported_book_id']) and ($imported_book = get_post($book['imported_book_id']))) {
		$post_id = wp_update_post(array(
			'ID' => $imported_book->ID,
			'post_title' => $book['title'],
		));
		$old_buybuttons = get_post_meta($post_id, 'mbt_buybuttons', true);
		if(empty($old_buybuttons)) { update_post_meta($post_id, 'mbt_buybuttons', $book['buybuttons']); }
		if(!empty($book['image_id'])) { update_post_meta($post_id, 'mbt_book_image_id', $book['image_id']); }
		if(!empty($book['price'])) { update_post_meta($post_id, 'mbt_price', $book['price']); }
		if(!empty($book['unique_id_isbn'])) { update_post_meta($post_id, 'unique_id_isbn', $book['unique_id_isbn']); }
		if(!empty($book['unique_id_asin'])) { update_post_meta($post_id, 'unique_id_asin', $book['unique_id_asin']); }
		if(!empty($book['publisher_name'])) { update_post_meta($post_id, 'mbt_publisher_name', $book['publisher_name']); }
		if(!empty($book['publisher_url'])) { update_post_meta($post_id, 'mbt_publisher_url', $book['publisher_url']); }
		if(!empty($book['publication_year'])) { update_post_meta($post_id, 'mbt_publication_year', $book['publication_year']); }
		if(!empty($book['authors'])) { wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['authors'], 'mbt_author'), 'mbt_author'); }
		if(!empty($book['series'])) { wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['series'], 'mbt_series'), 'mbt_series'); }
		if(!empty($book['genres'])) { wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['genres'], 'mbt_genre'), 'mbt_genre'); }
		if(!empty($book['publishers'])) { wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['publishers'], 'mbt_publisher'), 'mbt_publisher'); }
		if(!empty($book['tags'])) { wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['tags'], 'mbt_tag'), 'mbt_tag'); }
		if(!empty($book['sample_url'])) { update_post_meta($post_id, 'mbt_sample_url', $book['sample_url']); }
		if(!empty($book['book_length'])) { update_post_meta($post_id, 'mbt_book_length', $book['book_length']); }
		if(!empty($book['sale_price'])) { update_post_meta($post_id, 'mbt_sale_price', $book['sale_price']); }
		if(!empty($book['show_instant_preview'])) { update_post_meta($post_id, 'mbt_show_instant_preview', $book['show_instant_preview']); }
		if(!empty($book['series_order'])) { update_post_meta($post_id, 'mbt_series_order', $book['series_order']); }
		if(!empty($book['display_mode'])) { update_post_meta($post_id, 'mbt_display_mode', $book['display_mode']); }
	} else {
		$post_id = wp_insert_post(array(
			'post_title' => $book['title'],
			'post_content' => $book['content'],
			'post_excerpt' => $book['excerpt'],
			'post_status' => $book['post_status'],
			'post_type' => 'mbt_book'
		));
		update_post_meta($post_id, 'mbt_buybuttons', $book['buybuttons']);
		update_post_meta($post_id, 'mbt_book_image_id', $book['image_id']);
		update_post_meta($post_id, 'mbt_price', $book['price']);
		update_post_meta($post_id, 'mbt_unique_id_isbn', $book['unique_id_isbn']);
		update_post_meta($post_id, 'mbt_unique_id_asin', $book['unique_id_asin']);
		update_post_meta($post_id, 'mbt_publisher_name', $book['publisher_name']);
		update_post_meta($post_id, 'mbt_publisher_url', $book['publisher_url']);
		update_post_meta($post_id, 'mbt_publication_year', $book['publication_year']);
		wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['authors'], 'mbt_author'), 'mbt_author');
		wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['series'], 'mbt_series'), 'mbt_series');
		wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['genres'], 'mbt_genre'), 'mbt_genre');
		wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['publishers'], 'mbt_publisher'), 'mbt_publisher');
		wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['tags'], 'mbt_tag'), 'mbt_tag');
		update_post_meta($post_id, 'mbt_sample_url', $book['sample_url']);
		update_post_meta($post_id, 'mbt_book_length', $book['book_length']);
		update_post_meta($post_id, 'mbt_sale_price', $book['sale_price']);
		update_post_meta($post_id, 'mbt_show_instant_preview', $book['show_instant_preview']);
		update_post_meta($post_id, 'mbt_series_order', $book['series_order']);
		update_post_meta($post_id, 'mbt_display_mode', $book['display_mode']);

		if(!empty($book['source_id'])) { update_post_meta($book['source_id'], 'mbt_imported_book_id', $post_id); }
	}

	return $post_id;
}

function mbt_import_taxonomy_terms($term_names, $taxonomy) {
	$returns = array();
	foreach($term_names as $name) {
		if(term_exists($name, $taxonomy)) {
			$new_term = (array)get_term_by('name', $name, $taxonomy);
		} else {
			$new_term = (array)wp_insert_term($name, $taxonomy);
		}
		$returns[] = $new_term['term_id'];
	}
	return $returns;
}



/*---------------------------------------------------------*/
/* Pages                                                   */
/*---------------------------------------------------------*/

function mbt_add_custom_page($name, $function, $permissions="edit_posts") {
	$add_sort_books_page = function() use ($name, $permissions, $function) {
		add_submenu_page("mbt_dashboard", "", "", $permissions, $name, $function);
	};

	$remove_sort_books_page = function() use ($name) {
		remove_submenu_page("mbt_dashboard", $name);
	};

	add_action('admin_menu', $add_sort_books_page, 9);
	add_action('admin_head', $remove_sort_books_page);
}

function mbt_get_custom_page_url($name) {
	return admin_url('admin.php?page='.$name);
}



/*---------------------------------------------------------*/
/* Styles                                                  */
/*---------------------------------------------------------*/

function mbt_image_url($image) {
	$url = mbt_current_style_url($image);
	return apply_filters('mbt_image_url', empty($url) ? plugins_url('styles/'.mbt_get_default_style_pack().'/'.$image, dirname(__FILE__)) : $url, $image);
}

function mbt_current_style_url($file) {
	$style = mbt_get_setting('style_pack');
	if(empty($style)) { $style = mbt_get_default_style_pack(); }

	$url = mbt_style_url($file, $style);
	if(empty($url) and $style !== mbt_get_default_style_pack()) { $url = mbt_style_url($file, mbt_get_default_style_pack()); }

	return $url;
}

function mbt_style_url($file, $style) {
	foreach(mbt_get_style_folders() as $folder) {
		if(file_exists($folder['dir'].'/'.$style)) {
			if(file_exists($folder['dir'].'/'.$style.'/'.$file)) {
				return $folder['url'].'/'.rawurlencode($style).'/'.$file;
			}
		}
	}

	$meta = mbt_get_style_pack_meta($style);
	if($meta['template']) { return mbt_style_url($file, $meta['template']); }

	return '';
}

function mbt_get_style_packs() {
	$folders = mbt_get_style_folders();
	$styles = array();

	foreach($folders as $folder) {
		if(file_exists($folder['dir']) and $handle = opendir($folder['dir'])) {
			while(false !== ($entry = readdir($handle))) {
				if ($entry != '.' and $entry != '..' and !in_array($entry, $styles)) {
					$styles[] = $entry;
				}
			}
			closedir($handle);
		}
	}

	sort($styles);
	return $styles;
}

function mbt_get_style_pack_meta($style) {
	$default_headers = array(
		'name' => 'Style Pack Name',
		'stylepack_uri' => 'Style Pack URI',
		'template' => 'Template',
		'version' => 'Version',
		'desc' => 'Description',
		'author' => 'Author',
		'author_uri' => 'Author URI',
	);

	$data = array(
		'name' => $style,
		'stylepack_uri' => '',
		'template' => '',
		'version' => '',
		'desc' => '',
		'author' => '',
		'author_uri' => '',
	);

	$readme = '';
	foreach(mbt_get_style_folders() as $folder) {
		if(file_exists($folder['dir'].'/'.$style)) {
			if(file_exists($folder['dir'].'/'.$style.'/readme.txt')) {
				$readme = $folder['dir'].'/'.$style.'/readme.txt';
				break;
			}
		}
	}
	if($readme) { $data = get_file_data($readme, $default_headers, 'mbt_style_pack'); }

	return $data;
}

function mbt_get_default_style_pack() {
	return apply_filters('mbt_default_style_pack', 'silver');
}

function mbt_get_style_folders() {
	return apply_filters('mbt_style_folders', array());
}

function mbt_add_default_style_folder($folders) {
	$folders[] = array('dir' => plugin_dir_path(dirname(__FILE__)).'styles', 'url' => plugins_url('styles', dirname(__FILE__)));
	return $folders;
}
add_filter('mbt_style_folders', 'mbt_add_default_style_folder', 100);

function mbt_add_uploaded_style_folder($folders) {
	$upload_dir = wp_upload_dir();
	$folders[] = array('dir' => $upload_dir['basedir'].DIRECTORY_SEPARATOR.'mbt_styles', 'url' => $upload_dir['baseurl'].'/'.'mbt_styles');
	return $folders;
}
add_filter('mbt_style_folders', 'mbt_add_uploaded_style_folder', 100);



/*---------------------------------------------------------*/
/* Analytics                                               */
/*---------------------------------------------------------*/

function mbt_init_tracking() {
	if(mbt_get_setting('allow_tracking') !== 'yes') { return; }

	if(!wp_next_scheduled('mbt_periodic_tracking')) { wp_schedule_event(time(), 'weekly', 'mbt_periodic_tracking'); }
	add_action('mbt_periodic_tracking', 'mbt_send_tracking_data');
}
add_action('mbt_init', 'mbt_init_tracking');

function mbt_load_tracking_data() {
	global $mbt_tracking_data;
	if(empty($mbt_tracking_data)) {
		mt_srand(time());
		$mbt_tracking_data = get_option('mbt_tracking_data');
		if(empty($mbt_tracking_data)) {
			$payload = strval(get_bloginfo('url')).strval(time()).strval(rand());
			if(function_exists('hash')) {
				$id = hash('sha256', $payload);
			} else {
				$id = sha1($payload);
			}

			$mbt_tracking_data = array(
				'id' => $id,
				'events' => array(),
				'ab_status' => array(),
			);

			update_option('mbt_tracking_data', $mbt_tracking_data);
		}
	}
}

function mbt_get_tracking_data($name) {
	global $mbt_tracking_data;
	mbt_load_tracking_data();
	return isset($mbt_tracking_data[$name]) ? $mbt_tracking_data[$name] : NULL;
}

function mbt_update_tracking_data($name, $value) {
	global $mbt_tracking_data;
	mbt_load_tracking_data();
	$mbt_tracking_data[$name] = $value;
	update_option('mbt_tracking_data', $mbt_tracking_data);
}

function mbt_track_event($name, $instance=false) {
	$events = mbt_get_tracking_data('events');
	if(!isset($events[$name])) { $events[$name] = array(); }
	if(!isset($events[$name]['count'])) { $events[$name]['count'] = 0; }
	$events[$name]['count'] += 1;
	$events[$name]['last_time'] = time();

	if($instance !== false) {
		if(!is_array($instance)) { $instance = array(); }
		$instance['time'] = time();
		if(!isset($events[$name]['instances'])) { $events[$name]['instances'] = array(); }
		$events[$name]['instances'][] = $instance;
	}

	mbt_update_tracking_data('events', $events);
}

function mbt_send_tracking_data() {
	if(mbt_get_setting('allow_tracking') !== 'yes') { return; }

	$books_query = new WP_Query(array('post_type' => 'mbt_book', 'posts_per_page' => -1));
	$books = $books_query->posts;

	$book_metas = array_map(create_function('$post', 'return get_post_custom($post->ID);'), $books);

	$num_sample_chapters = 0;
	$num_prices = 0;
	$num_isbns = 0;
	$num_asins = 0;
	$num_publisher_names = 0;
	$display_mode_usage = array();
	foreach($book_metas as $meta) {
		if(!empty($meta['mbt_sample_url'][0])) { $num_sample_chapters++; }
		if(!empty($meta['mbt_price'][0])) { $num_prices++; }
		if(!empty($meta['mbt_unique_id_isbn'][0])) { $num_isbns++; }
		if(!empty($meta['mbt_unique_id_asin'][0])) { $num_asins++; }
		if(!empty($meta['mbt_publisher_name'][0])) { $num_publisher_names++; }
		if(!empty($meta['mbt_display_mode'][0])) {
			$mode = $meta['mbt_display_mode'][0];
			if(!isset($display_modes[$mode])) { $display_modes[$mode] = 1; } else { $display_modes[$mode] += 1; }
		}
	}

	$stores = mbt_get_stores();
	$buybuttons_stats = array();
	$buybuttons_display = array('button' => 0, 'text' => 0);
	foreach($book_metas as $meta) {
		if(!empty($meta['mbt_buybuttons'][0])) {
			$buybuttons = maybe_unserialize($meta['mbt_buybuttons'][0]);
			if(is_array($buybuttons)) {
				foreach($buybuttons as $buybutton) {
					$store = $buybutton['store']; $display = $buybutton['display'];
					if(!isset($buybuttons_stats[$store])) { $buybuttons_stats[$store] = 0; } else { $buybuttons_stats[$store] += 1; }
					if(!isset($buybuttons_display[$display])) { $buybuttons_display[$display] = 0; } else { $buybuttons_display[$display] += 1; }
				}
			}
		}
	}

	$amazon_affiliates = mbt_get_setting('amazon_buybutton_affiliate_code');
	$linkshare_affiliates = mbt_get_setting('linkshare_affiliate_code');
	$cj_affiliates = mbt_get_setting('cj_website_id');
	$goodreads_integration = mbt_get_setting('goodreads_developer_key');
	$aws_integration = mbt_get_setting('aws_access_key_id');
	$mailchimp_integration = mbt_get_setting('mailchimp_api_key');
	$email_updates_list = mbt_get_setting('email_updates_list');
	$genius_link_integration = mbt_get_setting('genius_link_tsid');
	$google_integration = mbt_get_setting('google_api_key');

	$data = array(
		'id' => mbt_get_tracking_data('id'),
		'time' => time(),
		'version' => MBT_VERSION,
		'settings' => array(
			'installed_examples' => mbt_get_setting('installed_examples'),
			'compatibility_mode' => mbt_get_setting('compatibility_mode'),
			'style_pack' => mbt_get_setting('style_pack'),
			'image_size' => mbt_get_setting('image_size'),
			'reviews_type' => mbt_get_setting('reviews_type'),
			'enable_socialmedia_single_book' => mbt_get_setting('enable_socialmedia_single_book'),
			'enable_socialmedia_book_excerpt' => mbt_get_setting('enable_socialmedia_book_excerpt'),
			'enable_seo' => mbt_get_setting('enable_seo'),
			'buybutton_shadowbox' => mbt_get_setting('buybutton_shadowbox'),
			'enable_breadcrumbs' => mbt_get_setting('enable_breadcrumbs'),
			'show_series' => mbt_get_setting('show_series'),
			'book_button_size' => mbt_get_setting('book_button_size'),
			'listing_button_size' => mbt_get_setting('listing_button_size'),
			'widget_button_size' => mbt_get_setting('widget_button_size'),
			'posts_per_page' => mbt_get_setting('posts_per_page'),
			'enable_default_affiliates' => (mbt_get_setting('enable_default_affiliates') or mbt_get_upgrade()),
			'product_name' => mbt_get_setting('product_name'),
			'hide_domc_notice' => mbt_get_setting('hide_domc_notice'),
			'using_goodreads_integration' => !empty($goodreads_integration),
			'using_google_integration' => !empty($google_integration),
			'display_mode_usage' => $display_modes,
		),
		'upgrade' => array(
			'name' => mbt_get_upgrade(),
			'version' => mbt_get_upgrade_version(),
			'settings' => array(
				'using_amazon_affiliates' => !empty($amazon_affiliates),
				'using_linkshare_affiliates' => !empty($linkshare_affiliates),
				'using_cj_affiliates' => !empty($cj_affiliates),
				'using_aws_integration' => !empty($aws_integration),
				'using_mailchimp_integration' => !empty($mailchimp_integration),
				'using_genius_link_integration' => !empty($genius_link_integration),
				'disable_amazon_affiliates' => mbt_get_setting('disable_amazon_affiliates'),
				'disable_linkshare_affiliates' => mbt_get_setting('disable_linkshare_affiliates'),
				'disable_cj_affiliates' => mbt_get_setting('disable_cj_affiliates'),
				'enable_gridview' => mbt_get_setting('enable_gridview'),
				'using_email_updates_form' => !empty($email_updates_list),
			)
		),
		'statistics' => array(
			'num_books' => count($books),
			'num_sample_chapters' => $num_sample_chapters,
			'num_prices' => $num_prices,
			'num_isbns' => $num_isbns,
			'num_asins' => $num_asins,
			'num_publisher_names' => $num_publisher_names,
			'buybuttons' => $buybuttons_stats,
			'buybuttons_display' => $buybuttons_display,
		),
		'events' => mbt_get_tracking_data('events'),
		'ab_status' => mbt_get_tracking_data('ab_status'),
		'plugins' => array(
			'has_mybookprogress' => defined('MBP_VERSION'),
			'has_myspeakingpage' => defined('MSP_VERSION'),
			'has_myspeakingevents' => defined('MSE_VERSION'),
		),
	);

	global $wp_version;
	$options = array(
		'timeout' => ((defined('DOING_CRON') && DOING_CRON) ? 30 : 3),
		'body' => array('data' => serialize($data)),
		'user-agent' => 'WordPress/'.$wp_version
	);

	$response = wp_remote_post('http://api.authormedia.com/plugins/mybooktable/analytics/submit', $options);
}

function mbt_get_ab_testing_status($name=false, $options=array(true,false)) {
	$ab_status = mbt_get_tracking_data('ab_status');
	if(!isset($ab_status[$name])) {
		$i = mt_rand(0, count($options)-1);
		$ab_status[$name] = $options[$i];
		mbt_update_tracking_data('ab_status', $ab_status);
	}
	return $ab_status[$name];
}



/*---------------------------------------------------------*/
/* API / Upgrades                                          */
/*---------------------------------------------------------*/

function mbt_verify_api_key() {
	global $wp_version;

	$to_send = array(
		'action' => 'basic_check',
		'version' => MBT_VERSION,
		'api-key' => mbt_get_setting('api_key'),
		'site' => get_bloginfo('url')
	);

	$options = array(
		'timeout' => 3,
		'body' => $to_send,
		'user-agent' => 'WordPress/'.$wp_version
	);

	$raw_response = wp_remote_post('http://api.authormedia.com/plugins/apikey/check', $options);

	if(is_wp_error($raw_response) || 200 != wp_remote_retrieve_response_code($raw_response)) {
		mbt_update_setting('api_key_status', -1);
		mbt_update_setting('api_key_message', __('Unable to connect to server!', 'mybooktable'));
		return;
	}

	$response = maybe_unserialize(wp_remote_retrieve_body($raw_response));

	if(!is_array($response) or empty($response['status'])) {
		mbt_update_setting('api_key_status', -2);
		mbt_update_setting('api_key_message', __('Invalid response received from server', 'mybooktable'));
		return;
	}

	$status = $response['status'];

	if($status >= 10) {
		$permissions = array();
		if(!empty($response['permissions']) and is_array($response['permissions'])) {
			$permissions = $response['permissions'];
		}

		if(in_array('mybooktable-dev3', $permissions)) {
			mbt_update_setting('api_key_status', $status);
			mbt_update_setting('upgrade_active', 'mybooktable-dev3');
			mbt_update_setting('api_key_message', __('Valid for MyBookTable Developer 3.0', 'mybooktable'));
		} else if(in_array('mybooktable-pro3', $permissions)) {
			mbt_update_setting('api_key_status', $status);
			mbt_update_setting('upgrade_active', 'mybooktable-pro3');
			mbt_update_setting('api_key_message', __('Valid for MyBookTable Professional 3.0', 'mybooktable'));
		} else if(in_array('mybooktable-dev2', $permissions)) {
			mbt_update_setting('api_key_status', $status);
			mbt_update_setting('upgrade_active', 'mybooktable-dev2');
			mbt_update_setting('api_key_message', __('Valid for MyBookTable Developer 2.0', 'mybooktable'));
		} else if(in_array('mybooktable-pro2', $permissions)) {
			mbt_update_setting('api_key_status', $status);
			mbt_update_setting('upgrade_active', 'mybooktable-pro2');
			mbt_update_setting('api_key_message', __('Valid for MyBookTable Professional 2.0', 'mybooktable'));
		} else if(in_array('mybooktable-dev', $permissions)) {
			mbt_update_setting('api_key_status', $status);
			mbt_update_setting('upgrade_active', 'mybooktable-dev');
			mbt_update_setting('api_key_message', __('Valid for MyBookTable Developer 1.0', 'mybooktable'));
		} else if(in_array('mybooktable-pro', $permissions)) {
			mbt_update_setting('api_key_status', $status);
			mbt_update_setting('upgrade_active', 'mybooktable-pro');
			mbt_update_setting('api_key_message', __('Valid for MyBookTable Professional 1.0', 'mybooktable'));
		} else {
			mbt_update_setting('api_key_status', -20);
			mbt_update_setting('api_key_message', __('Permissions error!', 'mybooktable'));
			mbp_update_setting('upgrade_active', false);
		}
	} else if($status == -10) {
		mbt_update_setting('api_key_status', $status);
		mbt_update_setting('api_key_message', __('Key not found', 'mybooktable'));
		mbt_update_setting('upgrade_active', false);
	} else if($status == -11) {
		mbt_update_setting('api_key_status', $status);
		mbt_update_setting('api_key_message', __('Key has been deactivated', 'mybooktable'));
		mbt_update_setting('upgrade_active', false);
	} else {
		mbt_update_setting('api_key_status', -2);
		mbt_update_setting('api_key_message', __('Invalid response received from server', 'mybooktable'));
	}
}

function mbt_init_api_key_check() {
	if(!wp_next_scheduled('mbt_periodic_api_key_check')) { wp_schedule_event(time(), 'weekly', 'mbt_periodic_api_key_check'); }
	add_action('mbt_periodic_api_key_check', 'mbt_verify_api_key');
}
add_action('mbt_init', 'mbt_init_api_key_check');

function mbt_get_upgrade() {
	$upgrade_active = mbt_get_setting('upgrade_active');
	return empty($upgrade_active) ? false : $upgrade_active;
}

function mbt_get_upgrade_version() {
	$upgrade = mbt_get_upgrade();
	if($upgrade == 'mybooktable-dev3' and defined('MBTDEV3_VERSION')) { return MBTDEV3_VERSION; }
	if($upgrade == 'mybooktable-pro3' and defined('MBTPRO3_VERSION')) { return MBTPRO3_VERSION; }
	if($upgrade == 'mybooktable-dev2' and defined('MBTDEV2_VERSION')) { return MBTDEV2_VERSION; }
	if($upgrade == 'mybooktable-pro2' and defined('MBTPRO2_VERSION')) { return MBTPRO2_VERSION; }
	if($upgrade == 'mybooktable-dev' and defined('MBTDEV_VERSION')) { return MBTDEV_VERSION; }
	if($upgrade == 'mybooktable-pro' and defined('MBTPRO_VERSION')) { return MBTPRO_VERSION; }
	return false;
}

function mbt_get_upgrade_plugin_exists($active=true) {
	if(!$active) { return defined('MBTDEV3_VERSION') or defined('MBTPRO3_VERSION') or defined('MBTDEV2_VERSION') or defined('MBTPRO2_VERSION') or defined('MBTDEV_VERSION') or defined('MBTPRO_VERSION'); }
	$upgrade = mbt_get_upgrade();
	if($upgrade == 'mybooktable-dev3') { return defined('MBTDEV3_VERSION'); }
	if($upgrade == 'mybooktable-pro3') { return defined('MBTPRO3_VERSION'); }
	if($upgrade == 'mybooktable-dev2') { return defined('MBTDEV2_VERSION'); }
	if($upgrade == 'mybooktable-pro2') { return defined('MBTPRO2_VERSION'); }
	if($upgrade == 'mybooktable-dev')  { return defined('MBTDEV_VERSION'); }
	if($upgrade == 'mybooktable-pro')  { return defined('MBTPRO_VERSION'); }
	return false;
}

function mbt_get_upgrade_message($require_upgrade=true, $upgrade_text=null, $thankyou_text=null) {
	if(mbt_get_upgrade()) {
		if(mbt_get_upgrade_plugin_exists()) {
			if($require_upgrade) {
				return '<a href="http://www.authormedia.com/all-products/mybooktable/upgrades/" target="_blank">'.($upgrade_text !== null ? $upgrade_text : __('Upgrade your MyBookTable to enable these advanced features!', 'mybooktable')).'</a>';
			} else {
				return ($thankyou_text !== null ? $thankyou_text : (__('Thank you for purchasing a MyBookTable Upgrade!', 'mybooktable').' <a href="http://authormedia.freshdesk.com/support/home" target="_blank">'.__('Get premium support.', 'mybooktable').'</a>'));
			}
		} else {
			return '<a href="'.admin_url('admin.php?page=mbt_dashboard&subpage=mbt_get_upgrade_page').'">'.__('Download your MyBookTable Upgrade plugin to enable your advanced features!', 'mybooktable').'</a>';
		}
	} else {
		if(mbt_get_upgrade_plugin_exists(false)) {
			$api_key = mbt_get_setting('api_key');
			if(empty($api_key)) {
				return '<a href="'.admin_url('admin.php?page=mbt_settings').'">'.__('Insert your License Key to enable your advanced features!', 'mybooktable').'</a>';
			} else {
				return '<a href="'.admin_url('admin.php?page=mbt_settings').'">'.__('Update your License Key to enable your advanced features!', 'mybooktable').'</a>';
			}
		} else {
			return '<a href="http://www.authormedia.com/all-products/mybooktable/upgrades/" target="_blank">'.($upgrade_text !== null ? $upgrade_text : __('Upgrade your MyBookTable to enable these advanced features!', 'mybooktable')).'</a>';
		}
	}
}

<?php

/*---------------------------------------------------------*/
/* Check for Updates                                       */
/*---------------------------------------------------------*/

function mbt_update_check() {
	$version = mbt_get_setting('version');

	if(version_compare($version, '1.1.0') < 0) { mbt_update_1_1_0(); }
	if(version_compare($version, '1.1.3') < 0) { mbt_update_1_1_3(); }
	if(version_compare($version, '1.1.4') < 0) { mbt_update_1_1_4(); }
	if(version_compare($version, '1.2.7') < 0) { mbt_update_1_2_7(); }
	if(version_compare($version, '1.3.1') < 0) { mbt_update_1_3_1(); }
	if(version_compare($version, '1.3.8') < 0) { mbt_update_1_3_8(); }
	if(version_compare($version, '2.0.1') < 0) { mbt_update_2_0_1(); }
	if(version_compare($version, '2.0.4') < 0) { mbt_update_2_0_4(); }
	if(version_compare($version, '2.1.0') < 0) { mbt_update_2_1_0(); }
	if(version_compare($version, '2.2.0') < 0) { mbt_update_2_2_0(); }
	if(version_compare($version, '2.3.0') < 0) { mbt_update_2_3_0(); }
	if(version_compare($version, '3.0.0') < 0) { mbt_update_3_0_0(); }

	if($version !== MBT_VERSION) {
		mbt_update_setting('version', MBT_VERSION);
		mbt_track_event('plugin_updated', array('version' => MBT_VERSION));
	}
}

function mbt_update_1_1_0() {
	if(mbt_get_setting('compatibility_mode') !== false) {
		mbt_update_setting('compatibility_mode', true);
	}
}

function mbt_update_1_1_3() {
	global $wpdb;
	$books = $wpdb->get_col('SELECT ID FROM '.$wpdb->posts.' WHERE post_type = "mbt_book"');
	if(!empty($books)) {
		foreach($books as $book_id) {
			$image_id = get_post_meta($book_id, '_thumbnail_id', true);
			$mbt_book_image_id = get_post_meta($book_id, 'mbt_book_image_id', true);
			if(empty($mbt_book_image_id) && !empty($image_id)) { update_post_meta($book_id, 'mbt_book_image_id', $image_id); }
		}
	}
}

function mbt_update_1_1_4() {
	global $wpdb;
	$books = $wpdb->get_col('SELECT ID FROM '.$wpdb->posts.' WHERE post_type = "mbt_book"');
	if(!empty($books)) {
		foreach($books as $book_id) {
			delete_post_meta($book_id, '_thumbnail_id');

			$buybuttons = get_post_meta($book_id, 'mbt_buybuttons', true);
			if(is_array($buybuttons) and !empty($buybuttons)) {
				for($i = 0; $i < count($buybuttons); $i++)
				{
					if($buybuttons[$i]['type']) {
						$buybuttons[$i]['store'] = $buybuttons[$i]['type'];
						unset($buybuttons[$i]['type']);
					}
				}
			}
			update_post_meta($book_id, 'mbt_buybuttons', $buybuttons);
		}
	}
}

function mbt_update_1_2_7() {
	if(mbt_get_setting('enable_default_affiliates') !== false) {
		mbt_update_setting('enable_default_affiliates', true);
	}
}

function mbt_update_1_3_1() {
	mbt_update_setting('help_page_email_subscribe_popup', 'show');
	mbt_update_setting('product_name', __("Books"));
	mbt_update_setting('product_slug', _x('books', 'URL slug', 'mybooktable'));
}

function mbt_update_1_3_8() {
	mbt_update_setting('domc_notice_text', __('Disclosure of Material Connection: Some of the links in the page above are "affiliate links." This means if you click on the link and purchase the item, I will receive an affiliate commission. I am disclosing this in accordance with the Federal Trade Commission\'s <a href="http://www.access.gpo.gov/nara/cfr/waisidx_03/16cfr255_03.html" target="_blank">16 CFR, Part 255</a>: "Guides Concerning the Use of Endorsements and Testimonials in Advertising."', 'mybooktable'));
}

function mbt_update_2_0_1() {
	mbt_verify_api_key();
}

function mbt_update_2_0_4() {
	mbt_update_setting('show_find_bookstore_buybuttons_shadowbox', true);
}

function mbt_update_2_1_0() {
	global $wpdb;
	$books = $wpdb->get_col('SELECT ID FROM '.$wpdb->posts.' WHERE post_type = "mbt_book"');
	if(!empty($books)) {
		foreach($books as $book_id) {
			$buybuttons = get_post_meta($book_id, 'mbt_buybuttons', true);
			if(is_array($buybuttons) and !empty($buybuttons)) {
				for($i = 0; $i < count($buybuttons); $i++) {
					$buybuttons[$i]['display'] = ($buybuttons[$i]['display'] == 'text_only' or $buybuttons[$i]['display'] == 'text') ? 'text' : 'button';
				}
			}
			update_post_meta($book_id, 'mbt_buybuttons', $buybuttons);
		}
	}

	mbt_update_setting('buybutton_shadowbox', mbt_get_setting('enable_buybutton_shadowbox') ? 'all' : 'none');
}

function mbt_update_2_2_0() {
	global $wpdb;
	$books = $wpdb->get_col('SELECT ID FROM '.$wpdb->posts.' WHERE post_type = "mbt_book"');
	if(!empty($books)) {
		foreach($books as $book_id) {
			update_post_meta($book_id, 'mbt_unique_id_isbn', get_post_meta($book_id, 'mbt_unique_id', true));
		}
	}
}

function mbt_update_2_3_0() {
	$style_pack = mbt_get_setting('style_pack');
	if($style_pack == 'Default') { mbt_update_setting('style_pack', 'silver'); }
	if($style_pack == 'Golden') { mbt_update_setting('style_pack', 'golden'); }
	if($style_pack == 'Blue Flat') { mbt_update_setting('style_pack', 'blue_flat'); }
	if($style_pack == 'Gold Flat') { mbt_update_setting('style_pack', 'gold_flat'); }
	if($style_pack == 'Green Flat') { mbt_update_setting('style_pack', 'green_flat'); }
	if($style_pack == 'Grey Flat') { mbt_update_setting('style_pack', 'grey_flat'); }
	if($style_pack == 'Orange Flat') { mbt_update_setting('style_pack', 'orange_flat'); }
}

function mbt_update_3_0_0() {
	mbt_update_setting('show_about_author', true);
	mbt_update_setting('reviews_type', mbt_get_setting('reviews_box'));
	mbt_update_setting('enable_socialmedia_single_book', mbt_get_setting('enable_socialmedia_badges_single_book') or mbt_get_setting('enable_socialmedia_bar_single_book'));
	mbt_update_setting('enable_socialmedia_book_excerpt', mbt_get_setting('enable_socialmedia_badges_book_excerpt'));

	$books_query = new WP_Query(array('post_type' => 'mbt_book', 'posts_per_page' => -1));
	foreach ($books_query->posts as $book) {
		update_post_meta($book->ID, 'mbt_display_mode', 'storefront');

		if(get_post_meta($book->ID, 'mbt_unique_id_asin', true) == '') {
			$buybuttons = get_post_meta($book->ID, 'mbt_buybuttons', true);
			if(is_array($buybuttons) and !empty($buybuttons)) {
				foreach($buybuttons as $buybutton) {
					if($buybutton['store'] == 'amazon' or $buybutton['store'] == 'kindle') {
						$asin = mbt_get_amazon_AISN($buybutton['url']);
						if(!empty($asin)) { update_post_meta($book->ID, 'mbt_unique_id_asin', $asin); }
						break;
					}
				}
			}
		}
	}
}



/*---------------------------------------------------------*/
/* Rewrites Check                                          */
/*---------------------------------------------------------*/

function mbt_rewrites_check_init() {
	add_action('wp_loaded', 'mbt_rewrites_check', 999);
}
add_action('mbt_init', 'mbt_rewrites_check_init');

function mbt_rewrites_check() {
	if(!mbt_check_rewrites()) {
		flush_rewrite_rules();
		if(!mbt_check_rewrites()) { add_action('admin_notices', 'mbt_rewrites_check_admin_notice'); }
	}
}

function mbt_check_rewrites() {
	global $pagenow;
	if($pagenow == 'options-permalink.php' and !empty($_GET["settings-updated"])) { return true; }

	global $wp_rewrite;
	$rules = $wp_rewrite->wp_rewrite_rules();
	if(empty($rules) or !is_array($rules)) { return true; }

	$book_page_correct = false;
	$books = new WP_Query(array('post_type' => 'mbt_book', 'post_status' => 'publish', 'posts_per_page' => 1));
	if(empty($books->posts)) {
		$book_page_correct = true;
	} else {
		$book = $books->posts[0];
		$book_page_correct = mbt_get_rewrite($rules, get_permalink($book)) === 'index.php?mbt_book=$matches[1]&page=$matches[2]';
	}

	$archive_correct = mbt_get_rewrite($rules, get_post_type_archive_link('mbt_book')) === 'index.php?post_type=mbt_book';
	$genres_correct = mbt_check_tax_rewrites($rules, 'mbt_genre');
	$publishers_correct = mbt_check_tax_rewrites($rules, 'mbt_publisher');
	$authors_correct = mbt_check_tax_rewrites($rules, 'mbt_author');
	$series_correct = mbt_check_tax_rewrites($rules, 'mbt_series');
	$tags_correct = mbt_check_tax_rewrites($rules, 'mbt_tag');

	return $archive_correct and $book_page_correct and $genres_correct and $publishers_correct and $authors_correct and $series_correct and $tags_correct;
}

function mbt_check_tax_rewrites($rules, $tax) {
	$terms = get_terms($tax);
	if(empty($terms)) { return true; }
	return mbt_get_rewrite($rules, get_term_link(reset($terms), $tax)) === 'index.php?'.$tax.'=$matches[1]';
}

function mbt_get_rewrite($rules, $url) {
	$parts = parse_url(home_url('/'));
	$default_path = $parts['path'];
	$parts = parse_url($url);
	$url = $parts['path'];
	$url = substr($url, strlen($default_path));

	foreach($rules as $match => $query) {
		if(preg_match("#^$match#", $url)) {
			return $query;
		}
	}
	return '';
}

function mbt_rewrites_check_admin_notice() {
	?>
	<div id="message" class="error">
		<p>
			<strong><?php _e('MyBookTable Rewrites Error', 'mybooktable'); ?></strong> &#8211;
			<?php _e('You have a plugin or theme that has post types or taxonomies that are conflicting with MyBookTable. MyBookTable pages will not display correctly.', 'mybooktable'); ?>
		</p>
	</div>
	<?php
}



/*---------------------------------------------------------*/
/* Admin notices                                           */
/*---------------------------------------------------------*/

function mbt_admin_notices_init() {
	add_action('admin_init', 'mbt_add_admin_notices', 20);
}
add_action('mbt_init', 'mbt_admin_notices_init');

function mbt_add_admin_notices() {
	if(!mbt_get_setting('installed')) {
		if(isset($_GET['install_mbt'])) {
			mbt_install();
			mbt_update_setting('installed', 'check_api_key');
		} elseif(isset($_GET['skip_install_mbt']) || mbt_get_setting('booktable_page') != 0) {
			mbt_update_setting('installed', 'check_api_key');
		} else {
			add_action('admin_notices', 'mbt_admin_install_notice');
		}
	}
	if(mbt_get_setting('installed') == 'check_api_key') {
		if(!mbt_get_setting('api_key') and mbt_get_upgrade_plugin_exists(false)) {
			add_action('admin_notices', 'mbt_admin_setup_api_key_notice');
		} else {
			mbt_update_setting('installed', 'setup_default_affiliates');
		}
	}
	if(mbt_get_setting('installed') == 'setup_default_affiliates') {
		if(!mbt_get_setting('enable_default_affiliates') and mbt_get_upgrade() === false and !isset($_GET['mbt_setup_default_affiliates'])) {
			add_action('admin_notices', 'mbt_admin_setup_default_affiliates_notice');
		} else {
			mbt_update_setting('installed', 'post_install');
		}
	}
	if(mbt_get_setting('installed') == 'post_install') {
		if(isset($_GET['mbt_finish_install'])) {
			do_action('mbt_installed');
			mbt_update_setting('installed', 'done');
		} else {
			add_action('admin_notices', 'mbt_admin_installed_notice');
		}
	}

	if(isset($_GET['mbt_allow_tracking'])) {
		if($_GET['mbt_allow_tracking'] === 'yes') {
			mbt_update_setting('allow_tracking', 'yes');
			mbt_track_event('tracking_allowed', true);
			mbt_send_tracking_data();
		} else if($_GET['mbt_allow_tracking'] === 'no') {
			mbt_track_event('tracking_denied', true);
			mbt_send_tracking_data();
			mbt_update_setting('allow_tracking', 'no');
		}
	}

	if(mbt_get_setting('installed') == 'done' or is_int(mbt_get_setting('installed'))) {
		if(!mbt_get_setting('api_key') and mbt_get_upgrade_plugin_exists(false)) {
			add_action('admin_notices', 'mbt_admin_setup_api_key_notice');
		} else if(mbt_get_upgrade() and !mbt_get_upgrade_plugin_exists()) {
			add_action('admin_notices', 'mbt_admin_enable_upgrade_notice');
		} else if(!mbt_get_setting('allow_tracking') and current_user_can('manage_options')) {
			add_action('admin_notices', 'mbt_admin_allow_tracking_notice');
		}
	}

	if(mbt_get_setting('email_subscribe_pointer') !== 'done') {
		wp_enqueue_style('wp-pointer');
		wp_enqueue_script('wp-pointer');
		add_action('admin_print_footer_scripts', 'mbt_email_subscribe_pointer');
	}

	if(!mbt_has_seen_new_feature('display_mode')) {
		wp_enqueue_style('wp-pointer');
		wp_enqueue_script('wp-pointer');
		add_action('admin_print_footer_scripts', 'mbt_new_feature_display_mode_pointer');
	}

	if(isset($_GET['mbt_install_examples'])) {
		mbt_install_examples();
	}

	if(isset($_GET['mbt_add_booktable_page'])) {
		mbt_add_booktable_page();
	}

	if(isset($_GET['mbt_remove_booktable_page'])) {
		mbt_update_setting('booktable_page', 0);
	}
}

function mbt_admin_install_notice() {
	?>
	<div class="mbt-admin-notice">
		<h4><?php echo('<strong>'.__('Welcome to MyBookTable', 'mybooktable').'</strong> &#8211; '.__("You're almost ready to start promoting your books", 'mybooktable').' :)'); ?></h4>
		<a class="notice-button primary" href="<?php echo(admin_url('admin.php?page=mbt_settings&install_mbt=1')); ?>"><?php _e('Install MyBookTable Pages', 'mybooktable'); ?></a>
		<a class="notice-button secondary" href="<?php echo(admin_url('admin.php?page=mbt_settings&skip_install_mbt=1')); ?>"><?php _e('Skip setup', 'mybooktable'); ?></a>
	</div>
	<?php
}

function mbt_admin_installed_notice() {
	?>
	<div id="message" class="mbt-admin-notice">
		<h4><?php echo('<strong>'.__('MyBookTable has been installed', 'mybooktable').'</strong> &#8211; '.__("You're ready to start promoting your books", 'mybooktable').' :)'); ?></h4>
		<a class="notice-button primary" href="<?php echo(admin_url('admin.php?page=mbt_help&mbt_finish_install=1')); ?>"><?php _e('Show Me How', 'mybooktable'); ?></a>
		<a class="notice-button secondary" href="<?php echo(admin_url('admin.php?page=mbt_settings&mbt_finish_install=1')); ?>"><?php _e('Thanks, I Got This', 'mybooktable'); ?></a>
	</div>
	<?php
}

function mbt_admin_setup_api_key_notice() {
	?>
	<div id="message" class="mbt-admin-notice">
		<h4><?php echo('<strong>'.__('Setup your License Key', 'mybooktable').'</strong> &#8211; '.__('MyBookTable needs your License key to enable enhanced features', 'mybooktable')); ?></h4>
		<a class="notice-button primary" href="<?php echo(admin_url('admin.php?page=mbt_settings')); ?>" data-mbt-track-event-override="admin_notice_setup_api_key_click"><?php _e('Go To Settings', 'mybooktable'); ?></a>
	</div>
	<?php
}

function mbt_admin_setup_default_affiliates_notice() {
	?>
	<div id="message" class="mbt-admin-notice">
		<h4><?php echo('<strong>'.__('Setup your Amazon and Barnes &amp; Noble Buttons', 'mybooktable').'</strong> &#8211; '.__('MyBookTable needs your input to enable these features', 'mybooktable')); ?></h4>
		<a class="notice-button primary" href="<?php echo(admin_url('admin.php?page=mbt_settings&mbt_setup_default_affiliates=1')); ?>"><?php _e('Go To Settings', 'mybooktable'); ?></a>
	</div>
	<?php
}

function mbt_admin_enable_upgrade_notice() {
	if(isset($_GET['subpage']) and $_GET['subpage'] == 'mbt_get_upgrade_page') { return; }
	?>
	<div id="message" class="mbt-admin-notice">
		<h4><?php echo('<strong>'.__('Enable your Upgrade', 'mybooktable').'</strong> &#8211; '.__('Download or Activate your MyBookTable Upgrade plugin to enable your advanced features!', 'mybooktable')); ?></h4>
		<a class="notice-button primary" href="<?php echo(admin_url('admin.php?page=mbt_dashboard&subpage=mbt_get_upgrade_page')); ?>" data-mbt-track-event-override="admin_notice_enable_upgrade_click"><?php _e('Enable', 'mybooktable'); ?></a>
	</div>
	<?php
}

function mbt_admin_allow_tracking_notice() {
	?>
	<div class="mbt-admin-notice">
		<h4><?php echo('<strong>'.__('Help Improve MyBookTable', 'mybooktable').'</strong> &#8211; '.__('Please help make MyBookTable easier to use by allowing it to gather anonymous usage statistics.', 'mybooktable')); ?></h4>
		<a class="notice-button primary" href="<?php echo(admin_url('admin.php?page=mbt_settings&mbt_allow_tracking=yes')); ?>"><?php _e('No Problem', 'mybooktable'); ?></a>
		<a class="notice-button secondary" href="<?php echo(admin_url('admin.php?page=mbt_settings&mbt_allow_tracking=no')); ?>"><?php _e('I\'d Rather Not', 'mybooktable'); ?></a>
	</div>
	<?php
}

function mbt_email_subscribe_pointer() {
	global $current_screen; global $pagenow;
	if(!($current_screen->post_type === 'mbt_book' and ((isset($_REQUEST['action']) and $_REQUEST['action'] === 'edit') or $pagenow === 'post-new.php'))) { return;}

	$current_user = wp_get_current_user();
	$email = $current_user->user_email;

	$content  = '<h3>'.htmlspecialchars(__('Learn the Secrets of Amazing Author Websites', 'mybooktable'), ENT_QUOTES).'</h3>';
	$content .= '<p>'.htmlspecialchars(__("Want an author website that doesn't just look good, but also boosts book sales? Find out in this practical (and totally free) course by Author Media CEO, Thomas Umstattd Jr.", 'mybooktable'), ENT_QUOTES).'</p>';
	$content .= '<p>'.'<input type="text" name="mbt-pointer-email" id="mbt-pointer-email" autocapitalize="off" autocorrect="off" placeholder="you@example.com" value="'.$email.'" style="width: 100%">'.'</p>';
	$content .= '<div class="mbt-pointer-buttons wp-pointer-buttons">';
	$content .= '<a id="mbt-pointer-yes" class="button-primary" style="float:left">'.htmlspecialchars(__("Let's do it!", 'mybooktable'), ENT_QUOTES).'</a>';
	$content .= '<a id="mbt-pointer-no" class="button-secondary">'.htmlspecialchars(__('No, thanks', 'mybooktable'), ENT_QUOTES).'</a>';
	$content .= '</div>';

	?>
	<script type="text/javascript">
		var mbt_email_subscribe_pointer_options = {
			pointerClass: 'mbt-email-pointer',
			content: '<?php echo($content); ?>',
			position: {edge: 'top', align: 'center'},
			buttons: function() {}
		};

		function mbt_email_subscribe_pointer_subscribe() {
			if(!/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/.test(jQuery('#mbt-pointer-email').val())) {
				jQuery('#mbt-pointer-email').addClass('error').focus();
			} else {
				mbt_track_event('admin_notice_email_subscribe_click');
				jQuery('#mbt-pointer-yes').attr('disabled', 'disabled');
				jQuery('#mbt-pointer-no').attr('disabled', 'disabled');
				jQuery('#mbt-pointer-email').attr('disabled', 'disabled');
				jQuery.post(ajaxurl,
					{
						action: 'mbt_email_subscribe_pointer',
						subscribe: 'yes',
						email: jQuery('#mbt-pointer-email').val()
					},
					function(response) {
						jQuery('.mbt-email-pointer .wp-pointer-content').html(response);
					}
				);
			}
		}

		jQuery(document).ready(function () {
			jQuery('#wpadminbar').pointer(mbt_email_subscribe_pointer_options).pointer('open');

			jQuery('#mbt-pointer-yes').click(function() {
				mbt_email_subscribe_pointer_subscribe();
			});

			jQuery('#mbt-pointer-email').keypress(function(event) {
				 if(event.which == 13) {
					mbt_email_subscribe_pointer_subscribe();
				 }
			});

			jQuery('#mbt-pointer-no').click(function() {
				mbt_track_event('admin_notice_email_subscribe_deny_click');
				jQuery.post(ajaxurl, {action: 'mbt_email_subscribe_pointer', subscribe: 'no'});
				jQuery('#wpadminbar').pointer('close');
			});

			jQuery('.mbt-email-pointer').on('click', '#mbt-pointer-close', function() {
				jQuery('#wpadminbar').pointer('close');
			});
		});
	</script>
	<?php
}

function mbt_email_subscribe_pointer_ajax() {
	if(empty($_REQUEST['subscribe'])) { die(); }
	if($_REQUEST['subscribe'] === 'yes') {
		$email = $_POST['email'];
		wp_remote_post('http://AuthorMedia.us1.list-manage1.com/subscribe/post', array(
			'method' => 'POST',
			'body' => array(
				'u' => 'b7358f48fe541fe61acdf747b',
				'id' => '6b5a675fcf',
				'MERGE0' => $email,
				'MERGE1' => '',
				'MERGE3' => '',
				'group[3045][64]' => 'on',
				'b_b7358f48fe541fe61acdf747b_6b5a675fcf' => ''
			)
		));

		$content  = '<h3>'.htmlspecialchars(__('Learn the Secrets of Amazing Author Websites', 'mybooktable'), ENT_QUOTES).'</h3>';
		$content .= '<p>'.htmlspecialchars(__('Thank you for subscribing! Please check your inbox for a confirmation letter.', 'mybooktable'), ENT_QUOTES).'</p>';
		$content .= '<div class="mbt-pointer-buttons wp-pointer-buttons">';

		$email_title = '';
		$email_link = '';
		if(strpos($email , '@yahoo') !== false) {
			$email_title = htmlspecialchars(__('Go to Yahoo! Mail', 'mybooktable'), ENT_QUOTES);
			$email_link = 'https://mail.yahoo.com/';
		} else if(strpos($email, '@hotmail') !== false) {
			$email_title = htmlspecialchars(__('Go to Hotmail', 'mybooktable'), ENT_QUOTES);
			$email_link = 'https://www.hotmail.com/';
		} else if(strpos($email, '@gmail') !== false) {
			$email_title = htmlspecialchars(__('Go to Gmail', 'mybooktable'), ENT_QUOTES);
			$email_link = 'https://mail.google.com/';
		} else if(strpos($email, '@aol') !== false) {
			$email_title = htmlspecialchars(__('Go to AOL Mail', 'mybooktable'), ENT_QUOTES);
			$email_link = 'https://mail.aol.com/';
		}
		if(!empty($email_title)) {
			$content .= '<a class="button-primary" style="float:left" href="'.$email_link.'" target="_blank">'.$email_title.'</a>';
		}

		$content .= '<a id="mbt-pointer-close" class="button-secondary">'.htmlspecialchars(__('Close', 'mybooktable'), ENT_QUOTES).'</a>';
		$content .= '</div>';
		echo($content);
	}
	mbt_update_setting('email_subscribe_pointer', 'done');
	die();
}
add_action('wp_ajax_mbt_email_subscribe_pointer', 'mbt_email_subscribe_pointer_ajax');

function mbt_new_feature_display_mode_pointer() {
	$content  = '<h3>'.htmlspecialchars(__('New!', 'mybooktable'), ENT_QUOTES).'</h3>';
	$content .= '<p>'.htmlspecialchars(__('Use the new Display Mode selector to change the way your book page looks!', 'mybooktable'), ENT_QUOTES).'</p>';
	$content .= '<div class="mbt-pointer-buttons wp-pointer-buttons">';
	$content .= '<a id="mbt-pointer-ok" class="button-primary" style="float:left">'.htmlspecialchars(__("Cool, thanks!", 'mybooktable'), ENT_QUOTES).'</a>';
	$content .= '</div>';

	mbt_new_feature_pointer('display_mode', $content);
}

function mbt_has_seen_new_feature($feature) {
	$seen_features = mbt_get_setting('seen_new_features');
	if(empty($seen_features)) { $seen_features = array(); }
	return in_array($feature, $seen_features);
}

function mbt_new_feature_pointer($feature, $content) {
	global $current_screen; global $pagenow;
	if(!($current_screen->post_type === 'mbt_book' and ((isset($_REQUEST['action']) and $_REQUEST['action'] === 'edit') or $pagenow === 'post-new.php'))) { return;}

	?>
	<script type="text/javascript">
		var mbt_new_feature_pointer_options = {
			pointerClass: 'mbt-new-feature-pointer',
			content: '<?php echo($content); ?>',
			feature: '<?php echo($feature); ?>',
			position: {edge: 'top', align: 'center'},
			buttons: function() {}
		};

		jQuery(document).ready(function () {
			jQuery('#wpadminbar').pointer(mbt_new_feature_pointer_options).on('pointeropen', function(event, data) {
				data.pointer.position({
					of: jQuery('#mbt_display_mode_field'),
					collision: 'fit none', my: 'right center', 'at': 'left center'
				}).css('position', 'absolute').removeClass( 'wp-pointer-top' ).addClass( 'wp-pointer-right' );
			}).pointer('open');

			jQuery('#mbt-pointer-ok').click(function() {
				jQuery.post(ajaxurl, {action: 'mbt_new_feature_seen', feature: mbt_new_feature_pointer_options.feature});
				jQuery('#wpadminbar').pointer('close');
			});

			jQuery('.mbt-email-pointer').on('click', '#mbt-pointer-close', function() {
				jQuery.post(ajaxurl, {action: 'mbt_new_feature_seen', feature: mbt_new_feature_pointer_options.feature});
				jQuery('#wpadminbar').pointer('close');
			});
		});
	</script>
	<?php
}

function mbt_new_feature_seen_ajax() {
	if(empty($_REQUEST['feature'])) { die(); }
	if(!mbt_has_seen_new_feature(strval($_REQUEST['feature']))) {
		$seen_features = mbt_get_setting('seen_new_features');
		if(empty($seen_features)) { $seen_features = array(); }
		$seen_features[] = strval($_REQUEST['feature']);
		mbt_update_setting('seen_new_features', $seen_features);
	}
	die();
}
add_action('wp_ajax_mbt_new_feature_seen', 'mbt_new_feature_seen_ajax');





/*---------------------------------------------------------*/
/* Installation Functions                                  */
/*---------------------------------------------------------*/

function mbt_install() {
	mbt_add_booktable_page();
	mbt_install_examples();
}

function mbt_add_booktable_page() {
	if(mbt_get_setting('booktable_page') <= 0 or !get_page(mbt_get_setting('booktable_page'))) {
		$post_id = wp_insert_post(array(
			'post_title' => __('Book Table', 'mybooktable'),
			'post_content' => '',
			'post_status' => 'publish',
			'post_type' => 'page'
		));
		mbt_update_setting("booktable_page", $post_id);
	}
}

function mbt_install_examples() {
	if(!mbt_get_setting('installed_examples')) {
		include("examples.php");
		mbt_update_setting('installed_examples', true);
	}
}

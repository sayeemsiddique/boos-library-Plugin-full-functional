<a class="mbt-book-anchor" id="mbt-book-overview-anchor" name="mbt-book-overview-anchor"></a>
<div class="mbt-book-section mbt-book-overview-section">
	<div class="mbt-book-section-title"><?php global $post; _e(apply_filters('mbt_book_section_title', 'About the Book', $post->ID, 'overview'), 'mybooktable'); ?></div>
	<div class="mbt-book-section-content">
		<div class="mbt-book-overview">
			<?php
				if(function_exists('st_remove_st_add_link')) { st_remove_st_add_link(''); }
				global $post; echo(apply_filters('the_content', $post->post_content));
			?>
		</div>
		<?php if(mbt_get_setting('enable_socialmedia_single_book')) { mbt_the_book_socialmedia_bar(); } ?>
	</div>
</div>
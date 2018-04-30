<a class="mbt-book-anchor" id="mbt-book-meta-anchor" name="mbt-book-meta-anchor"></a>
<div class="mbt-book-section mbt-book-meta-section">
	<div class="mbt-book-section-title"><?php global $post; _e(apply_filters('mbt_book_section_title', 'Details', $post->ID, 'meta'), 'mybooktable'); ?></div>
	<div class="mbt-book-section-content">
		<div class="mbt-book-meta">
			<?php mbt_the_book_authors_list(); ?>
			<?php mbt_the_book_series_list(); ?>
			<?php mbt_the_book_genres_list(); ?>
			<?php mbt_the_book_tags_list(); ?>
			<?php mbt_the_book_publishers_list(); ?>
			<?php mbt_the_book_publisher(); ?>
			<?php mbt_the_book_metadata('mbt_publication_year', __('Publication Year', 'mybooktable')); ?>
			<?php mbt_the_book_metadata('mbt_book_format', __('Format', 'mybooktable')); ?>
			<?php mbt_the_book_metadata('mbt_book_length', __('Length', 'mybooktable')); ?>
			<?php mbt_the_book_metadata('mbt_narrator', __('Narrator', 'mybooktable')); ?>
			<?php mbt_the_book_metadata('mbt_illustrator', __('Illustrator', 'mybooktable')); ?>
			<?php mbt_the_book_unique_id(); ?>
			<?php mbt_the_book_star_rating(); ?>
			<?php mbt_the_book_metadata('mbt_price', __('List Price', 'mybooktable')); ?>
			<?php mbt_the_book_metadata('mbt_ebook_price', __('eBook Price', 'mybooktable')); ?>
			<?php mbt_the_book_metadata('mbt_audiobook_price', __('Audiobook Price', 'mybooktable')); ?>
		</div>
	</div>
</div>

<?php

do_action('mbt_before_book_archive_header');
?><header class="mbt-book-archive-header"><?php
	do_action('mbt_book_archive_header_image');
	if(!mbt_has_template_context('compatability') or mbt_has_template_context('shortcode')) { do_action('mbt_book_archive_header_title'); }
	do_action('mbt_book_archive_header_description');
	?><div style="clear:both;"></div><?php
?></header><?php
do_action('mbt_before_book_archive_after');
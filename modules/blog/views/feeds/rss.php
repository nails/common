<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	>

	<channel>
		<title><?=htmlentities( APP_NAME )?></title>
		<atom:link href="<?=site_url( app_setting( 'url', 'blog' ) . 'rss' )?>" rel="self" type="application/rss+xml" />
		<link><?=site_url()?></link>
		<description><?=htmlentities( strip_tags( NAILS_APP_STRAPLINE ) )?></description>
		<lastBuildDate>Sun, 13 Apr 2014 19:49:30 +0000</lastBuildDate>
		<language>en-UK</language>
			<sy:updatePeriod>hourly</sy:updatePeriod>
			<sy:updateFrequency>1</sy:updateFrequency>
		<generator>http://nailsapp.co.uk/version/<?=NAILS_VERSION?></generator>

		<?php foreach( $posts AS $post ) : ?>

			<item>
				<title><?=htmlentities( $post->title )?></title>
				<link><?=$post->url?></link>
				<guid isPermaLink="false"><?=site_url( app_setting( 'url', 'blog' ) . '?id=' . $post->id )?></guid>
				<?=app_setting( 'comments_enabled', 'blog' ) ? '<comments>' . $post->url . '#comments</comments>' : ''?>
				<pubDate><?=date( 'r', strtotime( $post->published ) )?></pubDate>
				<dc:creator><![CDATA[<?=$post->author->first_name . ' ' . $post->author->last_name?>]]></dc:creator>
				<description><![CDATA[<?=$post->excerpt?>]]></description>
				<content:encoded><![CDATA[<?php

					if ( $post->image_id ) :

						echo '<p>' . img( cdn_serve( $post->image_id ) ) . '</p<name />';

					endif;

					echo $post->body;


				?>]]></content:encoded>
			</item>

		<?php endforeach; ?>

	</channel>
</rss>
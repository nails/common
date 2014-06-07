<hr />
<?php

	$_shortname = app_setting( 'comments_disqus_shortname', 'blog' );

	if ( $_shortname ) :

		?>
		<div id="disqus_thread"></div>
		<script type="text/javascript">
			/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
			var disqus_shortname = '<?=$_shortname?>'; // required: replace example with your forum shortname

			/* * * DON'T EDIT BELOW THIS LINE * * */
			(function() {
				var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
				dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
				(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
			})();
		</script>
		<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
		<a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
		<?php

	elseif( $this->user->is_admin() ) :

		?>
		<p class="alert alert-danger">
			<strong>Disqus is not configured correctly</strong>
			<br />A shortname has not been specified for this website. Please create a shortname and define it in this site's settings.
		</p>
		<?php

	endif;
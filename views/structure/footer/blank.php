        <script type="text/javascript">
            var ENVIRONMENT         = '<?=nailsEnvironment('get')?>';
            window.SITE_URL         = '<?=site_url('', isPageSecure())?>';
            window.NAILS            = {};
            window.NAILS.URL        = '<?=NAILS_ASSETS_URL?>';
            window.NAILS.LANG       = {};
            window.NAILS.USER       = {};
            window.NAILS.USER.ID    = <?=activeUser('id') ? activeUser('id') : 'null'?>;
            window.NAILS.USER.FNAME = '<?=addslashes(activeUser('first_name'))?>';
            window.NAILS.USER.LNAME = '<?=addslashes(activeUser('last_name'))?>';
            window.NAILS.USER.EMAIL = '<?=addslashes(activeUser('email'))?>';
        </script>
        <?php

        //  Load JS
        $this->asset->output('JS');
        $this->asset->output('JS-INLINE-FOOTER');

        //  Analytics
        if (appSetting('google_analytics_account')) {

            ?>
            <script>
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

                ga('create', '<?=appSetting('google_analytics_account')?>', 'auto');
                ga('send', 'pageview');

            </script>
            <?php

        }

        ?>
    </body>
</html>
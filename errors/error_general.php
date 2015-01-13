<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
    <title><?=$heading?></title>
    <style type="text/css">

        html,
        body
        {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
            color: #333;
            overflow: hidden;
            font-family: "Helvetica Neue", Helvetica, "Segoe UI", Arial, freesans, sans-serif;
            font-size: 16px;
            line-height: 1.6em;
        }

        #container {
            word-wrap: break-word;
            max-width: 600px;
            min-width: 200px;
            margin: 0 auto;
            padding: 30px;
            text-align: center;
        }

        h1
        {
            position: relative;
            margin: 0.67em 0;
            margin-top: 1em;
            margin-bottom: 16px;
            padding-bottom: 0.3em;
            font-size: 2.25em;
            font-weight: bold;
            line-height: 1.2;
        }

        h1,
        hr
        {
            border: 0;
            border-bottom: 1px solid #eee;
        }

        img
        {
            border: 0;
            max-width: 100%;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        p
        {
            margin-top: 0;
            margin-bottom: 16px;
        }

        a
        {
            background: transparent;
            color: #4183c4;
            text-decoration: none;
        }

        a:active,
        a:hover
        {
            outline: 0;
        }

        a:hover,
        a:focus,
        a:active
        {
            text-decoration: underline;
        }

        small
        {
            font-size:0.65em;
        }

    </style>
</head>
<body>
    <div id="container">
        <h1>
            <img src="<?=NAILS_ASSETS_URL?>img/nails/icon/icon@2x.png" width="125" height="125" />
        </h1>
        <?=$message?>
        <hr />
        <p>
            <small>
                Powered by <a href="http://nailsapp.co.uk">Nails</a>
            </small>
        </p>
    </div>
</body>
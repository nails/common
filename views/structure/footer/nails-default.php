    <hr />
    <div class="row">
        <p class="text-center">
            <small>
                &copy; <?=APP_NAME?> <?=date('Y') == '2014' ? '2014' : '2014-' . date('Y')?>
                <br />
                <?=lang('nails_footer_powered_by', array(NAILS_PACKAGE_URL, NAILS_PACKAGE_NAME))?>
            </small>
        </p>
    </div><!-- /.row -->
</div><!-- /.container -->
<?php

    $this->load->view('structure/footer/blank');

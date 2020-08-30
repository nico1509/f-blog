<div class="wrap">
    <h1>Facebook Blog</h1>
    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php
            settings_fields( 'facebook_blog_settings' );
            do_settings_sections( 'facebook_blog' );
            submit_button();
        ?>
    </form>
</div>

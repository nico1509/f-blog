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

    <h2>Zeitplan</h2>
    <p>Nächste Übertragung: <?= date( 'd.m.Y H:i:s e', wp_next_scheduled( Nico1509\Facebookblog\Cron\ImportTask::CRON_HOOK ) ) ?></p>
    <p>Aktuelle Serverzeit: <?= date( 'd.m.Y H:i:s e' ) ?></p>
    <p>
        <a class="button button-secondary" href="?page=facebook_blog&facebook_blog_run=1">Jetzt ausführen</a>
        <?php
            if ( isset( $_GET['facebook_blog_run'] ) ) {
                do_action( Nico1509\Facebookblog\Cron\ImportTask::CRON_HOOK );
                echo '<span class="dashicons-yes"></span>';
            }
        ?>
    </p>
</div>

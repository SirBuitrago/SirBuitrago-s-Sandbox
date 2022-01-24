<?php if ( !isset($_COOKIE['acceptedCookiePrompt']) && get_field('activate_gdpr', 'option') ) : ?>
    <div role="region" aria-label="<?php _e('Cookie consent banner', 'rpmdomain') ?>" class="cookie-banner">
        <div class="container">
            <p><?= get_field('gdpr_label', 'option') ?></p>
            <div class="cookie-banner__actions">
                <button class="accept"><?php _e('Accept', 'rpmdomain') ?></button>
                <button class="dismiss"><?php _e('Dismiss', 'rpmdomain') ?></button>
            </div>
        </div>
    </div>
<?php endif; ?>
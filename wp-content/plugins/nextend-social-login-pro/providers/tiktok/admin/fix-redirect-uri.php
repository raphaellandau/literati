<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProviderAdmin */

$provider = $this->getProvider();
?>
<ol>
    <li><?php printf(__('Navigate to %s', 'nextend-facebook-connect'), '<a href="https://developers.tiktok.com/" target="_blank">https://developers.tiktok.com/</a>'); ?></li>
    <li><?php printf(__('Log in to your %s developer account, if you are not logged in yet.', 'nextend-facebook-connect'), 'TikTok'); ?></li>
    <li><?php printf(__('On the top right corner click on %1$s then click on the name of that App that you used for the configuration.', 'nextend-facebook-connect'), '<strong>My apps</strong>'); ?></li>
    <li><?php printf(__('Scroll down to the %s section.', 'nextend-facebook-connect'), '<strong>Platform info</strong>'); ?></li>
    <li><?php
        $loginUrls = $provider->getAllRedirectUrisForAppCreation();
        printf(__('Add the following URL to the %s field:', 'nextend-facebook-connect'), '<strong>Callback URL</strong>');
        echo "<ul>";
        foreach ($loginUrls as $loginUrl) {
            echo "<li><strong>" . $loginUrl . "</strong></li>";
        }
        echo "</ul>";
        ?>
    </li>
    <li><?php printf(__('Enter your domain name into the %1$s field, if it is not added already: %2$s', 'nextend-facebook-connect'), '<strong>Redirect domain</strong>', '<strong>' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . '</strong>'); ?></li>
    <li><?php printf(__('Press the %s button.', 'nextend-facebook-connect'), '<strong>Submit</strong>'); ?></li>
    <li><?php _e('Allow some time for these modifications to take effect.', 'nextend-facebook-connect') ?></li>
</ol>
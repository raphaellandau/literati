<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProviderAdmin */

$lastUpdated = '2021-10-26';

$provider = $this->getProvider();
?>
<div class="nsl-admin-sub-content">

    <?php if (substr($provider->getLoginUrl(), 0, 8) !== 'https://'): ?>
        <div class="error">
            <p><?php printf(__('%1$s allows HTTPS OAuth Redirects only. You must move your site to HTTPS in order to allow login with %1$s.', 'nextend-facebook-connect'), 'TikTok'); ?></p>
            <p>
                <a href="https://nextendweb.com/nextend-social-login-docs/facebook-api-changes/#how-to-add-ssl-to-wordpress"><?php _e("How to get SSL for my WordPress site?", 'nextend-facebook-connect'); ?></a>
            </p>
        </div>
    <?php else: ?>
        <div class="nsl-admin-getting-started">

            <h2 class="title"><?php _e('Getting Started', 'nextend-facebook-connect'); ?></h2>

            <p><?php printf(__('To allow your visitors to log in with their %1$s account, first you must create an %1$s App. The following guide will help you through the %1$s App creation process. After you have created your %1$s App, head over to "Settings" and configure the given "%2$s" and "%3$s" according to your %1$s App.', 'nextend-facebook-connect'), "TikTok", "Client Key", "Client Secret"); ?></p>

            <p><?php do_action('nsl_getting_started_warnings', $provider, $lastUpdated); ?></p>

            <h2 class="title"><?php printf(_x('Create %s', 'App creation', 'nextend-facebook-connect'), 'TikTok App'); ?></h2>

            <ol>
                <li><?php printf(__('Navigate to %s', 'nextend-facebook-connect'), '<a href="https://developers.tiktok.com/" target="_blank">https://developers.tiktok.com/</a>'); ?></li>
                <li><?php printf(__('Log in to your %s developer account or register one if you don\'t have any!', 'nextend-facebook-connect'), 'TikTok'); ?></li>
                <li><?php printf(__('On the top right corner click on %1$s then click on the %2$s option.', 'nextend-facebook-connect'), '<strong>My apps</strong>', '<strong>Connect a new app</strong>'); ?></li>
                <li><?php printf(__('A modal will appear where you need to select an image as %1$s and enter a name into the %2$s field.', 'nextend-facebook-connect'), '<strong>App icon</strong>', '<strong>App name</strong>'); ?></li>
                <li><?php printf(__('Press the %1$s button.', 'nextend-facebook-connect'), '<strong>Start</strong>'); ?></li>
                <li><?php printf(__('For %1$s choose the %2$s option.', 'nextend-facebook-connect'), '<strong>Platform</strong>', '<strong>Web</strong>'); ?></li>
                <li><?php printf(__('Under the %s section you should fill all of the required fields.', 'nextend-facebook-connect'), '<strong>Basic info</strong>'); ?></li>
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
                <li><?php printf(__('Enter your domain name to the %1$s field, probably: %2$s', 'nextend-facebook-connect'), '<strong>Redirect domain</strong>', '<strong>' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . '</strong>'); ?></li>
                <li><?php printf(__('Under the %1$s section you need to make sure the option %2$s is checked.', 'nextend-facebook-connect'), '<strong>Permissions</strong>', '<strong>user.info.basic</strong>'); ?></li>
                <li><?php printf(__('Into the %1$s field you should write a text, that describes what you are going to do with the App. In this particular case, you will use it to offer %2$s login option for your visitors.', 'nextend-facebook-connect'), '<strong>Reason for using</strong>', 'TikTok'); ?></li>
                <li><?php printf(__('Press the %s button.', 'nextend-facebook-connect'), '<strong>Submit</strong>'); ?></li>
                <li><?php printf(__('Wait until your App gets approved. This can take a couple of days. If you scroll up to the top of the page you will be able to find the %s below the name of your App.', 'nextend-facebook-connect'), '<strong>Application Status</strong>'); ?></li>
                <li><?php printf(__('Once the %1$s says %2$s, the %3$s and %4$s will appear below the %1$s text. You will need these for the provider configuration.', 'nextend-facebook-connect'), '<strong>Application Status</strong>', '<strong>Approved</strong>', '<strong>Client Key</strong>', '<strong>Client Secret</strong>'); ?></li>
            </ol>
            <p><?php printf(__('<b>WARNING:</b> The %1$s API can not return any email address or phone number! %2$sLearn more%3$s.', 'nextend-facebook-connect'), 'TikTok', '<a href="https://nextendweb.com/nextend-social-login-docs/provider-tiktok/#empty_email" target="_blank">', '</a>'); ?></p>

            <a href="<?php echo $this->getUrl('settings'); ?>"
               class="button button-primary"><?php printf(__('I am done setting up my %s', 'nextend-facebook-connect'), 'TikTok App'); ?></a>
        </div>
    <?php endif; ?>

</div>
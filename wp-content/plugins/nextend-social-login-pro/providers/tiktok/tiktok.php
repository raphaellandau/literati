<?php

use NSL\Notices;

class NextendSocialPROProviderTiktok extends NextendSocialProvider {

    /** @var NextendSocialProviderTiktokClient */
    protected $client;

    protected $color = '#000000';

    protected $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path fill="#25F4EE" d="M10.18 9.474v-.948c-.315-.079-.63-.079-.944-.079C5.225 8.447 2 11.684 2 15.71c0 2.448 1.258 4.658 3.067 5.921l-.078-.079c-1.18-1.263-1.81-3-1.81-4.815 0-4.026 3.147-7.184 7-7.263Z"/><path fill="#25F4EE" d="M10.337 20.053c1.81 0 3.225-1.422 3.304-3.237V1.026h2.831C16.393.711 16.393.395 16.393 0h-3.932v15.79a3.3 3.3 0 0 1-3.304 3.157c-.55 0-1.1-.158-1.494-.394.63.868 1.573 1.5 2.674 1.5ZM21.9 6.395v-.948a5.523 5.523 0 0 1-2.989-.868c.787.868 1.81 1.579 2.989 1.816Z"/><path fill="#FE2C55" d="M18.91 4.579c-.865-.947-1.337-2.21-1.337-3.632h-1.101A5.718 5.718 0 0 0 18.91 4.58Zm-9.674 7.737c-1.809 0-3.303 1.5-3.303 3.315 0 1.264.786 2.369 1.809 2.921-.394-.552-.63-1.184-.63-1.894 0-1.816 1.495-3.316 3.304-3.316.314 0 .629.079.944.158V9.474c-.315-.08-.63-.08-.944-.08h-.158v3c-.393 0-.707-.078-1.022-.078Z"/><path fill="#FE2C55" d="M21.899 6.395v3c-2.045 0-3.933-.632-5.427-1.737v8.053c0 4.026-3.225 7.263-7.236 7.263a7.43 7.43 0 0 1-4.169-1.264 7.237 7.237 0 0 0 5.27 2.29c4.011 0 7.236-3.237 7.236-7.263V8.684A9.45 9.45 0 0 0 23 10.421V6.474c-.315 0-.708 0-1.101-.08Z"/><path fill="#fff" d="M16.472 15.71V7.658a9.45 9.45 0 0 0 5.427 1.737v-3.08c-1.18-.236-2.202-.868-2.989-1.736-1.258-.79-2.123-2.132-2.36-3.632h-2.91v15.79a3.3 3.3 0 0 1-3.303 3.158c-1.101 0-2.045-.553-2.674-1.343-1.023-.473-1.73-1.578-1.73-2.842 0-1.815 1.494-3.315 3.303-3.315.315 0 .63.079.944.158v-3.08C6.247 9.554 3.1 12.79 3.1 16.738c0 1.895.708 3.631 1.966 4.973 1.18.79 2.596 1.343 4.169 1.343 4.011-.08 7.236-3.395 7.236-7.343Z"/></svg>';

    public function __construct() {
        $this->id    = 'tiktok';
        $this->label = 'TikTok';

        $this->path = dirname(__FILE__);

        $this->requiredFields = array(
            'client_key'    => 'Client Key',
            'client_secret' => 'Client Secret'
        );

        parent::__construct(array(
            'client_key'         => '',
            'client_secret'      => '',
            'login_label'        => 'Continue with <b>TikTok</b>',
            'register_label'     => 'Sign up with <b>TikTok</b>',
            'link_label'         => 'Link account with <b>TikTok</b>',
            'unlink_label'       => 'Unlink account from <b>TikTok</b>',
            'profile_image_size' => 'mini'
        ));
    }

    protected function forTranslation() {
        __('Continue with <b>TikTok</b>', 'nextend-facebook-connect');
        __('Sign up with <b>TikTok</b>', 'nextend-facebook-connect');
        __('Link account with <b>TikTok</b>', 'nextend-facebook-connect');
        __('Unlink account from <b>TikTok</b>', 'nextend-facebook-connect');
    }

    public function validateSettings($newData, $postedData) {
        $newData = parent::validateSettings($newData, $postedData);

        foreach ($postedData AS $key => $value) {

            switch ($key) {
                case 'tested':
                    if ($postedData[$key] == '1' && (!isset($newData['tested']) || $newData['tested'] != '0')) {
                        $newData['tested'] = 1;
                    } else {
                        $newData['tested'] = 0;
                    }
                    break;
                case 'client_key':
                case 'client_secret':
                    $newData[$key] = trim(sanitize_text_field($value));
                    if ($this->settings->get($key) !== $newData[$key]) {
                        $newData['tested'] = 0;
                    }

                    if (empty($newData[$key])) {
                        Notices::addError(sprintf(__('The %1$s entered did not appear to be a valid. Please enter a valid %2$s.', 'nextend-facebook-connect'), $this->requiredFields[$key], $this->requiredFields[$key]));
                    }
                    break;
                case 'profile_image_size':
                    $newData[$key] = trim(sanitize_text_field($value));
                    break;
            }
        }

        return $newData;
    }

    /**
     * @return NextendSocialAuth|NextendSocialProviderTiktokClient
     */
    public function getClient() {
        if ($this->client === null) {

            require_once dirname(__FILE__) . '/tiktok-client.php';

            $this->client = new NextendSocialProviderTiktokClient($this->id);
            $this->client->setClientId($this->settings->get('client_key'));
            $this->client->setClientSecret($this->settings->get('client_secret'));
            $this->client->setRedirectUri($this->getRedirectUriForOAuthFlow());
        }

        return $this->client;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getCurrentUserInfo() {
        $user = $this->getClient()
                     ->get("/userinfo/");

        if (isset($user['data'])) {
            return $user['data'];
        }

        return $user;
    }

    public function getAuthUserData($key) {
        switch ($key) {
            case 'id':
                return $this->authUserData['union_id'];
            case 'email':
                return '';
            case 'name':
                return $this->authUserData['display_name'];
            case 'first_name':
                $name = explode(' ', $this->getAuthUserData('name'), 2);

                return isset($name[0]) ? $name[0] : '';
            case 'last_name':
                $name = explode(' ', $this->getAuthUserData('name'), 2);

                return isset($name[1]) ? $name[1] : '';
            case 'picture':
                $profile_image_size = $this->settings->get('profile_image_size');
                if ($profile_image_size === 'large') {
                    return isset($this->authUserData['avatar_larger']) ? $this->authUserData['avatar_larger'] : '';
                }

                return isset($this->authUserData['avatar']) ? $this->authUserData['avatar'] : '';
        }

        return parent::getAuthUserData($key);
    }

    public function syncProfile($user_id, $provider, $access_token) {
        if ($this->needUpdateAvatar($user_id)) {

            if ($this->getAuthUserData('picture')) {
                $this->updateAvatar($user_id, $this->getAuthUserData('picture'));
            }
        }

        $this->storeAccessToken($user_id, $access_token);
    }

    public function deleteLoginPersistentData() {
        parent::deleteLoginPersistentData();

        if ($this->client !== null) {
            $this->client->deleteLoginPersistentData();
        }
    }

}

NextendSocialLogin::addProvider(new NextendSocialPROProviderTiktok());
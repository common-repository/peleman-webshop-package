<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\Editor_Auth_Provider;
use PWP\includes\hookables\abstracts\Abstract_Ajax_Hookable;

/**
 * @deprecated 1.0.0
 * hook for loading cart thumbnails through an AJAX call; replaced by a more
 * native solution based on Wordpress's lazy loading system.
 */
class Ajax_Load_Cart_Thumbnail extends Abstract_Ajax_Hookable
{

    public function __construct()
    {
        parent::__construct(
            'Ajax_Load_Cart_Thumbnail',
            plugins_url('../js/pwp-load-cart-thumbnail.js', __FILE__),
            10
        );
    }

    public function callback(): void
    {
        $projectId = sanitize_key($_REQUEST['id']);

        $url = $this->generate_thumbnail_request_url($projectId);

        try {
            //TODO:
            //once the api returns a proper response when it cannot find a thumbnail,
            //we can remove the error control operator
            //right now we use it to simply suppress warnings in the error log
            //avoiding clutter
            //important to note that the error control operator doesn't ignore errors, it simply stops them from being logged.
            //as such, the try/catch block still works as intended.

            @$img = base64_encode(file_get_contents($url, true));

            if (!$img || $img === false) wp_send_json_error();
        } catch (\Throwable $error) {
            wp_send_json_error();
        }

        $response = array(
            'src' => 'data:image/jpeg;base64, ' . $img,
        );

        wp_send_json_success($response, 200);
    }

    public function callback_nopriv(): void
    {
        $this->callback();
    }

    private function generate_thumbnail_request_url(string $projectId): string
    {
        $domain = get_option('pie_domain');
        $auth = new Editor_Auth_Provider();

        $query = array(
            'projectid' => $projectId,
            'customerapikey' => $auth->get_api_key(),
        );

        return $domain . "/editor/api/getprojectthumbnailAPI.php" . '?' . http_build_query($query);
    }

    final protected function object_data(): array
    {
        return array(
            
        );
    }
}

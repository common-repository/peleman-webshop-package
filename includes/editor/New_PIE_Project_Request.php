<?php

declare(strict_types=1);

namespace PWP\includes\editor;

use PWP\includes\exceptions\Invalid_Response_Exception;
use PWP\includes\Abstract_Request;


global $post;

#region PIE EDITOR CONSTANTS
/**
 * Open editor in design mode for editors.
 */
define('PIE_USE_DESIGN_MODE', 'usedesignmode');

/**
 * Allow user to upload images.
 */
define('PIE_USE_IMAGE_UPLOAD', 'useimageupload');

/**
 * Use backgrounds.
 */
define('PIE_USE_BACKGROUNDS', 'usebackgrounds');

/**
 * Allow users to use custom designs. Set to false.
 */
define('PIE_USE_DESIGNS', 'usedesigns');
define('PIE_USE_ELEMENTS', 'useelements');
define('PIE_USE_DOWNLOAD_PREVIEW', 'usedownloadpreview');
define('PIE_USE_OPEN_FILE', 'useopenfile');
define('PIE_USE_EXPORT', 'useexport');
define('PIE_SHOW_CROP_ZONE', 'useshowcropzone');
define('PIE_SHOW_SAFE_ZONE', 'useshowsafezone');
define('PIE_SHOW_STOCK_PHOTOS', 'useshowstockphotos');
define('PIE_USE_TEXT', 'usetext');
#endregion

class New_PIE_Project_Request extends Abstract_PIE_Request
{
    #region CLASS VARIABLES
    private ?Product_PIE_Data $editorData;

    private int $userId;
    private $userEmail;
    private string $language;
    private array $editorInstructions;
    private string $projectName;
    private string $returnUrl;
    private ?string $organisationId;
    private $organisationApiKey;
    private array $params;
    private string $formatId;
    #endregion

    public function __construct(Editor_Auth_Provider $auth)
    {
        $endpoint = '/editor/api/createprojectAPI.php';
        parent::__construct($auth, $endpoint);

        $this->editorData = null;

        $this->userId = 0;
        $this->userEmail = '';
        $this->language = substr(get_locale(), 0, 2) ?: 'en';
        $this->editorInstructions = [];
        $this->projectName = '';
        $this->returnUrl = '';
        $this->organisationId ='';
        $this->organisationApiKey = '';

        $this->formatId = '';
        $this->params = [];

        $this->set_GET();
    }

    #region BUILDER METHODS

    public static function new(Editor_Auth_Provider $auth): self
    {
        return new New_PIE_Project_Request($auth);
    }

    public function initialize_from_product(\WC_Product $product): self
    {
        $this->editorData = new Product_PIE_Data($product);
        return $this;
    }

    public function initialize_from_pie_data(Product_PIE_Data $data): self
    {
        $this->editorData = $data;
        return $this;
    }

    public function set_user_id(int $userId): void
    {
        $this->userId = $userId;
    }

    public function set_user_email($userEmail): void
    {
        $this->userEmail = $userEmail;
    }

    public function set_return_url(string $returnURL): void
    {
        if ($this->organisationId) {
            $this->returnUrl = $returnURL . '&organisationid=' . $this->organisationId;
        } else {
            $this->returnUrl = $returnURL;
        }
    }

    public function set_organisation_id(?string $organisationId)
    {
        $this->organisationId = $organisationId;
    }

    public function set_organisation_apikey($organisationApiKey): void
    {
        if ($this->organisationId != '' || $this->organisationId != null) {
            $this->organisationApiKey = $organisationApiKey;
        } else {
            $this->organisationApiKey  = get_option('pie_api_key');
        }
    }

    public function getOrganisationId(): ?string
    {
        return $this->organisationId;
    }

    public function getOrganisationApiKey(): ?string
    {
        return $this->organisationApiKey;
    }

    public function set_editor_instructions(string ...$args): void
    {
        $this->editorInstructions = $args;
    }

    public function set_language(string $lang): void
    {
        $this->language = $lang;
    }

    public function set_project_name(string $name): void
    {
        $this->projectName = $name;
    }

    public function set_format_id(string $id): void
    {
        $this->formatId = $id;
    }

    #endregion

    public function data(): Product_PIE_Data
    {
        return $this->editorData;
    }

    public function is_customizable(): bool
    {
        // Project is only customizable if it is set to customizable AND it has a template Id.
        return ($this->editorData != null) && ($this->editorData->get_template_id());
    }

    public function make_request(): PIE_Project
    {
        $response = wp_remote_request(
            $this->get_endpoint_url(),
            array(
                'method'    => $this->get_method(),
                'timeout'   => $this->timeout,
                'header'    => $this->generate_request_header(),
                'body'      => $this->generate_request_body(),
            )
        );

        // TODO: Use improved request feedback to bolster error response system
        if (is_wp_error($response)) {
            error_log($response->get_error_code() . ": " . $response->get_error_message());
            throw new Invalid_Response_Exception(__('Could not connect to Peleman Image Editor. Please try again later.', 'Peleman-Webshop-Package'));
        }

        $responseBody = sanitize_key($response['body']);
        $responseArr = $response['response'];
        error_log('editor response: ' . print_r($responseBody, true));
        if (empty($responseBody) || is_bool($responseBody)) {
            throw new Invalid_Response_Exception(__('No valid response received. Likely an authentication issue. Please check the validity of your Peleman Editor credentials.', 'Peleman-Webshop-Package'));
        }

        return new PIE_Project($this->editorData, $responseBody);
    }

    /**
     * Add request parameter.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function add_request_parameter(string $key, $value): void
    {
        $this->params[$key] = $value;
    }

    protected function generate_request_body(): array
    {
        $price = $this->editorData->get_cover_price_per_page();
        $basePrice = $this->editorData->get_base_price(); 
		$pageAmount = $this->editorData->get_page_amount();
		$symbol = get_woocommerce_currency_symbol();
		$locale = get_locale();
		
		$locale_info = localeconv();
		$decimal_point = $locale_info['decimal_point'];
        $request = array(
			'customerid'            => $this->get_customer_id(),
			'a'                     => $this->getOrganisationApiKey(),
			'userid'                => $this->userId,
			'useremail'             => $this->userEmail,
			'lang'                  => $this->language,
			'templateid'            => $this->editorData->get_template_id(),
			'designid'              => $this->editorData->get_design_id(),
			'backgroundid'          => $this->editorData->get_background_id(),
			'colorcode'             => $this->editorData->get_color_code(),
			'formatid'              => $this->editorData->get_format_id(),
			'editorinstructions'    => array_merge($this->editorData->get_editor_instruction_array(), $this->editorInstructions),
			'projectname'           => $this->projectName,
			'organisationid'        => $this->organisationId,
			'returnurl'             => $this->returnUrl,
			'pricing'               => array(
												'base' => array(
													'price' => $basePrice,
												),
												'page' => array(
													'price' => $price,
												),
											),
			'includedpages'         => $pageAmount,
			'currency'				=> array(
												'symbol' => $symbol,
												'locale' => $locale,
												'decimal' => $decimal_point
			),
			
		);

        $request += $this->params;
		error_log('Request: ' . print_r($request, true));
		
        $request = apply_filters(
            'pwp_new_pie_project_request_params',
            $request,
            $this->editorData->get_parent(),
            $this->editorData
        );
        return $request;
    }
}
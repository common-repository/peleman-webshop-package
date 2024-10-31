<?php

declare(strict_types=1);

namespace PWP\includes\editor;

use WC_Product;

class Product_PIE_Data extends Product_Meta
{
    public const MY_EDITOR              = 'PIE';

    public const PIE_TEMPLATE_ID_KEY    = 'pie_template_id';
    public const PIE_COVER_PRICE_PER_PAGE    = 'cover_price_per_page';
    public const PIE_BASE_PRICE			= 'pie_base_price';
    public const PIE_PAGE_AMOUNT 		= 'default_page_amount';

    public const DESIGN_ID_KEY          = 'pie_design_project_id';
    public const COLOR_CODE_KEY         = 'pie_color_code';
    public const BACKGROUND_ID_KEY      = 'pie_background_id';

    public const USE_IMAGE_UPLOAD_KEY   = 'pie_image_upload';
    public const MAX_IMAGES_KEY         = 'pie_max_images';
    public const MIN_IMAGES_KEY         = 'pie_min_images';

    public const NUM_PAGES_KEY          = 'pie_num_pages';
    public const AUTOFILL_KEY           = 'pie_autofill';
    public const FORMAT_ID_KEY          = 'pie_format_id';

    public string $templateId;
    public string $designId;
    public string $designProjectId;
    public string $colorCode;
    public string $backgroundId;
    private PIE_Editor_Instructions $editorInstructions;

    public bool $usesImageUpload;
    public int $minImages;
    public int $maxImages;

    public int $numPages;
    public bool $autofill;
    public string $formatId;

    public float $coverPricePerPage;
    public float $basePrice;
    private int $default_page_amount;

    private string $variantId;
    private string $editorId;



    public function __construct(WC_Product $parent)
    {
        parent::__construct($parent);

        $this->templateId = $this->parent->get_meta(self::PIE_TEMPLATE_ID_KEY) ?? '';
        $this->designId = $this->parent->get_meta(self::DESIGN_ID_KEY) ?? '';
        $this->colorCode = $this->parent->get_meta(self::COLOR_CODE_KEY) ?? '';
        $this->backgroundId =  $this->parent->get_meta(self::BACKGROUND_ID_KEY) ?? '';

        $this->editorInstructions = new PIE_Editor_Instructions($this->parent);
        $this->usesImageUpload = (bool) $this->parent->get_meta(self::USE_IMAGE_UPLOAD_KEY);
        $this->minImages = (int)$this->parent->get_meta(self::MIN_IMAGES_KEY) ?? 0;
        //if max_images is 0, we can assume there is no limit to the amount of images.
        $this->maxImages = (int)$this->parent->get_meta(self::MAX_IMAGES_KEY) ?? 0;

        $this->numPages = (int)$this->parent->get_meta(self::NUM_PAGES_KEY) ?? -1;
        $this->autofill = (bool)$this->parent->get_meta(self::AUTOFILL_KEY);
        $this->formatId = $this->parent->get_meta(self::FORMAT_ID_KEY ?? '');
        $this->coverPricePerPage = (float) $this->parent->get_meta(self::PIE_COVER_PRICE_PER_PAGE) ?? 0.0;
	$this->basePrice = (float) $this->parent->get_meta(self::PIE_BASE_PRICE) ?? 0.0;
	$this->default_page_amount = (int) $this->parent->get_meta(self::PIE_PAGE_AMOUNT) ?? 1;

    }

    public function get_num_pages(): int
    {
        return $this->numPages;
    }

    public function set_num_pages(int $count): self
    {
        $this->numPages = max($count, 0);
        return $this;
    }


    public function get_autofill(): bool
    {
        return $this->autofill;
    }

    public function set_autofill(bool $autofill): self
    {
        $this->autofill = $autofill;
        return $this;
    }


    public function get_format_id(): string
    {
        return $this->formatId;
    }

    public function set_format_id(string $id): self
    {
        $this->formatId = $id;
        return $this;
    }

    /**
     * returns a dictionary of editor instruction objects
     *
     * @return PIE_Instruction[]
     */
    public function get_editor_instructions(): array
    {
        return $this->editorInstructions->get_instructions();
    }

    public function set_editor_instructions(string $instructions): self
    {
        $this->editorInstructions->set_instructions_from_string($instructions);
        return $this;
    }

    public function get_editor_instruction_string(): string
    {
        return $this->editorInstructions->get_instructions_string();
    }
    /**
     * returns a string containing the keys of all enabled editor instructions.
     *
     * @return string[]
     */
    public function get_editor_instruction_array(): array
    {
        return $this->editorInstructions->get_instruction_array();
    }

    public function parse_instruction_array(array $instructions): self
    {
        $this->editorInstructions->parse_instruction_array($instructions);
        return $this;
    }


    public function parse_instruction_array_loop(array $instructions, int $loop): self
    {
        $this->editorInstructions->parse_instruction_array_loop($instructions, $loop);
        return $this;
    }

    public function get_template_id(): string
    {
        return $this->templateId;
    }

    public function set_template_id(string $id): self
    {
        $this->templateId = $id;
        return $this;
    }
	
	public function get_page_amount(): int
	{
		return $this->default_page_amount ?: 2;
	}
	
    public function set_cover_price_per_page(float $coverPricePerPage): self
    {
        $this->coverPricePerPage = $coverPricePerPage;
        return $this;
    }

    public function get_cover_price_per_page(): float
    {
        return $this->coverPricePerPage;
    }

	public function get_base_price()
	{
		return $this->parent->get_price();
	}

	public function set_base_price(float $WCBasePrice): self
	{
		$this->basePrice = $WCBasePrice;
		$this->parent->update_meta_data(self::PIE_BASE_PRICE, $WCBasePrice);
		return $this;
	}
	    
    public function get_design_id(): string
    {
        return $this->designId;
    }

    public function set_design_id(string $code): self
    {
        $this->designId = $code;
        return $this;
    }

    public function get_color_code(): string
    {
        return $this->colorCode;
    }

    public function set_color_code(string $code): self
    {
        $this->colorCode = $code;
        return $this;
    }

    public function get_background_id(): string
    {
        return $this->backgroundId;
    }

    public function set_background_id(string $id): self
    {
        $this->backgroundId = $id;
        return $this;
    }

    public function get_variant_id(): string
    {
        return $this->variantId;
    }

    public function set_variant_id(string $variantId): self
    {
        $this->variantId = $variantId;
        return $this;
    }

    public function uses_image_upload(): bool
    {
        return $this->usesImageUpload;
    }

    public function set_uses_image_upload(bool $useUpload): self
    {
        $this->usesImageUpload = $useUpload;
        return $this;
    }

    public function get_max_images(): int
    {
        return $this->maxImages;
    }

    public function set_max_images(int $count): self
    {
        $count = max(0, $count);
        $this->maxImages = $count;
        return $this;
    }

    public function get_min_images(): int
    {
        return $this->minImages;
    }

    public function set_min_images(int $count): self
    {
        $count = max(0, $count);
        $this->minImages = $count;
        return $this;
    }

    // public function set_as_editor(): self
    // {
    //     $this->editorId = "PIE";
    //     return $this;
    // }

    public function update_meta_data(): void
    {

        $this->parent->update_meta_data(self::PIE_TEMPLATE_ID_KEY, $this->templateId);
        $this->parent->update_meta_data(self::BACKGROUND_ID_KEY, $this->backgroundId);
        $this->parent->update_meta_data(self::COLOR_CODE_KEY, $this->colorCode);
        $this->parent->update_meta_data(self::DESIGN_ID_KEY, $this->designId);

        $this->parent->update_meta_data(self::USE_IMAGE_UPLOAD_KEY, $this->usesImageUpload ? 1 : 0);
        $this->parent->update_meta_data(self::MIN_IMAGES_KEY, $this->minImages);
        $this->parent->update_meta_data(self::MAX_IMAGES_KEY, $this->maxImages);

        $this->parent->update_meta_data(self::AUTOFILL_KEY, $this->autofill ? 1 : 0);
        $this->parent->update_meta_data(self::FORMAT_ID_KEY, $this->formatId);
        $this->parent->update_meta_data(self::NUM_PAGES_KEY, $this->numPages);

        $this->editorInstructions->update_meta_data();
        $this->parent->save_meta_data();
    }

    public function get_editor_params(): array
    {
        $params = array();
        if ($this->designId)
            $params['designprojectid'] =  $this->designId;
        if ($this->get_max_images() > 0)
            $params['maximages'] = $this->get_max_images();
        if ($this->get_min_images() > 0)
            $params['minimages'] = $this->get_min_images();
        if ($this->autofill)
            $params['autofill'] = $this->autofill;
        if ($this->formatId)
            $params['formatid'] = $this->formatId;
        if ($this->numPages)
            $params['numpages'] = $this->numPages;


        return $params;
    }
}
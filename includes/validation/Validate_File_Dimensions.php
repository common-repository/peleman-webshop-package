<?php

declare(strict_types=1);

namespace PWP\includes\validation;

use PWP\includes\wrappers\PDF_Upload;
use PWP\includes\utilities\notification\I_Notification;

class Validate_File_Dimensions extends File_Validator
{
    private int $heightRange;
    private int $widthRange;
    private float $precision;


    public function __construct(int $height, int $width, float $precision)
    {
        parent::__construct();
        $this->heightRange = $height;
        $this->widthRange = $width;
        $this->precision = $precision;
    }

    public function handle(PDF_Upload $data, ?I_Notification $notification = null): bool
	{
		
		error_log('Height Range: ' . $this->heightRange);
		error_log('Width Range: ' . $this->widthRange);
		error_log('Margin of Error: ' . $this->precision);

		$heightFit = $this->value_is_in_range(
			$data->get_height(),
			$this->heightRange,
			$this->precision
		);

		$widthFit = $this->value_is_in_range(
			$data->get_width(),
			$this->widthRange,
			$this->precision
		);
		if ($heightFit && $widthFit) {
			return $this->handle_next($data, $notification);
		}
		$notification->add_error(
			'Dimensions not valid',
			sprintf(
				__('The width or height of the uploaded file do not match the required width & height.', 'Peleman-Webshop-Package'),
				number_format($data->get_width(), 1),
				number_Format($data->get_height(), 1),
				$this->widthRange,
				$this->heightRange,
			)
		);
		return false;
	}


    private function value_is_in_range(float $value, float $range, float $precision): bool
    {
        if ($range === 0) return true;
        return $precision >= abs($value - $range);
    }
}

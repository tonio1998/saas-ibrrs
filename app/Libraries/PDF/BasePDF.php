<?php

namespace App\Libraries\PDF;

use FPDF;

class BasePDF extends FPDF
{
    public array $meta = [];

    public function setMeta(array $meta)
    {
        $this->meta = $meta;
    }
}

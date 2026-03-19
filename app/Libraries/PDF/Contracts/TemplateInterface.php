<?php
namespace App\Libraries\PDF\Contracts;

interface TemplateInterface
{
    public function render($pdf, $cert): void;
}

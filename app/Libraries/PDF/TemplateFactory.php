<?php

namespace App\Libraries\PDF;

use App\Libraries\PDF\Templates\BusinessClearanceTemplate;
use App\Libraries\PDF\Templates\ClearanceTemplate;
use App\Libraries\PDF\Templates\IndigencyTemplate;
use App\Libraries\PDF\Templates\JobSeekerTemplate;
use App\Libraries\PDF\Templates\ResidencyTemplate;

class TemplateFactory
{
    public static function make(string $template)
    {
        return match ($template) {
            'barangay_clearance_template' => new ClearanceTemplate(),
            'certificate_of_residency_template' => new ResidencyTemplate(),
            'certificate_of_indigency_template' => new IndigencyTemplate(),
            'first_time_job_seeker' => new JobSeekerTemplate(),
            'business_clearance_template' => new BusinessClearanceTemplate(),
            default => null
        };
    }
}

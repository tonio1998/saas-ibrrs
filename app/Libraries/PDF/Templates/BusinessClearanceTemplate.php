<?php

namespace App\Libraries\PDF\Templates;

use App\Libraries\PDF\Contracts\TemplateInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class BusinessClearanceTemplate implements TemplateInterface
{
    private float $margin = 12;
    private function microWatermark($pdf, $text = null)
    {
        $pdf->SetDrawColor(200, 200, 200);

        $pageWidth = $pdf->GetPageWidth();
        $pageHeight = $pdf->GetPageHeight();

        $count = 350; // reduced density

        for ($i = 0; $i < $count; $i++) {

            $startX = mt_rand(0, (int)$pageWidth);
            $startY = mt_rand(0, (int)$pageHeight);

            $length = mt_rand(3, 7); // much shorter (hair-like)
            $angle = deg2rad(mt_rand(0, 360));
            $curve = mt_rand(-1, 1); // very subtle curve

            $prevX = $startX;
            $prevY = $startY;

            $segments = 3; // fewer segments = simpler stroke

            for ($s = 1; $s <= $segments; $s++) {

                $t = $s / $segments;

                $x = $startX + cos($angle) * $length * $t;
                $y = $startY + sin($angle) * $length * $t;

                $y += sin($t * pi()) * $curve;

                $pdf->Line($prevX, $prevY, $x, $y);

                $prevX = $x;
                $prevY = $y;
            }
        }
    }
    public function render($pdf, $cert): void
    {
        $pdf->SetMargins($this->margin, $this->margin, $this->margin);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        $w = $pdf->GetPageWidth();
        $cw = $w - ($this->margin * 2);

        $text = strtoupper(config('client.watermark'));
        $pdf->SetTextColor(rand(220,235), rand(220,235), rand(220,235));
        $this->microWatermark($pdf, $text);
        $pdf->SetTextColor(0, 0, 0);
        /* LOGOS */
        $pdf->Image(public_path('images/logo-left.png'), $this->margin, 15, 20);
        $pdf->Image(public_path('images/logo-right.png'), $w - $this->margin - 20, 15, 20);

        $pdf->SetY($this->margin);

        /* HEADER */
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell($cw, 5, 'Republic of the Philippines', 0, 1, 'C');
        $pdf->Cell($cw, 5, 'Province of ' . config('client.province'), 0, 1, 'C');
        $pdf->Cell($cw, 5, 'Municipality of ' . config('client.municipality'), 0, 1, 'C');
        $pdf->Cell($cw, 5, 'Barangay ' . config('client.barangay'), 0, 1, 'C');

        $pdf->Ln(4);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell($cw, 6, 'OFFICE OF THE PUNONG BARANGAY', 0, 1, 'C');

        $pdf->Ln(1);

        $pdf->SetFont('Arial', 'B', 13);
        $pdf->Cell($cw, 6, 'BARANGAY BUSINESS CLEARANCE', 0, 1, 'C');

        $pdf->Ln(8);

        /* INTRO */
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell($cw, 6, 'To whom it may concern:', 0, 1);

        $pdf->Ln(5);

        $pdf->Cell($cw, 6, 'This is to certify that the business or trade activity described below:', 0, 1);

        $pdf->Ln(3);

        /* UNDERLINED FIELDS */
        $business = strtoupper($cert->business->business_name ?? '');
        $location = strtoupper($cert->business->full_address ?? '');

        if($cert?->business?->operator_type === 'resident'){
            $owner = strtoupper(trim(($cert?->resident->info->full_name ?? '')));
        }else{
            $owner = strtoupper(trim(($cert?->business->operator_name ?? '')));
        }

        $address = strtoupper($cert?->resident->info->full_address ?? '');


        $this->lineField($pdf, $cw, $business, '(Business Name or Trade Activity)');
        $this->lineField($pdf, $cw, $location, '(Location)', 9);
        $this->lineField($pdf, $cw, $owner, '(Operator/Manager)');
        $this->lineField($pdf, $cw, $address, '(Address)', 9);

        $pdf->Ln(6);

        /* PARAGRAPH */
        $pdf->MultiCell($cw, 5,
            "Proposed to be established in this Barangay and is being applied for a Barangay Clearance to be used in securing a corresponding Mayor's Permit has been found to be:"
        );

        $pdf->Ln(5);

        /* CHECKLIST */
        $this->checkLine($pdf, $cw, "In conformity with the provisions of existing Barangay Ordinances, rules, and regulations being enforced in this Barangay;");
        $this->checkLine($pdf, $cw, "Not among those businesses or trade activities with pending cases and/or being banned to be established in this barangay;");

        $pdf->Ln(5);

        $pdf->MultiCell($cw, 5, "In view of the foregoing, this barangay thru the undersigned:");

        $pdf->Ln(5);

        $this->checkLine($pdf, $cw, "Interposes no objection for the issuance of the corresponding Mayor's Permit being applied for.");

        $pdf->Ln(5);

        $pdf->MultiCell($cw, 5,
            "PERMIT, HOWEVER, is subject for cancellation if the specific purpose granted by the Barangay Council is not consonant with the actual operation of the business."
        );

        $pdf->Ln(6);

        /* DATE */
        $pdf->Cell($cw, 6,
            "Issued this " . now()->format('jS') . " day of " . now()->format('F Y') .
            " at Barangay " . config('client.barangay') . ".", 0, 1
        );

        $pdf->Ln(7);

        /* SIGNATURE */
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell($cw, 6, strtoupper(config('client.captain_name')), 0, 1, 'R');

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell($cw, 5, 'Punong Barangay', 0, 1, 'R');

        $pdf->Ln(10);

        /* FOOTER DETAILS */
        $pdf->SetFont('Arial', 'I', 9);
        $pdf->Cell($cw, 5, '(Note: Not Valid without Barangay Dry Seal)', 0, 1);

        $pdf->Ln(15);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell($cw, 5, 'Paid Under:', 0, 1);

        $pdf->Ln(4);

        $this->footerLine2($pdf, 'O.R. No.:', $cert?->certificateRecord?->or_number ?? '', 'TIN No.:', $cert->business->TinNo ?? '');
        $this->footerLine2($pdf, 'Date Paid:', date('M d, Y h:i:s A', strtotime($cert?->certificateRecord?->payment_date)) ?? '', 'Date Issued:', date('M d, Y', strtotime($cert?->certificateRecord?->payment_date)) ?? '');
        $this->footerLine2($pdf, 'Place:', config('client.barangay'), 'Place Issued:', config('client.barangay'));

        /* QR */
        $qr = $this->generateQR($cert);
        $pdf->Image($qr, $w - $this->margin - 35, 230, 35);

        if (file_exists($qr)) unlink($qr);
    }

    private function lineField($pdf, $width, $value, $label, $fontSize = 11)
    {
        $pdf->SetFont('Arial', 'B', $fontSize);

        $pdf->Cell($width, 6, '', 0, 1);
        $y = $pdf->GetY();

        $pdf->Line($this->margin, $y, $this->margin + $width, $y);

        $pdf->SetY($y - 6);
        $pdf->Cell($width, 6, $value, 0, 1, 'C');

        $pdf->SetFont('Arial', 'I', 9);
        $pdf->Cell($width, 5, $label, 0, 1, 'C');
    }


    private function footerLine2($pdf, $lLabel, $lValue, $rLabel, $rValue)
    {
        $pdf->SetFont('Arial', '', 10);

        $startX = $this->margin;
        $totalWidth = $pdf->GetPageWidth() - ($this->margin * 2);
        $half = $totalWidth / 2;

        $y = $pdf->GetY();

        // LEFT
        $pdf->SetXY($startX, $y);
        $pdf->Cell(30, 5, $lLabel, 0, 0);

        $x = $pdf->GetX();
        $pdf->Line($x, $y + 5, $x + ($half - 30), $y + 5);
        $pdf->Cell($half - 30, 5, strtoupper($lValue), 0, 0);

        // RIGHT
        $pdf->Cell(30, 5, $rLabel, 0, 0);

        $x2 = $pdf->GetX();
        $pdf->Line($x2, $y + 5, $x2 + ($half - 30), $y + 5);
        $pdf->Cell($half - 30, 5, strtoupper($rValue), 0, 1);
    }
    private function checkLine($pdf, $width, $text)
    {
        $pdf->Cell(10, 5, 'X', 0, 0);
        $pdf->Cell(5, 5, '/', 0, 0);
        $pdf->MultiCell($width - 15, 5, $text);
    }

    private function footerLine($pdf, $label, $value)
    {
        $pdf->SetFont('Arial', '', 10);

        $pdf->Cell(45, 5, $label, 0, 0);

        $x = $pdf->GetX();
        $y = $pdf->GetY();

        $lineWidth = 60;

        $pdf->Line($x, $y + 5, $x + $lineWidth, $y + 5);

        $pdf->SetXY($x, $y);
        $pdf->Cell($lineWidth, 5, strtoupper($value), 0, 1);
    }

    private function generateQR($cert)
    {
        $path = storage_path('app/public/qr-' . $cert->ControlNo . '.png');

        if (!file_exists($path)) {
            Builder::create()
                ->writer(new PngWriter())
                ->data(route('cert.verify', $cert->ControlNo))
                ->size(120)
                ->build()
                ->saveToFile($path);
        }

        return $path;
    }
}

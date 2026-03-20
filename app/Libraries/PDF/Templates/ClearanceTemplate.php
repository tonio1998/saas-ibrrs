<?php

namespace App\Libraries\PDF\Templates;

use App\Libraries\PDF\Contracts\TemplateInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class ClearanceTemplate implements TemplateInterface
{
    private float $margin = 25.4;

    private function styledHeaderText($pdf, $text, $contentWidth)
    {
        $pdf->SetFont('Arial', 'B', 16);

        $startX = $pdf->GetX();
        $y = $pdf->GetY();

        $letters = str_split($text);
        $spacing = 1.5;

        $totalWidth = 0;
        foreach ($letters as $char) {
            $totalWidth += $pdf->GetStringWidth($char) + $spacing;
        }

        $x = $startX + ($contentWidth - $totalWidth) / 2;

        foreach ($letters as $char) {
            $pdf->SetTextColor(0, 70, 160);
            $pdf->SetXY($x, $y);
            $pdf->Cell(0, 6, $char, 0, 0);

            $charWidth = $pdf->GetStringWidth($char);
            $pdf->Line($x, $y + 6, $x + $charWidth, $y + 6);

            $x += $charWidth + $spacing;
        }

        $pdf->Ln(8);
        $pdf->SetTextColor(0, 0, 0);
    }

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

        $text = strtoupper(config('client.watermark'));
        $pdf->SetTextColor(rand(220,235), rand(220,235), rand(220,235));
        $this->microWatermark($pdf, $text);

        $pdf->SetTextColor(0, 0, 0);
        $pageWidth = $pdf->GetPageWidth();
        $contentWidth = $pageWidth - ($this->margin * 2);

        $logoY = 20;

        $pdf->Image(public_path('images/logo-left.png'), $this->margin, $logoY, 22);
        $pdf->Image(public_path('images/logo-right.png'), $pageWidth - $this->margin - 22, $logoY, 22);

        $pdf->SetY($this->margin);

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell($contentWidth, 5, 'Republic of the Philippines', 0, 1, 'C');
        $pdf->Cell($contentWidth, 5, 'Province of ' . config('client.province'), 0, 1, 'C');
        $pdf->Cell($contentWidth, 5, 'Municipality of ' . config('client.municipality'), 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell($contentWidth, 6, "BARANGAY " . strtoupper(config('client.barangay')), 0, 1, 'C');

        $pdf->Ln(2);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell($contentWidth, 6, 'OFFICE OF THE PUNONG BARANGAY', 0, 1, 'C');

        $pdf->Ln(3);

        $this->styledHeaderText($pdf, "BARANGAY CLEARANCE", $contentWidth);

        $pdf->Ln(4);

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell($contentWidth, 6, 'TO WHOM IT MAY CONCERN:', 0, 1);

        $pdf->Ln(3);

        $residentName = strtoupper(trim(($cert?->resident->FirstName ?? '') . ' ' . ($cert?->resident->LastName ?? '')));
        $address = strtoupper($cert?->resident->info->full_address ?? '');
        $purpose = strtoupper($cert->purpose ?? 'WHATEVER LEGAL PURPOSE IT MAY SERVE');

        $this->writeMixed($pdf, [
            ['text' => 'This is to certify that '],
            ['text' => $residentName, 'style' => 'B'],
            ['text' => ', of legal age, is a bonafide resident of '],
            ['text' => $address, 'style' => 'B'],
            ['text' => ".\n\n"]
        ]);

        $text2 =
            "Further, this is to certify that the above-named person is known to be of good moral character and has no derogatory record on file in this barangay.\n\n" .
            "This certification is issued upon the request of the above-named person for " . $purpose . ".\n\n" .
            "Issued this " . now()->format('jS') . " day of " . now()->format('F Y') .
            " at Barangay " . strtoupper(config('client.barangay')) . ".";

        $this->fitText($pdf, $text2);

        $pdf->Ln(10);

        $pdf->Cell($contentWidth, 6, 'Approved by:', 0, 1, 'R');

        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell($contentWidth, 6, strtoupper(config('client.captain_name')), 0, 1, 'R');

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell($contentWidth, 5, 'Punong Barangay', 0, 1, 'R');

        $qrPath = $this->generateQR($cert);

        $qrSize = 35;
        $qrX = $pageWidth - $this->margin - $qrSize;

        $pdf->Image($qrPath, $qrX, 205, $qrSize);

        $this->footer($pdf, $cert);

        if (file_exists($qrPath)) {
            unlink($qrPath);
        }
    }

    private function writeMixed($pdf, $segments)
    {
        foreach ($segments as $seg) {
            $pdf->SetFont('Arial', $seg['style'] ?? '', 11);
            $pdf->Write(6, $seg['text']);
        }
    }

    private function fitText($pdf, $text)
    {
        $fontSize = 11;

        while ($fontSize >= 9) {
            $pdf->SetFont('Arial', '', $fontSize);

            $startY = $pdf->GetY();
            $pdf->MultiCell(0, 6, $text, 0, 'J');

            if ($pdf->GetY() <= 190) break;

            $pdf->SetY($startY);
            $fontSize--;
        }
    }

    private function generateQR($cert)
    {
        $path = storage_path('app/public/qr-' . $cert->ControlNo . '.png');

        if (!file_exists($path)) {
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data(route('cert.verify', $cert->ControlNo))
                ->size(150)
                ->margin(5)
                ->build();

            $result->saveToFile($path);
        }

        return $path;
    }

    private function footer($pdf, $cert)
    {
        $pageWidth = $pdf->GetPageWidth();
        $contentWidth = $pageWidth - ($this->margin * 2);

        $pdf->SetY(-35);
        $pdf->SetFont('Arial', '', 9);

        $pdf->Cell($contentWidth, 5, 'Documentary Stamp Tax PAID', 0, 1, 'L');

        $pdf->Cell($contentWidth / 2, 5, 'OR#: ' . ($cert?->certificateRecord?->or_number ?? 'N/A'), 0, 0, 'L');
        $pdf->Cell($contentWidth / 2, 5, 'Payment: ' . strtoupper($cert?->certificateRecord?->payment_method ?? 'N/A'), 0, 1, 'R');

        $pdf->Cell($contentWidth / 2, 5, 'Cert Fee: ' . number_format($cert?->certificateRecord?->Fee ?? 0, 2), 0, 0, 'L');
        $pdf->Cell($contentWidth / 2, 5, 'Paid: ' . number_format($cert?->certificateRecord?->amount_paid ?? 0, 2), 0, 1, 'R');

        $pdf->Cell($contentWidth / 2, 5, 'DST: ' . number_format($cert?->certificateRecord?->Fee ?? 0, 2), 0, 0, 'L');
        $pdf->Cell($contentWidth / 2, 5, 'Date: ' . date('M d, Y h:i:s A', strtotime($cert?->certificateRecord?->payment_date)), 0, 1, 'R');

        $pdf->Ln(2);

        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetTextColor(200, 0, 0);
        $pdf->Cell($contentWidth, 5, 'Note: NOT VALID WITHOUT BARANGAY SEAL.', 0, 0, 'R');

        $pdf->SetTextColor(0, 0, 0);
    }
}

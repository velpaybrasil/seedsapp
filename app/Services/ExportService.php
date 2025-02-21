<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use TCPDF;

class ExportService {
    private string $exportPath;
    
    public function __construct() {
        $this->exportPath = dirname(dirname(__DIR__)) . '/public/exports';
        if (!is_dir($this->exportPath)) {
            mkdir($this->exportPath, 0777, true);
        }
    }
    
    public function exportToExcel(array $data, array $columns, string $filename): string {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $col = 'A';
        foreach ($columns as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        
        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4E73DF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];
        $sheet->getStyle('A1:' . $col . '1')->applyFromArray($headerStyle);
        
        // Add data
        $row = 2;
        foreach ($data as $item) {
            $col = 'A';
            foreach ($columns as $key => $header) {
                $value = $item[$key] ?? '';
                if ($value instanceof \DateTime) {
                    $value = $value->format('d/m/Y H:i:s');
                }
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        // Style data cells
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];
        $sheet->getStyle('A2:' . $col . ($row - 1))->applyFromArray($dataStyle);
        
        // Save file
        $writer = new Xlsx($spreadsheet);
        $filePath = $this->exportPath . '/' . $filename . '.xlsx';
        $writer->save($filePath);
        
        return '/exports/' . $filename . '.xlsx';
    }
    
    public function exportToPdf(array $data, array $columns, string $filename, array $options = []): string {
        $pdf = new TCPDF(
            $options['orientation'] ?? 'P',
            'mm',
            $options['format'] ?? 'A4',
            true,
            'UTF-8'
        );
        
        // Set document information
        $pdf->SetCreator('SeedsApp');
        $pdf->SetAuthor($options['author'] ?? 'SeedsApp');
        $pdf->SetTitle($options['title'] ?? 'Relatório');
        
        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', '', 10);
        
        // Add title if provided
        if (!empty($options['title'])) {
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(0, 10, $options['title'], 0, 1, 'C');
            $pdf->Ln(5);
            $pdf->SetFont('helvetica', '', 10);
        }
        
        // Calculate columns width
        $pageWidth = $pdf->getPageWidth() - 30; // 30 = left margin + right margin
        $colWidth = $pageWidth / count($columns);
        
        // Add headers
        $pdf->SetFillColor(78, 115, 223);
        $pdf->SetTextColor(255);
        $pdf->SetFont('helvetica', 'B', 10);
        
        foreach ($columns as $header) {
            $pdf->Cell($colWidth, 7, $header, 1, 0, 'C', true);
        }
        $pdf->Ln();
        
        // Add data
        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetFillColor(248, 249, 252);
        $fill = false;
        
        foreach ($data as $row) {
            foreach ($columns as $key => $header) {
                $value = $row[$key] ?? '';
                if ($value instanceof \DateTime) {
                    $value = $value->format('d/m/Y H:i:s');
                }
                $pdf->Cell($colWidth, 6, $value, 1, 0, 'L', $fill);
            }
            $pdf->Ln();
            $fill = !$fill;
        }
        
        // Save file
        $filePath = $this->exportPath . '/' . $filename . '.pdf';
        $pdf->Output($filePath, 'F');
        
        return '/exports/' . $filename . '.pdf';
    }
    
    public function exportToCsv(array $data, array $columns, string $filename): string {
        $filePath = $this->exportPath . '/' . $filename . '.csv';
        $file = fopen($filePath, 'w');
        
        // Add UTF-8 BOM for Excel compatibility
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Add headers
        fputcsv($file, array_values($columns));
        
        // Add data
        foreach ($data as $row) {
            $csvRow = [];
            foreach ($columns as $key => $header) {
                $value = $row[$key] ?? '';
                if ($value instanceof \DateTime) {
                    $value = $value->format('d/m/Y H:i:s');
                }
                $csvRow[] = $value;
            }
            fputcsv($file, $csvRow);
        }
        
        fclose($file);
        
        return '/exports/' . $filename . '.csv';
    }
    
    public function cleanupExports(int $maxAgeHours = 24): void {
        $files = glob($this->exportPath . '/*');
        $now = time();
        
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= $maxAgeHours * 3600) {
                    unlink($file);
                }
            }
        }
    }
}

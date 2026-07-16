<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Asset::select('id', 'name', 'brand', 'status', 'price', 'category_id', 'Warranty')->get();
    }
    public function styles($Worksheet)
    {

        return [
            '1' =>[ 'font' => ['bold' => true, 'size' => 13]],
            'A:Z' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]


        ];
    }
    public function headings(): array
    {
        return[
            'ID',
            'Name',
            'Brand',
            'Status',
            'Price',
            'Category',
            'Warranty'
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->id,
            $asset->name,
            $asset->brand,
            $this->formatStatus($asset->status),
            $asset->price,
            $asset->category?->name ?? 'No Category',
            $this->formatDate($asset->Warranty),
        ];
    }

    //  private function formatCategory($category)
    // {
    //     return ucfirst($category);
    // }

    /**
     * Convert status into readable labels
     */
    private function formatStatus($status)
    {
        return match ($status) {
            'available' => 'Available',
            'assigned' => 'Assigned',
            'maintenance' => 'Under Maintenance',
            default => ucfirst($status),
        };
    }

    /**
     * Format date for Excel readability
     */
    private function formatDate($date)
    {
        return $date ? date('d M Y', strtotime($date)) : null;
    }
}

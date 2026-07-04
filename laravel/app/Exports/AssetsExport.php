<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Asset::select('id', 'name', 'brand', 'status', 'price', 'category', 'Warranty')->get();
    }

    public function headings(): array
    {
        return[
            'ID',
            'Asset Name',
            'Brand',
            'Status',
            'Price',
            'Category',
            'Warranty expiry date'
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->id,
            $asset->name,
            $asset->brand,
            $asset->category?->name ?? 'No Category',
            $asset->assigned_to ?? 'Unassigned',
            $this->formatStatus($asset->status),
            $this->formatDate($asset->purchase_date),
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

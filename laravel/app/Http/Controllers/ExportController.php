<?php

namespace App\Http\Controllers;
use App\Exports\UsersExport;
use App\Exports\AssetsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Models\Asset;
class ExportController extends Controller
{
    //
    public function ExportUserstoExcel()
    {
        return Excel::download(new UsersExport(), 'user-list.xlsx');
    }
    public function ExportAssetstoExcel()
    {
        return Excel::download(new AssetsExport(), 'asset-list.xlsx');
    }
    public function ExportUserstoPdf()
    {
        $users = User::select('id','username','email','role')->get();

        $pdf = Pdf::loadView('pdf.users', compact('users'));

        return $pdf->download('user-list.pdf');
    }
    public function ExportAssetstoPdf()
    {
        $assets = Asset::select('id', 'name', 'brand', 'status', 'price', 'category_id', 'Warranty')->get();
        
        $pdf = Pdf::loadView('pdf.assets', compact('assets'));

        return $pdf->download('asset-list.pdf');
    }
}

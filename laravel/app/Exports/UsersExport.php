<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\User;
class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // return User::all();
        return User::select('id', 'username', 'email', 'role')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Username',
            'Email',
            'Role'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->username,
            $user->email,
            $this->formatRole($user->role),
        ];
    }

    private function formatRole($role)
    {
        return match ($role) {
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'viewer' => 'Viewer',
            default => ucfirst($role),
        };
    }
}

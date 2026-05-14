<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Hitung Gaji')
                ->label('Hitung Gaji')
                ->color('success')
                ->url('/payroll')
                ->visible(fn () => Auth::user()?->hasRole('super_admin')),
            CreateAction::make(),
        ];
    }
}

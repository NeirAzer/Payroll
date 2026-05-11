<?php

namespace App\Filament\Resources\Leaves\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class LeaveForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->components([
                    DatePicker::make('start_date')
                        ->required(),
                    DatePicker::make('end_date')
                        ->required(),
                    Textarea::make('reason')
                        ->required()
                        ->columnSpanFull(),
                ]),
                Section::make()->components([
                    TextInput::make('user_id')
                        ->required()
                        ->numeric(),
                    Select::make('status')
                        ->required()
                        ->default('pending')
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ]),
                    Textarea::make('notes')
                        ->nullable()
                        ->columnSpanFull(),
                ])->visible(fn () => Auth::user()?->hasRole('super_admin')),
            ]);
    }
}

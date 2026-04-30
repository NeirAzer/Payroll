<?php

namespace App\Filament\Resources\Schedules\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->components([
                       Section::make()
                            ->components([
                                Select::make('user_id')
                                    ->label('Nama Pegawai')
                                    ->options(User::query()->pluck('name', 'id'))
                                    ->preload()
                                    ->searchable()
                                    ->required(),
                                Select::make('shift_id')
                                    ->relationship('shift', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->required(),
                                Select::make('office_id')
                                    ->relationship('office', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->required(),
                                Toggle::make('is_wfa')
                                    ->label('WFA')
                                    ->onIcon(Heroicon::Check)
                                    ->offIcon(Heroicon::XMark),
                            ]) 
                    ]),
            ]);
    }
}

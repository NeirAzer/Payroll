<?php

namespace App\Filament\Resources\Attendances\Tables;

use App\Models\Attendance;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Jam Masuk')
                    ->time()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('Jam Pulang')
                    ->time()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->date()
                    ->sortable()
                    ->label('Tanggal'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_late')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function ($record){
                        return $record->isLate() ? 'Late' : 'On Time';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Late' => 'danger',
                        'On Time' => 'success',
                    })
                    ->description(fn (Attendance $record): string => 'Work Duration: ' . $record->workDuration()),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

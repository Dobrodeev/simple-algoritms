<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $modelLabel = 'курс';

    protected static ?string $pluralModelLabel = 'курсы';

    protected static ?string $recordTitleAttribute = 'symbol';

    /** Quote currencies used for highlighting and filtering. */
    private const QUOTES = ['BTC' => 'success', 'ETH' => 'warning', 'USDT' => 'info'];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('symbol')
                    ->label('Валютная пара')
                    ->required()
                    ->maxLength(20)
                    ->regex('/^\s*[A-Za-z0-9]+\s*$/')
                    ->rule(fn (?Course $record) => function (string $attribute, mixed $value, \Closure $fail) use ($record) {
                        $exists = Course::where('symbol', strtoupper(trim((string) $value)))
                            ->when($record, fn (Builder $q) => $q->whereKeyNot($record->getKey()))
                            ->exists();
                        if ($exists) {
                            $fail('Такая валютная пара уже есть.');
                        }
                    })
                    ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                    ->dehydrateStateUsing(fn (?string $state) => strtoupper(trim((string) $state))),
                Forms\Components\TextInput::make('price')
                    ->label('Курс')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(9999999999999999),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('symbol')
            ->columns([
                Tables\Columns\TextColumn::make('symbol')
                    ->label('Валютная пара')
                    ->badge()
                    ->color(fn (string $state) => self::quoteColor($state))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Курс')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлён')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('quote')
                    ->label('Валюта котировки')
                    ->options(array_combine(array_keys(self::QUOTES), array_keys(self::QUOTES)))
                    ->query(fn (Builder $query, array $data) => $data['value']
                        ? $query->where('symbol', 'like', '%'.$data['value'])
                        : $query),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }

    private static function quoteColor(string $symbol): string
    {
        foreach (['USDT', 'BTC', 'ETH'] as $quote) {
            if (str_ends_with($symbol, $quote)) {
                return self::QUOTES[$quote];
            }
        }

        return 'gray';
    }
}

<?php

namespace App\Filament\Resources\Products\Schemas;

use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->live(onBlur: true)
                    ->required()
                    ->afterStateUpdated(function (string $operation, $state, callable $set) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->required(),
                Select::make('department_id')
                    ->relationship('department', 'name')
                    ->label(__('Department'))
                    ->preload()
                    ->searchable()
                    ->required()
                    ->reactive() // To reload categories when department changes
                    ->afterStateUpdated(function (callable $set) {
                        $set('category_id', null); // Reset category when department changes
                    }),
                Select::make('category_id')
                    ->relationship(
                        name: 'category',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query, callable $get) {
                            // Modify the category query based on the selected department
                            $departmentId = $get('department_id');
                            if ($departmentId) {
                                $query->where('department_id', $departmentId); //Filter categories by department
                            }
                        }
                    )
                    ->label(__('Category'))
                    ->preload()
                    ->searchable()
                    ->required(),
                RichEditor::make('description')
                    ->required()
                    ->toolbarButtons([
                        'blockquote',
                        'bold',
                        'bulletList',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                        'table',
                    ])
                    ->columnSpan(2),
                TextInput::make('price')
                    ->required()
                    ->numeric(),
                TextInput::make('quantity')
                    ->integer(),
                Select::make('status')
                    ->options(\App\Enums\ProductStatusEnum::labels())
                    ->default(\App\Enums\ProductStatusEnum::Draft->value)
                    ->required(),
            ]);
    }
}

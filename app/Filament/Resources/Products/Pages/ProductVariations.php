<?php

namespace App\Filament\Resources\Products\Pages;

use App\Enums\ProductVariationTypeEnum;
use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class ProductVariations extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected static ?string $title = 'Variations';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    public static function getNavigationLabel(): string
    {
        return 'Variations';
    }

    public function form(Schema $schema): Schema
    {
        $types = $this->record->variationTypes;
        // dd($types);
        $fields = [];
        foreach ($types as $type) {
            $fields[] = TextInput::make('variation_type_' . ($type->id) . '.id')
                ->label($type->id);
            $fields[] = TextInput::make('variation_type_' . ($type->id) . '.name')
                ->label($type->name);
        }
        return $schema
            ->schema([
                Repeater::make('variations')
                    ->collapsible()
                    ->hiddenLabel()
                    ->addable(false)
                    ->defaultItems(1)
                    ->schema([
                        Section::make()
                            ->schema($fields)
                            ->columns(3),
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric(),
                        TextInput::make('price')
                            ->label('Price')
                            ->numeric()

                    ])
                    ->columns(2)
                    ->columnSpan(2)
            ]);

    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // dd($this->record);
        $variations = $this->record->variations->toArray();
        $data['variations'] = $this->mergeCartesianWithExisting($this->record->variationTypes, $variations);
        return $data;
    }

    private function mergeCarteSianWithExisting($variationTypes, $existingData): array
    {
        $defaultQuantity = $this->record->quantity;
        $defaultPrice = $this->record->price;
        $cartesianProduct = $this->cartesianProduct($variationTypes, $defaultQuantity, $defaultPrice);
        $mergedResult = [];

        foreach ($cartesianProduct as $product) {
            // Extract option IDs from the current product combination as an array
            $optionIds = collect($product)
                ->filter(fn($value, $key) => str_starts_with($key, 'variation_type'))
                ->map(fn($option) => $option['id'])
                ->values()
                ->toArray();

            // Find matching entry in existing data
            $match = array_filter($existingData, function ($existingOption) use ($optionIds) {
                return $existingOption['variation_type_option_ids'] === $optionIds;
            });

            // If match is found, override quantity and price
            if (!empty($match)) {
                $existingEntry = reset($match);
                $product['quantity'] = $existingEntry['quantity'];
                $product['price'] = $existingEntry['price'];
            } else {
                // Set default quantity and price if no match
                $product['quantity'] = $defaultQuantity;
                $product['price'] = $defaultPrice;
            }

            $mergedResult[] = $product;
        }
        return $mergedResult;
    }

    private function cartesianProduct($variationTypes, $defaultQuantity = null, $defaultPrice = null): array
    {
        $result = [[]];

        foreach ($variationTypes as $index => $variationType) {
            $temp = [];

            foreach ($variationType->options as $option) {
                // dd($option);
                // Add the current option to all existing combination
                foreach ($result as $combination) {
                    $newCombination = $combination + [
                        'variation_type_' . ($variationType->id) => [
                            'id' => $option->id,
                            'name' => $option->name,
                            'label' => $option->label
                        ],
                    ];

                    $temp[] = $newCombination;
                }
            }

            $result = $temp; // Update results with the new combination
        }

        // Add quantity and price to completed combinations
        foreach ($result as $combination) {
            if (count($combination) === count($variationTypes)) {
                $combination['quantity'] = $defaultQuantity;
                $combination['price'] = $defaultPrice;
            }
        }
        // dd($result);
        return $result;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Initialize an array to hold the formatted data
        $formattedData = [];
        // dd($data);
        // Loop through each variation to restructure it
        foreach ($data['variations'] as $option) {
            $variationTypeOptionIds = [];
            foreach ($this->record->variationTypes as $i => $variationType) {
                // dd($option['variation_type_' . $variationType->id . '.id']);
                $variationTypeOptionIds[] = $option['variation_type_' . ($variationType->id)]['id'];
            }

            $quantity = $option['quantity'];
            $price = $option['price'];

            // Prepare the data structure for the 
            $formattedData[] = [
                'variation_type_option_ids' => $variationTypeOptionIds,
                'quantity' => $quantity,
                'price' => $price
            ];
        }
        
        $data['variations'] = $formattedData;
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $variations = $data['variations'];
        unset($data['variations']);

        $record->update($data);
        $record->variations()->delete();
        $record->variations()->createMany($variations);
        return $record;
    }
}


<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Boutique;
use App\Models\Livraison;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class ListLivraison extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(Livraison::query()->orderBy('created_at', 'desc'))
            // ->paginated(false)
            ->columns([
                TextColumn::make('adresse')
                    ->label('Adresse')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('details')
                    ->label('Détails')
                    ->searchable(),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->searchable(),

                TextColumn::make('date_livraison')
                    ->label('Date livraison')
                    ->searchable(),

                TextColumn::make('ville')
                    ->label('Ville')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable(),

            ])
            ->headerActions([

            ])
            ->filters([
            ])
            ->actions([

            ])
            ->bulkActions([]);
    }

    public function render()
    {
        return view('livewire.list-livraison');
    }
}

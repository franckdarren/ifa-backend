<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Boutique;
use App\Models\Reclamation;
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

class ListReclamation extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(Reclamation::query()->orderBy('created_at', 'desc'))
            // ->paginated(false)
            ->columns([
                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->searchable(),

                TextColumn::make('commande.numero')
                    ->label('Commande')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Description')
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
        return view('livewire.list-reclamation');
    }
}

<?php

namespace App\Filament\Resources\BrandResource\Pages;

use App\Filament\Resources\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreateBrand extends CreateRecord
{
    protected static string $resource = BrandResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

 
    protected function getCreatedNotification(): ?Notification
    {
        $recipient = Auth::user();
        return Notification::make()
            ->success()
            ->title('Brand registered')
            ->body('The brand has been created successfully.')
            ->sendToDatabase($recipient);
    }
}

<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        $recipient = Auth::user();
        return Notification::make()
            ->success()
            ->title('Category registered')
            ->body('The category has been created successfully.')
            ->sendToDatabase($recipient);
    }
}

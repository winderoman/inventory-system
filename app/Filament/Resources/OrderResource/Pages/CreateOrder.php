<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {
        // Extraemos los datos del formulario
        $data = $this->data;
        $totalPrice = 0;

        foreach ($data['items'] as $item) {
            $totalPrice += floatval($item['unit_price']) * intval($item['quantity']);
        }

        $order = Order::create([
            'customer_id'    => $data['customer_id'],
            'number'         => $data['number'],
            'total_price'    => $totalPrice,
            'shipping_price' => $data['shipping_price'],
            'notes'          => $data['notes'],
            'status'         => $data['status'],
        ]);

        foreach ($data['items'] as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'quantity'   => intval($item['quantity']),
                'unit_price' => floatval($item['unit_price']),
            ]);
        }

        Notification::make()
            ->success()
            ->title('Order successfully registered')
            ->send();

        $this->redirect($this->getRedirectUrl());
        $this->halt();
    }

}
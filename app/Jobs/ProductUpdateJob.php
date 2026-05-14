<?php

namespace App\Jobs;

use App\Models\Product;
use App\Enums\ProductUpdateType;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProductUpdateJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;
    public $tries = 3;
    public $backoff = [60, 300, 600];

    public function __construct(
        public string $sku,
        public ProductUpdateType $type,
        public array $data
    ) {}

    public function handle(): void
    {   
        Log::info("Processando job: {$this->uniqueId()} para o produto {$this->sku}");
        

        $product = Product::where('sku', $this->sku)->first();

        if (!$product) {
            $this->fail(new \Exception("O produto com SKU {$this->sku} não foi encontrado."));
            return;
        }

        match($this->type) {
            ProductUpdateType::PRICE => $product->price = $this->data['price'],
            ProductUpdateType::STOCK => $product->stock = $this->data['stock'], 
            ProductUpdateType::DESCRIPTION => $product->description = $this->data['description'],
            ProductUpdateType::IMAGES => $product->images = $this->data['images'],
            ProductUpdateType::TAGS => $product->tags = $this->data['tags'],
        };

        $product->save();

        Log::info("Job processado com sucesso: {$this->uniqueId()} para o produto {$this->sku}");
    }

    public function uniqueId(): string
    {
        return $this->sku . '-' . $this->type->value . '-' . md5(json_encode($this->data));
    }

    public function failed(\Throwable $exception): void{
        Log::error("ALERTA CRÍTICO: Job falhou: {$this->uniqueId()} para o produto {$this->sku}", [
            'type' => $this->type->value,
            'payload' => $this->data,
            'error_message' => $exception->getMessage(),
        ]);
    }
}
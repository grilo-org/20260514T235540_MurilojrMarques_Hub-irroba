<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\ProductUpdateType;
use App\Http\Requests\UpdatePriceRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Http\Requests\UpdateDescriptionRequest;
use App\Http\Requests\UpdateImagesRequest;
use App\Http\Requests\UpdateTagsRequest;
use App\Jobs\ProductUpdateJob;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductUpdateController extends Controller
{
    #[OA\Patch(path: '/api/products/{sku}/price', operationId: 'updatePrice', summary: 'Atualiza o preço do produto', tags: ['Produtos ATUALIZAR'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'sku', in: 'path', required: true, description: 'Código único do produto', schema: new OA\Schema(type: 'string', example: 'SKU-12345'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['price'], properties: [new OA\Property(property: 'price', type: 'number', example: 299.90)]))]
    #[OA\Response(response: 202, description: 'Solicitação aceita e enviada para a fila SQS')]
    public function updatePrice(UpdatePriceRequest $request, string $sku): JsonResponse
    {
        return $this->dispatchUpdate($sku, ProductUpdateType::PRICE, $request->validated());
    }

    #[OA\Patch(path: '/api/products/{sku}/stock', operationId: 'updateStock', summary: 'Atualiza o estoque do produto', tags: ['Produtos ATUALIZAR'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'sku', in: 'path', required: true, description: 'Código único do produto', schema: new OA\Schema(type: 'string', example: 'SKU-12345'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['stock'], properties: [new OA\Property(property: 'stock', type: 'integer', example: 50)]))]
    #[OA\Response(response: 202, description: 'Solicitação aceita e enviada para a fila SQS')]
    public function updateStock(UpdateStockRequest $request, string $sku): JsonResponse
    {
        return $this->dispatchUpdate($sku, ProductUpdateType::STOCK, $request->validated());
    }

    #[OA\Patch(path: '/api/products/{sku}/description', operationId: 'updateDescription', summary: 'Atualiza a descrição', tags: ['Produtos ATUALIZAR'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'sku', in: 'path', required: true, description: 'Código único do produto', schema: new OA\Schema(type: 'string', example: 'SKU-12345'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['description'], properties: [new OA\Property(property: 'description', type: 'string', example: 'Nova descrição detalhada do produto...')]))]
    #[OA\Response(response: 202, description: 'Solicitação aceita e enviada para a fila SQS')]
    public function updateDescription(UpdateDescriptionRequest $request, string $sku): JsonResponse
    {
        return $this->dispatchUpdate($sku, ProductUpdateType::DESCRIPTION, $request->validated());
    }

    #[OA\Patch(path: '/api/products/{sku}/images', operationId: 'updateImages', summary: 'Atualiza as imagens do produto', tags: ['Produtos ATUALIZAR'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'sku', in: 'path', required: true, description: 'Código único do produto', schema: new OA\Schema(type: 'string', example: 'SKU-12345'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['images'], properties: [
        new OA\Property(property: 'images', type: 'array', description: 'Lista de URLs das imagens', items: new OA\Items(type: 'string', example: 'https://meusite.com/imagem1.jpg'))
    ]))]
    #[OA\Response(response: 202, description: 'Solicitação aceita e enviada para a fila SQS')]
    public function updateImages(UpdateImagesRequest $request, string $sku): JsonResponse
    {
        return $this->dispatchUpdate($sku, ProductUpdateType::IMAGES, $request->validated());
    }

    #[OA\Patch(path: '/api/products/{sku}/tags', operationId: 'updateTags', summary: 'Atualiza as tags do produto', tags: ['Produtos ATUALIZAR'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'sku', in: 'path', required: true, description: 'Código único do produto', schema: new OA\Schema(type: 'string', example: 'SKU-12345'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['tags'], properties: [
        new OA\Property(property: 'tags', type: 'array', description: 'Palavras-chave do produto', items: new OA\Items(type: 'string', example: 'lancamento'))
    ]))]
    #[OA\Response(response: 202, description: 'Solicitação aceita e enviada para a fila SQS')]
    public function updateTags(UpdateTagsRequest $request, string $sku): JsonResponse
    {
        return $this->dispatchUpdate($sku, ProductUpdateType::TAGS, $request->validated());
    }

    private function dispatchUpdate(string $sku, ProductUpdateType $type, array $data): JsonResponse
    {   
        ProductUpdateJob::dispatch($sku, $type, $data);

        return response()->json([
            'status' => 'success',
            'message' => "Solicitação de atualização de {$type->value} aceita e enviada para a fila.",
            'data' => [
                'sku' => $sku,
                'type' => $type->value,
                'payload' => $data
            ]
        ], 202);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product; 
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        operationId: 'getProducts',
        summary: 'Lista todos os produtos (Catálogo)',
        description: 'Retorna uma lista paginada dos produtos existentes no banco de dados. Útil para o recrutador validar o estado dos dados antes e depois do processamento das filas.',
        tags: ['Produtos BUSCAR'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(response: 200, description: 'Lista de produtos retornada com sucesso')]
    public function index(): JsonResponse
    {
        $products = Product::paginate(10);

        return response()->json($products, 200);
    }

    #[OA\Get(
        path: '/api/products/{sku}',
        operationId: 'getProductBySku',
        summary: 'Busca os detalhes de um produto específico',
        tags: ['Produtos BUSCAR'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(name: 'sku', in: 'path', required: true, description: 'Código único do produto', schema: new OA\Schema(type: 'string', example: 'SKU-12345'))]
    #[OA\Response(response: 200, description: 'Produto encontrado')]
    #[OA\Response(response: 404, description: 'Produto não encontrado')]
    public function show(string $sku): JsonResponse
    {
        $product = Product::where('sku', $sku)->firstOrFail();

        return response()->json($product, 200);
    }
}
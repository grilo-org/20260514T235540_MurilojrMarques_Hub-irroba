<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "Documentação da API de integração para e-commerce. Processamento assíncrono de catálogo via Amazon SQS.",
    title: "API HUB Irroba"
)]
#[OA\Server(url: "http://localhost:8000", description: "Servidor Local (Docker)")]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
abstract class Controller
{
    //
}
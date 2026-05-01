<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'DangDuc API',
    description: 'Tài liệu API công khai và quản trị cho nền tảng thương mại điện tử DangDuc.'
)]
#[OA\Server(
    url: '/',
    description: 'Máy chủ ứng dụng hiện tại'
)]
class ShopApiDocumentation
{
    #[OA\Get(
        path: '/api/v1/health',
        summary: 'Application health check',
        tags: ['Health'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Application is healthy or degraded',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'healthy'),
                        new OA\Property(property: 'timestamp', type: 'string', example: '2026-04-25T02:00:00.000000Z'),
                        new OA\Property(property: 'version', type: 'string', example: '1.0.0'),
                        new OA\Property(property: 'environment', type: 'string', example: 'local'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function health(): void {}
}

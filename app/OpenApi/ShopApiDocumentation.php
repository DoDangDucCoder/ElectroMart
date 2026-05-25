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
                description: 'Application is reachable',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'ok'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function health(): void {}
}

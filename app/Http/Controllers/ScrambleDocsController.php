<?php

namespace App\Http\Controllers;

use Dedoc\Scramble\Generator;
use Dedoc\Scramble\Scramble;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;

class ScrambleDocsController extends Controller
{
    /**
     * Generate API docs JSON for api.php routes
     */
    public function apiJson()
    {
        Scramble::routes(function (Route $route) {
            return Str::startsWith($route->uri, 'api/') || Str::startsWith($route->uri, 'api');
        });

        Scramble::extendOpenApi(function (\Dedoc\Scramble\Support\Generator\OpenApi $openApi) {
            $openApi->components->securitySchemes['token'] = \Dedoc\Scramble\Support\Generator\SecurityScheme::apiKey('header', 'token')
                ->setDescription('معرف التوكن الخاص بالمستخدم (token)');
            
            $openApi->components->securitySchemes['password'] = \Dedoc\Scramble\Support\Generator\SecurityScheme::apiKey('header', 'password')
                ->setDescription('كلمة مرور الـ API (password)');

            $openApi->security[] = [
                'token' => [],
                'password' => []
            ];
        });

        config(['scramble.api_path' => 'api']);

        try {
            $generator = app(Generator::class);
            return response()->json($generator());
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Generate API docs JSON for admin.php routes
     */
    public function adminJson()
    {
        Scramble::routes(function (Route $route) {
            return Str::startsWith($route->uri, 'admin/');
        });

        config(['scramble.api_path' => 'admin']);

        try {
            $generator = app(Generator::class);
            return response()->json($generator());
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Generate API docs JSON for web.php routes
     */
    public function webJson()
    {
        Scramble::routes(function (Route $route) {
            return !Str::startsWith($route->uri, 'api/')
                && !Str::startsWith($route->uri, 'api')
                && !Str::startsWith($route->uri, 'admin/')
                && !Str::startsWith($route->uri, 'member_panel/')
                && !Str::startsWith($route->uri, 'parking_panel/')
                && !Str::startsWith($route->uri, 'assistant_panel/')
                && !Str::startsWith($route->uri, 'docs/')
                && !Str::startsWith($route->uri, '_');
        });

        config(['scramble.api_path' => '']);

        try {
            $generator = app(Generator::class);
            return response()->json($generator());
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}

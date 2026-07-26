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


        config(['scramble.api_path' => 'api']);

        try {
            $generator = app(Generator::class);
            $docs = $generator();
            
            if (!isset($docs['components']['securitySchemes'])) {
                $docs['components']['securitySchemes'] = [];
            }
            $docs['components']['securitySchemes']['token'] = [
                'type' => 'apiKey',
                'in' => 'header',
                'name' => 'token',
                'description' => 'معرف التوكن الخاص بالمستخدم (token)',
            ];
            $docs['components']['securitySchemes']['password'] = [
                'type' => 'apiKey',
                'in' => 'header',
                'name' => 'password',
                'description' => 'كلمة مرور الـ API (password)',
            ];
            $docs['security'][] = [
                'token' => [],
                'password' => []
            ];

            return response()->json($docs);
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
            $docs = $generator();
            
            if (!isset($docs['components']['securitySchemes'])) {
                $docs['components']['securitySchemes'] = [];
            }
            $docs['components']['securitySchemes']['token'] = [
                'type' => 'apiKey',
                'in' => 'header',
                'name' => 'token',
                'description' => 'معرف التوكن الخاص بالمستخدم (token)',
            ];
            $docs['components']['securitySchemes']['password'] = [
                'type' => 'apiKey',
                'in' => 'header',
                'name' => 'password',
                'description' => 'كلمة مرور الـ API (password)',
            ];
            $docs['security'][] = [
                'token' => [],
                'password' => []
            ];

            return response()->json($docs);
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
            $docs = $generator();
            
            if (!isset($docs['components']['securitySchemes'])) {
                $docs['components']['securitySchemes'] = [];
            }
            $docs['components']['securitySchemes']['token'] = [
                'type' => 'apiKey',
                'in' => 'header',
                'name' => 'token',
                'description' => 'معرف التوكن الخاص بالمستخدم (token)',
            ];
            $docs['components']['securitySchemes']['password'] = [
                'type' => 'apiKey',
                'in' => 'header',
                'name' => 'password',
                'description' => 'كلمة مرور الـ API (password)',
            ];
            $docs['security'][] = [
                'token' => [],
                'password' => []
            ];

            return response()->json($docs);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}

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
    public function apiJson(Generator $generator)
    {
        Scramble::routes(function (Route $route) {
            return Str::startsWith($route->uri, 'api/') || Str::startsWith($route->uri, 'api');
        });

        return $generator();
    }

    /**
     * Generate API docs JSON for admin.php routes
     */
    public function adminJson(Generator $generator)
    {
        Scramble::routes(function (Route $route) {
            return Str::startsWith($route->uri, 'admin/');
        });

        // Override config for this request so paths are trimmed correctly
        config(['scramble.api_path' => 'admin']);

        return $generator();
    }

    /**
     * Generate API docs JSON for web.php routes
     */
    public function webJson(Generator $generator)
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

        return $generator();
    }
}

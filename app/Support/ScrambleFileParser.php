<?php

namespace App\Support;

use Dedoc\Scramble\Infer\Services\FileParser;
use Dedoc\Scramble\Infer\Services\FileParserResult;
use Dedoc\Scramble\Infer\Services\FileNameResolver;
use PhpParser\NameContext;
use PhpParser\ErrorHandler\Throwing;
use PhpParser\Error;

class ScrambleFileParser extends FileParser
{
    public function parseContent(string $content): FileParserResult
    {
        try {
            return parent::parseContent($content);
        } catch (Error $e) {
            // Attempt to fix interface parsing bug by removing the trailing brace
            if (str_ends_with(trim($content), '}')) {
                $content = preg_replace('/\}\s*$/', '', $content);
                try {
                    return parent::parseContent($content);
                } catch (\Throwable $e2) {
                    // Ignore and fallback
                }
            }
            
            return new FileParserResult([], new FileNameResolver(new NameContext(new Throwing())));
        }
    }
}

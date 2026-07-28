<?php

namespace App\Services;

class MarkdownSanitizer
{
    private \HTMLPurifier $purifier;

    public function __construct()
    {
        $config = \HTMLPurifier_Config::createDefault();

        // Usa diretório de cache do Laravel (já writable)
        $cachePath = storage_path('app/htmlpurifier');
        if (!is_dir($cachePath)) {
            mkdir($cachePath, 0755, true);
        }
        $config->set('Cache.SerializerPath', $cachePath);

        $this->purifier = new \HTMLPurifier($config);
    }

    public function render(string $markdown): string
    {
        $html = \Illuminate\Support\Str::markdown($markdown);

        return $this->purifier->purify($html);
    }
}

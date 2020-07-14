<?php

namespace Spatie\LaravelTypescriptTransformer\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Spatie\TypescriptTransformer\TypescriptTransformer;
use Spatie\TypescriptTransformer\TypeScriptTransformerConfig;

class MapOptionsToTypescriptCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'typescript:transform {input?} {output?}';

    protected $description = 'Map PHP structures to Typescript';

    public function handle(
        TypeScriptTransformerConfig $config
    ): void {
        $this->confirmToProceed();

        if($inputPath = $this->resolveInputPath()){
            $config->searchingPath($inputPath);
        }

        if($inputPath = $this->resolveOutputPath()){
            $config->outputFile($inputPath);
        }

        $transformer = new TypescriptTransformer($config);

        try {
            $collection = $transformer->transform();
        } catch (Exception $exception) {
            $this->error($exception->getMessage());

            return;
        }

        $this->info("Transformed {$collection->count()} PHP types to Typescript");

        foreach ($collection->getTypes() as $class => $type) {
            $this->info("{$class} -> {$type->getTypescriptName()}");
        }
    }

    private function resolveInputPath(): ?string
    {
        $path = $this->argument('input');

        if ($path === null) {
            return null;
        }

        if (file_exists($path)) {
            return $path;
        }

        return app_path($path);
    }

    private function resolveOutputPath(): ?string
    {
        $path = $this->argument('output');

        if ($path === null) {
            return null;
        }

        if (file_exists($path)) {
            return $path;
        }

        return resource_path($path);
    }
}

<?php

namespace StatamicVaporCompatibility\Http\Middleware;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use StatamicVaporCompatibility\Exceptions\InvalidFilesRepositorySettings;
use StatamicVaporCompatibility\Tools\TemporaryStorage;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Statamic\Console\Processes\Process;
use StatamicVaporCompatibility\Traits\GitManageable;
use Throwable;
use function array_shift;
use function base_path;
use function config;
use function escapeshellarg;
use function explode;
use function file_get_contents;
use function str_replace;
use function strpos;
use function substr;
use function throw_unless;
use function trim;

class StatamicVaporCheckIfTempFilesExist
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Throwable
     */
    public function handle(Request $request, Closure $next)
    {
        if (empty($_ENV['LAMBDA_TASK_ROOT']) || app()->isLocal()) {
            return $next($request);
        }

        $temporaryStorage = Storage::disk(TemporaryStorage::TEMP_DISK_NAME);

        // TODO: convert to command for vapor build step
        foreach(config('statamic-vapor-compatibility.symlinks') as $src => $dest) {
            if(! $temporaryStorage->exists($dest)) {
                $temporaryStorage->makeDirectory($dest);
            }
        }

        return $next($request);
    }
}

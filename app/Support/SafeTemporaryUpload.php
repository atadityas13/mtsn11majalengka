<?php

namespace App\Support;

use Closure;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

class SafeTemporaryUpload
{
    /**
     * @param  list<string>  $extensions
     */
    public static function rules(array $extensions, int $maxKilobytes, string $label = 'Berkas'): Closure
    {
        $extensions = array_map(strtolower(...), $extensions);

        return function () use ($extensions, $maxKilobytes, $label): Closure {
            return function (string $attribute, mixed $value, Closure $fail) use ($extensions, $maxKilobytes, $label): void {
                foreach ((array) $value as $file) {
                    if (! $file instanceof TemporaryUploadedFile) {
                        continue;
                    }

                    try {
                        if (! $file->exists()) {
                            $fail($label.' sementara sudah hilang atau kedaluwarsa. Unggah ulang file-nya.');

                            continue;
                        }

                        $sizeKb = (int) ceil($file->getSize() / 1024);

                        if ($sizeKb > $maxKilobytes) {
                            $fail(sprintf(
                                '%s maksimal %s MB. Kompres dulu jika terlalu besar.',
                                $label,
                                rtrim(rtrim(number_format($maxKilobytes / 1024, 1, '.', ''), '0'), '.')
                            ));

                            continue;
                        }
                    } catch (Throwable) {
                        $fail($label.' sementara sudah hilang atau kedaluwarsa. Unggah ulang file-nya.');

                        continue;
                    }

                    $ext = strtolower((string) ($file->getClientOriginalExtension() ?: $file->guessExtension() ?: ''));

                    if ($extensions !== [] && ! in_array($ext, $extensions, true)) {
                        $fail(sprintf(
                            '%s harus berformat %s.',
                            $label,
                            strtoupper(implode(', ', $extensions))
                        ));
                    }
                }
            };
        };
    }
}

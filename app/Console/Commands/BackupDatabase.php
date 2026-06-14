<?php

namespace App\Console\Commands;

use App\Models\BackupLog;
use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    /**
     * Ejecutar manualmente: php artisan backup:database
     */
    protected $signature = 'backup:database';

    protected $description = 'Genera un mysqldump de la base de datos y registra el resultado en backup_logs';

    public function handle(): void
    {
        $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $dir      = storage_path('app/backups');
        $path     = $dir . DIRECTORY_SEPARATOR . $filename;

        // Asegurar que el directorio de backups exista
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $password     = env('DB_PASSWORD', '');
        $passwordFlag = !empty($password) ? " --password=\"{$password}\"" : '';

        $command = '"C:\xampp\mysql\bin\mysqldump.exe"'
            . ' --user="' . env('DB_USERNAME') . '"'
            . $passwordFlag
            . ' --host="' . env('DB_HOST') . '"'
            . ' ' . env('DB_DATABASE')
            . ' > "' . $path . '" 2>&1';

        exec($command, $output, $returnVar);

        $succeeded  = ($returnVar === 0 && file_exists($path) && filesize($path) > 0);
        $fileSize   = ($succeeded && file_exists($path)) ? filesize($path) : 0;
        $errorMsg   = !$succeeded ? implode("\n", $output) : null;

        BackupLog::create([
            'filename'      => $filename,
            'file_size'     => $fileSize,
            'status'        => $succeeded ? 'success' : 'failed',
            'error_message' => $errorMsg,
            'created_at'    => now(),
        ]);

        if ($succeeded) {
            $this->info("[" . now()->format('Y-m-d H:i:s') . "] Backup creado correctamente: {$filename} (" . round($fileSize / 1024, 1) . " KB)");
        } else {
            $this->error("[" . now()->format('Y-m-d H:i:s') . "] Error al crear el backup. Revisa backup_logs para más detalles.");
        }
    }
}

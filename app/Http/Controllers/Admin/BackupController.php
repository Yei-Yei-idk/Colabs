<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function backup()
    {
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        // Crear carpeta si no existe
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $password = env('DB_PASSWORD');
        $passwordFlag = !empty($password) ? " --password=\"$password\"" : "";

        $command = "\"C:\\xampp\\mysql\\bin\\mysqldump.exe\" --user=\"" . env('DB_USERNAME') . "\"" . 
        $passwordFlag . 
        " --host=\"" . env('DB_HOST') . "\" " . 
        env('DB_DATABASE') . " > \"$path\" 2>&1";

        exec($command, $output, $returnVar);

        if ($returnVar !== 0 || !file_exists($path)) {
            return back()->with('error', 'Error al crear la copia de seguridad. Asegúrate de que MySQL/MariaDB esté activo y las credenciales sean correctas.');
        }

        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function restore(Request $request) 
    {
        $request->validate([
            'backup' => 'required|file|mimes:sql,txt'
        ], [
            'backup.required' => 'Debes seleccionar un archivo SQL.',
            'backup.file' => 'El archivo no es válido.',
            'backup.mimes' => 'El archivo debe tener extensión .sql o .txt.'
        ]);

        $file = $request->file('backup');
        $fullPath = $file->getPathname();

        $password = env('DB_PASSWORD');
        $passwordFlag = !empty($password) ? " --password=\"$password\"" : "";

        $command = "\"C:\\xampp\\mysql\\bin\\mysql.exe\" --user=\"" . env('DB_USERNAME') . "\"" . 
        $passwordFlag . 
        " --host=\"" . env("DB_HOST") . "\" " . 
        env('DB_DATABASE') . " < \"$fullPath\" 2>&1";

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return back()->with('error', 'Error al restaurar la base de datos. Comprueba el formato de tu archivo SQL.');
        }

        return back()->with('success', 'Base de datos restaurada correctamente');
    }

    public function menu() {
        return view('admin.copia_seguridad.menu');
    }
}



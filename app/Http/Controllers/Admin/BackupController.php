<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackupLog;
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

        $fileSize = filesize($path);
        BackupLog::create([
            'filename' => $filename,
            'file_size' => $fileSize,
            'status' => 'success',
            'error_message' => null
        ]);

        return back()->with('success', 'Copia de seguridad creada y guardada correctamente.');
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
        $logs = BackupLog::orderByDesc('created_at')->paginate(20);
        return view('admin.copia_seguridad.menu', compact('logs'));
    }



    public function download($id)
    {
        $log = BackupLog::findOrFail($id);
        $path = storage_path('app/backups/' . $log->filename);

        if (!file_exists($path)) {
            return back()->with('error', 'El archivo no existe en el servidor.');
        }

        return response()->download($path);
    }

    public function restoreFromHistory($id)
    {
        $log = BackupLog::findOrFail($id);
        $path = storage_path('app/backups/' . $log->filename);

        if (!file_exists($path)) {
            return back()->with('error', 'El archivo no existe en el servidor.');
        }

        $password = env('DB_PASSWORD');
        $passwordFlag = !empty($password) ? " --password=\"$password\"" : "";

        $command = "\"C:\\xampp\\mysql\\bin\\mysql.exe\" --user=\"" . env('DB_USERNAME') . "\"" . 
        $passwordFlag . 
        " --host=\"" . env("DB_HOST") . "\" " . 
        env('DB_DATABASE') . " < \"$path\" 2>&1";

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return back()->with('error', 'Error al restaurar la base de datos.');
        }

        return back()->with('success', 'Base de datos restaurada correctamente desde el historial.');
    }

    public function delete($id)
    {
        $log = BackupLog::findOrFail($id);
        $path = storage_path('app/backups/' . $log->filename);

        if (file_exists($path)) {
            unlink($path);
        }

        $log->delete();

        return back()->with('success', 'Copia de seguridad eliminada correctamente.');
    }
}

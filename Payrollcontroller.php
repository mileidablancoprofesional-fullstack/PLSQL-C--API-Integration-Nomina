<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Usando sintaxis de Laravel para ejemplo
use Illuminate\Http\JsonResponse;

class PayrollController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/payroll/recalculate-saldos",
     * summary="Dispara el recálculo concurrente de saldos post-aprobación de novedades.",
     * // ... (Documentación Swagger/OpenAPI)
     * )
     */
    public function recalculateSaldos(Request $request): JsonResponse
    {
        // 1. VALIDACIÓN DEL REQUEST
        $validatedData = $request->validate([
            // Esperamos un array de objetos con las novedades críticas
            'novelties' => 'required|array|min:1',
            'novelties.*.novelty_id' => 'required|integer',
            'novelties.*.user_id' => 'required|integer',
            'novelties.*.effective_date' => 'required|date_format:Y-m-d',
        ]);

        $noveltiesToProcess = $validatedData['novelties'];
        
        // El ejecutable C++ que creaste (asume que está en /opt/payroll_engine/)
        $cxx_executable_path = '/opt/payroll_engine/recalc_engine'; 

        // 2. PREPARACIÓN DE DATOS PARA C++
        // Serializamos los datos de entrada a JSON para pasarlos al ejecutable C++
        // Esto simplifica el parsing en C++ (usando librerías como nlohmann/json)
        $inputJson = json_encode($noveltiesToProcess);
        
        if ($inputJson === false) {
             return response()->json([
                'status' => 'error',
                'message' => 'Error al serializar datos de entrada para el motor C++.'
            ], 500);
        }

        // 3. INVOCACIÓN DEL MÓDULO C++ CONCURRENTE
        // Usamos 'shell_exec' o 'proc_open' para ejecutar el binario de C++
        // La entrada de datos se pasa como un argumento de línea de comandos.

        $command = escapeshellcmd($cxx_executable_path) . ' ' . escapeshellarg($inputJson);
        
        // Ejecutar el comando. Para procesos asíncronos y largos, se debería usar
        // un sistema de colas (Queue) o ejecutar el comando en segundo plano (nohup &).
        // Aquí, usamos shell_exec para obtener la salida directamente (síncrono simple).
        $output = shell_exec($command);
        
        // 4. PROCESAMIENTO DE LA SALIDA DEL C++
        // Asumimos que el módulo C++ devuelve una cadena JSON con los resultados de cada thread.
        $results = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
             // Si el C++ falló o devolvió un formato incorrecto.
             return response()->json([
                'status' => 'error',
                'message' => 'Error de comunicación o formato de salida del motor C++.',
                'cxx_output_raw' => $output // Para debug
            ], 500);
        }
        
        // 5. RESPUESTA Y MONITOREO
        // Revisamos si todas las tareas tuvieron éxito (código de estado 0)
        $failedTasks = array_filter($results, function ($task) {
            return $task['status_code'] !== 0;
        });

        if (empty($failedTasks)) {
            return response()->json([
                'status' => 'success',
                'message' => 'El recálculo concurrente se completó exitosamente para todas las novedades.',
                'details' => $results
            ], 200);
        } else {
             return response()->json([
                'status' => 'partial_failure',
                'message' => 'El recálculo finalizó, pero algunas tareas fallaron. Revise los detalles.',
                'failed_tasks_count' => count($failedTasks),
                'details' => $results // Devolvemos todos los resultados para auditoría
            ], 202); // 202 Accepted, ya que el proceso ha finalizado con resultados mixtos.
        }
    }
}

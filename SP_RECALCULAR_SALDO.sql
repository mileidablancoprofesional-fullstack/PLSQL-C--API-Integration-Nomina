CREATE OR REPLACE PROCEDURE SP_RECALCULAR_SALDO (
    p_novelty_id      IN NUMBER,       -- ID de la novedad que disparó el recálculo
    p_user_id         IN NUMBER,       -- ID del usuario afectado
    p_effective_date  IN DATE,         -- Fecha de aplicación del cambio
    p_status_code     OUT NUMBER,      -- Código de salida (0 = Éxito, <0 = Error)
    p_error_message   OUT VARCHAR2     -- Mensaje de error, si lo hay
)
AS
    -- Variables locales para el cálculo y la lógica
    v_current_balance   NUMBER;
    v_recalc_adjustment NUMBER;
    v_new_balance       NUMBER;
    
BEGIN
    -- 1. INICIALIZACIÓN
    p_status_code := 0; -- Asumimos éxito al inicio

    -- Desactivar temporalmente la ejecución automática de COMMIT si aplica
    -- (En un SP, el commit/rollback es explícito o manejado por la sesión)

    -- 2. LÓGICA DE NEGOCIO Y CÁLCULO
    
    -- a. Obtener el saldo actual (SELECT FOR UPDATE bloquea la fila para evitar concurrencia)
    SELECT balance_amount INTO v_current_balance
    FROM TBL_SALDOS
    WHERE user_id = p_user_id
    FOR UPDATE OF balance_amount NOWAIT; -- Bloquea la fila y lanza error si ya está bloqueada

    -- b. Determinar el ajuste basado en la novedad (Lógica compleja aquí)
    -- *** Esta es la parte más compleja del PL/SQL que usa la p_novelty_id ***
    v_recalc_adjustment := FUNC_GET_ADJUSTMENT(p_novelty_id, p_effective_date);
    
    -- c. Calcular el nuevo saldo
    v_new_balance := v_current_balance + v_recalc_adjustment;

    -- 3. ACTUALIZACIONES DE DATOS (Transacción Atómica)
    
    -- a. Actualización principal de la tabla de saldos
    UPDATE TBL_SALDOS
    SET balance_amount = v_new_balance,
        last_recalc_date = SYSDATE
    WHERE user_id = p_user_id;
    
    -- b. Registro de la transacción en la tabla de historial (Para auditoría)
    INSERT INTO TBL_HISTORIAL_SALDOS (
        user_id, transaction_date, old_balance, new_balance, novelty_id
    ) VALUES (
        p_user_id, SYSDATE, v_current_balance, v_new_balance, p_novelty_id
    );

    -- 4. FINALIZACIÓN ATÓMICA
    -- Si llegamos aquí, todas las sentencias fueron exitosas.
    COMMIT; 

EXCEPTION
    -- BLOQUE DE MANEJO DE ERRORES (Asegura el ROLLBACK)
    WHEN NO_DATA_FOUND THEN
        -- El user_id no existe en TBL_SALDOS
        p_status_code := -100;
        p_error_message := 'Error: Usuario no encontrado para el recálculo.';
        ROLLBACK;
        
    WHEN OTHERS THEN
        -- Captura cualquier otro error (bloqueo de fila, error de SQL, etc.)
        p_status_code := -999;
        p_error_message := 'Error en recálculo transaccional: ' || SQLERRM;
        ROLLBACK;
        
END SP_RECALCULAR_SALDO;
/

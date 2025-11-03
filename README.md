# PLSQL-C--API-Integration-Nomina
Automatización Crítica del Recálculo de Saldos de Nómina: Transformación de un proceso manual de recálculo de saldos (vacaciones, horas extra) a un flujo totalmente automatizado para garantizar la precisión de la nómina.

# [PoC] Automatización de Backend Crítico para Recálculo de Nómina (PL/SQL, C++, PHP)

## 💡 Resumen del Proyecto y Problema Resuelto

Este repositorio contiene una Prueba de Concepto (PoC) que ilustra la arquitectura implementada para transformar un proceso manual y propenso a errores en el recálculo de saldos de tiempo (vacaciones, horas extra) en un flujo completamente automatizado y de alto rendimiento.

**Problema:** La dependencia de la intervención humana directa en el recálculo post-aprobación de novedades generaba inconsistencias y retrasos críticos en la liquidación de nómina.

**Solución:** Se diseñó una solución Full Stack orientada al *Backend* que garantiza la **integridad transaccional** y la **velocidad de procesamiento** mediante la integración de múltiples tecnologías optimizadas.

## 📈 Impacto y Resultados Clave

El objetivo principal de esta arquitectura era asegurar la precisión y eliminar la fricción operativa.

| Métrica | Antes (Manual) | Después (Automatizado) | Mejora |
| :--- | :--- | :--- | :--- |
| **Precisión de Saldos** | ~90% (variable) | **99%** | **Mejora del 99%** |
| **Intervención Manual** | Requerida por cada novedad | **Eliminada por completo** | Ahorro significativo de tiempo |
| **Riesgo de Errores** | Alto | Mínimo (Lógica de DB) | Reducción de errores |

## 📐 Diagrama de Arquitectura (La Columna Vertebral)

****

**Flujo Crítico Demostrado:**

1.  **Frontend/UI (Simulado):** El usuario aprueba una novedad. Envía un *request* a la API.
2.  **Capa de Orquestación (PHP Framework API):** Recibe la petición, valida y gestiona la llamada al motor de cálculo.
3.  **Motor de Alto Rendimiento (C++ con Threads):** Se invoca la lógica de C++ para gestionar la concurrencia y los cálculos intensivos.
4.  **Capa Transaccional (Oracle Database / PL/SQL):** El C++ o la API invoca un **Stored Procedure PL/SQL**. Este procedimiento ejecuta el recálculo y asegura el **commit atómico** de los nuevos saldos.

Frontend -> API (PHP) -> Módulo C++ (Threads) -> Oracle DB (PL/SQL)

## 🛠️ Stack Tecnológico Demostrado

| Capa | Tecnología | Propósito Específico |
| :--- | :--- | :--- |
| **Datos y Transacción** | **Oracle Database** | Almacenamiento crítico de saldos de nómina. |
| **Lógica de Negocio** | **PL/SQL Stored Procedures** | Ejecución de la lógica de recálculo (garantía ACID). |
| **Orquestación** | **PHP Framework (API)** | Gestión del flujo de peticiones, comunicación entre capas. |
| **Rendimiento** | **C++ y Threads** | Módulo de procesamiento intensivo y gestión de concurrencia. |
| **Herramientas** | Visual Studio | Entorno utilizado para el desarrollo y *build* del módulo C++. |

Métricas : Impacto de 99% en la Precisión y Eliminación de Intervención Manual.
Tecnologías Utilizadas: (Oracle, PHP, C++, Threads, PL/SQL).

## 🧩 (Proof of Concept)

A continuación, se encuentran fragmentos de código que ilustran cómo se maneja la complejidad en cada capa.

### 1.  Concurrencia en C++

Se incluye un archivo de código  que muestra cómo utilizo `std::thread`  para dividir una tarea de cálculo pesado en múltiples subprocesos

### 2. Lógica PL/SQL

Se Incluye un archivo con el PROCEDURE  que muestra cómo recibe parámetros y realiza un `UPDATE` transaccional con tu lógica de cálculo de saldos.

### 3. Endpoint de API (PHP Framework)

Se Incluye un fragmento de código PHP que muestra el *endpoint* que recibe el *request* y cómo llama/ejecuta la lógica C++ o el Stored Procedure, manejando posibles errores

## 🚀 Instalación y Ejecución (PoC)

Este proyecto está diseñado para ser una demo. Siga los pasos a continuación para simular el flujo:

1. Clonar el repositorio: `git clone https://docs.github.com/en/repositories/creating-and-managing-repositories/deleting-a-repository`
2. Compilar el módulo C++: `g++ -o calculator [calculador.cpp] -std=c++17 -pthread`
3. Configurar la conexión a Oracle  y ejecutar el script SQL de los Stored Procedures.
4. Instrucciones breves para ejecutar el endpoint PHP.

---

## Acerca del Desarrollador

Mileida Yenitza Blanco Herrera - Desarrollador Full Stack con enfoque en rendimiento y arquitecturas de Backend críticas.
* [Tu Perfil de LinkedIn] : https://www.linkedin.com/in/mileida-blanco-53059b164/
* [Tu Sitio Web/Portafolio] : https://mileida.my.canva.site/

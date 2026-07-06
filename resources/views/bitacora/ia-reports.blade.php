@extends('layouts.ferreteria')

@section('title', 'Reportes con Inteligencia Artificial - Ferretería Guisella')
@section('wrap_class', 'wide')

@section('content')
<div class="animate-fade-up">
    <div class="page-header" style="justify-content: flex-start; gap: 15px; margin-bottom: 25px;">
        <a href="{{ route('inventario') }}" class="btn-circle" style="text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <div>
            <h1 style="margin: 0; background: linear-gradient(135deg, #00af9a, #00796b); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Reportes por IA (Voz)</h1>
            <p class="subtitle" style="margin: 0;">Consulta y genera reportes personalizados del sistema mediante comandos de voz</p>
        </div>
    </div>

    <!-- Panel de control de voz -->
    <div class="card" style="margin-bottom: 30px; border-top: 4px solid #00af9a; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);">
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px 10px; gap: 20px;">
            
            <!-- Botón del Micrófono -->
            <div style="position: relative;">
                <button type="button" id="btn-microphone" style="width: 80px; height: 80px; border-radius: 50%; border: none; background: #00af9a; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); outline: none; box-shadow: 0 10px 20px rgba(0, 175, 154, 0.3);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mic-icon"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
                </button>
                <div id="mic-pulse" style="position: absolute; top: -5px; left: -5px; right: -5px; bottom: -5px; border-radius: 50%; border: 3px solid #ef4444; opacity: 0; pointer-events: none; transition: all 0.3s; transform: scale(1);"></div>
            </div>

            <div style="text-align: center;">
                <h3 id="mic-status" style="margin: 0; font-size: 1.1rem; color: var(--text-main); font-weight: 700;">Presiona el micrófono para hablar</h3>
                <p style="margin: 4px 0 0; font-size: 0.85rem; color: var(--text-light);">Por ejemplo: "Muéstrame las marcas registradas ordenadas alfabéticamente" o "Productos con stock menor a 5"</p>
            </div>

            <!-- Formulario de Consulta -->
            <form id="form-ia-query" style="width: 100%; max-width: 700px; display: flex; gap: 10px; margin-top: 10px;">
                @csrf
                <div style="position: relative; flex-grow: 1;">
                    <input type="text" id="input-consulta" name="consulta" placeholder="Escribe o dicta tu reporte aquí..." required style="width: 100%; padding: 14px 45px 14px 16px; border-radius: 12px; border: 2px solid var(--border); outline: none; font-size: 1rem; transition: all 0.2s; font-weight: 500;" onfocus="this.style.borderColor='#00af9a';" onblur="this.style.borderColor='var(--border)';">
                    <button type="button" id="btn-clear" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-light); cursor: pointer; display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                <button type="submit" class="btn-save" style="border-radius: 12px; padding: 0 24px; font-weight: 700; white-space: nowrap; display: flex; align-items: center; gap: 8px;">
                    <span>Consultar</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Indicador de carga -->
    <div id="loading-container" style="display: none; text-align: center; margin: 40px 0;">
        <div style="display: inline-block; width: 50px; height: 50px; border: 4px solid rgba(0,175,154,0.1); border-left-color: #00af9a; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        <p style="margin-top: 15px; font-weight: 600; color: var(--text-muted);">La Inteligencia Artificial está procesando tu consulta...</p>
    </div>

    <!-- Mensaje de Error -->
    <div id="error-container" style="display: none; margin-bottom: 25px;" class="animate-fade-up">
        <div class="alert alert-error" style="border-radius: 12px; padding: 18px; display: flex; gap: 12px; align-items: flex-start;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
            <div>
                <strong style="display: block; margin-bottom: 4px; font-size: 1.05rem;">Hubo un problema al procesar la solicitud</strong>
                <span id="error-message"></span>
            </div>
        </div>
    </div>

    <!-- Resultados del Reporte -->
    <div id="results-container" style="display: none;" class="animate-fade-up">
        <div class="card" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                <h2 style="margin: 0; font-size: 1.3rem; color: var(--text-main); font-weight: 800;">Resultado del Reporte</h2>
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <span id="results-count" style="font-weight: 700; font-size: 0.85rem; color: #00af9a; background: rgba(0, 175, 154, 0.1); padding: 6px 14px; border-radius: 20px;">0 registros encontrados</span>
                    <button type="button" id="btn-export-csv" class="btn-action" style="padding: 6px 14px; display: flex; align-items: center; gap: 6px; font-weight: 700; border-radius: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        CSV
                    </button>
                    <button type="button" id="btn-export-pdf" class="btn-action" style="padding: 6px 14px; display: flex; align-items: center; gap: 6px; font-weight: 700; border-radius: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                        PDF
                    </button>
                </div>
            </div>

            <div class="table-wrap" style="overflow-x: auto;">
                <table id="table-results">
                    <thead id="table-head">
                        <!-- Generado dinámicamente -->
                    </thead>
                    <tbody id="table-body">
                        <!-- Generado dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Animaciones personalizadas -->
<style>
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    @keyframes pulse {
        0% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.15); opacity: 0.8; }
        100% { transform: scale(1); opacity: 0.5; }
    }
    .mic-recording {
        background: #ef4444 !important;
        box-shadow: 0 0 25px rgba(239, 68, 68, 0.5) !important;
        animation: pulse 1.5s infinite;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnMic = document.getElementById('btn-microphone');
    const micPulse = document.getElementById('mic-pulse');
    const micStatus = document.getElementById('mic-status');
    const inputConsulta = document.getElementById('input-consulta');
    const btnClear = document.getElementById('btn-clear');
    const formIaQuery = document.getElementById('form-ia-query');
    const loadingContainer = document.getElementById('loading-container');
    const errorContainer = document.getElementById('error-container');
    const errorMessage = document.getElementById('error-message');
    const resultsContainer = document.getElementById('results-container');
    const resultsCount = document.getElementById('results-count');
    const tableHead = document.getElementById('table-head');
    const tableBody = document.getElementById('table-body');
    const btnExportCsv = document.getElementById('btn-export-csv');
    const btnExportPdf = document.getElementById('btn-export-pdf');

    // Variables globales para guardar los datos actuales de la consulta
    let currentData = [];
    let currentColumns = [];

    // Manejar el botón de limpiar texto
    inputConsulta.addEventListener('input', function() {
        btnClear.style.display = this.value ? 'block' : 'none';
    });

    btnClear.addEventListener('click', function() {
        inputConsulta.value = '';
        btnClear.style.display = 'none';
        inputConsulta.focus();
    });

    // Configurar la Web Speech API
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    let recognition = null;
    let isRecording = false;

    if (SpeechRecognition) {
        recognition = new SpeechRecognition();
        recognition.lang = 'es-BO'; // Idioma preferido (Español Bolivia/Latinoamérica)
        recognition.continuous = false;
        recognition.interimResults = false;

        recognition.onstart = function() {
            isRecording = true;
            btnMic.classList.add('mic-recording');
            micPulse.style.opacity = '1';
            micPulse.style.transform = 'scale(1.1)';
            micStatus.textContent = "Escuchando... Habla ahora.";
            micStatus.style.color = '#ef4444';
        };

        recognition.onend = function() {
            isRecording = false;
            btnMic.classList.remove('mic-recording');
            micPulse.style.opacity = '0';
            micPulse.style.transform = 'scale(1)';
            micStatus.textContent = "Presiona el micrófono para hablar";
            micStatus.style.color = 'var(--text-main)';
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error: ', event.error);
            micStatus.textContent = "Error al reconocer voz: " + event.error;
            micStatus.style.color = '#ef4444';
        };

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            inputConsulta.value = transcript;
            btnClear.style.display = 'block';
            
            // Enviar automáticamente el formulario tras el dictado
            formIaQuery.dispatchEvent(new Event('submit'));
        };

        btnMic.addEventListener('click', function() {
            if (isRecording) {
                recognition.stop();
            } else {
                // Limpiar resultados previos
                errorContainer.style.display = 'none';
                resultsContainer.style.display = 'none';
                
                recognition.start();
            }
        });
    } else {
        // El navegador no soporta SpeechRecognition
        btnMic.style.background = '#94a3b8';
        btnMic.style.boxShadow = 'none';
        btnMic.style.cursor = 'not-allowed';
        micStatus.textContent = "Reconocimiento de voz no soportado en este navegador.";
        micStatus.style.color = 'var(--text-muted)';
        btnMic.disabled = true;
    }

    // Procesar la consulta en el servidor
    formIaQuery.addEventListener('submit', function(e) {
        e.preventDefault();

        const consultaVal = inputConsulta.value.trim();
        if (!consultaVal) return;

        // Reset de la UI
        errorContainer.style.display = 'none';
        resultsContainer.style.display = 'none';
        loadingContainer.style.display = 'block';

        currentData = [];
        currentColumns = [];

        fetch("{{ route('reportes-ia.consultar') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ consulta: consultaVal })
        })
        .then(response => response.json().then(data => ({ status: response.status, data })))
        .then(({ status, data }) => {
            loadingContainer.style.display = 'none';

            if (status !== 200 || !data.success) {
                // Mostrar error
                errorContainer.style.display = 'block';
                errorMessage.textContent = data.message || 'Error desconocido';
                return;
            }

            // Guardar datos en variables globales
            currentData = data.data;
            currentColumns = data.columns;

            // Mostrar resultados
            resultsContainer.style.display = 'block';
            resultsCount.textContent = `${data.data.length} registros encontrados`;

            // Limpiar tabla
            tableHead.innerHTML = '';
            tableBody.innerHTML = '';

            if (data.data.length === 0) {
                // No hay filas, pero mostramos las columnas si las hay
                if (data.columns && data.columns.length > 0) {
                    let headRow = '<tr>';
                    data.columns.forEach(col => {
                        headRow += `<th>${col}</th>`;
                    });
                    headRow += '</tr>';
                    tableHead.innerHTML = headRow;
                }
                
                tableBody.innerHTML = `<tr><td colspan="${data.columns.length || 1}" class="text-center" style="padding: 30px; color: var(--text-muted); font-weight: 500;">No se encontraron registros para esta consulta.</td></tr>`;
                return;
            }

            // Generar cabecera de la tabla
            let headRow = '<tr>';
            data.columns.forEach(col => {
                headRow += `<th>${col}</th>`;
            });
            headRow += '</tr>';
            tableHead.innerHTML = headRow;

            // Generar filas de la tabla
            data.data.forEach(row => {
                let bodyRow = '<tr>';
                data.columns.forEach(col => {
                    let val = row[col];
                    // Formatear nulos
                    if (val === null || val === undefined) {
                        bodyRow += '<td class="muted" style="font-style: italic;">null</td>';
                    } else {
                        bodyRow += `<td>${val}</td>`;
                    }
                });
                bodyRow += '</tr>';
                tableBody.innerHTML += bodyRow;
            });
        })
        .catch(err => {
            loadingContainer.style.display = 'none';
            errorContainer.style.display = 'block';
            errorMessage.textContent = 'Ocurrió un error al procesar el reporte: ' + err.message;
        });
    });

    // Exportar a CSV
    btnExportCsv.addEventListener('click', function() {
        if (currentData.length === 0) return;

        // BOM UTF-8 para compatibilidad con caracteres especiales (acentos, ñ, etc.) en Excel
        let csvContent = "\uFEFF";
        
        // Agregar cabeceras
        csvContent += currentColumns.join(",") + "\n";
        
        // Agregar filas
        currentData.forEach(row => {
            let rowData = currentColumns.map(col => {
                let cell = (row[col] === null || row[col] === undefined) ? '' : String(row[col]);
                // Escapar comillas dobles
                cell = cell.replace(/"/g, '""');
                // Envolver en comillas si tiene comas, saltos de línea o comillas
                if (cell.includes(',') || cell.includes('\n') || cell.includes('"')) {
                    cell = `"${cell}"`;
                }
                return cell;
            });
            csvContent += rowData.join(",") + "\n";
        });

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", `reporte_ia_${new Date().toISOString().slice(0, 10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // Exportar a PDF (a través de impresión del navegador formateada)
    btnExportPdf.addEventListener('click', function() {
        if (currentData.length === 0) return;

        const printWindow = window.open('', '_blank');
        
        let html = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Reporte IA - Ferretería Guisella</title>
            <style>
                body {
                    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                    color: #1e293b;
                    padding: 30px;
                }
                .header {
                    border-bottom: 2px solid #00af9a;
                    padding-bottom: 15px;
                    margin-bottom: 25px;
                }
                h1 {
                    color: #00796b;
                    margin: 0 0 5px 0;
                    font-size: 1.8rem;
                }
                p.date {
                    font-size: 0.85rem;
                    color: #64748b;
                    margin: 0;
                    font-weight: 500;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                }
                th, td {
                    border: 1px solid #e2e8f0;
                    padding: 10px 12px;
                    text-align: left;
                    font-size: 0.85rem;
                }
                th {
                    background-color: #f1f5f9;
                    color: #0f172a;
                    font-weight: 700;
                }
                tr:nth-child(even) {
                    background-color: #f8fafc;
                }
                .muted {
                    color: #94a3b8;
                    font-style: italic;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Reporte Inteligente</h1>
                <p class="date">Fecha de Emisión: ${new Date().toLocaleString('es-BO')}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        ${currentColumns.map(col => `<th>${col}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
                    ${currentData.map(row => `
                        <tr>
                            ${currentColumns.map(col => {
                                let val = row[col];
                                if (val === null || val === undefined) {
                                    return '<td class="muted">null</td>';
                                }
                                return `<td>${val}</td>`;
                            }).join('')}
                        </tr>
                    `).join('')}
                </tbody>
            </table>
            <script>
                window.onload = function() {
                    window.print();
                    setTimeout(function() { window.close(); }, 500);
                };
            <\/script>
        </body>
        </html>
        `;
        
        printWindow.document.open();
        printWindow.document.write(html);
        printWindow.document.close();
    });
});
</script>
@endsection

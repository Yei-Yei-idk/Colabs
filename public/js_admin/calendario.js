document.addEventListener('DOMContentLoaded', () => {
    // 1. CRONÓMETRO REGRESIVO (COUNTDOWN)
    function actualizarContadores() {
        const badges = document.querySelectorAll('.countdown-badge');
        const ahora = new Date();

        badges.forEach(badge => {
            const fechaFinStr = badge.dataset.fin; // YYYY-MM-DD HH:MM:SS
            if (!fechaFinStr) return;

            // Arreglar compatibilidad de fecha en Safari/iOS (reemplazar guiones por slashes)
            const f = new Date(fechaFinStr.replace(/-/g, '/'));
            const diffMs = f - ahora;

            const parentCell = badge.closest('td');
            // Si el badge no está dentro de un <td> o no se puede determinar el estado, lo ocultamos.
            if (!parentCell) {
                badge.style.display = 'none';
                return;
            }

            const isAccepted = parentCell.classList.contains('aceptada');
            // La condición es: debe ser aceptada (Ocupada/Rojo) Y aún tener tiempo restante
            if (isAccepted && diffMs > 0) {
                badge.style.display = ''; // Aseguramos que sea visible

                const totalSeconds = Math.floor(diffMs / 1000);
                const hrs = Math.floor(totalSeconds / 3600);
                const mins = Math.floor((totalSeconds % 3600) / 60);
                const secs = totalSeconds % 60;
                
                let text = "⏱ Quedan ";
                if(hrs > 0) text += `${hrs}h ${mins}m ${secs}s`;
                else if (mins > 0) text += `${mins}m ${secs}s`;
                else text += `${secs}s`;
                
                badge.textContent = text;

                // Colores (basados en minutos totales para mantener la lógica de urgencia)
                const diffMinutos = diffMs / 60000;
                if (diffMinutos > 30) badge.className = "countdown-badge cd-green";
                else if (diffMinutos > 5) badge.className = "countdown-badge cd-yellow";
                else badge.className = "countdown-badge cd-red";
            } else {
                // Si no es aceptada o el tiempo ya expiró, ocultamos el badge.
                badge.style.display = 'none';
            }
        });
    }

    // Ejecutar contador inmediatamente y luego cada segundo para precisión EXACTA
    actualizarContadores();
    setInterval(actualizarContadores, 1000);

    // 2. MODAL DE CAMBIO DE HORA
    window.cerrarModalHora = function() {
        document.getElementById('modal-hora').style.display = 'none';
    }

    const celdasOcupadas = document.querySelectorAll('.tabla-reservas td[data-reserva-id]');
    celdasOcupadas.forEach(celda => {
        // Si la reserva está pendiente, no permitimos abrir el modal para agregar/cambiar hora
        if (celda.classList.contains('pendiente')) {
            return;
        }

        // Al darle click a una celda reservada en el calendario, abre el modal
        celda.addEventListener('click', (e) => {
            const reservaId = celda.dataset.reservaId;
            const horaFinRaw = celda.dataset.horaFinRaw; // HH:MM:SS
            
            if(reservaId && horaFinRaw) {
                document.getElementById('modal-reserva-id').value = reservaId;
                // HH:MM:SS -> HH:MM para el input type="time"
                document.getElementById('modal-hora-fin').value = horaFinRaw.substring(0, 5);
                document.getElementById('modal-hora').style.display = 'flex';
            }
        });
        
        // Solo ponemos el cursor pointer en las celdas que realmente abren el modal (no pendientes)
        celda.style.cursor = 'pointer';
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const reservadoCells = document.querySelectorAll('.tabla-reservas td[data-reserva-id]');
    let tooltip = document.getElementById('reserva-tooltip');

    if (!tooltip) {
        // Si el tooltip no existe, lo creamos y le damos estilos básicos para que sea funcional
        console.log('DEBUG: Creando elemento de tooltip dinámicamente.');
        tooltip = document.createElement('div');
        tooltip.id = 'reserva-tooltip';
        tooltip.className = 'reserva-tooltip';
        // Estilos básicos para asegurar visibilidad y posicionamiento
        tooltip.style.position = 'absolute';
        tooltip.style.zIndex = '10000'; // Asegura que esté por encima de otros elementos
        tooltip.style.pointerEvents = 'none'; // Permite interactuar con elementos debajo del tooltip
        tooltip.style.visibility = 'hidden';
        tooltip.style.opacity = '0';
        tooltip.style.transition = 'opacity 0.2s ease-in-out'; // Transición suave
        tooltip.style.backgroundColor = 'rgba(0,0,0,0.8)';
        tooltip.style.color = 'white';
        tooltip.style.padding = '8px 12px';
        tooltip.style.borderRadius = '4px';
        tooltip.style.fontSize = '12px';
        tooltip.style.whiteSpace = 'nowrap'; // Evita que el texto se rompa
        document.body.appendChild(tooltip);
    } else {
        console.log('DEBUG: Elemento de tooltip encontrado en el DOM.');
    }

    if (reservadoCells.length === 0) {
        console.warn('DEBUG: No se encontraron celdas con reservas para adjuntar tooltips.');
    }

    reservadoCells.forEach(cell => {
        console.log('DEBUG: Adjuntando eventos de mouse a celda reservada:', cell);
        cell.addEventListener('mouseenter', () => {
            console.log('DEBUG: MouseEnter en celda:', cell);
            const userName = cell.dataset.userName || 'N/A';
            const userEmail = cell.dataset.userEmail || 'N/A';
            const espacioNombre = cell.dataset.espacioNombre || 'N/A';
            const reservaId = cell.dataset.reservaId || 'N/A';
            const userPhone = cell.dataset.userPhone || 'N/A';

            tooltip.innerHTML = `
                <p><strong>Reserva ID:</strong> ${reservaId}</p>
                <p><strong>Espacio:</strong> ${espacioNombre}</p>
                <p><strong>Usuario:</strong> ${userName}</p>
                <p><strong>Email:</strong> ${userEmail}</p>
                <p><strong>Teléfono:</strong> ${userPhone}</p>
            `;

            const rect = cell.getBoundingClientRect();
            // Posicionar el tooltip centrado horizontalmente sobre la celda y 10px arriba
            tooltip.style.left = `${rect.left + window.scrollX + (rect.width / 2) - (tooltip.offsetWidth / 2)}px`;
            tooltip.style.top = `${rect.top + window.scrollY - tooltip.offsetHeight - 10}px`; // 10px de margen superior

            // Ajuste para evitar que el tooltip se salga por la izquierda de la pantalla
            if (parseFloat(tooltip.style.left) < 5) { // 5px de margen
                tooltip.style.left = `${rect.left + window.scrollX + 5}px`;
            }
            // Ajuste para evitar que el tooltip se salga por la derecha de la pantalla
            const rightEdge = parseFloat(tooltip.style.left) + tooltip.offsetWidth;
            if (rightEdge > window.innerWidth + window.scrollX - 5) { // 5px de margen
                tooltip.style.left = `${window.innerWidth + window.scrollX - tooltip.offsetWidth - 5}px`;
            }

            tooltip.style.visibility = 'visible';
            tooltip.style.opacity = '1';
        });

        cell.addEventListener('mouseleave', () => {
            console.log('DEBUG: MouseLeave en celda:', cell);
            tooltip.style.visibility = 'hidden';
            tooltip.style.opacity = '0';
        });
    });
});
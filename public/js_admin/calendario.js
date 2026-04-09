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

            if (diffMs <= 0) {
                badge.textContent = "⏱ Tiempo Agotado";
                badge.className = "countdown-badge cd-expired";
            } else {
                const diffMinutos = Math.floor(diffMs / 60000);
                const hrs = Math.floor(diffMinutos / 60);
                const mins = diffMinutos % 60;
                
                let text = "⏱ Quedan ";
                if(hrs > 0) text += `${hrs}h ${mins}m`;
                else text += `${mins}m`;
                
                badge.textContent = text;

                // Colores
                if (diffMinutos > 30) badge.className = "countdown-badge cd-green";
                else if (diffMinutos > 5) badge.className = "countdown-badge cd-yellow";
                else badge.className = "countdown-badge cd-red";
            }
        });
    }

    // Ejecutar contador inmediatamente y luego cada 10 segundos
    actualizarContadores();
    setInterval(actualizarContadores, 10000);

    // 2. MODAL DE CAMBIO DE HORA
    window.cerrarModalHora = function() {
        document.getElementById('modal-hora').style.display = 'none';
    }

    const celdasOcupadas = document.querySelectorAll('.tabla-reservas td[data-reserva-id]');
    celdasOcupadas.forEach(celda => {
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
        
        // Quitar el cursor pointer si esta disponible para que quede claro que solo ocupados son clickeables
        celda.style.cursor = 'pointer';
    });
});
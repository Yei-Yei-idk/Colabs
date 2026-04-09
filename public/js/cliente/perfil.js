/**
 * Lógica para la página de Perfil del Cliente
 * Validación al guardar cambios e indicador de seguridad de contraseña
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formPerfil');
    const newpassInput = document.getElementById('newpassword');
    const requirementsContainer = document.getElementById('password-requirements-profile');
    const btnGuardar = document.querySelector('.btn-guardar');
    
    if (!form || !newpassInput) return;

    const requirements = {
        length: { element: document.getElementById('req-length'), regex: /.{8,}/ },
        mixed: { element: document.getElementById('req-mixed'), regex: /^(?=.*[a-z])(?=.*[A-Z]).+$/ },
        numbers: { element: document.getElementById('req-numbers'), regex: /(?=.*[0-9])/ },
        symbols: { element: document.getElementById('req-symbols'), regex: /(?=.*[\W_])/ }
    };

    function validatePassword() {
        const val = newpassInput.value;
        
        if (val.length > 0) {
            requirementsContainer.style.display = 'block';
            let passedCount = 0;

            Object.keys(requirements).forEach(key => {
                const req = requirements[key];
                const isPassed = req.regex.test(val);
                
                if (isPassed) {
                    req.element.style.color = '#059669';
                    req.element.querySelector('.icon').innerText = '✓';
                    passedCount++;
                } else {
                    req.element.style.color = '#9ca3af';
                    req.element.querySelector('.icon').innerText = '○';
                }
            });

            if (passedCount === 4) {
                btnGuardar.disabled = false;
                btnGuardar.style.opacity = '1';
                btnGuardar.title = '';
            } else {
                btnGuardar.disabled = true;
                btnGuardar.style.opacity = '0.7';
                btnGuardar.title = 'La nueva contraseña no cumple con los requisitos mínimos de seguridad.';
            }
        } else {
            requirementsContainer.style.display = 'none';
            btnGuardar.disabled = false;
            btnGuardar.style.opacity = '1';
            btnGuardar.title = '';
        }
    }

    newpassInput.addEventListener('input', validatePassword);

    form.addEventListener('submit', function(e) {
        const val = newpassInput.value;
        if (val.length > 0) {
            let passedCount = 0;
            Object.keys(requirements).forEach(key => {
                if (requirements[key].regex.test(val)) passedCount++;
            });

            if (passedCount < 4) {
                e.preventDefault();
                alert('La nueva contraseña debe cumplir con todos los requisitos de seguridad.');
            }
        }
    });
});

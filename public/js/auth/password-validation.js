/**
 * Password Validation Module
 * Handles password strength requirements and UI updates.
 */

class PasswordValidator {
    constructor(config) {
        this.input = document.getElementById(config.inputId);
        this.submitBtn = document.getElementById(config.submitBtnId);
        this.confirmInput = document.getElementById(config.confirmId) || null;
        this.matchStatus = document.getElementById(config.matchStatusId) || null;
        
        this.requirements = {
            length: { element: document.getElementById('req-length'), regex: /.{8,}/ },
            mixed: { element: document.getElementById('req-mixed'), regex: /^(?=.*[a-z])(?=.*[A-Z]).+$/ },
            numbers: { element: document.getElementById('req-numbers'), regex: /(?=.*[0-9])/ },
            symbols: { element: document.getElementById('req-symbols'), regex: /(?=.*[\W_])/ }
        };

        if (this.input) {
            this.init();
        }
    }

    init() {
        this.input.addEventListener('input', () => this.validate());
        if (this.confirmInput) {
            this.confirmInput.addEventListener('input', () => this.validate());
        }
        
        // Initial validation state
        this.validate();
    }

    validate() {
        const val = this.input.value;
        let passedCount = 0;

        // Check individual requirements
        Object.keys(this.requirements).forEach(key => {
            const req = this.requirements[key];
            if (!req.element) return;

            const isPassed = req.regex.test(val);
            const icon = req.element.querySelector('.icon');

            if (isPassed) {
                req.element.classList.add('passed');
                if (icon) icon.innerText = '✓';
                passedCount++;
            } else {
                req.element.classList.remove('passed');
                if (icon) icon.innerText = '○';
            }
        });

        // Check confirmation match if applicable
        let matches = true;
        if (this.confirmInput) {
            const confVal = this.confirmInput.value;
            matches = (val === confVal && val.length > 0);
            
            if (this.matchStatus) {
                if (confVal.length > 0) {
                    this.matchStatus.style.display = 'block';
                    if (matches) {
                        this.matchStatus.innerText = '✓ Las contraseñas coinciden';
                        this.matchStatus.style.color = '#059669';
                    } else {
                        this.matchStatus.innerText = '❌ Las contraseñas no coinciden';
                        this.matchStatus.style.color = '#ef4444';
                    }
                } else {
                    this.matchStatus.style.display = 'none';
                }
            }
        }

        // Toggle submit button
        if (this.submitBtn) {
            const isFullyValid = (passedCount === 4 && matches);
            this.submitBtn.disabled = !isFullyValid;
            this.submitBtn.style.opacity = isFullyValid ? '1' : '0.7';
            
            if (!isFullyValid) {
                this.submitBtn.title = "Completa los requisitos de seguridad para continuar";
            } else {
                this.submitBtn.title = "";
            }
        }
    }
}

// Global initialization helper
window.initPasswordValidation = function(config) {
    return new PasswordValidator(config);
};

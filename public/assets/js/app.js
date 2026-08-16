document.addEventListener('DOMContentLoaded',()=>{document.querySelectorAll('[data-password-toggle]').forEach(button=>button.addEventListener('click',()=>{const input=document.querySelector(button.dataset.passwordToggle);if(!input)return;input.type=input.type==='password'?'text':'password';button.setAttribute('aria-pressed',String(input.type==='text'));}));window.setTimeout(()=>document.querySelectorAll('[data-auto-dismiss]').forEach(alert=>bootstrap.Alert.getOrCreateInstance(alert).close()),5000);});

document.addEventListener("DOMContentLoaded", function () {
    const togglePassword = document.getElementById("togglePassword");
    const password = document.getElementById("password");

    if (!togglePassword || !password) {
        return;
    }

    togglePassword.addEventListener("click", function () {
        const isPassword = password.type === "password";

        password.type = isPassword ? "text" : "password";

        const icon = this.querySelector("i");

        if (icon) {
            icon.classList.toggle("bi-eye", !isPassword);
            icon.classList.toggle("bi-eye-slash", isPassword);
        }

        this.setAttribute(
            "aria-label",
            isPassword ? "Hide password" : "Show password"
        );
    });


    const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");
    const confirmpassword = document.getElementById("confirmpassword");

    if (!toggleConfirmPassword || !password) {
        return;
    }

    toggleConfirmPassword.addEventListener("click", function () {
        const isPassword = confirmpassword.type === "password";

        confirmpassword.type = isPassword ? "text" : "password";

        const icon = this.querySelector("i");

        if (icon) {
            icon.classList.toggle("bi-eye", !isPassword);
            icon.classList.toggle("bi-eye-slash", isPassword);
        }

        this.setAttribute(
            "aria-label",
            isPassword ? "Hide password" : "Show password"
        );
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const viewers = document.querySelectorAll('[data-image-viewer]');

    viewers.forEach((viewer) => {
        const image = viewer.querySelector('[data-preview-image]');

        if (!image) {
            return;
        }

        const card = viewer.closest('.promotion-kit-card');

        if (!card) {
            return;
        }

        let scale = 1;
        let rotation = 0;

        const updateTransform = () => {
            image.style.transform =
                `scale(${scale}) rotate(${rotation}deg)`;
        };

        const reset = () => {
            scale = 1;
            rotation = 0;
            updateTransform();
        };

        const zoomIn = () => {
            scale = Math.min(scale + 0.2, 3);
            updateTransform();
        };

        const zoomOut = () => {
            scale = Math.max(scale - 0.2, 0.5);
            updateTransform();
        };

        const rotate = () => {
            rotation = (rotation + 90) % 360;
            updateTransform();
        };

        const fullscreen = async () => {
            const stage = card.querySelector(
                '.promotion-kit-preview-stage'
            );

            if (!stage) {
                return;
            }

            try {
                if (!document.fullscreenElement) {
                    await stage.requestFullscreen();
                } else {
                    await document.exitFullscreen();
                }
            } catch (error) {
                console.error(
                    'Unable to toggle fullscreen:',
                    error
                );
            }
        };

        const buttons = card.querySelectorAll(
            '[data-action]'
        );

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                switch (button.dataset.action) {
                    case 'zoom-in':
                        zoomIn();
                        break;

                    case 'zoom-out':
                        zoomOut();
                        break;

                    case 'rotate':
                        rotate();
                        break;

                    case 'reset':
                        reset();
                        break;

                    case 'fullscreen':
                        fullscreen();
                        break;
                }
            });
        });

        image.addEventListener('dblclick', reset);

        image.addEventListener('dragstart', (event) => {
            event.preventDefault();
        });

        reset();
    });
});


document.addEventListener('DOMContentLoaded', () => {

    const deleteModal = document.getElementById(
        'deletePressReleaseModal'
    );

    const confirmDeleteButton = document.getElementById(
        'confirmDeletePressRelease'
    );

    const deleteTitle = document.getElementById(
        'deletePressReleaseTitle'
    );

    if (!deleteModal || !confirmDeleteButton) {
        return;
    }

    deleteModal.addEventListener('show.bs.modal', (event) => {

        const button = event.relatedTarget;

        if (!button) {
            return;
        }

        const formId = button.getAttribute(
            'data-delete-form'
        );

        const title = button.getAttribute(
            'data-delete-title'
        );

        // Update the title shown in the confirmation modal.
        if (deleteTitle) {
            deleteTitle.textContent = title || 'Press Release';
        }

        // Tell the confirmation button which form to submit.
        confirmDeleteButton.setAttribute(
            'form',
            formId
        );
    });

});

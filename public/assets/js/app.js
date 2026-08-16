document.addEventListener('DOMContentLoaded',()=>{document.querySelectorAll('[data-password-toggle]').forEach(button=>button.addEventListener('click',()=>{const input=document.querySelector(button.dataset.passwordToggle);if(!input)return;input.type=input.type==='password'?'text':'password';button.setAttribute('aria-pressed',String(input.type==='text'));}));window.setTimeout(()=>document.querySelectorAll('[data-auto-dismiss]').forEach(alert=>bootstrap.Alert.getOrCreateInstance(alert).close()),5000);});
const lhModal = document.getElementById('lhModal')
if (lhModal) {
  lhModal.addEventListener('show.bs.modal', event => {
    // Button that triggered the modal
    const button = event.relatedTarget
    // Extract info from data-bs-* attributes
    const recipient = button.getAttribute('data-bs-recipient')
    // If necessary, you could initiate an Ajax request here
    // and then do the updating in a callback.

    // Update the modal's content.
    const modalTitle = lhModal.querySelector('.modal-title')
    const modalBodyInput = lhModal.querySelector('.modal-body input')

    modalTitle.textContent = `New message to ${recipient}`
    modalBodyInput.value = recipient
  })
}

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


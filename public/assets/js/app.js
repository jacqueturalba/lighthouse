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



$(function () {
  $('[data-bs-toggle="tooltip"]').tooltip()
})


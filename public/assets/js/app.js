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
document.querySelectorAll('.lh-calendar-day[data-href]').forEach(day => {

    day.addEventListener('click', function (event) {

        // Don't redirect when clicking an event link
        if (event.target.closest('.lh-calendar-event')) {
            return;
        }

        window.location.href = this.dataset.href;
    });

    day.addEventListener('keydown', function (event) {

        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            window.location.href = this.dataset.href;
        }

    });

});

/*
 * Shared upload UI. XHR is used deliberately: fetch does not expose reliable
 * upload-progress events in all supported browsers. Files are never read into
 * JavaScript memory; FormData streams the browser-selected file to PHP.
 */
document.addEventListener('DOMContentLoaded', () => {
    const manager = document.getElementById('lh-upload-manager');
    const managerOpen = document.getElementById('lh-upload-manager-open');
    const items = document.querySelector('[data-upload-manager-items]');
    const toasts = document.getElementById('lh-upload-toasts');
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!manager || !items || !token) return;

    document.querySelectorAll('[data-multi-upload="sflex"]').forEach((zone) => {
        const input = zone.querySelector('input[type="file"]');
        const summary = zone.querySelector('[data-upload-summary]');
        const previews = zone.querySelector('[data-upload-previews]');
        let files = [];
        const message = (text, invalid = false) => { summary.textContent = text; summary.classList.toggle('text-danger', invalid); };
        const render = () => {
            input._lhFiles = files;
            previews.innerHTML = '';
            files.forEach((file, index) => {
                const item = document.createElement('div'); item.className = 'lh-upload-preview';
                const url = URL.createObjectURL(file);
                item.innerHTML = file.type.startsWith('video/') ? `<video muted src="${url}"></video>` : `<img src="${url}" alt="Selected image">`;
                const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'btn-close shadow-sm'; remove.setAttribute('aria-label', `Remove ${file.name}`);
                remove.addEventListener('click', () => { URL.revokeObjectURL(url); files.splice(index, 1); render(); });
                item.append(remove); previews.append(item);
            });
            message(files.length ? (files[0].type.startsWith('video/') ? '1 video selected' : `${files.length} / 10 images selected`) : 'Drop up to 10 images here, or click to browse. A video must be uploaded alone.');
        };
        const add = (incoming) => {
            const next = Array.from(incoming);
            if (!next.length) return;
            if (next.some(file => !file.type.startsWith('image/') && !file.type.startsWith('video/'))) { message('Only image or video files are allowed.', true); return; }
            const combined = [...files, ...next];
            const hasVideo = combined.some(file => file.type.startsWith('video/'));
            if ((hasVideo && combined.length !== 1) || (!hasVideo && combined.length > 10)) { message('Choose up to 10 images, or one video by itself.', true); return; }
            files = combined; render();
        };
        input.addEventListener('change', () => { files = []; add(input.files); });
        ['dragenter','dragover'].forEach(eventName => zone.addEventListener(eventName, event => { event.preventDefault(); zone.classList.add('is-dragging'); }));
        ['dragleave','drop'].forEach(eventName => zone.addEventListener(eventName, event => { event.preventDefault(); zone.classList.remove('is-dragging'); }));
        zone.addEventListener('drop', event => add(event.dataTransfer.files));
    });

    const jobs = new Map();
    let activeUploads = 0;
    const escapeHtml = (value) => {
        const element = document.createElement('span');
        element.textContent = value || '';
        return element.innerHTML;
    };
    const labelFor = (job) => job.upload_type === 'promotion_kit' ? 'Promotion kit' : 'SFlex media';
    const retryUrlFor = (job) => job.upload_type === 'promotion_kit' ? '/promotion-kit-upload' : '/sflex';
    const statusText = (job) => {
        if (job.status === 'completed') return 'Ready';
        if (job.status === 'failed') return job.error_message || 'Upload failed. Please select the file and try again.';
        return `Uploading ${Math.max(0, Math.min(100, Number(job.progress) || 0))}%`;
    };
    const render = () => {
        const visible = [...jobs.values()].filter((job) => job.status !== 'completed');
        const dismissed = localStorage.getItem('lh-upload-manager-dismissed') === '1';
        const minimized = localStorage.getItem('lh-upload-manager-minimized') === '1';
        manager.hidden = visible.length === 0 || dismissed || minimized;
        if (managerOpen) managerOpen.hidden = visible.length === 0 || dismissed || !minimized;
        items.innerHTML = visible.map((job) => `
            <div class="lh-upload-job">
                <div class="d-flex justify-content-between gap-2 small"><span>${escapeHtml(labelFor(job))}</span><span>${escapeHtml(statusText(job))}</span></div>
                ${job.status === 'failed' ? `<a class="small" href="${retryUrlFor(job)}">Select file and try again</a>` : `<div class="progress mt-2" role="progressbar" aria-label="Upload progress" aria-valuenow="${Number(job.progress) || 0}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width:${Number(job.progress) || 0}%"></div></div>`}
            </div>`).join('');
    };
    const notify = (message, type = 'success', link = '') => {
        if (!toasts || !message) return;
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-bg-${type} border-0`;
        toast.setAttribute('role', 'status');
        toast.innerHTML = `<div class="d-flex"><div class="toast-body">${escapeHtml(message)}${link ? ` <a class="link-light fw-semibold" href="${escapeHtml(link)}">View</a>` : ''}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>`;
        toasts.append(toast);
        bootstrap.Toast.getOrCreateInstance(toast, { delay: 7000 }).show();
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    };
    const post = async (url, values) => {
        const body = new URLSearchParams({ _token: token, ...values });
        const response = await fetch(url, { method: 'POST', body, credentials: 'same-origin', headers: { Accept: 'application/json' } });
        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data.error || 'The upload request failed.');
        return data;
    };
    const refresh = async () => {
        try {
            const response = await fetch('/uploads/status', { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const data = await response.json();
            (data.jobs || []).forEach((job) => jobs.set(Number(job.id), job));
            render();
        } catch (_) { /* Status recovery is non-critical while offline. */ }
    };
    const updateProgress = (id, progress, type, filename) => {
        jobs.set(id, { id, upload_type: type, original_filename: filename, status: 'uploading', progress });
        render();
    };

    document.querySelector('[data-upload-manager-minimize]')?.addEventListener('click', () => { localStorage.setItem('lh-upload-manager-minimized','1'); render(); });
    document.querySelector('[data-upload-manager-dismiss]')?.addEventListener('click', () => { localStorage.setItem('lh-upload-manager-dismissed','1'); render(); });
    managerOpen?.addEventListener('click', () => { localStorage.removeItem('lh-upload-manager-minimized'); render(); });
    refresh();
    window.setInterval(refresh, 15000);

    document.querySelectorAll('form[data-async-upload]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            const input = form.querySelector('input[type="file"]');
            const selectedFiles = input?._lhFiles || Array.from(input?.files || []);
            const file = selectedFiles[0];
            if (!file) return; // Caption-only SFlex posts retain the original form path.
            event.preventDefault();
            if (!form.reportValidity()) return;

            const submit = form.querySelector('[type="submit"]');
            submit?.setAttribute('disabled', 'disabled');
            const type = form.dataset.asyncUpload;
            let jobId;
            try {
                const started = await post('/uploads/start', { upload_type: type, original_filename: file.name, file_size: file.size });
                jobId = Number(started.id);
                updateProgress(jobId, 0, type, file.name);
            } catch (error) {
                notify(error.message || 'Could not prepare the upload.', 'danger');
                submit?.removeAttribute('disabled');
                return;
            }

            const data = new FormData(form);
            // FormData is rebuilt explicitly so every picker/drop selection is
            // sent, rather than depending on a browser's live FileList.
            if (input && selectedFiles.length) {
                data.delete(input.name);
                selectedFiles.forEach((selected) => data.append(input.name, selected, selected.name));
            }
            data.set('ajax', '1');
            data.set('upload_job_id', String(jobId));
            let lastReported = -1;
            let lastReportAt = 0;
            const report = (progress) => {
                const now = Date.now();
                if (progress === lastReported || (now - lastReportAt < 12000 && progress < 99)) return;
                lastReported = progress;
                lastReportAt = now;
                post(`/uploads/${jobId}/progress`, { progress }).catch(() => {});
            };
            const xhr = new XMLHttpRequest();
            activeUploads += 1;
            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.upload.addEventListener('progress', (progressEvent) => {
                if (!progressEvent.lengthComputable) return;
                const progress = Math.min(99, Math.round((progressEvent.loaded / progressEvent.total) * 100));
                updateProgress(jobId, progress, type, file.name);
                report(progress);
            });
            xhr.addEventListener('load', () => {
                activeUploads -= 1;
                let response = {};
                try { response = JSON.parse(xhr.responseText || '{}'); } catch (_) {}
                if (xhr.status >= 200 && xhr.status < 300 && response.ok) {
                    jobs.set(jobId, { id: jobId, upload_type: type, status: 'completed', progress: 100 });
                    render();
                    form.reset();
                    notify(response.message || 'Upload complete.', 'success', response.link || '');
                    refresh();
                } else {
                    const message = response.error || 'Your file could not be uploaded. Please try again.';
                    jobs.set(jobId, { id: jobId, upload_type: type, status: 'failed', progress: 0, error_message: message });
                    render();
                    post(`/uploads/${jobId}/fail`, { message }).catch(() => {});
                    notify(message, 'danger');
                }
                submit?.removeAttribute('disabled');
            });
            xhr.addEventListener('error', () => {
                activeUploads -= 1;
                const message = 'Network error. Your file could not be uploaded. Please select it and try again.';
                jobs.set(jobId, { id: jobId, upload_type: type, status: 'failed', progress: 0, error_message: message });
                render();
                post(`/uploads/${jobId}/fail`, { message }).catch(() => {});
                notify(message, 'danger');
                submit?.removeAttribute('disabled');
            });
            xhr.send(data);
        });
    });
});

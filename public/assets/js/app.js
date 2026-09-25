document.addEventListener('DOMContentLoaded',()=>{document.querySelectorAll('[data-password-toggle]').forEach(button=>button.addEventListener('click',()=>{const input=document.querySelector(button.dataset.passwordToggle);if(!input)return;input.type=input.type==='password'?'text':'password';button.setAttribute('aria-pressed',String(input.type==='text'));}));window.setTimeout(()=>document.querySelectorAll('[data-auto-dismiss]').forEach(alert=>bootstrap.Alert.getOrCreateInstance(alert).close()),5000);});

document.addEventListener('DOMContentLoaded', () => {
    const root = document.documentElement;
    const preference = window.matchMedia('(prefers-color-scheme: dark)');
    const currentTheme = () => root.dataset.bsTheme === 'dark' ? 'dark' : 'light';
    const updateThemeControls = () => {
        const dark = currentTheme() === 'dark';
        document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
            button.setAttribute('aria-label', `Switch to ${dark ? 'light' : 'dark'} mode`);
            button.setAttribute('aria-pressed', String(dark));
            button.title = `Switch to ${dark ? 'light' : 'dark'} mode`;
            const icon = button.querySelector('i');
            if (icon) icon.className = `bi ${dark ? 'bi-sun sun-icon' : 'bi-moon-stars'}`;
            const label = button.querySelector('.d-lg-none');
            if (label) label.textContent = dark ? 'Light mode' : 'Dark mode';
        });
    };
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => button.addEventListener('click', () => {
        const theme = currentTheme() === 'dark' ? 'light' : 'dark';
        root.dataset.bsTheme = theme;
        try { localStorage.setItem('lh-theme', theme); } catch (_) {}
        updateThemeControls();
    }));
    preference.addEventListener?.('change', (event) => {
        try {
            if (!localStorage.getItem('lh-theme')) {
                root.dataset.bsTheme = event.matches ? 'dark' : 'light';
                updateThemeControls();
            }
        } catch (_) {}
    });
    window.addEventListener('storage', (event) => {
        if (event.key !== 'lh-theme' && event.key !== null) return;
        try {
            const saved = localStorage.getItem('lh-theme');
            root.dataset.bsTheme = saved === 'light' || saved === 'dark' ? saved : (preference.matches ? 'dark' : 'light');
            updateThemeControls();
        } catch (_) {}
    });
    updateThemeControls();
});

document.addEventListener('slid.bs.carousel', (event) => {
    const indicator = event.target.querySelector('[data-carousel-indicator]');
    if (!indicator || !Number.isInteger(event.to)) return;
    indicator.textContent = `${event.to + 1} / ${indicator.dataset.total}`;
});

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
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!token) return;

    let editingId = null;
    let deletingId = null;

    const editModal = document.getElementById('sflexEdit');
    const deleteSPostModal = document.getElementById('deleteSPostModal');
    const confirmDeleteSPost = document.getElementById('confirmDeleteSPost');
    const deleteSPostTitle = document.getElementById('deleteSPostTitle');

    document.addEventListener('click', async (event) => {
        const edit = event.target.closest('[data-sflex-edit]');
        const remove = event.target.closest('[data-sflex-delete]');

        // Edit
        if (edit && editModal) {
            editingId = edit.dataset.postId;

            editModal.querySelector('[name="caption"]').value = edit.dataset.caption;
            editModal.querySelector('[data-sflex-edit-error]').textContent = '';

            bootstrap.Modal.getOrCreateInstance(editModal).show();
            return;
        }

        // Delete - only prepare and show confirmation modal
        if (remove && deleteSPostModal) {
            deletingId = remove.dataset.postId;

            if (deleteSPostTitle) {
                deleteSPostTitle.textContent = remove.dataset.deleteTitle || '';
            }

            bootstrap.Modal.getOrCreateInstance(deleteSPostModal).show();
        }
    });

    // Confirm Delete
    confirmDeleteSPost?.addEventListener('click', async () => {
        if (!deletingId) return;

        const postId = deletingId;
        const button = confirmDeleteSPost;

        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Deleting...';

        try {
            const body = new FormData();
            body.append('_token', token);

            const response = await fetch(`/sflex/${postId}/delete`, {
                method: 'POST',
                body
            });

            if (!response.ok) {
                throw new Error('Delete failed');
            }

            // Remove post from the page
            document
                .querySelector(`[data-sflex-post="${postId}"]`)
                ?.remove();

            // Close modal
            bootstrap.Modal.getOrCreateInstance(deleteSPostModal).hide();

            // If currently viewing the post detail page, return to SFlex
            if (document.body.dataset.sflexDetail === '1') {
                location.href = '/sflex';
            }

        } catch (error) {
            alert('This post could not be deleted.');
        } finally {
            deletingId = null;

            button.disabled = false;
            button.innerHTML = '<i class="bi bi-trash me-1"></i> Yes, Delete';
        }
    });

    // Edit form
    document.getElementById('sflex-edit-form')?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const form = event.currentTarget;
        const body = new FormData(form);

        body.append('_token', token);

        const response = await fetch(`/sflex/${editingId}/edit`, {
            method: 'POST',
            body
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            form.querySelector('[data-sflex-edit-error]').textContent =
                data.error || 'Could not save changes.';
            return;
        }

        const card = document.querySelector(`[data-sflex-post="${editingId}"]`);

        card
            ?.querySelector('[data-sflex-caption]')
            ?.replaceChildren(data.caption);

        bootstrap.Modal.getOrCreateInstance(editModal).hide();
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
    const activityCount = document.querySelector('[data-upload-manager-count]');
    const viewAllButton = document.querySelector('[data-upload-manager-view-all]');
    const toasts = document.getElementById('lh-upload-toasts');
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!manager || !items || !token) return;

    document.querySelectorAll('[data-multi-upload="sflex"],[data-multi-upload="promotion_kit"]').forEach((zone) => {
        const uploadType = zone.dataset.multiUpload;
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
                if (file.type.startsWith('video/')) item.innerHTML = `<video muted src="${url}"></video>`;
                else if (file.type.startsWith('image/')) item.innerHTML = `<img src="${url}" alt="Selected image">`;
                else { item.classList.add('lh-upload-file-preview'); const label = document.createElement('span'); label.textContent = file.name; item.append(label); }
                const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'btn-close shadow-sm'; remove.setAttribute('aria-label', `Remove ${file.name}`);
                remove.addEventListener('click', () => { URL.revokeObjectURL(url); files.splice(index, 1); render(); });
                item.append(remove); previews.append(item);
            });
            message(files.length ? (uploadType === 'promotion_kit' ? (files.length === 1 && !files[0].type.startsWith('image/') ? `${files[0].name} selected` : `${files.length} / 10 images selected`) : (files[0].type.startsWith('video/') ? '1 video selected' : `${files.length} / 10 images selected`)) : (uploadType === 'promotion_kit' ? 'Select one ZIP, PDF, DOCX, PPTX, JPG, or PNG file, or choose up to 10 images.' : 'Drop up to 10 images here, or click to browse. A video must be uploaded alone.'));
        };
        const add = (incoming) => {
            const next = Array.from(incoming);
            if (!next.length) return;
            if (uploadType === 'promotion_kit') {
                const combined = [...files, ...next];
                const images = combined.every(file => file.type.startsWith('image/'));
                if ((!images && combined.length > 1) || (images && combined.length > 10) || combined.some(file => !file.type.startsWith('image/') && !/\.(zip|pdf|docx|pptx|jpe?g|png)$/i.test(file.name))) { message('Select one supported kit file, or up to 10 images.', true); return; }
                files = combined; render(); return;
            }
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
    let viewAll = false;
    let refreshSequence = 0;
    const escapeHtml = (value) => {
        const element = document.createElement('span');
        element.textContent = value || '';
        return element.innerHTML;
    };
    const labelFor = (job) => job.upload_type === 'promotion_kit' ? 'Promotion kit' : 'SFlex media';
    const retryUrlFor = (job) => job.upload_type === 'promotion_kit' ? '/promotion-kit-upload' : '/sflex';
    const statusText = (job) => {
        if (job.status === 'completed') return 'Ready';
        if (job.status === 'failed') return 'Failed';
        return `Uploading ${Math.max(0, Math.min(100, Number(job.progress) || 0))}%`;
    };
    const activityTime = (job) => Date.parse(job.updated_at || job.created_at || '') || Number(job.client_updated_at) || 0;
    const cleanupOldJobs = () => {
        // Remove any stale job elements that may already exist in the DOM.
        items.querySelectorAll('.lh-upload-job').forEach((element) => {
            const jobId = Number(element.dataset.jobId);

            if (!jobId || !jobs.has(jobId)) {
                element.remove();
            }
        });
    };

    const render = () => {
        cleanupOldJobs();

        const history = [...jobs.values()].sort((left, right) => activityTime(right) - activityTime(left));
        const visible = viewAll ? history : history.slice(0, 3);

        const dismissed =
            localStorage.getItem('lh-upload-manager-dismissed') === '1';

        const minimized =
            localStorage.getItem('lh-upload-manager-minimized') === '1';

        manager.hidden =
            visible.length === 0 || dismissed || minimized;

        if (managerOpen) {
            managerOpen.hidden =
                history.length === 0 || dismissed || !minimized;
        }

        manager.classList.toggle('is-view-all', viewAll);
        viewAllButton?.setAttribute('aria-pressed', String(viewAll));
        if (viewAllButton) {
            viewAllButton.title = viewAll ? 'Show recent upload activity' : 'View all upload activity';
            viewAllButton.innerHTML = viewAll
                ? '<i class="bi bi-arrows-angle-contract me-1" aria-hidden="true"></i><span>Recent</span>'
                : '<i class="bi bi-arrows-angle-expand me-1" aria-hidden="true"></i><span>View all</span>';
        }
        if (activityCount) {
            activityCount.textContent = viewAll
                ? `Showing all ${history.length} activities`
                : `Showing ${visible.length} most recent ${visible.length === 1 ? 'activity' : 'activities'}`;
        }

        items.innerHTML = visible.map((job) => `
            <div class="lh-upload-job" data-job-id="${Number(job.id)}">
                <div class="d-flex justify-content-between gap-2 small">
                    <span class="fw-semibold">${escapeHtml(labelFor(job))}</span>
                    <span class="text-nowrap">${escapeHtml(statusText(job))}</span>
                </div>
                ${job.original_filename ? `<div class="small text-secondary text-truncate" title="${escapeHtml(job.original_filename)}">${escapeHtml(job.original_filename)}</div>` : ''}
                ${
                    job.status === 'failed'
                        ? `<div class="small text-danger lh-upload-job-error">${escapeHtml(job.error_message || 'Upload failed. Please select the file and try again.')}</div>
                            <div class="d-flex align-items-center justify-content-between gap-2 mt-1">
                                <a class="small" href="${retryUrlFor(job)}">Select file and try again</a>
                                <button type="button" class="btn btn-sm btn-outline-danger lh-upload-job-delete" data-upload-job-delete="${Number(job.id)}"><i class="bi bi-trash me-1" aria-hidden="true"></i>Delete</button>
                            </div>`
                        : job.status === 'completed'
                            ? '<div class="small text-success"><i class="bi bi-check-circle-fill me-1" aria-hidden="true"></i>Upload complete</div>'
                            : `
                            <div
                                class="progress mt-2"
                                role="progressbar"
                                aria-label="Upload progress"
                                aria-valuenow="${Number(job.progress) || 0}"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            >
                                <div
                                    class="progress-bar progress-bar-striped progress-bar-animated"
                                    style="width:${Number(job.progress) || 0}%"
                                ></div>
                            </div>
                        `
                }
            </div>
        `).join('');
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
        const sequence = ++refreshSequence;
        try {
            const response = await fetch(`/uploads/status${viewAll ? '?all=1' : ''}`, { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const data = await response.json();
            if (sequence !== refreshSequence) return;
            const serverJobs = data.jobs || [];
            const serverJobIds = new Set(
                serverJobs.map((job) => Number(job.id))
            );

            serverJobs.forEach((job) => {
                const id = Number(job.id);

                jobs.set(id, job);
            });

            // Remove client-side jobs that no longer exist on the server.
            [...jobs.keys()].forEach((id) => {
                if (!serverJobIds.has(id)) {
                    jobs.delete(id);
                }
            });

            render();
        } catch (_) { /* Status recovery is non-critical while offline. */ }
    };
    const updateProgress = (id, progress, type, filename) => {
        jobs.set(id, { id, upload_type: type, original_filename: filename, status: 'uploading', progress, client_updated_at: Date.now() });
        render();
    };

    viewAllButton?.addEventListener('click', () => {
        viewAll = !viewAll;
        render();
        refresh();
    });
    items.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-upload-job-delete]');
        if (!button) return;
        const id = Number(button.dataset.uploadJobDelete);
        if (!id || !window.confirm('Delete this failed upload activity?')) return;

        button.disabled = true;
        try {
            await post(`/uploads/${id}/delete`, {});
            jobs.delete(id);
            render();
        } catch (error) {
            button.disabled = false;
            notify(error.message || 'Could not delete this upload activity.', 'danger');
        }
    });

    document.querySelector('[data-upload-manager-minimize]')?.addEventListener('click', () => { 
        localStorage.setItem('lh-upload-manager-minimized','1'); 
        render(); 
    });
    document.querySelector('[data-upload-manager-dismiss]')?.addEventListener('click', () => { 
        localStorage.setItem('lh-upload-manager-dismissed','1'); 
        render(); 
    });
    managerOpen?.addEventListener('click', () => { 
        localStorage.removeItem('lh-upload-manager-minimized'); 
        render(); 
    });
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
                const started = await post('/uploads/start', { 
                    upload_type: type, 
                    original_filename: file.name, 
                    file_size: file.size 
                });

                jobId = Number(started.id);

                // A new upload should always bring the upload manager back.
                localStorage.removeItem('lh-upload-manager-dismissed');
                //localStorage.removeItem('lh-upload-manager-minimized');

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
                    jobs.set(jobId, { ...jobs.get(jobId), id: jobId, upload_type: type, original_filename: file.name, status: 'completed', progress: 100, client_updated_at: Date.now() });
                    render();
                    form.reset();
                    notify(response.message || 'Upload complete.', 'success', response.link || '');
                    refresh();
                } else {
                    const message = response.error || 'Your file could not be uploaded. Please try again.';
                    jobs.set(jobId, { ...jobs.get(jobId), id: jobId, upload_type: type, original_filename: file.name, status: 'failed', progress: 0, error_message: message, client_updated_at: Date.now() });
                    render();
                    post(`/uploads/${jobId}/fail`, { message }).then(refresh).catch(() => {});
                    notify(message, 'danger');
                }
                submit?.removeAttribute('disabled');
            });
            xhr.addEventListener('error', () => {
                activeUploads -= 1;
                const message = 'Network error. Your file could not be uploaded. Please select it and try again.';
                jobs.set(jobId, { ...jobs.get(jobId), id: jobId, upload_type: type, original_filename: file.name, status: 'failed', progress: 0, error_message: message, client_updated_at: Date.now() });
                render();
                post(`/uploads/${jobId}/fail`, { message }).then(refresh).catch(() => {});
                notify(message, 'danger');
                submit?.removeAttribute('disabled');
            });
            xhr.send(data);
        });
    });
});

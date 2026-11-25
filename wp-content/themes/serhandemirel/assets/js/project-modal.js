
// Remember the first outside page that sent the visitor (search engine,
// AI assistant, LinkedIn...) for this visit; it is sent with the form.
(function () {
    try {
        if (!sessionStorage.getItem('sdReferrer')) {
            var ref = document.referrer;
            var external = ref && new URL(ref).host !== window.location.host;
            sessionStorage.setItem('sdReferrer', external ? ref : '-');
        }
    } catch (e) {}
})();

// Service chip: "About: Digital Products", removable.
function sdSetService(name) {
    var input = document.getElementById('sd-service');
    var chip = document.getElementById('sd-service-chip');
    if (!input || !chip) return;
    input.value = name || '';
    document.getElementById('sd-service-name').textContent = name || '';
    chip.classList.toggle('hidden', !name);
    chip.classList.toggle('inline-flex', !!name);
}
(function () {
    var clear = document.getElementById('sd-service-clear');
    if (clear) clear.addEventListener('click', function () { sdSetService(''); });
})();

// Form Submission AJAX
document.getElementById('projectForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const submitBtn = document.getElementById('submitBtn');
    const formMessage = document.getElementById('formMessage');
    const btnText = submitBtn.querySelector('span');

    // Loading state
    const originalText = btnText.innerText;
    const i18n = sdContact.i18n || {};
    btnText.innerText = i18n.sending || 'Sending...';
    submitBtn.style.pointerEvents = 'none';
    submitBtn.style.opacity = '0.7';
    formMessage.classList.add('hidden');
    formMessage.classList.remove('bg-green-500/10', 'text-green-400', 'bg-red-500/10', 'text-red-400', 'border', 'border-green-500/20', 'border-red-500/20');

    const formData = new FormData(form);
    formData.append('action', sdContact.action);
    formData.append('lang', sdContact.lang || '');
    formData.append('source', window.location.href);
    try {
        var ref = sessionStorage.getItem('sdReferrer');
        if (ref && ref !== '-') formData.set('referrer', ref);
    } catch (err) {}

    fetch(sdContact.ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(res => {
        formMessage.classList.remove('hidden');

        if (res.status === 200 && res.body.status === 'success') {
            // Extract first name
            const fullName = formData.get('Name').trim();
            const firstName = fullName.split(' ')[0] || i18n.friend || 'Friend';

            // Update success container
            document.getElementById('successName').innerText = firstName;

            // Hide form container, show success container with animation
            const formContainer = document.getElementById('formContainer');
            const successContainer = document.getElementById('successContainer');

            formContainer.classList.add('hidden');
            successContainer.classList.remove('hidden');

            // Optional: tiny animation effect on show
            successContainer.style.opacity = '0';
            successContainer.style.transform = 'scale(0.95)';
            setTimeout(() => {
                successContainer.style.transition = 'all 0.4s ease';
                successContainer.style.opacity = '1';
                successContainer.style.transform = 'scale(1)';
            }, 50);

            if (window.sdTrack) window.sdTrack('sd_form_submit', { form_name: 'start_a_project' });

            form.reset();
        } else {
            formMessage.classList.add('bg-red-500/10', 'text-red-400', 'border', 'border-red-500/20');
            formMessage.innerText = res.body.message || i18n.error || 'Something went wrong. Please try again.';
        }
    })
    .catch(error => {
        formMessage.classList.remove('hidden');
        formMessage.classList.add('bg-red-500/10', 'text-red-400', 'border', 'border-red-500/20');
        formMessage.innerText = i18n.network || 'Network error. Please try again later.';
    })
    .finally(() => {
        btnText.innerText = originalText;
        submitBtn.style.pointerEvents = 'auto';
        submitBtn.style.opacity = '1';
    });
});

function openProjectModal(service) {
    const modal = document.getElementById('projectModal');
    // On a service page every "Start a project" button carries the service.
    if (typeof service !== 'string') {
        const page = document.querySelector('[data-sd-service]');
        service = page ? page.getAttribute('data-sd-service') : '';
    }
    sdSetService(service);
    const backdrop = document.getElementById('projectModalBackdrop');
    const content = document.getElementById('projectModalContent');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    setTimeout(() => {
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');

        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);

    document.body.style.overflow = 'hidden';
}

function closeProjectModal() {
    const modal = document.getElementById('projectModal');

    // Wait for transition to finish, then reset state
    setTimeout(() => {
        const formContainer = document.getElementById('formContainer');
        const successContainer = document.getElementById('successContainer');
        const formMessage = document.getElementById('formMessage');
        if(formContainer) formContainer.classList.remove('hidden');
        if(successContainer) successContainer.classList.add('hidden');
        if(formMessage) formMessage.classList.add('hidden');
    }, 300);
    const backdrop = document.getElementById('projectModalBackdrop');
    const content = document.getElementById('projectModalContent');

    backdrop.classList.remove('opacity-100');
    backdrop.classList.add('opacity-0');

    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('projectModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeProjectModal();
        }
    }
});

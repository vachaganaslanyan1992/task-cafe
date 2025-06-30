window.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('userForm');
    const resetBtn = document.getElementById('resetBtn');
    const messageBox = document.querySelector('.messageBox');
    const tbody = document.getElementById('userTableBody');

    form.onsubmit = async function (e) {
        e.preventDefault();
        clearValidation();

        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        try {
            const res = await fetch('/api/users', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (!res.ok || data.error) {
                handleValidationErrors(data.error || { general: ['Something went wrong.'] });
                return;
            }

            form.reset();
            updateTable(data.users);
            showMessage('Participant added successfully!', 'success');
        } catch (error) {
            console.error(error);
            showMessage('Network error. Try again.', 'danger');
        }
    };

    resetBtn.onclick = async function () {
        try {
            const res = await fetch('/api/reset', { method: 'POST' });
            const data = await res.json();

            if (!res.ok || data.error) {
                showMessage(data.error || 'Reset failed.', 'danger');
                return;
            }

            updateTable([]);
            showMessage('List reset successfully!', 'secondary');
        } catch (error) {
            console.error(error);
            showMessage('Reset request failed.', 'danger');
        }
    };

    function updateTable(users) {
        tbody.innerHTML = '';
        users.forEach(u => {
            const row = `<tr><td>${u.name}</td><td>${u.email}</td><td>€${u.share}</td></tr>`;
            tbody.innerHTML += row;
        });
    }

    function handleValidationErrors(errors) {
        Object.entries(errors).forEach(([field, messages]) => {
            const input = document.querySelector(`[name="${field}"]`);
            const feedback = document.querySelector(`[data-error="${field}"]`);

            if (input && feedback) {
                input.classList.add('is-invalid');
                feedback.textContent = messages.join(', ');
            } else {
                showMessage(messages.join(', '), 'danger');
            }
        });
    }

    function clearValidation() {
        document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }

    function showMessage(text, type = 'success') {
        if (!messageBox) return;

        messageBox.classList.add(`alert-${type}`);
        messageBox.textContent = text;
        messageBox.classList.remove('d-none');

        setTimeout(() => {
            messageBox.classList.add('d-none');
            messageBox.classList.remove(`alert-${type}`);
        }, 2000);
    }
});

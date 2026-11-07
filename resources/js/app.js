//
const sidebar = document.querySelector('[data-sidebar]');
const menuToggle = document.querySelector('[data-menu-toggle]');

menuToggle?.addEventListener('click', () => {
    sidebar?.classList.toggle('-translate-x-full');
});

document.querySelectorAll('[data-close-modal]').forEach((button) => {
    button.addEventListener('click', () => button.closest('dialog')?.close());
});

const memberDialog = document.querySelector('#member-dialog');
const memberForm = document.querySelector('[data-member-form]');
const memberPlanSelect = memberForm?.elements.namedItem('membership_plan_id');
const configureMemberPlans = (selectedId = '') => {
    memberPlanSelect?.querySelectorAll('option[data-active="0"]').forEach((option) => {
        option.disabled = option.value !== String(selectedId);
    });
};

document.querySelectorAll('[data-add-member]').forEach((button) => {
    button.addEventListener('click', () => {
        memberForm?.reset();
        memberForm.action = '/members';
        memberForm.querySelector('[name="_method"]')?.remove();
        configureMemberPlans();
        memberDialog?.querySelector('[data-dialog-title]').replaceChildren('Add a member');
        memberDialog?.showModal();
    });
});

document.querySelectorAll('[data-edit-member]').forEach((button) => {
    button.addEventListener('click', () => {
        const values = button.dataset;
        memberForm.action = `/members/${values.id}`;
        let method = memberForm.querySelector('[name="_method"]');
        if (!method) {
            method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            memberForm.append(method);
        }
        method.value = 'PATCH';
        configureMemberPlans(values.membership_plan_id);
        ['full_name', 'email', 'phone', 'gender', 'age', 'address', 'membership_plan_id', 'started_at'].forEach((field) => {
            const input = memberForm.elements.namedItem(field);
            if (input) input.value = values[field] ?? '';
        });
        memberDialog.querySelector('[data-dialog-title]').replaceChildren('Edit member');
        memberDialog.showModal();
    });
});

const planDialog = document.querySelector('#plan-dialog');
const planForm = document.querySelector('[data-plan-form]');

document.querySelectorAll('[data-add-plan]').forEach((button) => {
    button.addEventListener('click', () => {
        planForm?.reset();
        planForm.action = '/membership-plans';
        planForm.querySelector('[name="_method"]')?.remove();
        planDialog?.querySelector('[data-dialog-title]').replaceChildren('Create a plan');
        planDialog?.showModal();
    });
});

document.querySelectorAll('[data-edit-plan]').forEach((button) => {
    button.addEventListener('click', () => {
        const values = button.dataset;
        planForm.action = `/membership-plans/${values.id}`;
        let method = planForm.querySelector('[name="_method"]');
        if (!method) {
            method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            planForm.append(method);
        }
        method.value = 'PATCH';
        ['name', 'duration_days', 'price'].forEach((field) => {
            planForm.elements.namedItem(field).value = values[field];
        });
        planForm.elements.namedItem('is_active').checked = values.active === '1';
        planDialog.querySelector('[data-dialog-title]').replaceChildren('Edit membership plan');
        planDialog.showModal();
    });
});

document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
});

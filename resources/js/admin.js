const schedule = document.querySelector('[data-schedule]');

if (schedule) {
    const rows = schedule.querySelector('[data-schedule-rows]');
    const template = schedule.querySelector('template[data-schedule-template]');
    const addButton = schedule.querySelector('[data-schedule-add]');

    let nextIndex = Number.parseInt(schedule.dataset.nextIndex || '0', 10);

    addButton?.addEventListener('click', () => {
        if (! rows || ! template) {
            return;
        }

        const fragment = template.content.cloneNode(true);
        const row = fragment.querySelector('[data-schedule-row]');

        fragment.querySelectorAll('[name]').forEach((field) => {
            field.name = field.name.replaceAll('__index__', String(nextIndex));
            field.id = field.id.replaceAll('__index__', String(nextIndex));
        });

        nextIndex += 1;
        rows.append(fragment);
        row?.querySelector('select, input')?.focus();
    });

    rows?.addEventListener('click', (event) => {
        const button = event.target.closest('[data-schedule-remove]');

        if (! button) {
            return;
        }

        const row = button.closest('[data-schedule-row]');

        if (! row) {
            return;
        }

        if (rows.querySelectorAll('[data-schedule-row]').length <= 1) {
            row.querySelectorAll('[name]').forEach((field) => {
                field.value = '';
            });

            return;
        }

        row.remove();
    });
}

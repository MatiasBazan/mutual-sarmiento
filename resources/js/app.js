import './bootstrap';

import Alpine from 'alpinejs';
import Choices from 'choices.js';

window.Alpine = Alpine;
window.Choices = Choices;

Alpine.start();

// ─── SVG Icons para el filtro de celular ───────────────────────────────
const CELULAR_ICONS = {
    // Smartphone (todos)
    todos: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>`,
    '': `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>`,
    // Phone check (con celular)
    con: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><path d="M9 12l2 2 4-4"/></svg>`,
    // Phone off (sin celular)
    sin: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/></svg>`,
};

// Convertir todos los <select> en dropdowns estilizados con Choices.js.
function initChoices() {
    document.querySelectorAll('select:not([data-choices-init]):not([data-no-choices])').forEach((el) => {
        el.setAttribute('data-choices-init', '1');

        // Detectar clases especiales del <select> original
        const isEstado  = el.classList.contains('select-estado');
        const isFiltro  = el.classList.contains('filtro-select');
        const isCelular = el.classList.contains('filtro-celular');

        // Construir las clases extras para el contenedor Choices
        const extraClasses = [];
        if (isEstado)  extraClasses.push('choices--estado');
        if (isFiltro)  extraClasses.push('filtro-select');
        if (isCelular) extraClasses.push('filtro-celular');

        const instance = new Choices(el, {
            searchEnabled: false,
            itemSelectText: '',
            shouldSort: false,
            allowHTML: true,
            classNames: {
                containerOuter: ['choices', ...extraClasses],
            },
            callbackOnCreateTemplates: function () {
                const self = this;
                return {
                    // Template para cada opción en el dropdown
                    choice: function ({ classNames }, data) {
                        const div = document.createElement('div');
                        div.className = `${classNames.item} ${classNames.itemChoice}`;
                        if (data.isSelected) div.className += ` ${classNames.selectedState}`;
                        if (data.isDisabled) div.className += ` ${classNames.disabledState}`;
                        div.setAttribute('data-choice', '');
                        div.setAttribute('data-id', data.id);
                        div.setAttribute('data-value', data.value);
                        div.setAttribute('data-select-text', self.config.itemSelectText);
                        div.setAttribute('role', data.groupId > 0 ? 'treeitem' : 'option');

                        // Estado: puntito de color
                        if (isEstado) {
                            const dot = document.createElement('span');
                            dot.className = 'choices-dot';
                            dot.setAttribute('data-status', data.value);
                            div.appendChild(dot);
                            div.setAttribute('data-status', data.value);
                        }

                        // Celular: ícono SVG
                        if (isCelular && CELULAR_ICONS[data.value]) {
                            const icon = document.createElement('span');
                            icon.className = 'choices-icon';
                            icon.innerHTML = CELULAR_ICONS[data.value];
                            div.appendChild(icon);
                        }

                        const label = document.createTextNode(data.label);
                        div.appendChild(label);

                        return div;
                    },
                    // Template para el item seleccionado
                    item: function ({ classNames }, data) {
                        const div = document.createElement('div');
                        div.className = `${classNames.item} ${classNames.itemSelectable}`;
                        div.setAttribute('data-item', '');
                        div.setAttribute('data-id', data.id);
                        div.setAttribute('data-value', data.value);
                        div.setAttribute('role', 'option');

                        // Estado: puntito de color
                        if (isEstado) {
                            const dot = document.createElement('span');
                            dot.className = 'choices-dot';
                            dot.setAttribute('data-status', data.value);
                            div.appendChild(dot);
                            div.setAttribute('data-status', data.value);
                        }

                        // Celular: ícono SVG
                        if (isCelular && CELULAR_ICONS[data.value]) {
                            const icon = document.createElement('span');
                            icon.className = 'choices-icon';
                            icon.innerHTML = CELULAR_ICONS[data.value];
                            div.appendChild(icon);
                        }

                        const label = document.createTextNode(data.label);
                        div.appendChild(label);

                        return div;
                    },
                };
            },
        });

        // Almacenar referencia para poder destruir/re-init si hace falta
        el._choicesInstance = instance;
    });
}

document.addEventListener('DOMContentLoaded', initChoices);
// Re-init después de navegaciones Alpine/Livewire que inyecten nuevos selects.
document.addEventListener('alpine:initialized', initChoices);

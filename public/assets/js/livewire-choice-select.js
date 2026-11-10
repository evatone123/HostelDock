(function () {
    'use strict';

    const findLivewireComponent = (element) => {
        const root = element.closest('[wire\\:id]');

        if (! root || typeof window.Livewire === 'undefined' || typeof window.Livewire.find !== 'function') {
            return null;
        }

        return window.Livewire.find(root.getAttribute('wire:id'));
    };

    const normalize = (value, isMultiple) => {
        if (! isMultiple) {
            return value === null || value === undefined ? '' : String(value);
        }

        const list = Array.isArray(value)
            ? value
            : (value === null || value === undefined || value === '' ? [] : [value]);

        return list.map((item) => String(item)).filter((item) => item !== '');
    };

    const bindCommitSync = () => {
        if (window.__livewireChoiceCommitBound || typeof window.Livewire === 'undefined' || typeof window.Livewire.hook !== 'function') {
            return;
        }

        window.__livewireChoiceCommitBound = true;

        window.Livewire.hook('commit', ({ component }) => {
            if (! component || ! component.el) {
                return;
            }

            component.el.querySelectorAll('[data-livewire-choice]').forEach((select) => {
                if (typeof select._syncChoiceValue === 'function') {
                    select._syncChoiceValue();
                }
            });
        });
    };

    window.initLivewireChoiceSelect = function (element, config) {
        if (! element || element.dataset.choicesLivewireInit === 'true') {
            return;
        }

        element.dataset.choicesLivewireInit = 'true';

        if (element._choices) {
            try {
                element._choices.destroy();
            } catch (e) {}
        }

        const propertyName = config.propertyName;
        const isMultiple = Boolean(config.isMultiple);
        const shouldSort = Boolean(config.shouldSort);
        const limit = Number(config.maxItemCount);
        const maxCount = Number.isFinite(limit) && limit >= 0 ? limit : -1;
        const placeholderLabel = config.placeholderLabel || 'Select';
        const root = element.closest('.choice-select-livewire');
        const hiddenInput = isMultiple
            ? null
            : root?.querySelector('[data-choice-livewire-target]');

        const choices = new Choices(element, {
            shouldSort: shouldSort,
            removeItemButton: true,
            maxItemCount: maxCount,
            searchEnabled: true,
        });

        element._choices = choices;

        let suppressSync = false;

        const readValue = () => normalize(choices.getValue(true), isMultiple);

        const syncToLivewire = () => {
            if (suppressSync) {
                return;
            }

            const value = readValue();
            const component = findLivewireComponent(element);

            if (hiddenInput) {
                hiddenInput.value = Array.isArray(value) ? (value[0] ?? '') : value;
                hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
            }

            if (component && typeof component.set === 'function') {
                component.set(propertyName, value, false);
            }
        };

        element._syncChoiceValue = syncToLivewire;

        choices.passedElement.element.addEventListener('change', syncToLivewire);
        choices.passedElement.element.addEventListener('addItem', syncToLivewire);
        choices.passedElement.element.addEventListener('removeItem', syncToLivewire);

        const writeToWidget = (next) => {
            suppressSync = true;
            choices.removeActiveItems();

            if (isMultiple ? next.length > 0 : next !== '') {
                choices.setChoiceByValue(next);
            }

            suppressSync = false;
        };

        const component = findLivewireComponent(element);

        if (component && typeof component.$watch === 'function') {
            component.$watch(propertyName, (incoming) => {
                const next = normalize(incoming, isMultiple);

                if (JSON.stringify(next) === JSON.stringify(readValue())) {
                    return;
                }

                writeToWidget(next);
            });
        }

        const placeholderChoice = { value: '', label: placeholderLabel, placeholder: true };
        let optionsSignature = root?.dataset.choiceOptions ?? '';

        const applyOptions = () => {
            const raw = root?.dataset.choiceOptions ?? '';

            if (raw === optionsSignature) {
                return;
            }
            optionsSignature = raw;

            let payload;
            try {
                payload = JSON.parse(raw || '[]');
            } catch (e) {
                return;
            }

            const previous = readValue();
            const available = payload.map((choice) => String(choice.value));
            const retained = isMultiple
                ? previous.filter((value) => available.includes(value))
                : (available.includes(previous) ? previous : '');

            suppressSync = true;
            choices.removeActiveItems();
            choices.setChoices([placeholderChoice, ...payload], 'value', 'label', true);
            suppressSync = false;

            writeToWidget(retained);

            if (JSON.stringify(retained) !== JSON.stringify(previous)) {
                syncToLivewire();
            }
        };

        if (root) {
            new MutationObserver(applyOptions).observe(root, {
                attributes: true,
                attributeFilter: ['data-choice-options'],
            });
        }

        const form = element.closest('form');
        if (form && ! form.dataset.choicesSubmitSyncBound) {
            form.dataset.choicesSubmitSyncBound = 'true';
            form.addEventListener('submit', () => {
                form.querySelectorAll('[data-livewire-choice]').forEach((select) => {
                    if (typeof select._syncChoiceValue === 'function') {
                        select._syncChoiceValue();
                    }
                });
            }, { capture: true });
        }

        bindCommitSync();
    };

    const startWhenLivewireReady = () => {
        bindCommitSync();
    };

    if (window.Livewire) {
        startWhenLivewireReady();
    } else {
        document.addEventListener('livewire:init', startWhenLivewireReady, { once: true });
    }
})();

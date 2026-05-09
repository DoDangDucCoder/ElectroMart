{{-- Shared storefront countdown timers. --}}
<script>
    (function () {
        const SECOND = 1000;
        const HOUR = 60 * 60 * SECOND;
        const DAY = 24 * HOUR;
        const STORAGE_PREFIX = 'dangduc_countdown_deadline_v2';
        const timers = new WeakMap();

        function toPositiveNumber(value) {
            const parsed = Number(value);

            return Number.isFinite(parsed) && parsed > 0 ? parsed : null;
        }

        function getConfiguredEndTime(element) {
            const value = element.getAttribute('data-end-time')
                || element.getAttribute('data-deadline')
                || element.getAttribute('data-flash-sale-countdown');

            if (!value) {
                return null;
            }

            const timestamp = new Date(value).getTime();

            return Number.isNaN(timestamp) ? null : timestamp;
        }

        function hasUnit(element, unit) {
            return Boolean(
                element.querySelector(`[data-countdown-unit="${unit}"], [data-flash-unit="${unit}"]`)
            );
        }

        function getCountdownType(element) {
            const configuredType = element.getAttribute('data-countdown-type');

            if (configuredType) {
                return configuredType;
            }

            return hasUnit(element, 'days') ? 'weekly' : 'daily';
        }

        function getCycleMs(element) {
            const cycleDays = toPositiveNumber(element.getAttribute('data-countdown-cycle-days'));
            const cycleHours = toPositiveNumber(element.getAttribute('data-countdown-cycle-hours'));

            if (cycleDays) {
                return cycleDays * DAY;
            }

            if (cycleHours) {
                return cycleHours * HOUR;
            }

            return getCountdownType(element) === 'daily' ? DAY : 7 * DAY;
        }

        function getStorageKey(element, index) {
            const configuredKey = element.getAttribute('data-countdown-storage-key');

            if (configuredKey) {
                return `${STORAGE_PREFIX}:${configuredKey}`;
            }

            return [
                STORAGE_PREFIX,
                window.location.pathname || '/',
                getCountdownType(element),
                String(index)
            ].join(':');
        }

        function nextCycleEndFrom(baseTime, cycleMs, now) {
            if (!Number.isFinite(baseTime)) {
                return now + cycleMs - SECOND;
            }

            if (baseTime > now) {
                return baseTime;
            }

            const elapsedCycles = Math.floor((now - baseTime) / cycleMs) + 1;

            return baseTime + elapsedCycles * cycleMs;
        }

        function readStoredDeadline(key) {
            try {
                const stored = Number(window.localStorage.getItem(key));

                return Number.isFinite(stored) ? stored : null;
            } catch (error) {
                return null;
            }
        }

        function writeStoredDeadline(key, deadline) {
            try {
                window.localStorage.setItem(key, String(deadline));
            } catch (error) {
                // Ignore storage failures; the timer still runs for the current page view.
            }
        }

        function resolveDeadline(element, index) {
            const now = Date.now();
            const cycleMs = getCycleMs(element);
            const configuredEndTime = getConfiguredEndTime(element);

            if (configuredEndTime) {
                return nextCycleEndFrom(configuredEndTime, cycleMs, now);
            }

            const storageKey = getStorageKey(element, index);
            const storedDeadline = readStoredDeadline(storageKey);

            if (storedDeadline && storedDeadline > now) {
                return storedDeadline;
            }

            const nextDeadline = now + cycleMs - SECOND;
            writeStoredDeadline(storageKey, nextDeadline);

            return nextDeadline;
        }

        function formatValue(value) {
            return String(Math.max(0, value)).padStart(2, '0');
        }

        function setUnitText(element, unit, value) {
            element.querySelectorAll(`[data-countdown-unit="${unit}"], [data-flash-unit="${unit}"]`).forEach((unitElement) => {
                unitElement.textContent = formatValue(value);
            });
        }

        function render(element, state) {
            const now = Date.now();

            if (state.deadline <= now) {
                state.deadline = now + state.cycleMs - SECOND;

                if (state.storageKey) {
                    writeStoredDeadline(state.storageKey, state.deadline);
                }
            }

            const totalSeconds = Math.max(0, Math.floor((state.deadline - now) / SECOND));
            const hasDays = state.hasDays;
            const days = hasDays ? Math.floor(totalSeconds / 86400) : 0;
            const hours = hasDays
                ? Math.floor((totalSeconds % 86400) / 3600)
                : Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            setUnitText(element, 'days', days);
            setUnitText(element, 'hours', hours);
            setUnitText(element, 'minutes', minutes);
            setUnitText(element, 'seconds', seconds);
            element.classList.toggle('is-ended', false);
        }

        function initCountdown(element, index) {
            const previousTimer = timers.get(element);

            if (previousTimer) {
                window.clearInterval(previousTimer);
            }

            const cycleMs = getCycleMs(element);
            const configuredEndTime = getConfiguredEndTime(element);
            const storageKey = configuredEndTime ? null : getStorageKey(element, index);
            const state = {
                cycleMs,
                storageKey,
                deadline: resolveDeadline(element, index),
                hasDays: hasUnit(element, 'days')
            };

            render(element, state);

            timers.set(element, window.setInterval(function () {
                render(element, state);
            }, SECOND));
        }

        function initAll() {
            document.querySelectorAll('[data-tech-countdown], [data-flash-sale-countdown]').forEach(initCountdown);
        }

        window.StorefrontCountdown = {
            init: initCountdown,
            initAll: initAll
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAll);
        } else {
            initAll();
        }
    })();
</script>

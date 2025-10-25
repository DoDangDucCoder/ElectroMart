{{-- Theme Toggle Component --}}
<div class="theme-toggle-container">
    <div class="theme-toggle" aria-label="Toggle theme" role="button" tabindex="0">
        <i class="theme-toggle-icon moon">🌙</i>
    </div>
</div>

<style>
.theme-toggle-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

.theme-toggle {
    background: var(--card-bg);
    border: 2px solid var(--border-color);
    border-radius: 50px;
    padding: 12px;
    cursor: pointer;
    box-shadow: 0 4px 12px var(--shadow);
    transition: all 0.3s ease;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.theme-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px var(--shadow);
}

.theme-toggle:focus {
    outline: 2px solid var(--accent-color);
    outline-offset: 2px;
}

.theme-toggle-icon {
    font-size: 20px;
    transition: all 0.3s ease;
    display: block;
}

.theme-toggle-icon.sun {
    color: #ffc107;
}

.theme-toggle-icon.moon {
    color: var(--text-secondary);
}

[data-theme="dark"] .theme-toggle-icon.sun {
    color: #ffc107;
}

[data-theme="dark"] .theme-toggle-icon.moon {
    color: #ffffff;
}

/* Responsive */
@media (max-width: 768px) {
    .theme-toggle-container {
        top: 15px;
        right: 15px;
    }
    
    .theme-toggle {
        width: 40px;
        height: 40px;
        padding: 8px;
    }
    
    .theme-toggle-icon {
        font-size: 16px;
    }
}

/* Loading animation */
.theme-toggle.loading {
    pointer-events: none;
}

.theme-toggle.loading .theme-toggle-icon {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

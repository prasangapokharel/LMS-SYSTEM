<?php
/**
 * TailAdmin Theme - Alert Components
 * include/alerts.php
 */

function showSuccessAlert($title, $message, $link = null) {
    $linkHtml = $link ? '<a href="' . htmlspecialchars($link) . '" class="mt-3 inline-block text-sm font-medium text-gray-500 underline dark:text-gray-400">Learn more</a>' : '';
    
    echo '
    <div class="rounded-xl border border-success-500 bg-success-50 p-4 dark:border-success-500/30 dark:bg-success-500/15 mb-4">
        <div class="flex items-start gap-3">
            <div class="-mt-0.5 text-success-500">
                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M3.70186 12.0001C3.70186 7.41711 7.41711 3.70186 12.0001 3.70186C16.5831 3.70186 20.2984 7.41711 20.2984 12.0001C20.2984 16.5831 16.5831 20.2984 12.0001 20.2984C7.41711 20.2984 3.70186 16.5831 3.70186 12.0001ZM12.0001 1.90186C6.423 1.90186 1.90186 6.423 1.90186 12.0001C1.90186 17.5772 6.423 22.0984 12.0001 22.0984C17.5772 22.0984 22.0984 17.5772 22.0984 12.0001C22.0984 6.423 17.5772 1.90186 12.0001 1.90186ZM15.6197 10.7395C15.9712 10.388 15.9712 9.81819 15.6197 9.46672C15.2683 9.11525 14.6984 9.11525 14.347 9.46672L11.1894 12.6243L9.6533 11.0883C9.30183 10.7368 8.73198 10.7368 8.38051 11.0883C8.02904 11.4397 8.02904 12.0096 8.38051 12.3611L10.553 14.5335C10.7217 14.7023 10.9507 14.7971 11.1894 14.7971C11.428 14.7971 11.657 14.7023 11.8257 14.5335L15.6197 10.7395Z" fill=""/>
                </svg>
            </div>
            <div>
                <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">' . htmlspecialchars($title) . '</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">' . htmlspecialchars($message) . '</p>
                ' . $linkHtml . '
            </div>
        </div>
    </div>';
}

function showErrorAlert($title, $message, $link = null) {
    $linkHtml = $link ? '<a href="' . htmlspecialchars($link) . '" class="mt-3 inline-block text-sm font-medium text-gray-500 underline dark:text-gray-400">Learn more</a>' : '';
    
    echo '
    <div class="rounded-xl border border-error-500 bg-error-50 p-4 dark:border-error-500/30 dark:bg-error-500/15 mb-4">
        <div class="flex items-start gap-3">
            <div class="-mt-0.5 text-error-500">
                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M20.3499 12.0004C20.3499 16.612 16.6115 20.3504 11.9999 20.3504C7.38832 20.3504 3.6499 16.612 3.6499 12.0004C3.6499 7.38881 7.38833 3.65039 11.9999 3.65039C16.6115 3.65039 20.3499 7.38881 20.3499 12.0004ZM11.9999 22.1504C17.6056 22.1504 22.1499 17.6061 22.1499 12.0004C22.1499 6.3947 17.6056 1.85039 11.9999 1.85039C6.39421 1.85039 1.8499 6.3947 1.8499 12.0004C1.8499 17.6061 6.39421 22.1504 11.9999 22.1504ZM13.0008 16.4753C13.0008 15.923 12.5531 15.4753 12.0008 15.4753L11.9998 15.4753C11.4475 15.4753 10.9998 15.923 10.9998 16.4753C10.9998 17.0276 11.4475 17.4753 11.9998 17.4753L12.0008 17.4753C12.5531 17.4753 13.0008 17.0276 13.0008 16.4753ZM11.9998 6.62898C12.414 6.62898 12.7498 6.96476 12.7498 7.37898L12.7498 13.0555C12.7498 13.4697 12.414 13.8055 11.9998 13.8055C11.5856 13.8055 11.2498 13.4697 11.2498 13.0555L11.2498 7.37898C11.2498 6.96476 11.5856 6.62898 11.9998 6.62898Z" fill="#F04438"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">' . htmlspecialchars($title) . '</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">' . htmlspecialchars($message) . '</p>
                ' . $linkHtml . '
            </div>
        </div>
    </div>';
}

function showWarningAlert($title, $message, $link = null) {
    $linkHtml = $link ? '<a href="' . htmlspecialchars($link) . '" class="mt-3 inline-block text-sm font-medium text-gray-500 underline dark:text-gray-400">Learn more</a>' : '';
    
    echo '
    <div class="rounded-xl border border-warning-500 bg-warning-50 p-4 dark:border-warning-500/30 dark:bg-warning-500/15 mb-4">
        <div class="flex items-start gap-3">
            <div class="-mt-0.5 text-warning-500">
                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zM12 7a1 1 0 011 1v4a1 1 0 11-2 0V8a1 1 0 011-1zm0 8a1 1 0 100 2 1 1 0 000-2z" fill=""/>
                </svg>
            </div>
            <div>
                <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">' . htmlspecialchars($title) . '</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">' . htmlspecialchars($message) . '</p>
                ' . $linkHtml . '
            </div>
        </div>
    </div>';
}

function showInfoAlert($title, $message, $link = null) {
    $linkHtml = $link ? '<a href="' . htmlspecialchars($link) . '" class="mt-3 inline-block text-sm font-medium text-gray-500 underline dark:text-gray-400">Learn more</a>' : '';
    
    echo '
    <div class="rounded-xl border border-blue-light-500 bg-blue-light-50 p-4 dark:border-blue-light-500/30 dark:bg-blue-light-500/15 mb-4">
        <div class="flex items-start gap-3">
            <div class="-mt-0.5 text-blue-light-500">
                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M3.6501 11.9996C3.6501 7.38803 7.38852 3.64961 12.0001 3.64961C16.6117 3.64961 20.3501 7.38803 20.3501 11.9996C20.3501 16.6112 16.6117 20.3496 12.0001 20.3496C7.38852 20.3496 3.6501 16.6112 3.6501 11.9996ZM12.0001 1.84961C6.39441 1.84961 1.8501 6.39392 1.8501 11.9996C1.8501 17.6053 6.39441 22.1496 12.0001 22.1496C17.6058 22.1496 22.1501 17.6053 22.1501 11.9996C22.1501 6.39392 17.6058 1.84961 12.0001 1.84961ZM10.9992 7.52468C10.9992 8.07697 11.4469 8.52468 11.9992 8.52468H12.0002C12.5525 8.52468 13.0002 8.07697 13.0002 7.52468C13.0002 6.9724 12.5525 6.52468 12.0002 6.52468H11.9992C11.4469 6.52468 10.9992 6.9724 10.9992 7.52468ZM12.0002 17.371C11.586 17.371 11.2502 17.0352 11.2502 16.621V10.9445C11.2502 10.5303 11.586 10.1945 12.0002 10.1945C12.4144 10.1945 12.7502 10.5303 12.7502 10.9445V16.621C12.7502 17.0352 12.4144 17.371 12.0002 17.371Z" fill=""/>
                </svg>
            </div>
            <div>
                <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">' . htmlspecialchars($title) . '</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">' . htmlspecialchars($message) . '</p>
                ' . $linkHtml . '
            </div>
        </div>
    </div>';
}

// JavaScript functions for dynamic alerts
function generateAlertJS() {
    echo '
    <script>
    function showTailAdminAlert(type, title, message, duration = 5000) {
        const alertId = "alert-" + Date.now();
        const icons = {
            success: `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.70186 12.0001C3.70186 7.41711 7.41711 3.70186 12.0001 3.70186C16.5831 3.70186 20.2984 7.41711 20.2984 12.0001C20.2984 16.5831 16.5831 20.2984 12.0001 20.2984C7.41711 20.2984 3.70186 16.5831 3.70186 12.0001ZM12.0001 1.90186C6.423 1.90186 1.90186 6.423 1.90186 12.0001C1.90186 17.5772 6.423 22.0984 12.0001 22.0984C17.5772 22.0984 22.0984 17.5772 22.0984 12.0001C22.0984 6.423 17.5772 1.90186 12.0001 1.90186ZM15.6197 10.7395C15.9712 10.388 15.9712 9.81819 15.6197 9.46672C15.2683 9.11525 14.6984 9.11525 14.347 9.46672L11.1894 12.6243L9.6533 11.0883C9.30183 10.7368 8.73198 10.7368 8.38051 11.0883C8.02904 11.4397 8.02904 12.0096 8.38051 12.3611L10.553 14.5335C10.7217 14.7023 10.9507 14.7971 11.1894 14.7971C11.428 14.7971 11.657 14.7023 11.8257 14.5335L15.6197 10.7395Z" fill=""/></svg>`,
            error: `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M20.3499 12.0004C20.3499 16.612 16.6115 20.3504 11.9999 20.3504C7.38832 20.3504 3.6499 16.612 3.6499 12.0004C3.6499 7.38881 7.38833 3.65039 11.9999 3.65039C16.6115 3.65039 20.3499 7.38881 20.3499 12.0004ZM11.9999 22.1504C17.6056 22.1504 22.1499 17.6061 22.1499 12.0004C22.1499 6.3947 17.6056 1.85039 11.9999 1.85039C6.39421 1.85039 1.8499 6.3947 1.8499 12.0004C1.8499 17.6061 6.39421 22.1504 11.9999 22.1504ZM13.0008 16.4753C13.0008 15.923 12.5531 15.4753 12.0008 15.4753L11.9998 15.4753C11.4475 15.4753 10.9998 15.923 10.9998 16.4753C10.9998 17.0276 11.4475 17.4753 11.9998 17.4753L12.0008 17.4753C12.5531 17.4753 13.0008 17.0276 13.0008 16.4753ZM11.9998 6.62898C12.414 6.62898 12.7498 6.96476 12.7498 7.37898L12.7498 13.0555C12.7498 13.4697 12.414 13.8055 11.9998 13.8055C11.5856 13.8055 11.2498 13.4697 11.2498 13.0555L11.2498 7.37898C11.2498 6.96476 11.5856 6.62898 11.9998 6.62898Z" fill="#F04438"/></svg>`,
            warning: `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zM12 7a1 1 0 011 1v4a1 1 0 11-2 0V8a1 1 0 011-1zm0 8a1 1 0 100 2 1 1 0 000-2z" fill=""/></svg>`,
            info: `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.6501 11.9996C3.6501 7.38803 7.38852 3.64961 12.0001 3.64961C16.6117 3.64961 20.3501 7.38803 20.3501 11.9996C20.3501 16.6112 16.6117 20.3496 12.0001 20.3496C7.38852 20.3496 3.6501 16.6112 3.6501 11.9996ZM12.0001 1.84961C6.39441 1.84961 1.8501 6.39392 1.8501 11.9996C1.8501 17.6053 6.39441 22.1496 12.0001 22.1496C17.6058 22.1496 22.1501 17.6053 22.1501 11.9996C22.1501 6.39392 17.6058 1.84961 12.0001 1.84961ZM10.9992 7.52468C10.9992 8.07697 11.4469 8.52468 11.9992 8.52468H12.0002C12.5525 8.52468 13.0002 8.07697 13.0002 7.52468C13.0002 6.9724 12.5525 6.52468 12.0002 6.52468H11.9992C11.4469 6.52468 10.9992 6.9724 10.9992 7.52468ZM12.0002 17.371C11.586 17.371 11.2502 17.0352 11.2502 16.621V10.9445C11.2502 10.5303 11.586 10.1945 12.0002 10.1945C12.4144 10.1945 12.7502 10.5303 12.7502 10.9445V16.621C12.7502 17.0352 12.4144 17.371 12.0002 17.371Z" fill=""/></svg>`
        };
        
        const colors = {
            success: "border-success-500 bg-success-50 text-success-500",
            error: "border-error-500 bg-error-50 text-error-500", 
            warning: "border-warning-500 bg-warning-50 text-warning-500",
            info: "border-blue-light-500 bg-blue-light-50 text-blue-light-500"
        };
        
        const alertHTML = `
            <div id="${alertId}" class="rounded-xl border ${colors[type]} p-4 mb-4 opacity-0 transform translate-y-2 transition-all duration-300">
                <div class="flex items-start gap-3">
                    <div class="-mt-0.5">${icons[type]}</div>
                    <div class="flex-1">
                        <h4 class="mb-1 text-sm font-semibold text-gray-800">${title}</h4>
                        <p class="text-sm text-gray-500">${message}</p>
                    </div>
                    <button onclick="removeTailAdminAlert(\'${alertId}\')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        // Insert at top of main content
        const mainContent = document.querySelector("main") || document.body;
        mainContent.insertAdjacentHTML("afterbegin", alertHTML);
        
        // Animate in
        setTimeout(() => {
            const alert = document.getElementById(alertId);
            if (alert) {
                alert.classList.remove("opacity-0", "translate-y-2");
                alert.classList.add("opacity-100", "translate-y-0");
            }
        }, 100);
        
        // Auto remove
        if (duration > 0) {
            setTimeout(() => removeTailAdminAlert(alertId), duration);
        }
    }
    
    function removeTailAdminAlert(alertId) {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.classList.add("opacity-0", "translate-y-2");
            setTimeout(() => alert.remove(), 300);
        }
    }
    
    // Convenience functions
    window.showSuccessAlert = (title, message, duration) => showTailAdminAlert("success", title, message, duration);
    window.showErrorAlert = (title, message, duration) => showTailAdminAlert("error", title, message, duration);
    window.showWarningAlert = (title, message, duration) => showTailAdminAlert("warning", title, message, duration);
    window.showInfoAlert = (title, message, duration) => showTailAdminAlert("info", title, message, duration);
    </script>';
}
?>

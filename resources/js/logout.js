// logout.js
document.addEventListener('DOMContentLoaded', () => {
    const logoutRoute = document.head.querySelector('meta[name="logout-route"]').content;

    window.addEventListener('beforeunload', () => {
        navigator.sendBeacon(logoutRoute);
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            navigator.sendBeacon(logoutRoute);
        }
    });
});
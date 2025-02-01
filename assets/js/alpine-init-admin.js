/*
 * Admin-specific Alpine.js initialization
 * This script encapsulates any Alpine.js setup for the admin area in a dedicated class
 */

class AdminAlpineInitializer {
    constructor() {
        this.init();
    }

    init() {
        // Place your admin-specific Alpine.js initialization logic here
        console.log('Admin Alpine.js initialized');
        // Example: initialize Alpine components or plugins if needed
    }
}

document.addEventListener('alpine:init', () => {
    new AdminAlpineInitializer();
});

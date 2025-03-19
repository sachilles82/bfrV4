document.addEventListener('DOMContentLoaded', function() {
    // Initialize theme from database on page load (via Auth::user()->theme)
    const dbTheme = document.getElementById('htmlRoot').className
        .split(' ')
        .find(cls => cls.startsWith('theme-'))
        ?.replace('theme-', '') || 'default';

    // Check if localStorage theme exists
    const localTheme = localStorage.getItem('theme');

    // If themes don't match or localStorage is empty, update localStorage
    if (!localTheme || localTheme !== dbTheme) {
        localStorage.setItem('theme', dbTheme);
        console.log('Synchronized theme from database:', dbTheme);
    }

    // Apply theme from localStorage (which is now synchronized)
    applyTheme(localStorage.getItem('theme') || 'default');
});

function applyTheme(newTheme) {
    const rootEl = document.getElementById('htmlRoot');

    // Remove all possible theme classes
    rootEl.classList.remove(
        'theme-default',
        'theme-orange',
        'theme-green',
        'theme-blue',
        'theme-red',
        'theme-lime',
        'theme-pink'
    );

    // Add new theme class
    rootEl.classList.add('theme-' + newTheme);

    // Store in localStorage for persistence
    localStorage.setItem('theme', newTheme);

    console.log('Theme applied:', newTheme);
}


// (function() {
//     let localTheme = localStorage.getItem('theme');
//     if (localTheme) {
//         const htmlRoot = document.getElementById('htmlRoot');
//         htmlRoot.classList.remove(
//             'theme-default', 'theme-orange', 'theme-green', 'theme-blue', 'theme-red', 'theme-lime', 'theme-pink'
//         );
//         htmlRoot.classList.add('theme-' + localTheme);
//     }
// })
// ();

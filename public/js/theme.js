function applyTheme(newTheme) {
    const rootEl = document.getElementById('htmlRoot');

    rootEl.classList.remove(
        'theme-default',
        'theme-orange',
        'theme-green',
        'theme-blue',
        'theme-red',
        'theme-lime',
        'theme-pink'
    );
    rootEl.classList.add('theme-' + newTheme);

    localStorage.setItem('theme', newTheme);

    console.log('Theme geändert auf: ' + newTheme);
    console.log('Aktuelles Theme in localStorage ist:', localStorage.getItem('theme') || 'default');
}

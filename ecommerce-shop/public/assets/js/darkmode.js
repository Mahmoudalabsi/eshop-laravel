document.addEventListener( 'DOMContentLoaded', function ()
{
    const themeToggle = document.getElementById( 'theme-toggle' );
    const themeIcon = document.getElementById( 'theme-icon' );
    const themeText = document.getElementById( 'theme-text' ); // العنصر الجديد للنص
    const body = document.body;

    if ( localStorage.getItem( 'theme' ) === 'dark' )
    {
        enableDarkMode();
    }

    themeToggle.addEventListener( 'click', () =>
    {
        body.classList.contains( 'dark-mode' ) ? disableDarkMode() : enableDarkMode();
    } );

    function enableDarkMode ()
    {
        body.classList.add( 'dark-mode' );
        themeIcon.classList.replace( 'bi-moon-fill', 'bi-sun-fill' );
        if ( themeText ) themeText.innerText = "الوضع المضيء";
        themeToggle.classList.remove( 'btn-outline-light' );
        themeToggle.classList.add( 'btn-outline-warning' );
        localStorage.setItem( 'theme', 'dark' );

        // --- تحديث الرسوم البيانية ---
        refreshCharts();
    }

    function disableDarkMode ()
    {
        body.classList.remove( 'dark-mode' );
        themeIcon.classList.replace( 'bi-sun-fill', 'bi-moon-fill' );
        if ( themeText ) themeText.innerText = "الوضع المظلم";
        themeToggle.classList.remove( 'btn-outline-warning' );
        themeToggle.classList.add( 'btn-outline-light' );
        localStorage.setItem( 'theme', 'light' );

        // --- تحديث الرسوم البيانية ---
        refreshCharts();
    }

    function refreshCharts ()
    {
        if ( typeof lastCategoryData !== 'undefined' ) renderCategoryChart( lastCategoryData );
        if ( typeof lastLabels !== 'undefined' ) renderModernChart( lastLabels, lastValues );
    }

} );

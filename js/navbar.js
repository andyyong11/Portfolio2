document.addEventListener('DOMContentLoaded', function() {
    // Define the HTML content for the navbar
    const navbarHTML = `
    <h1>Andy Yong</h1>
    <nav>
        <ul>
            <li><a href="index.html" id="nav-home">Home</a></li>
            <li><a href="AndyYongResume.pdf" id="nav-resume">Resume</a></li>
            <li><a href="CIS_Syllabi.html" id="nav-syllabi">CIS Syllabi</a></li>
            <li><a href="java_projects.html" id="nav-java">Java Projects</a></li>
            <li><a href="csharp_projects.html" id="nav-csharp">C# Projects</a></li>
            <li><a href="webservices_projects.html" id="nav-webservices">Web Services</a></li>
            <li><a href="http://catalog.southeasttech.edu/preview_program.php?catoid=17&poid=2004&hl=%22programming%22" target="_blank">STI CIS Program</a></li>
        </ul>
    </nav>
    `;
    
    // Insert the navbar into the header element
    const headerElement = document.querySelector('header');
    if (headerElement) {
        headerElement.innerHTML = navbarHTML;
    }
    
    // Set the active class based on the current page
    const currentPage = window.location.pathname.split('/').pop();
    
    switch(currentPage) {
        case 'index.html':
        case '':
            document.getElementById('nav-home').classList.add('active');
            break;
        case 'CIS_Syllabi.html':
            document.getElementById('nav-syllabi').classList.add('active');
            break;
        case 'java_projects.html':
            document.getElementById('nav-java').classList.add('active');
            break;
        case 'csharp_projects.html':
            document.getElementById('nav-csharp').classList.add('active');
            break;
        case 'webservices_projects.html':
            document.getElementById('nav-webservices').classList.add('active');
            break;
    }
}); 
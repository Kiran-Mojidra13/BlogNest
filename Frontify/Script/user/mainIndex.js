document.addEventListener("DOMContentLoaded", function(event) {
   
    const showNavbar = (toggleId, navId, bodyId, headerId) =>{
    const toggle = document.getElementById(toggleId),
    nav = document.getElementById(navId),
    bodypd = document.getElementById(bodyId),
    headerpd = document.getElementById(headerId)
    
    // Validate that all variables exist
    if(toggle && nav && bodypd && headerpd){
    toggle.addEventListener('click', ()=>{
    // show navbar
    nav.classList.toggle('show')
    // change icon
    toggle.classList.toggle('bx-x')
    // add padding to body
    bodypd.classList.toggle('body-pd')
    // add padding to header
    headerpd.classList.toggle('body-pd')
    })
    }
    }
    
    showNavbar('header-toggle','nav-bar','body-pd','header')
    
    /*===== LINK ACTIVE =====*/
    const linkColor = document.querySelectorAll('.nav_link')
    
    function colorLink(){
    if(linkColor){
    linkColor.forEach(l=> l.classList.remove('active'))
    this.classList.add('active')
    }
    }
    linkColor.forEach(l=> l.addEventListener('click', colorLink))
    
     // Your code to run since DOM is loaded and ready
    });



    //---------------------------------- put data page on the Main-contant page at main index------------
  document.addEventListener("DOMContentLoaded", function() {
        // Function to handle dynamic content loading
        const loadContent = (url) => {
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    // Replace the content in the main container
                    document.getElementById("main-container").innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching content:', error);
                    document.getElementById("main-container").innerHTML =
                        "<p>Failed to load content.</p>";
                });
        };

        // Attach event listeners to navigation links
        document.querySelectorAll('.nav_link').forEach(link => {
            link.addEventListener('click', function(event) {
                const pageUrl = this.getAttribute('href');

                // Skip dynamic loading for `create_blog.php`
                if (pageUrl.includes('create_blog.php')) {
                    return; // Allow default behavior to navigate to the new page
                }

                // Prevent the default link click behavior for other pages
                event.preventDefault();

                // Highlight the active link
                document.querySelectorAll('.nav_link').forEach(l => l.classList.remove(
                    'active'));
                this.classList.add('active');

                // Load content dynamically
                loadContent(pageUrl);
            });
        });
    });

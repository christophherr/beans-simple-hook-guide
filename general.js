const bshgSelectInput = ( () => {
    const body = document.querySelector('body');

    body.addEventListener('click', function(e) {
       if ( ".beans-hook" === `.${e.target.parentElement.className}` ) {
            e.target.select();
       }
    } )

});

window.onload = bshgSelectInput();

// https://github.com/apvarun/toastify-js/blob/master/README.md

function showToast(message, severity){
    let color = '#6c757d'
    if(severity === 'error'){
        color = "#c82333";
    }else if(severity === 'warning'){
        color = "#ffc107";
    }else if(severity === 'info'){
        color = "#17a2b8";
    }else if(severity === 'success'){
        color = "#28a745";
    }
    Toastify({
        text: message,
        duration: 5000,
        close: false,
        gravity: "top", // `top` or `bottom`
        position: "right", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
            background: color,
        },
        onClick: function () { } // Callback after click
    }).showToast();

}
$(document).ready(function(){
   const refreshHandle = setInterval(refresh, 3000);
});

function refresh(){
    "use strict";
    $.ajax({
        url : "/list",
        type : "GET",
        context: "#messages",
        dataType : "html",
        success : function(code_html, statut){ // success est toujours en place, bien sûr !
            $(this).html(code_html);
        },
    });
}
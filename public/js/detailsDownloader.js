/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function getImageData(imageUrl){
    $.ajax({
        type     : "GET",
        url      : "http://localhost/webDetective/public/index/get-photo-info?url=" + imageUrl,
        success  : function(data) {
            $("#tagResults").empty().append(data);
        },
        failure : function(){
            alert("Failure!");
        }
    });
}

$(document).ready(function(){
    var a = $("#name").text();
    var url ="http://localhost/webDetective/public/index/google?name=" + a;
    $.ajax({
        type     : "GET",
        url      : url,
        success  : function(data) {
            $("#googleResults").empty().append(data);
        },
        failure : function(){
            alert("Failure!");
        }
    });
    var link = $("#image-container a").attr('href');
    $.ajax({
        type     : "GET",
        url      : "http://localhost/webDetective/public/naszaklasa/get-photo?link=" + link,
        success  : function(data) {
            $("#image-container").empty().append(data);
            var url = $("#image-container img").attr('src');
            getImageData(url);
        },
        failure : function(){
            alert("Failure!");
        }
    });
    var link = $("#fb-photo-link").text();
    $.ajax({
        type     : "GET",
        url      : "http://localhost/webDetective/public/facebook/fb-get-photo?url=" + link,
        success  : function(data) {
            $("#image-container").empty().append(data);
            var url = $("#image-container img").attr('src');
            getImageData(url);
        },
        failure : function(){
            alert("Failure!");
        }
    });
});


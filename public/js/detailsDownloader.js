/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){
    
        
    //    $(".google-search").click(function(e){
    //        e.preventDefault();
    var a = $("#name").text();
    var url ="http://localhost/webDetective/public/index/google?name=" + a;
    $.ajax({
        type     : "GET",
        url      : url,
        success  : function(data) {
            $("#googleResults").empty().append(data);
        },
        failure : function(){
            alert("Failure");
        }
    });
        
    var a = $("#name").text();
    var url ="http://localhost/webDetective/public/index/krs?name=" + a;
    $.ajax({
        type     : "GET",
        url      : url,
        success  : function(data) {
            $("#krsResults").empty().append(data);
        },
        failure : function(){
            alert("Failure");
        }
    });
    //    })
    //    
    $(".krs-ajax").click(function(e){
        e.preventDefault();
        var a = $("#name").text();
        var captcha = $("#captcha").val();
        var hidden = $("input[name='t:formdata']").val();
        alert(captcha);
        var url ="http://localhost/webDetective/public/index/krs-details?name=" + a + "&captcha=" + captcha + "&hidden=" + hidden;

        $.ajax({
            type     : "GET",
            url      : url,
            success  : function(data) {
            alert("Sukces");
            },
            failure : function(){
            //alert("Failure");
            }
        });
    })
});


// 1 -----------------------------------

function func() {
    var d = document;
    var s = 'script';
    var id = 'facebook-jssdk'
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); 
    js.id = id;
    js.src = "//connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v2.5&appId=1735690529983120";
    fjs.parentNode.insertBefore(js, fjs);
}

$(document).ready(function(){
    $('#bottom_place').load('/advanced/frontend/web/_bottom.php');
    $('#content img').addClass('img-thumbnail');
    $('li.dropdown[id!="site"]:has("ul.dropdown-menu"):has("li.active")').addClass('active');
    $('button[data-target="#donateModal"]').click(function(){
        $('#donate_form').load('/advanced/frontend/web/_donate.php');
    });
    
    setTimeout(function () {
        var obj = $.parseJSON($('#str_biskni').html());
        var banner = '<div class="panel panel-default"><div class="panel-heading"><a title="узнать подробнее о книге" href="'+obj.book_url+'" target="_blank"><strong>'+obj.name+'</strong></a></div><div class="panel-body"><p><a title="узнать подробнее о книге" href="'+obj.book_url+'" target="_blank"><img width="240" class="img-thumbnail" src="'+obj.picture+'" alt=""></a></p><div class="panel-footer text-right"><small>автор: '+obj.author_name+'</small></div></div></div>';        
        $('#random_book').append(banner); 
    }, 3000);    
    
}); 

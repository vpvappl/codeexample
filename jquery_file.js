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

// 2 ---------------------------------------

jQuery("iframe").removeAttr("frameborder").removeAttr("allowfullscreen");
		
jQuery("#start_search").click(function () {
	jQuery("#search_form").slideToggle("slow");
}); 
		
jQuery("[name=radiogroup],[name=radiocontext]").click(function(){
  var gets = jQuery(this + ":checked").attr('id');
  if(gets == 'inlinks' || gets == 'inphoto' || gets == 'invideo'){
	  jQuery("#in_title").attr('checked','checked');
	  jQuery("#in_content").attr('disabled','disabled');
  }else{
	  jQuery("#in_content").removeAttr('disabled');
  }
  jQuery("#ajaxsearch").val('');
  jQuery("#showsearch").html('');
  jQuery("#showsearch").slideUp();      
});

jQuery("#ajaxsearch").keyup(function(){
	var what = jQuery("#ajaxsearch").val();
	var where = jQuery('[name=radiogroup]:checked').attr('id');
	var whereis = jQuery('[name=radiogroup]:checked').attr('title');
	var context = jQuery('[name=radiocontext]:checked').attr('id');      
  if(what.length > 2){                  
	jQuery.get('/search/ajaxsearch/', {where:where,what:what,context:context}, function(res){
		jQuery("#showsearch").html("<b class='resultb'>Результаты поиска в материалах &laquo;"+whereis+"&raquo; по запросу &laquo;"+what+"&raquo;:</b>" + res);
		jQuery("#showsearch").slideDown();
	});
  }else{
	  jQuery("#showsearch").html('');
	  jQuery("#showsearch").slideUp();
  }    
});

// 3 ---------------------------------------

var cls = $('.mmk-content img').attr('class');
if (cls !== 'empty') {
	$('.mmk-content img').addClass('img-thumbnail');
}
$('.mmk-content img.img-responsive').removeClass('img-thumbnail');
$('table.descr table').addClass('table table-bordered');

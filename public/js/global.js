// HTML5 placeholder plugin version 0.3
// Enables cross-browser* html5 placeholder for inputs, by first testing
// for a native implementation before building one.
//
// USAGE: 
//$('input[placeholder]').placeholder();
(function ($) {
    $.extend({
        sajax: function (url, jsonData, success, options) {
            var config = {
                url: url,
                type: "POST",
                data: jsonData ? jsonData : null,
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                success: success
            };
            $.ajax($.extend(options, config));
        }
    });
})(jQuery);

(function($) {
    $.fn.hasScrollBar = function() {
        return this.get(0).scrollHeight > this.height();
    };
})(jQuery);

(function($){
  
  $.fn.placeholder = function(options) {
    return this.each(function() {
      if ( !("placeholder"  in document.createElement(this.tagName.toLowerCase()))) {
        var $this = $(this);
        var placeholder = $this.attr('placeholder');
        $this.val(placeholder).data('color', $this.css('color')).css('color', '#aaa');
        $this
          .focus(function(){ if ($.trim($this.val())===placeholder){ $this.val('').css('color', $this.data('color')); } })
          .blur(function(){ if (!$.trim($this.val())){ $this.val(placeholder).data('color', $this.css('color')).css('color', '#aaa'); } });
      }
    });
  };
}(jQuery));

var menuYloc = null;
var previewYloc = null;



function initAutocompleter(){

	$('.full').unbind().autocomplete({
		serviceUrl:'/index/autocompleter/',  
		minChars:2,
		maxHeight:400,
		width:330,
		deferRequestBy: 500,
		noCache: false,
		params: { },
		onSelect: function(value, data)
		{ 
			var type = data.substr(-1);
			data =  data.substr(0, data.length-1);
			switch(type)
			{
				case '1':
					location.replace('/contact/personrecord/id/'+data);
					break;
				case '2':
					location.replace('/contact/companyrecord/id/'+data);
					break;
				case '3':
					location.replace('/project/record/id/'+data);
					break;
			}
		}
	});
}

function removeTag(type, id_ref, id_tag)
{	
	data = new Object();
	data.id_ref = id_ref;
	data.type = type;
	data.id_tag = id_tag;
	$.sajax('/contact/removetag',data,function(newdata){
		if(newdata)
		{	
			var div = $('<div></div>');
			$.each(newdata, function(i,e){
				div.append('<span class="tag"><a href="/contact/sort/tag/'+e.id+'">'+e.name+'</a><a href="#" class="tag_delete" onclick="removeTag('+e.type+','+e.id_ref+','+e.id+')"> x</a></span> ');
			});
		}
		$('div.display_tags').html(div.html());
		//$('a.add_tag').show();
	});

}

function deleteTag(id)
{	
	var data = new Object();
	data.id = id;
	$.sajax('/contact/deletetag',data,function(newdata){
		if(newdata)
		{	
			$('span.tag'+id).remove();
		}
	});
}

function addToLastviewed(t,id)
{	
	
	if(t == 0)
	{
		var data = pushPerson(id);
		$.cookie('latest', JSON.stringify(data), { path: '/'});
	}else{
		var data = pushCompany(id);
		$.cookie('latest', JSON.stringify(data), { path: '/'});
	}
	 //console.dir(JSON.parse($.cookie('latest')));
}

function pushPerson(id)
{
	if($.cookie('latest'))
	{
		cookiedata = JSON.parse($.cookie('latest'));
		if(cookiedata.person)
		{	
			index = indexOf.call(cookiedata.person, id); // 1
			if(index == -1)
			{	
				if(cookiedata.person.length > 3){
					cookiedata.person.shift();
				}
				cookiedata.person.push(id);
			}
			
		}else{
			if(cookiedata){}
			else{
				cookiedata = new Object();
			}
			cookiedata.person = new Array();
			cookiedata.person.push(id);
			
		}
	}else{
		cookiedata = new Object();
		cookiedata.person = new Array();
		cookiedata.person.push(id);
	}
	return cookiedata;
}

function pushCompany(id)
{
	if($.cookie('latest'))
	{
		cookiedata = JSON.parse($.cookie('latest'));
		if(cookiedata.company)
		{	
			index = indexOf.call(cookiedata.company, id); // 1
			if(index == -1)
			{	
				if(cookiedata.company.length > 3){
					cookiedata.company.shift();
				}
				cookiedata.company.push(id);
			}
				
		}else{
			
			if(cookiedata){}
			else{
				cookiedata = new Object();
			}
			cookiedata.company = new Array();
			cookiedata.company.push(id);
		}
	}else{
		cookiedata = new Object();
		cookiedata.company = new Array();
		cookiedata.company.push(id);
	}
	return cookiedata;
}

var indexOf = function(needle) {
    if(typeof Array.prototype.indexOf === 'function') {
        indexOf = Array.prototype.indexOf;
    } else {
        indexOf = function(needle) {
            var i = -1, index = -1;

            for(i = 0; i < this.length; i++) {
                if(this[i] === needle) {
                    index = i;
                    break;
                }
            }

            return index;
        };
    }
    return indexOf.call(this, needle);
};


function replaceURLWithHTMLLinks(text) {
    var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    return text.replace(exp,"<a href='$1'>$1</a>"); 
}

function fitTextarea() 
{	
	if($('textarea.markItUpTextarea').hasScrollBar())
	{
		$('textarea.markItUpTextarea').css('max-height',"50px");
		$('textarea.markItUpTextarea').css('height',"50px");
	}
  
};


function resizeTextarea() 
{	
	if($('textarea.markItUpTextarea').hasScrollBar())
	{
		$('textarea.markItUpTextarea').css('max-height',"300px");
		$('textarea.markItUpTextarea').css('height',"300px");
	}
  
};


$(window).load(function(){
	$('textarea.markItUpTextarea').keydown(function() {
		resizeTextarea();
	});
	
	$('textarea.markItUpTextarea').blur(function() {
		fitTextarea();
	});
	$.each($('p'), function(i,e){
		$(e).html(replaceURLWithHTMLLinks($(e).html()));
	});
});


// perform JavaScript after the document is scriptable.
$(document).ready(function() {
	
	//tags ^^
	$('input#tag_submit').click(function()
		{	
			data = new Object();
			data.id_ref = $('div.add_tag > input#id_ref').val();
			data.type = $('div.add_tag > input#tag_type').val();
			data.tag = $('div.add_tag > input#tag').val();
			if(data.tag != '' && data.tag.length < 50 && data.tag.length > 0)
			{	
				$('div.add_tag').hide();
				$.sajax('/contact/addtag',data,function(newdata){
					if(newdata)
					{	
						var div = $('<div></div>');
						$.each(newdata, function(i,e){
							div.append('<span class="tag"><a href="/contact/sort/tag/'+e.id+'">'+e.name+'</a><a href="#" class="tag_delete" onclick="removeTag('+e.type+','+e.id_ref+','+e.id+')"> x</a></span> ');
						});
					}
					$('div.display_tags').html(div.html());
					$('a.add_tag').show();
					$('a.tag_delete').hide();
				});
			}
		}
	);
	
	$('input#tag_close').click(function()
			{	
				$('a.tag_delete').hide();
				$('div.add_tag').hide();
				$('a.add_tag').show();
				
			}
		);
	
	
	
	$("a.fancybox").fancybox({
		width:480, 
		height:480,
		overlayShow: false,
		centerOnScroll: true
	});
	
	$('a.toggle-file').click(function(){
		$('div.file-wrap').toggle();
	});
	
	$('img.add-file').click(
			function(){
				$(this).parent().append('<br /><input type="file" id="file'+$(this).parent().children().length+'" name="file'+$(this).parent().children().length+'" /><img class="remove-file" src="/images/icons/delete.png" />');
				$('img.remove-file').click(
						function(){
							$(this).prev().prev().remove();
							$(this).prev().remove();
							$(this).remove();
						}
				);
			}
		);
	
	
	initAutocompleter();

    /**
     * Setup Tooltips
     */
    // this set's up the sidebar tooltip for the recent contacts
    $('.recent .contact').tooltip({
        position: 'center right', // position it to the right
        effect: 'slide', // add a slide effect
        offset: [30,15], // adjust the position 30 pixels to the top and 19 pixels to the left
        onBeforeShow: function() {
            this.getTip().appendTo('body');
        }
    });
    $('[title]').tooltip({effect: 'slide', offset: [-14, 0]});

    // html element for the help popup
    $('body').append('<div class="apple_overlay black" id="overlay"><iframe class="contentWrap" style="width: 100%; height: 500px"></iframe></div>');

    // this is the help popup
    $("a.help[rel]").overlay({

        effect: 'apple',
        onBeforeLoad: function() {
            // grab wrapper element inside content
            var wrap = this.getOverlay().find(".contentWrap");
            // load the page specified in the trigger
            wrap.attr('src', this.getTrigger().attr("href"));
        }
    });

    /**
     * Setup Tabs
     */
    $("ul.tabs").tabs("div.panes > section");
    
    /**
     * Setup placeholder for text input
     */
    $('input[placeholder]').placeholder();

    // attach calendar to date inputs
    $(":date").dateinput({
        format: 'dd-mm-yyyy',
        trigger: false
    });

    // add close buttons to closeable message boxes
    $(".message.closeable").prepend('<span class="message-close"></span>')
        .find('.message-close')
        .click(function(){
            $(this).parent().fadeOut(function(){$(this).remove();});
        });

    // setup popup balloons (add contact / add task)
    $('.has-popupballoon').click(function(){
        // close all open popup balloons
        $('.popupballoon').fadeOut();
        $(this).next().fadeIn();
        return false;
    });

    $('.popupballoon .close').click(function(){
        $(this).parents('.popupballoon').fadeOut();
    });

  
    
    
    $('.list-view > li a:not(.more)').click(function(e){ e.stopPropagation(); });

    $('.preview-pane .preview .close').live('click', function(){
        $('.preview-pane .preview').animate({left: "-375px"}, 300);
        $('.list-view li').removeClass('current');
        return false;
    });
    // preview pane setup end

    // floating menu and preview pane
    if ($('#wrapper > header').length>0) { menuYloc = parseInt($('#wrapper > header').css("top").substring(0,$('#wrapper > header').css("top").indexOf("px")), 10); }
    if ($('.preview-pane .preview').length>0) { previewYloc = parseInt($('.preview-pane .preview').css("top").substring(0,$('.preview').css("top").indexOf("px")), 10); }
    $(window).scroll(function () {
        var offset = 0;
        if ($('#wrapper > header').length>0) {
            offset = menuYloc+$(document).scrollTop();
            if (!$.browser.msie) { $('#wrapper > header').animate({opacity: ($(document).scrollTop()<=10? 1 : 0.8)}); }
        }
        if ($('.preview-pane .preview').length>0) {
            offset = previewYloc+$(document).scrollTop()+400>=$('.main-section').height()? offset=$('.main-section').height()-400 : previewYloc+$(document).scrollTop();
            $('.preview-pane .preview').animate({top:offset},{duration:500,queue:false});
        }
    });

    if (!$.browser.msie) {
        $('#wrapper > header').hover(
            function(){$(this).animate({opacity: 1});},
            function(){$(this).animate({opacity: ($(document).scrollTop()<=10? 1 : 0.8)});}
        );
    }

    // Regular Expression to test whether the value is valid
    $.tools.validator.fn("[type=time]", "Please supply a valid time", function(input, value) { 
        return (/^\d\d:\d\d$/).test(value);
    });

    $.tools.validator.fn("[data-equals]", "Value not equal with the $1 field", function(input) {
        var name = input.attr("data-equals"),
        field = this.getInputs().filter("[name=" + name + "]"); 
        return input.val() === field.val() ? true : [name]; 
    });
     
    $.tools.validator.fn("[minlength]", function(input, value) {
        var min = input.attr("minlength");
        
        return value.length >= min ? true : {     
            en: "Please provide at least " +min+ " character" + (min > 1 ? "s" : "")
        };
    });
        
    $.tools.validator.localizeFn("[type=time]", {
        en: 'Please supply a valid time'
    });
     
    // setup the validators
    $(".form").validator({ 
        position: 'left', 
        offset: [25, 10],
        messageClass:'form-error',
        message: '<div><em/></div>' // em element is the arrow
    }).attr('novalidate', true);

    // setup the view switcher
    $('.main-content > header .view-switcher > h2 > a').click(function(){
        $(this).focus().parent().next().fadeIn();
        return false;
    }).blur(function(){
        $(this).parent().next().fadeOut();
        return false;
    });

    // promo closer
    $('#promo .close').click(function(){
        $('#promo').slideUp();
        $('body').removeClass('has-promo');
    });
    $('.multiselect').chosen();
    /*
     * Добавяне на фирми
     */
    $('#companySpan').click
    (
		function()
		{	
			if(!$(this).next().is("input"))
			{
				$(this).after($('<input id="companyInput" type="text" ></input>'));
				$('#companyInput').keypress
			    (
			    	function(e) 
				    {
			    		var code = (e.keyCode ? e.keyCode : e.which);
			    		 if(code == 13) 
			    		 { 
			    			var val = $('#companyInput').val();
			    			if(val && val != ""){
			    				addcompany(val);
			    			}
			    		 }
				    }
			    ); 
			}
		}
    );
    
    /*
     * Добавяне на контакт
     */
    $("#addContact").click
    (
		function()
		{	
			var data = new Object();
			data.firstname = $('input#firstName').val();
			data.lastname = $('input#lastName').val();
			data.id_company = $('select#company').val();
			//if($('#publiccompany').is(":checked"))
			//{
				data.publ = 0; //Видими за всички
			//}else{
				//data.publ = -1; //Видим само за мен
			//}
			var val = $('#companyInput').val();
			if(val && val != "")
			{	
				addcompanyandperson(val);
			}else{
				if(data.firstname != '' && data.lastname != "" && data.id_company != "")
				{	
					$.sajax("/contact/addperson",data, function(data){
						if(data.id){location.replace('/contact/index'); }
					});
				}
			}
		}
    );
    /*
     * Добавяне на категория задачи
     */
    $('#catSpan').click
    (
		function()
		{	
			if(!$(this).next().is("input"))
			{
				$(this).after($('<input id="catInput" type="text" ></input>'));
				$('#catInput').keypress
			    (
			    	function(e) 
				    {
			    		var code = (e.keyCode ? e.keyCode : e.which);
			    		 if(code == 13) 
			    		 { 
			    			 var val = $('#catInput').val();
			    			 addtaskcategory(val);
			    		 }
				    }
			    ); 
			}
		}
    );
    
    /*
     * Добавяне на задача
     */
    $("#addTask").click
    (
    		function()
    		{	
    			var data = new Object();
    			data.name = $('input#taskname').val();
    			data.owner = $('select#owner').val();
    			data.due = $('input#taskdue').val();
    			data.id_taskdue = $('select#id_taskdue').val();
    			data.cat = $('select#taskCat').val();
    			
    			if($('#publictask').is(":checked"))
    			{
    				data.publ = 1;
    			}else{
    				data.publ = 0;
    			}	
    			
				var val = $('#catInput').val();
				if(val && val != "")
				{	
					addcategoryandtask(val);
				}else{
	    			if(data.name != '' && (data.due != "" || data.id_taskdue != "6") && data.cat != "")
	    			{
	    				$.sajax("/task/add",data, function(data){
	    					if(data.id){location.replace('/task/index'); }
	    				});
	    			}
				}
    		}
    );
    
    
    /*
     *  Приключване на задача
     */
    $('.checkTask').click(
		function(){
			var data = new Object();
			data.id = $(this).attr('id');
			$.sajax("/task/finish",data, function(data){
				if(data){location.replace('/task/index'); }
			});
		}
    );
    editNote();
    
});



function addtaskcategory(val){
		$.sajax("/task/addcategory", {cat:val}, function(data){
			if(data)
			{
				$('#taskCat').append('<option value='+data.id+'>'+data.cat+'</option>');
				$('option:selected', '#taskCat').removeAttr('selected');
				$('#taskCat').children(':last-child').attr('selected','selected');
				$('#catInput').remove();
			}
		});
}


function editNote()
{
	 $('img.note_edit').click(
				function()
				{	
					if($(this).hasClass('edit'))
					{}
					else
					{	
						$(this).addClass('edit');
						var text = $(this).parent().parent().children('p').html();
						$(this).parent().parent().children('div:last').after('<textarea id="'+$(this).parent().attr('id')+'" style="width:95%">'+text+'</textarea><br /><img class="accept" id="'+$(this).parent().attr('id')+'"  style="cursor:pointer;" src="/images/icons/accept.png" />');
						$(this).parent().parent().children('img.accept').click(
								function()
								{	
									var newtext = $(this).parent().children('textarea').val();
									$.sajax('/index/editnote/id/'+$(this).parent().attr('id'),{"text":newtext},function(newdata)
									{	
										if(newdata.result)
										{
											$('li#'+newdata.id).children('div.entry-meta').after('<p>'+newtext+'</p>');
											$('li#'+newdata.id).children('textarea').remove();
											$('li#'+newdata.id).children('img.accept').remove();
											$('li#'+newdata.id).children('br:last').remove();
											$('img.note_edit').removeClass('edit');
											editNote();
											
										}
										else
										{
											$('li#'+newdata.id).children('div.entry-meta').after('<p>'+text+'</p>');
											$('li#'+newdata.id).children('textarea').remove();
											$('li#'+newdata.id).children('img.accept').remove();
											$('li#'+newdata.id).children('br:last').remove();
											$('img.note_edit').removeClass('edit');
											editNote();
										}
									});
								}	
						);
						$(this).parent().parent().children('p').remove();
					}
				}
		    );
}
function addcompany(val){
	$.sajax("/contact/addcompany", {company:val}, function(data){
		if(data)
		{
			$('select#company').append('<option value='+data.id+'>'+data.company+'</option>');
			$('option:selected', 'select#company').removeAttr('selected');
			$('select#company').children(':last-child').attr('selected','selected');
			$('#companyInput').remove();
		}
	});
}

function addcompanyandperson(val)
{
	$.sajax("/contact/addcompany", {company:val}, function(data){
		if(data)
		{
			$('select#company').append('<option value='+data.id+'>'+data.company+'</option>');
			$('option:selected', 'select#company').removeAttr('selected');
			$('select#company').children(':last-child').attr('selected','selected');
			$('#companyInput').remove();
			
			data.firstname = $('input#firstName').val();
			data.lastname = $('input#lastName').val();
			data.id_company = $('select#company').val();
			
			if(data.firstname != '' && data.lastname != "" && data.id_company)
			{
				$.sajax("/contact/addperson",data, function(data){
					if(data.id){location.replace('/contact/index'); }
				});
			}
		}
	});
}

function addcategoryandtask(val)
{	
	
	$.sajax("/task/addcategory", {cat:val}, function(data){
		if(data)
		{
			$('#taskCat').append('<option value='+data.id+'>'+data.cat+'</option>');
			$('option:selected', '#taskCat').removeAttr('selected');
			$('#taskCat').children(':last-child').attr('selected','selected');
			$('#catInput').remove();
			
			data.name = $('input#taskname').val();
			data.owner = $('select#owner').val();
			data.due = $('input#taskdue').val();
			data.cat = $('select#taskCat').val();
			
			
			if(data.name != '' && data.due != "" && data.cat)
			{
				$.sajax("/task/add", data, function(data){
					if(data.id){location.replace('/task/index'); }
				});
			}
		}
	});
	
	
}

function addNotes(data)
{
	$.sajax("/index/getnotes", data, function(newdata){
		//console.dir(newdata);
		if(newdata.length > 0)
		{	
			$.each(newdata, function(i,e){

				var li = $('<li class="note" id="'+e.id+'"></li>');
				var a = $('<a></a>');
				
				var span = $('<span class="timestamp"></span>');
				var span2 = $('<span class="timestamp_left"></span>');
				var p = $('<p></p>');
				var div = $('<div class="entry-meta"></div>');
				var divFiles = $('<div class="file-meta"></div>');
				//console.dir(e);
				if(e.personfirstname)
				{
					a.attr('href','/contact/personrecord/id/'+e.id_person);
					a.html(e.personfirstname+' '+e.personlastname);
					var a2 = $('<a></a>');
					a2.attr('href','/contact/companyrecord/id/'+e.id_company);
					a2.html(e.company);
					
					if(e.avatar){
						var divAvatar = $('<div style="float:left; margin-right: 10px;"><img width="55" height="55" src="'+e.avatar+'"/></div>');
						li.append(divAvatar);
					}
					li.append(a);
					if($.cookie('lang') == 'bg')
						{
							li.append(' от ');
						
						}
					else
						{	
							li.append(' from ');
						}
					li.append(a2);
				}
				else
				{
					if(e.company)
					{
						a.attr('href','/contact/companyrecord/id/'+e.id_company);
						a.html(e.company);
						li.append(a);
					}
					else
					{	
						a.attr('href','/project/record/id/'+e.id_case);
						a.html(e.case);
						li.append(a);
					}
				}
				
				if(e.files)
				{	
					$.each(e.files, function(l,p){
						var aFile = $('<a target="_blank"></a>');
						aFile.attr('href',p.file);
						var imgs = $('<img src="/images/icons/page.png">');
						switch(p.type)
						{
							case 1:
								imgs.attr('src',p.file);
								imgs.attr('width',50);
								imgs.attr('height',50);
								break;
							case 2:
								imgs.attr('src','/images/docIcons/pdf.png');
								imgs.attr('width',50);
								imgs.attr('height',50);
								break;
							case 3:
								imgs.attr('src','/images/docIcons/xls.png');
								imgs.attr('width',50);
								imgs.attr('height',50);
								break;
							case 4:
								imgs.attr('src','/images/docIcons/doc.png');
								imgs.attr('width',50);
								imgs.attr('height',50);
								break;
							case 5:
								imgs.attr('src','/images/docIcons/ppt.png');
								imgs.attr('width',50);
								imgs.attr('height',50);
								break;
						}
						
						aFile.append(imgs);
						divFiles.append(aFile);
					});
				}
				
				if($.cookie('lang') == 'en')
				{
					var d_names = new Array("Sunday", "Monday", "Tuesday",
						"Wednesday", "Thursday", "Friday", "Saturday");

					var m_names = new Array("January", "February", "March", 
						"April", "May", "June", "July", "August", "September", 
						"October", "November", "December");
				}else{
					var d_names = new Array("Неделя", "Понеделник", "Вторник",
							"Сряда", "Четвъртък", "Петък", "Събота");

						var m_names = new Array("Януари", "Февруари", "Март", 
							"Април", "Май", "Юни", "Юли", "Август", "Септември", 
							"Октомври", "Ноември", "Декември");
				}

				if(e.when){
					var d = new Date(e.when);
				}else{
					var d = new Date(e.cdate);
				}
				
				var curr_day = d.getDay();
				var curr_date = d.getDate();
				var curr_month = d.getMonth();
				var curr_year = d.getFullYear();
				
				span2.append('<b>'+d_names[curr_day] + ",  " + m_names[curr_month] + " " + curr_date + " " + curr_year+'</b>');
				if(profile.admin == 1 || profile.id == e.author)
				{
					span.append('<a href="/index/comment/id/'+e.id+'"><img src="/images/icons/comment.png"/></a><a class="fancybox" href="/index/editfancynote/id/'+e.id+'"> <img class="" src="/images/icons/pencil.png"/></a><a onclick="deleteNote('+e.id+')"><img id="'+e.id+'" class="delete_note" src="/images/icons/delete.png"/></a><br/>');
				}else{
					span.append('<a href="/index/comment/id/'+e.id+'"><img src="/images/icons/comment.png"/></a><br/>');
				}			
				p.append(replaceURLWithHTMLLinks(e.note));
				div.append(e.profile);
				
				li.append(span);
				li.append('<br />');
				li.append(span2);
				li.append(div);
				li.append(p);
				li.append(divFiles);
				
				$("ul.listing").append(li);
			});
			data.count = data.count + newdata.length;
			//console.dir(data.count);
			$(document).unbind('click.fb-start');
			$("a.fancybox").fancybox({
				width:480, 
				height:480,
				overlayShow: false,
				centerOnScroll: true
			});
		}
	});
//	editNote();
	return data;
}

function addNotesC(data)
{
	
	$.sajax("/index/getnotes", data, function(newdata){
		//console.dir(newdata);
		if(newdata.length > 0)
		{	
			$.each(newdata, function(i,e){

				var li = $('<li class="note" id="'+e.id+'"></li>');
				var span = $('<span class="timestamp"></span>');
				var span2 = $('<span class="timestamp_left_big"></span>');
				var p = $('<p></p>');
				var div = $('<div class="entry-meta"></div>');
				var divFiles = $('<div class="file-meta"></div>');
				//console.dir(e);
				
				
				if(e.files)
				{	
					$.each(e.files, function(l,p){
						var aFile = $('<a target="_blank"></a>');
						aFile.attr('href',p.file);
						var imgs = $('<img src="/images/icons/page.png">');
						switch(p.type)
						{
							case 1:
								imgs.attr('src',p.file);
								imgs.attr('width',50);
								imgs.attr('height',50);
								break;
							case 2:
								imgs.attr('src','/images/docIcons/pdf.png');
								imgs.attr('width',50);
								imgs.attr('height',50);
								break;
							case 3:
								imgs.attr('src','/images/docIcons/xls.png');
								imgs.attr('width',50);
								imgs.attr('height',50);
								break;
							case 4:
								imgs.attr('src','/images/docIcons/doc.png');
								imgs.attr('width',50);
								imgs.attr('height',50);
								break;
							case 5:
								imgs.attr('src','/images/docIcons/ppt.png');
								imgs.attr('width',50);
								imgs.attr('height',50);
								break;
						}
						aFile.append(imgs);
						divFiles.append(aFile);
					});
				}

				if($.cookie('lang') == 'en')
				{
					var d_names = new Array("Sunday", "Monday", "Tuesday",
						"Wednesday", "Thursday", "Friday", "Saturday");

					var m_names = new Array("January", "February", "March", 
						"April", "May", "June", "July", "August", "September", 
						"October", "November", "December");
				}else{
					var d_names = new Array("Неделя", "Понеделник", "Вторник",
							"Сряда", "Четвъртък", "Петък", "Събота");

						var m_names = new Array("Януари", "Февруари", "Март", 
							"Април", "Май", "Юни", "Юли", "Август", "Септември", 
							"Октомври", "Ноември", "Декември");
				}
				
				
				if(e.when){
					var d = new Date(e.when);
				}else{
					var d = new Date(e.cdate);
				}
				var curr_day = d.getDay();
				var curr_date = d.getDate();
				var curr_month = d.getMonth();
				var curr_year = d.getFullYear();
				
				span2.append('<b>'+d_names[curr_day] + ",  " + m_names[curr_month] + " " + curr_date + " " + curr_year+'</b>');
				span.append('<a href="/index/comment/id/'+e.id+'"><img src="/images/icons/comment.png"/></a><a class="fancybox" href="/index/editfancynote/id/'+e.id+'"><img class="" src="/images/icons/pencil.png"/></a> <a onclick="deleteNote('+e.id+')"><img id="'+e.id+'" class="delete_note" src="/images/icons/delete.png"/></a><br/>');
				p.append(replaceURLWithHTMLLinks(e.note));
				div.append(e.profile);
				
				if(e.avatar){
					var divAvatar = $('<div style="float:left; margin-right: 10px;"><img width="55" height="55" src="'+e.avatar+'"/></div>');
					li.append(divAvatar);
				}
				
				li.append(span);
				li.append(span2);
				li.append(div);
				li.append(p);
				li.append(divFiles);
				
				$("ul.listing").append(li);
			});
			data.count = data.count + newdata.length;
			//console.dir(data.count);
			
			$(document).unbind('click.fb-start');
			$("a.fancybox").fancybox({
				width:480, 
				height:480,
				overlayShow: false,
				centerOnScroll: true
			});
		}
	});
	
//	editNote();
	return data;
}

function addContacts(data)
{
	$.sajax("/contact/getcontacts", data, function(newdata){
		//console.dir(newdata);
		if(newdata.length > 0)
		{	
			$.each(newdata, function(i,e){
				e.phone = e.phone==null?'':e.phone;
				e.email = e.email==null?'':e.email;
				
				switch(e.type)
				{	
					case "1":
						e.avatar = (e.avatar)?e.avatar:'/images/user_32.png';
						var divCheck = $('<div class="check" style="float:left; padding: 0 10px"></div>');
						var inputCheck = $('<input name="contact" type="checkbox" class="person checks" id="'+e.id+'" />');
						var inputHidden = $('<input name="contact-type" type="hidden" id="'+e.type+'" />');
						var li = $('<li class="contact clearfix"></li>');
						var div1 = $('<div class="avatar"><img src="'+e.avatar+'" /></div>');
						var a = $('<a href="/contact/personrecord/id/'+e.id+'" class="name">'+e.name+'</a>');
						var span = $('<div class="meta"><a href="/contact/companyrecord/id/'+e.id_company+'" class="companyname">'+e.companyname+'</a><br><a href="mailto:'+e.email+'?bcc='+data.emailname+e.id_company+'a'+data.profile+'@'+data.emaildomain+'">'+e.email+'</a> '+e.phone+'</div>');
						var a1 = $('<a href="/contact/editperson/id/'+e.id+'" ><img src="/images/icons/pencil.png" /></a>');
						var a2 = $('<a href="/contact/deleteperson/id/'+e.id+'"><img src="/images/icons/delete.png" /></a>');
						break;
					case "0":
						e.avatar = (e.avatar)?e.avatar:'/images/users_business_32.png';
						var divCheck = $('<div class="check" style="float:left; padding: 0 10px"></div>');
						var inputCheck = $('<input name="contact" type="checkbox" class="company checks" id="'+e.id+'" />');
						var inputHidden = $('<input name="contact-type" type="hidden" id="'+e.type+'" />');
						var li = $('<li class="company clearfix"></li>');
						var div1 = $('<div class="avatar"><img src="'+e.avatar+'" /></div>');
						var a = $('<a href="/contact/companyrecord/id/'+e.id+'" class="name">'+e.name+'</a>');
						var span = $('<div class="meta_email"><a href="mailto:'+e.email+'?bcc='+data.emailname+e.id_company+'a'+data.profile+'@'+data.emaildomain+'">'+e.email+'</a> '+e.phone+'</div>');
						var a1 = $('<a href="/contact/editcompany/id/'+e.id+'"><img src="/images/icons/pencil.png" /></a>');
						var a2 = $('<a href="/contact/deletecompany/id/'+e.id+'"><img src="/images/icons/delete.png" /></a>');
						break;
				
				}
				
				var p = $('<p class="tags"></p>');
				if(e.tags)
				{
					$.each(e.tags, function(k,t){
						var tag = $('<span class="tag tag'+t.id+'"><a class="tag" href="/contact/sort/tag/'+t.id+'">'+t.name+'</a></span>');
						p.append(tag);
					});
				}
				//var tag = $('<span class="tag tag<?php echo $tag['id']; ?>"><a class="tag" href="/contact/sort/tag/<?php echo $tag['id']?>"><?php echo $tag['name']?></a></span>');
				
				var div2 = $('<div class="entry-meta"></div>');
				div2.append(a1);
				div2.append(a2);
				inputCheck.click(function(){toogleTools(inputCheck);})
				divCheck.append(inputCheck);
				divCheck.append(inputHidden);
				li.append(divCheck);
				li.append(div1);
				li.append(a);
				li.append(span);
				li.append(div2);
				li.append(p);
				$("ul.listing").append(li);
			});
			data.count = data.count + 50;
		}
	});
	
	return data;
}

function deleteNote(id)
{	
	$('li#'+id).remove();
	var data = new Object();
	data.id = id;
	$.sajax("/index/deletenote",data,function(newdata){
		if(newdata)
		{
			
		}
		
	});
}

function toogleTools(e)
{
	if($(e).attr("checked"))
	{
		$('div.tools').show();
		$('span#numberOfChecks').html(parseInt($('span#numberOfChecks').html())+1);
    }else{	
    	$('span#numberOfChecks').html(parseInt($('span#numberOfChecks').html())-1);
	    	var cflag = false;
			$.each($('input.checks'),function(i,e){
				if($(e).attr("checked"))
				{
					cflag = true;
				}
			});

			if(!cflag)
			{
				$('div.tools').hide();
			};
	};
};






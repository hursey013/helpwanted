$(window).load(function () {
	// Add hash to url if it's lost when selecting a sort order
	if(hashvalue) {
		window.location.hash = hashvalue;
	}	
	// Take hash value and select the corresponding project
	if(window.location.hash) {
		var hash = window.location.hash;
		$('#repo-list a[href$="' + hash + '"]').trigger("click");
	}
});

$(document).ready(function () {
	// Pagination
	$('#issues .list-group-item:lt(10)').show();
	$('#issues_more').click(function () {
		$('#issues .list-group-item').show();
		$(this).hide();
	});	
	
	// Submit form on select
	$('#issues select').change(function() {
			this.form.submit();
	});

  // Natural language dates
  $(".timeago").timeago();
  
  // Create array of classes
  classes = $("#issues .list-group-item").map(function(){
      return $(this).attr("class").split(' ');    
  });

  // Get distinct values
  var classList = distinctList(classes);

  // Create project link container
  var allItems = $('#issues .list-group-item').length;
  var repoList = '<ul class="list-unstyled" id="repo-list"></ul>';
  repoItem = '<li class="active"><a href="#All">All</a></li>';

  // Add links to project container
  $.each(classList, function(index,value){
    if (value != 'list-group-item') {
      var numItems = $('.' + value).length;
      repoItem += '<li><a href="#'+value+'">'+value+'</a></li>';
    }
  });

  // Add project container to page
  $("#repos div").append($(repoList).append(repoItem));
  
	// Click actions for project list
  $('#repo-list a').click(function(e){
    e.preventDefault();
    // Scroll to top when project link is clicked
    $('html,body').animate({scrollTop: 0}, 700);
		// Add active link to selected project link
    $(this).parent().addClass("active").siblings().removeClass("active");
		// Get hash value from project link
    var filterVal = this.hash.substr(1);
		// Update hidden field with hash value
		$('input#hashvalue').val(filterVal);
		// Add hash value to url
		window.location.hash = filterVal;
    if(filterVal == 'All') {
      $('#issues .list-group-item.hidden').fadeIn('slow').removeClass('hidden');
      $('#issues blockquote').fadeOut('fast');
    } else {
      $('#issues .list-group-item').each(function() {
        if(!$(this).hasClass(filterVal)) {
          $(this).fadeOut('normal').addClass('hidden');
        } else {
          $(this).fadeIn('slow').removeClass('hidden');
        }
      });
			// Add project description to page
			$('#issues blockquote').fadeIn('slow');
			$('#issues blockquote p').html('<a href="'+repos[filterVal].url+'" target="_blank">'+filterVal+'</a> <small>'+repos[filterVal].description+'</small>');		
    }
		// Pagination
		var issues = $('#issues .list-group-item').not('.hidden').size();
		if(issues > 10){
			$('#issues .list-group-item:visible:gt(9)').hide();
			$('#issues_more').show();
		}else{
			$('#issues_more').hide();
		}
    return false;
  });      
	
	// Back to top link
	var offset = 300,offset_opacity = 1200,scroll_top_duration = 700,$back_to_top = $('.cd-top');
	// Hide or show the back to top link
	$(window).scroll(function(){
		( $(this).scrollTop() > offset ) ? $back_to_top.addClass('cd-is-visible') : $back_to_top.removeClass('cd-is-visible cd-fade-out');
		if( $(this).scrollTop() > offset_opacity ) { 
			$back_to_top.addClass('cd-fade-out');
		}
	});
	
	// Smooth scroll to top
	$back_to_top.on('click', function(event){
		event.preventDefault();
		$('body,html').animate({
			scrollTop: 0 ,
		 	}, scroll_top_duration
		);
	});
});

// Function to create a distinct list from array
function distinctList(inputArray){
	var i;
	var length = inputArray.length;
	var outputArray = [];
	var temp = {};
	for (i = 0; i < length; i++) {
			temp[inputArray[i]] = 0;
	}
	for (i in temp) {
			outputArray.push(i);
	}
	outputArray.sort(function (a, b) {
			return a.toLowerCase().localeCompare(b.toLowerCase());
	});  
	return outputArray;
}  
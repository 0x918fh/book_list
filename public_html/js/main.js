function inlineEdit(obj, id){
	jQuery.ajax({
		method: 'post',
		url: '/inline_form/' + id,
		dataType: 'json',
		success: function(data, textStatus, jqXHR){
			if(data.status){
				var row = jQuery(obj).closest('tr');
				row.find('.bookCellInfo').addClass('hidden');
				row.find('.bookCellEdit').removeClass('hidden').html(data.html);
			}
			else{
				console.log(data);
			}
		},
		error: function(jqXHR, textStatus, errorThrow){
			alert('Не удалось показать форму редактирования');
		}
	});
}

function cancelInlineEdit(obj){
	var row = jQuery(obj).closest('tr');
	row.find('.bookCellEdit').addClass('hidden').html('');
	row.find('.bookCellInfo').removeClass('hidden');
}

function inlineAuthorDelete(obj){
	jQuery(obj).closest('.formInlineAuthor').remove();
}

function inlineAuthorParse(wrap){
	var authors = [];
	wrap.find('.formInlineAuthor').each(function(){
		authors.push(jQuery(this).attr('author'))
	});
	
	return authors.length;
}

function showAuthorSelect(letter = 'А', bookId = -1){
	jQuery.ajax({
		method: 'post',
		url: '/author_list_letter',
		dataType: 'json',
		data: {
			letter: letter,
		},
		success: function(data, textStatus, jqXHR){
			jQuery('#authorList').html('');
			if(data.count > 0){
				jQuery('#authorList').append(jQuery('<tr>', {
					append: jQuery('<td>', {
						align: 'center',
						html: '<strong><i>Авторы на букву "' + letter + '"</i></strong>',
					}),
				}));
				data.authors.forEach(function(item){
					jQuery('#authorList').append(jQuery('<tr>', {
						append: jQuery('<td>', {
							append: jQuery('<a>', {
								href: '#',
								html: item.fio,
								author_id: item.id,
								onclick: 'authorSelect(this); return false;',
							}),
						}),
					}));
				});
			}
			else{
				jQuery('#authorList').append(jQuery('<tr>', {
					append: jQuery('<td>', {
						align: 'center',
						html: '<strong><i>Нет авторов на букву "' + letter + '"</i></strong>',
					}),
				}));
			}
			if(bookId > 0){
				jQuery('#modalWrap').attr('bookId', bookId);
			}
			
			jQuery('#modalWrap').removeClass('hidden');
		},
		error: function(jqXHR, textStatus, errorThrow){
		  alert('Не удалось получить список авторов');
		}
	});
}

function hideModal(obj){
	if(event.target === obj){
		jQuery('#modalWrap').addClass('hidden');
	}
}

function authorLetterSelect(obj){
	showAuthorSelect(obj.getAttribute('letter'), jQuery('#modalWrap').attr('bookId'));
}

jQuery(document).ready(function(){
	jQuery('#book_edit_cover').change();
});

function showCover(obj){
	if(obj.files && obj.files[0]){
		var reader = new FileReader();
		reader.onload = function(e){
			jQuery('#coverSelector').attr('src', e.target.result);
		}
		reader.readAsDataURL(obj.files[0]);
	}
}

function showAuthorSelect(letter = 'А'){
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
	showAuthorSelect(obj.getAttribute('letter'));
}

function authorSelect(obj){
	jQuery('#bookAuthors').append(jQuery('<tr>', {
		append: jQuery('<td>', {
			class: 'authorId hidden',
			text: obj.getAttribute('author_id')
		}).add(jQuery('<td>', {
			class: 'authorFio',
			text: jQuery(obj).text(),
		})).add(jQuery('<td>', {
			append: jQuery('<button>', {
				type: 'button',
				onclick: 'removeAuthor(this)',
				text: 'удалить',
			}),
		})),
	}));
	
	hideModal(obj);
}

function removeAuthor(obj){
	jQuery(obj).closest('tr').remove();
}

function saveBook(){
	var authorList = [];
	jQuery('#bookAuthors .authorId').each(function(){
		authorList.push(+jQuery(this).text());
	});
	
	if(authorList.length < 1){
		alert('Не выбран ни один автор');
		event.preventDefault();
		return false;
	}
	
	jQuery('#book_edit_author').val(JSON.stringify(authorList));
}
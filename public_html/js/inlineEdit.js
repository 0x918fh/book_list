function authorSelect(obj){
	var bookId = jQuery('#modalWrap').attr('bookId');
	
	var cell = jQuery('.bookCellEdit[bookId="' + bookId + '"]');
	
	var fio = '';
	var fioWords = jQuery(obj).text().trim().split(' ');
	if(fioWords.length > 1){
		fio += fioWords[0];
		for(var i = 1; i < fioWords.length; i++){
			fio += ' ' + fioWords[i][0] + '.';
		}
	}
	
	jQuery(cell).find('.inlineAuthorList').append(jQuery('<span>', {
		class: 'formInlineAuthor',
		author: obj.getAttribute('author_id'),
		text: fio,
		append: jQuery('<button>', {
			type: 'button',
			class: 'formInlineAuthorDel',
			onclick: 'inlineAuthorDelete(this)',
			text: 'x',
		}),
	}));
	
	hideModal(obj);
}

function saveInlineBook(obj){
	var cell = jQuery(obj).closest('td');
	var book = {
		id: cell.find('.formInlineId').val(),
		title: cell.find('.formInlineTitle').val(),
		year: cell.find('.formInlineYear').val(),
		description: cell.find('.formInlineDescription').val(),
	};
	var authors = [];
	cell.find('.formInlineAuthor').each(function(){
		authors.push(+this.getAttribute('author'));
	});
	cell.find('.formInlineAuthorRaw').val(JSON.stringify(authors));
	book.authors = cell.find('.formInlineAuthorRaw').val();
	
	
	jQuery.ajax({
		method: 'post',
		url: '/book_inline_save',
		dataType: 'json',
		data: {
			book: book,
		},
		success: function(data, textStatus, jqXHR){
			console.log(data);
			if(data.status){
				cell.siblings('.bookCellInfo').html(data.bookCard).removeClass('hidden');
				cell.addClass('hidden');
			}
			else{
				alert(data.message);
				console.log(data);
			}
		},
		error: function(jqXHR, textStatus, errorThrow){
		  alert('Не удалось сохранить книгу');
		}
	});
}
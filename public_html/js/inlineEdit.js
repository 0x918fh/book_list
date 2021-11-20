function authorSelect(obj){
	var bookId = jQuery('#modalAuthor').attr('bookId');
	
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
		authorFio: jQuery(obj).text().trim(),
		text: fio,
		append: jQuery('<button>', {
			type: 'button',
			class: 'formInlineAuthorDel btn btn-danger btn-xs',
			onclick: 'inlineAuthorDelete(this)',
			text: 'x',
		}),
	}));
	
	jQuery('#modalAuthor').modal('hide');
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
	var authorsName = [];
	cell.find('.formInlineAuthor').each(function(){
		authorsName.push(this.getAttribute('authorFio'));
		authors.push(+this.getAttribute('author'));
	});
	//cell.find('.formInlineAuthorRaw').val(authorsName.join('; '));
	book.author = authorsName.join('; ');
	book.authors = JSON.stringify(authors);
	
	
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
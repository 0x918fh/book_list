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
				class: 'btn btn-danger btn-sm',
				onclick: 'removeAuthor(this)',
				text: 'убрать',
			}),
		})),
	}));
	
	jQuery('#modalAuthor').modal('hide');
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
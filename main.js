$(document).ready(function() {
        $('#exampleModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var title = button.data('title'); // Extract info from data-* attributes
            var content = button.data('content'); // Extract info from data-* attributes
            var author = button.data('author'); // Extract info from data-* attributes
            var id = button.data('id'); // Extract info from data-* attributes
            var modal = $(this);
            modal.find(".modal-body input[name='title']").val(title);
            modal.find(".modal-body textarea[name='content']").val(content);
            modal.find(".modal-body input[name='author']").val(author);
            modal.find(".modal-body input[name='id']").val(id);
        });
	$('#add-product-btn').on('click', addPunEvent);
	function addPunEvent(e) {
		e.preventDefault();
		let title = $('input[name="title"]');
		let content = $('textarea[name="content"]');
		let author = $('input[name="author"]');
		$.ajax({
			method: 'POST',
			url: 'add.php',
			data: { // Skickas till add.php i form av POST parametrar
				addBtn: true, 
				title: title.val(),
                content:content.val(),
                author:author.val()
			}, 
			dataType: 'json',
			success: function(data) {
//				console.log(data);
				$('#form-message').html(data['message']);
				appendPunList(data);
			},
		});
	}
	$('.delete-pun-btn').on('click', deletePunEvent);
	function deletePunEvent(e) {
		e.preventDefault();
		let hidId = $(this).parent().find('input[name="hidId"]');
		 console.log(hidId.val());
		$.ajax({
			method: 'POST',
			url: 'delete.php',
			data: { // Skickas till add.php i form av POST parametrar
				deleteBtn: true, 
				hidId: hidId.val() 
			},
			dataType: 'json',
			success: function(data) {
				console.log(data);
				$('#form-message').html(data['message']);
				appendPunList(data);
			},
		});
	}
	$('.update-pun-btn').on('click', updatePunEvent);
	function updatePunEvent(e) {
		e.preventDefault();
		let id = $('#exampleModal input[name="id"]');
		let title = $('#exampleModal input[name="title"]');
		let content = $('#exampleModal textarea[name="content"]');
		let author = $('#exampleModal input[name="author"]');
		// console.log(id.val());
		// console.log(pun.val());
		$.ajax({
			method: 'POST',
			url: 'update.php',
			data: { // Skickas till add.php i form av POST parametrar
				updateBtn: true, 
				title: title.val(),
				content: content.val(),
				author: author.val(),
				id: id.val()
			},
			dataType: 'json',
			success: function(data) {
				// console.log(data);
				$('#form-message').html(data['message']);
				appendPunList(data);
				$('#exampleModal').modal('toggle');
			},
		});
	}
    //search
    	$('#search-input').on('keyup', function() {
		//console.log($(this).val());
		getPunlist($(this).val());
	});
	function getPunlist(searchQuery) {
		$.ajax({
			method: 'GET',
			url: 'search.php',
			data: { // Skickas till search.php i form av GET parametrar
				searchQuery: searchQuery 
			},
			dataType: 'json',
			success: function(data) {
				console.log(data);				
				$('#form-message').html(data['message']);
				appendPunList(data);
			},
		});
	}
	// Run the function getPunlist, on new pageload
	window.load = getPunlist('');
//merge with html
	function appendPunList(data) {
		let html = '';
		for (pun of data['posts']) {
//			console.log(pun);
			html +=
				'<li class="list-group-item">' +
					'<p class="float-left">' +
						'<h3>'+ pun['title'] + '</h3>' +
						pun['content'] +
                '<h4>'+ pun['author'] + '</h4>' +
						pun['published_date'] +
					'</p>' +
					'<form action="" method="POST" class="float-right">' +
						'<input type="hidden" name="hidId" value="' + pun['id'] + '">' +
						'<input type="submit" name="deleteBtn" value="Delete" class="btn btn-danger delete-pun-btn">' +
					'</form>' +
					'<button type="button" class="btn btn-success float-right" data-toggle="modal" data-target="#exampleModal" data-title="' + pun['title'] + '" data-content="' + pun['content'] + '" data-author="' + pun['author'] + '" data-id="' + pun['id'] + '">Update</button>' +
				'</li>';
		}
		// Append newly generetad pun list
		$('#post-list').html(html);
		// Add eventlisteners
		$('.delete-pun-btn').on('click', deletePunEvent);
	}
});	
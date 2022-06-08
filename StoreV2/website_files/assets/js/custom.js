$(document).ready(function() {
	$('[data-toggle="tooltip"]').tooltip();

	var href = document.location.href;
	var path = href.substr(href.lastIndexOf('/') + 1);
	var total = 0.0;
	var items = new Array();
	
	$('#itemform').submit(function(e) {
		var count = 0;
		for (var i = 0; i < 10; i++) {
			if ($.cookie('item_'+i) != undefined) {
				count++;
			}
		}

		if (count == 10) {
			e.preventDefault();
			swal("Cart Empty", "Your shopping cart is empty!", "error");
			return;
		}
	});

	$('#pp_form').submit(function(e) {
		var empty = true;
		for (var i = 0; i < 10; i++) {
			if ($.cookie('item_'+i) != undefined) {
				empty = false;
			}
		}

		if (empty) {
			e.preventDefault();
			swal("Cart Empty", "Your shopping cart is empty!", "error");
			return;
		}
	});
	
	$("#clear").click(function(e) {
		e.preventDefault();
		swal({   
			title: "Are you sure?",
			text: "This will empty your cart and you will need to start over.",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: "Yes",
			closeOnConfirm: false
		}, function() {
			for (var i = 0; i < 10; i++) {
				$.removeCookie('item_'+i);
			}
			window.location = "index.php";
		});
	});
	
});
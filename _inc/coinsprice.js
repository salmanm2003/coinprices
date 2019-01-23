function changeLimit(){
	var limit = document.getElementById("limit").value;
	var table = document.getElementById("table_1");
	for (var i = 0; row = table.rows[i]; i++) {
		if(i < limit) {
			row.classList.add('show');
			row.classList.remove('hide');
		} else {
			row.classList.remove('show');
			row.classList.add("hide");
		}
	}
}
function changeLimit() {
    for(t = 1; t < 3; t++)
    {
	    var limit = document.getElementById("limit").value;
	    var table = document.getElementById("table_"+t);
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
}

function upward(rowId) {
//alert('clicked');
  var row   = document.getElementsByTagName('t1_rw_'+rowId);
  var table = document.getElementsByTagName('table_1');
  var clone = cloneNode(true);
  row.remove();
  console.log(e);  
}
<!DOCTYPE html>
<html>
<head>
</head>
<body onload="CreateTable();">
    
</body>

<script>
    function CreateTable() {

        // CREATE DYNAMIC TABLE.
        var table = document.createElement('table');

        // SET THE TABLE ID. 
        // WE WOULD NEED THE ID TO TRAVERSE AND EXTRACT DATA FROM THE TABLE.
        table.setAttribute('id', 'empTable');

        var arrHead = new Array();
        arrHead = ['ID', 'NOME', 'DESCRICAO'];

        var arrValue = new Array();
        arrValue.push(['1', 'TESTE', '1']);
        arrValue.push(['2', 'TESTE', '2']);
        arrValue.push(['3', 'TESTE', '3']);

        var tr = table.insertRow(-1);

        for (var h = 0; h < arrHead.length; h++) {
            var th = document.createElement('th');              // TABLE HEADER.
            th.innerHTML = arrHead[h];
            tr.appendChild(th);
        }

        for (var c = 0; c <= arrValue.length - 1; c++) {
            tr = table.insertRow(-1);

            for (var j = 0; j < arrHead.length; j++) {
                var td = document.createElement('td');          // TABLE DEFINITION.
                td = tr.insertCell(-1);
                td.innerHTML = arrValue[c][j];                  // ADD VALUES TO EACH CELL.
            }
        }

        // NOW CREATE AN INPUT BOX TYPE BUTTON USING createElement() METHOD.
        var button = document.createElement('input');
        var tml = document.createElement('div');
        tml.classList.add('timeline');
        tml.id = 'timeline';

        // SET INPUT ATTRIBUTE 'type' AND 'value'.
        button.setAttribute('type', 'button');
        button.setAttribute('value', 'Read Table Data');

        // ADD THE BUTTON's 'onclick' EVENT.
        button.setAttribute('onclick', 'GetTableValues()');

        // FINALLY ADD THE NEWLY CREATED TABLE AND BUTTON TO THE BODY.
        document.body.appendChild(table);
        document.body.appendChild(button);
        document.body.appendChild(tml);

    }

    function GetTableValues() {

      var eDiv = document.createElement('div');
      eDiv.classList.add('container');
      eDiv.classList.add('left');
      var iDiv = document.createElement('div');
      iDiv.classList.add('content');
      var h2 = document.createElement('h2');
      var tml = document.getElementById('timeline');
      var text = document.createTextNode('Hello World');

      tml.appendChild(eDiv);
      eDiv.appendChild(iDiv);
        iDiv.appendChild(h2);
          h2.appendChild(text);
        // var empTable = document.getElementById('empTable');

        // // CREATE A DIV WHERE WE'LL SHOW THE TABLE WITH DATA.
        // var div = document.createElement('div');
        // div.innerHTML = "";
        // div.innerHTML = '<br />';

        // // TRAVERSE THROUGH THE TABLE TO XTRACT CELL VALUES.
        // for (var r = 1; r <= empTable.rows.length - 1; r++) {        // EACH ROW IN THE TABLE.
        //     // EACH CELL IN A ROW.
        //     for (c = 0; c <= empTable.rows[r].cells.length - 1; c++) {      

        //         // ADD DATA TO THE DIV.
        //         div.innerHTML = div.innerHTML + ' ' +
        //                empTable.rows[r].cells[c].innerHTML;

        //     }
        //     div.innerHTML = div.innerHTML + '<br />';
        // }
        // document.body.appendChild(div);     // APPEND (ADD) THE CONTAINER TO THE BODY.
    }
</script>
<style type="text/css">
  
  * {
  box-sizing: border-box;
}

/* Set a background color */
body {
  font-family: Helvetica, sans-serif;
}

/* The actual timeline (the vertical ruler) */
.timeline {
  position: relative;
  max-width: 1200px;
  margin: 0 auto;
}

/* The actual timeline (the vertical ruler) */
.timeline::after {
  content: '';
  position: absolute;
  width: 6px;
  background-color: white;
  top: 0;
  bottom: 0;
  left: 50%;
  margin-left: -3px;
}

/* Container around content */
.container {
  padding: 10px 40px;
  position: relative;
  background-color: inherit;
  width: 50%;
}

/* The circles on the timeline */
.container::after {
  content: '';
  position: absolute;
  width: 25px;
  height: 25px;
  right: -17px;
  background-color: white;
  border: 4px solid #FF9F55;
  top: 15px;
  border-radius: 50%;
  z-index: 1;
}

/* Place the container to the left */
.left {
  left: 0;
}

/* Place the container to the right */
.right {
  left: 50%;
}

/* Add arrows to the left container (pointing right) */
.left::before {
  content: " ";
  height: 0;
  position: absolute;
  top: 22px;
  width: 0;
  z-index: 1;
  right: 30px;
  border: medium solid white;
  border-width: 10px 0 10px 10px;
  border-color: transparent transparent transparent white;
}

/* Add arrows to the right container (pointing left) */
.right::before {
  content: " ";
  height: 0;
  position: absolute;
  top: 22px;
  width: 0;
  z-index: 1;
  left: 30px;
  border: medium solid white;
  border-width: 10px 10px 10px 0;
  border-color: transparent white transparent transparent;
}

/* Fix the circle for containers on the right side */
.right::after {
  left: -16px;
}

/* The actual content */
.content {
  padding: 20px 30px;
  background-color: white;
  position: relative;
  border-radius: 6px;
}

/* Media queries - Responsive timeline on screens less than 600px wide */
@media screen and (max-width: 600px) {
/* Place the timelime to the left */
  .timeline::after {
    left: 31px;
  }

/* Full-width containers */
  .container {
    width: 100%;
    padding-left: 70px;
    padding-right: 25px;
  }

/* Make sure that all arrows are pointing leftwards */
  .container::before {
    left: 60px;
    border: medium solid white;
    border-width: 10px 10px 10px 0;
    border-color: transparent white transparent transparent;
  }

/* Make sure all circles are at the same spot */
  .left::after, .right::after {
    left: 15px;
  }

/* Make all right containers behave like the left ones */
  .right {
    left: 0%;
  }
}
</style>
</html>

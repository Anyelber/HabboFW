<?php
if(!is_admin_or_more(rol($_SESSION['id']))){
    alert("No tienes permiso para acceder a este modulo","./",0);
}
?>

<div style="padding: 20px;">
<h1><i class="nav-icon fas fa-user-secret"></i> Clones detectados</h1><br><br>
<table class="table table-striped cdt">
    <thead>
    <tr>
        <th>Avatar</th>
        <th>Nombre</th>
        <th>Rango</th>
        <th>Firma</th>
        <th>IP</th>
    </tr>
</thead>
<tbody>
    <?php

$q = $pdo->prepare("SELECT * FROM users WHERE LENGTH(ip) > 0 AND ip in (
    select ip from users group by ip having count(*) > 1
)  AND rol < 63
ORDER BY ip DESC");

$q->execute();

while($r = $q->fetch()){

    

        ?>
        <tr>
            <td><?=keko_user($r['id'])?></td>
            <td><?=nombre_habbo($r['id'])?></td>
            <td><?=nombre_rol(rol($r['id']))?></td>
            <td><?=firma($r['id'])?></td>
            <td><?=$r['ip']?></td>
        </tr>
        <?php
    

}

?>
</tbody>
</table>
</div>

<script>
var table;
var groupColumn = 4;
 
$(document).ready(function() {
 
  table = $('.cdt').DataTable({
    "displayStart": 0,
    "language": {
      "url": "https://cdn.datatables.net/plug-ins/1.10.19/i18n/English.json"
    },
    "columnDefs": [{
      "visible": false,
      "targets": groupColumn
    }],
    "order": [
      [groupColumn, 'asc']
    ],
    "processing": true,
    "pageLength": 25,
    "drawCallback": function(settings) {
      var api = this.api();
      var rows = api.rows({
        page: 'current'
      }).nodes();
      var last = null;
 
      api.column(groupColumn, {
        page: 'current'
      }).data().each(function(group, i) {
        if (last !== group) {
          $(rows).eq(i).before(
            '<tr class="group"><td colspan="15" style="font-weight: bold;">' + group + '</td></tr>'
          );
 
          last = group;
        }
      });
    },
    initComplete: function() {
      this.api().columns().every(function() {
        var column = this;
        var select = $('<select><option value=""></option></select>')
          .appendTo($(column.footer()).empty())
          .on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex(
              $(this).val()
            );
 
            column
              .search(val ? '^' + val + '$' : '', true, false)
              .draw();
          });
 
        column.data().unique().sort().each(function(d, j) {
          select.append('<option value="' + d + '">' + d + '</option>')
        });
      });
    },
  });
 
  // Order by the grouping
  $('#contact_overview_table tbody').on('click', 'tr.group', function() {
    var currentOrder = table.order()[0];
    if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
      table.order([groupColumn, 'desc']).draw();
    } else {
      table.order([groupColumn, 'asc']).draw();
    }
  });
});
    </script>
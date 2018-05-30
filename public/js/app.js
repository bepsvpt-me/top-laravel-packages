if ($('#top-packages').length) {
  let table = $('#top-packages').DataTable({
    'pageLength': 100,
    'order': [[1, 'desc'], [2, 'desc']],
    'columns': [
      { 'searchable': false, 'orderable': false },
      { 'searchable': false },
      { 'searchable': false },
      null,
      { 'orderable': false },
      null,
      null
    ]
  });

  table.on('order.dt search.dt', function () {
    table.column(0, { search:'applied', order:'applied' }).nodes().each(function (cell, i) {
      cell.innerHTML = i + 1;
    });
  }).draw();
}

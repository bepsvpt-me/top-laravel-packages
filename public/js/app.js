$('#hide-official-packages').change(function () {
  let url = URI(location.href);
  let query = url.search(true);

  query.hide_official_packages = $(this).is(":checked") ? '1' : '0';

  url.search(query);

  location.href = url.toString();
});

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

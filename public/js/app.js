$('#top-packages').DataTable({
  columns: [
    { 'searchable': false, 'orderable': false },
    { 'searchable': false },
    { 'searchable': false },
    null,
    { 'orderable': false },
    null,
    null,
  ],
  fixedHeader: true,
  order: [[1, 'desc'], [2, 'desc']],
  pageLength: 100,
  responsive: true,
  scrollX: true,
});
